<?php

namespace panix\mod\search\commands;

use yii\helpers\Console;
use yii\console\ExitCode;
use panix\engine\console\controllers\ConsoleController;
use Yii;

class DefaultController extends ConsoleController
{
    public $defaultAction = 'indexing';

    // Of course, this function should be in the console part of the application!
    public function actionIndexing()
    {
        /** @var \panix\mod\search\Search $search */
        $search = Yii::$app->search;
       // $search->indexDirectory = '@app/runtime/search/ru';
        $search->language = 'ru';
        $search->index();

       // $search2 = Yii::$app->search;
       // $search2->language = 'en';
      //  $search2->indexDirectory = '@app/runtime/search/en';
       // $search2->index();
        $this->stdout("Done!".PHP_EOL, Console::FG_GREEN);
        return ExitCode::OK;
    }
}
