<?php

namespace panix\mod\search;

use Yii;
use yii\base\Component;
use yii\base\InvalidConfigException;
use yii\helpers\FileHelper;
use ZendSearch\Lucene\Analysis\Analyzer\Analyzer;
use ZendSearch\Lucene\Analysis\Analyzer\Common\Utf8;
use ZendSearch\Lucene\Analysis\Analyzer\Common\Utf8\CaseInsensitive;
use ZendSearch\Lucene\Analysis\Analyzer\Common\Utf8Num;
use ZendSearch\Lucene\Analysis\Analyzer\Common\Utf8Num\CaseInsensitive as CaseInsensitiveNum;
use ZendSearch\Lucene\Document;
use ZendSearch\Lucene\Document\Field;
use ZendSearch\Lucene\Index\Term as IndexTerm;
use ZendSearch\Lucene\Lucene;
use ZendSearch\Lucene\Search\Query\Boolean as QueryBoolean;
use ZendSearch\Lucene\Search\Query\Term;
use ZendSearch\Lucene\Search\Query\Wildcard;
use ZendSearch\Lucene\Search\QueryParser;

/**
 * Zend Lucine search
 *
 * @see https://framework.zend.com/manual/1.12/en/zend.search.lucene.html
 */
class Search extends Component
{
    /** @var array */
    public $models = [];

    /** @var string alias or directory path */
    public $indexDirectory = '@app/runtime/search';

    /** @var boolean */
    public $caseSensitivity = false;

    /** @var int Minimum term prefix length (number of minimum non-wildcard characters) */
    public $minPrefixLength = 3;

    /** @var int 0 means no limit */
    public $resultsLimit = 0;

    /** @var boolean */
    public $parseNumeric = false;

    /** @var \ZendSearch\Lucene\Index[] */
    protected $luceneIndex;
    public $language = 'ru';

    public function __destruct()
    {
        $this->luceneIndex->commit();
    }

    public function init()
    {
        QueryParser::setDefaultEncoding('UTF-8');
        if ($this->caseSensitivity) {
            Analyzer::setDefault($this->parseNumeric ? new Utf8Num() : new Utf8());
        } else {
            Analyzer::setDefault($this->parseNumeric ? new CaseInsensitiveNum() : new CaseInsensitive());
        }

        foreach (Yii::$app->languageManager->languages as $lang) {
//echo $this->indexDirectory.'/'.$lang->code;die;
            // $this->indexDirectory[$lang->code] = FileHelper::normalizePath(Yii::getAlias('@app/runtime/search/'.$lang->code));
            // $this->luceneIndex[$lang->code] = $this->getLuceneIndex($this->indexDirectory[$lang->code]);
        }
        $this->indexDirectory = FileHelper::normalizePath(Yii::getAlias($this->indexDirectory));
        $this->luceneIndex = $this->getLuceneIndex($this->indexDirectory);
//print_r($this->luceneIndex);die;

    }

    public function path($path)
    {
        $this->indexDirectory = FileHelper::normalizePath(Yii::getAlias($path));
        //echo $this->indexDirectory;die;
        $this->luceneIndex = $this->getLuceneIndex($this->indexDirectory);

        return $this;
    }

    /**
     * Indexing the contents of the specified models.
     * @throws InvalidConfigException
     */
    public function index($language = 'ru')
    {
        if ($this->luceneIndex->count() !== 0) {
            $this->luceneIndex = Lucene::create($this->indexDirectory);
        }

        /** @var \yii\db\ActiveRecord $modelName */
        foreach ($this->models as $modelName) {
            /** @var behaviors\SearchBehavior $model */
            /** @var array $page */
            $model = new $modelName;
            if ($model->hasMethod('getSearchModels')) {
                foreach ($model->getSearchModels()->all() as $pageModel) {
                    // $test = call_user_func($model->searchFields, $pageModel);
                    // foreach ($test as $k=>$i){
                    //     $this->luceneIndex->addDocument($this->createDocument($i));
                    // }
                    $this->luceneIndex->addDocument($this->createDocument(
                        call_user_func($model->searchFields, $pageModel)
                    ));
                    //  print_r(call_user_func($model->searchFields, $pageModel));die;

                }
            } else {
                throw new InvalidConfigException(
                    "Not found right `SearchBehavior` behavior in `{$modelName}`."
                );
            }
        }
    }

    /**
     * Search page for the term in the index.
     * @param string $term
     * @param array $fields (string => string)
     * @return array ('results' => \ZendSearch\Lucene\Search\QueryHit[], 'query' => string)
     */
    public function find($term, $fields = [])
    {
        Wildcard::setMinPrefixLength($this->minPrefixLength);
        Lucene::setResultSetLimit($this->resultsLimit);

        if (empty($fields)) {
            return [
                'results' => $this->luceneIndex->find($term),
                'query' => $term
            ];
        }

        $query = new QueryBoolean();
        $query->addSubquery(QueryParser::parse($term), true);
        foreach ($fields as $field => $fieldText) {
            $query->addSubquery(new Term(new IndexTerm($fieldText, $field)), true);
        }
        return [
            'results' => $this->luceneIndex->find($query),
            'query' => $term
        ];
    }

    /**
     * Delete document from the index.
     * @param string $text
     * @param string|null $field
     */
    public function delete($text, $field = null)
    {
        $query = new Term(new IndexTerm($text, $field));
        $hits = $this->luceneIndex->find($query);
        foreach ($hits as $hit) {
            $this->luceneIndex->delete($hit);
        }
    }

    /**
     * Add document to the index.
     * @param array $fields ('name' => string, 'value' => string, ['type' => string])
     * Default type is 'text'.
     */
    public function add($fields)
    {
        $this->luceneIndex->addDocument(
            $this->createDocument($fields)
        );
    }

    /**
     * Full index optimization.
     * Each index segment is entirely independent portion of data.
     * So indexes containing more segments need more memory and time for searching.
     * Index optimization is a process of merging several segments into a new one.
     * Index optimization works with data streams and doesn't
     * take a lot of memory but does require processor resources and time.
     */
    public function optimize()
    {
        $this->luceneIndex->optimize();
    }

    /**
     * @param string $directory
     * @return \ZendSearch\Lucene\SearchIndexInterface
     */
    protected function getLuceneIndex($directory)
    {
        if (file_exists($directory . DIRECTORY_SEPARATOR . 'segments.gen')) {
            return Lucene::open($directory);
        } else {
            return Lucene::create($directory);
        }
    }

    /**
     * @param array $fields ('name' => string, 'value' => string, ['type' => string])
     * Default type is 'text'.
     * @return Document
     */
    protected function createDocument($fields)
    {
        $document = new Document();
        foreach ($fields as $field) {
            if (isset($field['type'])) {
                $currentType = $field['type'];
            } else {
                $currentType = behaviors\SearchBehavior::FIELD_TEXT;
            }

            $document->addField(Field::$currentType(
                $field['name'],
                $field['value']
            //  $field['language']
            ));
        }
        return $document;
    }
}
