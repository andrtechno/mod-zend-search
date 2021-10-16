Zend Lucene Search
====================================
Zend Lucene search

[![Latest Stable Version](https://poser.pugx.org/panix/mod-zend-search/v/stable)](https://packagist.org/packages/panix/mod-zend-search)
[![Total Downloads](https://poser.pugx.org/panix/mod-zend-search/downloads)](https://packagist.org/packages/panix/mod-zend-search)
[![Monthly Downloads](https://poser.pugx.org/panix/mod-zend-search/d/monthly)](https://packagist.org/packages/panix/mod-zend-search)
[![Daily Downloads](https://poser.pugx.org/panix/mod-zend-search/d/daily)](https://packagist.org/packages/panix/mod-zend-search)
[![Latest Unstable Version](https://poser.pugx.org/panix/mod-zend-search/v/unstable)](https://packagist.org/packages/panix/mod-zend-search)
[![License](https://poser.pugx.org/panix/mod-zend-search/license)](https://packagist.org/packages/panix/mod-zend-search)

Installation
------------
The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

* Add

```json
"panix/mod-zend-search" : "*",
"zendframework/zendsearch": "2.0.0rc6"
```

to the require section of your application's `composer.json` file.

* Add a new component in `components` section of your application's configuration file, for example:

```php
'components' => [
    'search' => [
        'class' => 'panix\mod\search\Search',
        'models' => [
            'app\modules\page\models\Page',
        ],
    ],
    // ...
],
```

* Add behavior in the AR models, for example:

```php
use panix\mod\search\behaviors\SearchBehavior;

public function behaviors()
{
    return [
        'search' => [
            'class' => SearchBehavior::className(),
            'searchScope' => function ($model) {
                /** @var \yii\db\ActiveQuery $model */
                $model->select(['title', 'body', 'url']);
                $model->andWhere(['indexed' => true]);
            },
            'searchFields' => function ($model) {
                /** @var self $model */
                return [
                    ['name' => 'title', 'value' => $model->title],
                    ['name' => 'body', 'value' => strip_tags($model->body)],
                    ['name' => 'url', 'value' => $model->url, 'type' => SearchBehavior::FIELD_KEYWORD],
                    ['name' => 'model', 'value' => 'page', 'type' => SearchBehavior::FIELD_UNSTORED],
                ];
            }
        ],
    ];
}
```

Resources
---------
* [Zend Lucene](http://framework.zend.com/manual/1.12/en/zend.search.lucene.html)
