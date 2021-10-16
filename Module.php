<?php

namespace panix\mod\search;

use panix\engine\WebModule;
use yii\base\BootstrapInterface;
use yii\web\GroupUrlRule;


class Module extends WebModule implements BootstrapInterface
{
  //  public $controllerNamespace = 'panix\mod\search\controllers';




    public function bootstrap($app)
    {

        /*$groupUrlRule = new GroupUrlRule([
            'prefix' => $this->id,
            'rules' => [
                '<slug:[0-9a-zA-Z_\-]+>/page/<page:\d+>/per-page/<per-page:\d+>' => 'default/view',
                '<slug:[0-9a-zA-Z_\-]+>/page/<page:\d+>' => 'default/view',
                '<slug:[0-9a-zA-Z_\-]+>' => 'default/view',
                'page/<page:\d+>/per-page/<per-page:\d+>' => 'default/index',
                'page/<page:\d+>' => 'default/index',
                'tag/<tag:[\w\d\s]+>' => 'default/index',
                '' => 'default/index',
            ],
        ]);
        $app->getUrlManager()->addRules($groupUrlRule->rules, false);*/


        $rules = [];

            $rules['<q:\w+>'] = 'default/index';


        $rules[''] = 'default/index';
        $groupUrlRule = new GroupUrlRule([
            'prefix' => $this->id,
            'rules' => $rules,
        ]);
        $app->getUrlManager()->addRules($groupUrlRule->rules, false);
    }

}
