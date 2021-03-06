<?php

namespace panix\mod\search\controllers;

use panix\engine\CMS;
use Yii;
use panix\engine\controllers\WebController;
use yii\data\ArrayDataProvider;

class DefaultController extends WebController
{
    const PAGE_SIZE = 10;

    public function actionIndex()
    {

       $q = Yii::$app->request->getQueryParam('q');
        if(!$q){
            return $this->error404();
        }
        /** @var \panix\mod\search\Search $search */
        $search = Yii::$app->search;
        // CMS::dump($search);die;
        //  $searchData = $search->find($q); // Search by full index.
        $searchData = $search->find($q, ['language' => Yii::$app->language]); // Search by index provided only by model `page`.
//CMS::dump($searchData);die;
        $dataProvider = new ArrayDataProvider([
            'allModels' => $searchData['results'],
            'pagination' => ['pageSize' => self::PAGE_SIZE],
        ]);

        return $this->render(
            'index',
            [
                'hits' => $dataProvider->getModels(),
                'pagination' => $dataProvider->getPagination(),
                'query' => $searchData['query'],
                'totalCount'=>$dataProvider->totalCount
            ]
        );
    }

    // Of course, this function should be in the console part of the application!
    public function actionIndexing()
    {
        /** @var \panix\mod\search\Search $search */
        $search = Yii::$app->search;
        $search->index();
    }
}
