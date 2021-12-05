<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var ZendSearch\Lucene\Search\QueryHit[] $hits */
/** @var string $query */
/** @var yii\data\Pagination $pagination */

$query = yii\helpers\Html::encode($query);

$this->title = "Результат поиска \"$query\"";
$this->context->pageName = 'Результат поиска';
$this->params['breadcrumbs'] = ['Поиск', $this->title];

panix\mod\search\SearchAssets::register($this);
$this->registerJs("jQuery('.search').highlight('{$query}');");

//\panix\engine\CMS::dump($hits);die;
?>


    <div class="container">
        <div class="row">
            <div class="col-lg-9 col-md-12">
                <div class="process case-dt mg-case-details">
                    <?php
                    if (!empty($hits)) { ?>
                        <h1 class=""><?= Yii::t('search/default','RESULT',[$query,$totalCount]); ?></h1>

                            <?php foreach ($hits as $key => $hit) {

                                ?>
                                <div class="card mb-3">
                                    <div class="card-header">
                                        <?php if (isset($hit->url)) { ?>
                                            <a href="<?= yii\helpers\Url::to(\yii\helpers\Json::decode($hit->url)) ?>"><?= $hit->title ?></a>
                                        <?php } else { ?>
                                            <?= $hit->title ?>
                                        <?php } ?>
                                    </div>

                                    <div class="card-body">
                                        <?php if (!empty($hit->short_text)) { ?>
                                            <?= html_entity_decode($hit->short_text) ?>
                                        <?php } else { ?>
                                            <?= html_entity_decode($hit->text) ?>
                                        <?php } ?>

                                    </div>
                                </div>

                            <?php } ?>

                    <?php } else { ?>
                        <h3><?= Yii::t('search/default','NOT_RESULT',$query); ?></h3>
                        <?php
                    }


                    echo \yii\bootstrap4\LinkPager::widget([
                        'pagination' => $pagination,
                    ]);
                    ?>

                </div>

            </div>
        </div>
    </div>

