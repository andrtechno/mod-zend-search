<?php

namespace panix\mod\search;

use yii\web\AssetBundle;

class SearchAssets extends AssetBundle
{
    public $sourcePath = __DIR__.'/assets';

    public $depends = [
        'yii\web\JqueryAsset'
    ];

    public $css = ['css/search.css'];

    public function init()
    {
        parent::init();
        $this->js[] = YII_DEBUG ? 'js/jquery.highlight-5.js' : 'js/jquery.highlight-5.closure.js';
    }
}
