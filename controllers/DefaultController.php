<?php

namespace panix\mod\search\controllers;

use panix\engine\CMS;
use Yii;
use panix\engine\controllers\WebController;
use yii\data\ArrayDataProvider;

class DefaultController extends WebController
{
    const PAGE_SIZE = 10;

    public function actionIndex($q = '')
    {
        /** @var \himiklab\yii2\search\Search $search */
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
                'query' => $searchData['query']
            ]
        );
    }

    // Of course, this function should be in the console part of the application!
    public function actionIndexing()
    {
        /** @var \himiklab\yii2\search\Search $search */
        $search = Yii::$app->search;
        $search->index();
    }
}
