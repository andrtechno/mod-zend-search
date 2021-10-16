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

app\modules\search\SearchAssets::register($this);
$this->registerJs("jQuery('.search').highlight('{$query}');");

//\panix\engine\CMS::dump($hits);die;
?>

<div class="case-details">
    <div class="container">
        <div class="row">
            <div class="col-lg-9 col-md-12">
                <div class="process case-dt mg-case-details">
                    <?php
                    if (!empty($hits)) { ?>
                        <h3 class="title d-none">По запросу "<?= $query ?>" found!</h3>
                        <ul class="process-steps">
                            <?php foreach ($hits as $key => $hit) {

                                ?>
                                <li class="steps">
                                    <div class="steps-name">
                                        <span class="number"><?= $key + 1; ?></span>
                                        <span class="name">
                                <?php if (isset($hit->url)) { ?>
                                    <a href="<?= yii\helpers\Url::to(\yii\helpers\Json::decode($hit->url)) ?>"><?= $hit->title ?></a>
                                <?php } else { ?>
                                    <?= $hit->title ?>
                                <?php } ?>
                                        </span>
                                    </div>
                                    <div class="content-steps">
                                        <?php if (!empty($hit->short_text)) { ?>
                                            <?= html_entity_decode($hit->short_text) ?>
                                        <?php } else { ?>
                                            <?= html_entity_decode($hit->text) ?>
                                        <?php } ?>

                                    </div>
                                </li>


                                <hr/>
                            <?php } ?>
                        </ul>
                    <?php } else { ?>
                        <h3>По запросу "<?= $query ?>" нечего не найдено.</h3>
                        <?php
                    }


                    echo yii\widgets\LinkPager::widget([
                        'pagination' => $pagination,
                    ]);
                    ?>

                </div>

            </div>
            <div class="col-lg-3 col-md-12">
                <div class="sidebar-case">
                    <div class="widget">
                        <h3 class="widget-title"><span>Популярные услуги</span></h3>
                    </div>


                    <?php

                    $services = \app\modules\services\models\Services::find()
                        ->andWhere(['important' => 1])
                        ->published()
                        ->limit(3)
                        ->all();
                    foreach ($services as $item) {
                        $image = $item->getImageUrl('image', '370x216');
                        ?>
                        <div class="similar-case">
                            <div class="featured-post">
                                <div class="entry-image">
                                    <?= Html::img($image, ['alt' => $item->name, 'title' => $item->name]); ?>

                                </div>
                            </div>
                            <div class="case-content">
                                <h3 class="title"><?= Html::a($item->name, $item->getUrl($item->category->slug)); ?></h3>
                            </div>
                        </div>
                    <?php } ?>


                </div>
            </div>
        </div>
    </div>
</div><!-- case-details -->
