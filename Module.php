<?php

namespace panix\mod\search;

use panix\engine\WebModule;
use yii\base\BootstrapInterface;
use yii\web\GroupUrlRule;


class Module extends WebModule implements BootstrapInterface
{

    public function bootstrap($app)
    {

      /*  $app->urlManager->addRules(
            [
                'tester' => 'search/default/index',
            ],
            false
        );*/

      //  $rules['tag/<tag:[\w\d\s]+>'] = 'default/index';
        $rules['query'] = 'default/index';
        $groupUrlRule = new GroupUrlRule([
            'prefix' => $this->id,
            'rules' => $rules,
        ]);
        $app->getUrlManager()->addRules($groupUrlRule->rules, false);
    }

}
