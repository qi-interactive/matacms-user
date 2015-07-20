<?php

/*
 * This file is part of the mata project.
 *
 * (c) mata project <http://github.com/mata>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

use yii\helpers\Html;
use yii\widgets\Pjax;
use yii\widgets\ListView;
use yii\helpers\Inflector;

/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var matacms\user\models\UserSearch $searchModel
 */

$this->title = Yii::t('user', 'Manage users');
$this->params['breadcrumbs'][] = $this->title;
?>
<h1><?= Html::encode($this->title) ?> <?= Html::a(Yii::t('user', 'Create a user account'), ['create'], ['class' => 'btn btn-success']) ?></h1>

<?= $this->render('/_alert', [
  'module' => Yii::$app->getModule('user'),
  ]) ?>

  <div class="content-block-index">
    <div class="content-block-top-bar">
      <div class="row">
        <div class="btns-container">
          <div class="elements">
            <?= Html::a(sprintf('Create %s', Yii::$app->controller->getModel()->getModelLabel()), ['create'], ['class' => 'btn btn-success']) ?>
          </div>
        </div>
        <div class="search-container">
          <div class="search-input-container">
            <input class="search-input" id="item-search" placeholder="Type to search" value="" name="search">
            <div class="search-submit-btn"><input type="submit" value=""></div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <?php

  $pjax = Pjax::begin([
   "timeout" => 10000,
   "scrollTo" => false
   ]);

   if (count($searchModel->filterableAttributes()) > 0):  ?>

   <div class="content-block-index">
     <div class="content-block-top-bar sort-by-wrapper">
       <div class="top-bar-sort-by-container">
         <ul>
           <li class="sort-by-label"> Sort by </li>
           <?php foreach ($searchModel->filterableAttributes() as $attribute): ?>
             <li>
              <?php

            //  Sorting resets page count
              $link = $sort->link($attribute);
              echo preg_replace("/page=\d*/", "page=1", $link);
              ?> </li>
            <?php endforeach; ?>
          </ul>
        </div>
      </div>
    </div>

  <?php endif; ?>


  <div class="border"> </div>

  <?php echo ListView::widget([
    'dataProvider' => $dataProvider,
    'id' => 'infinite-list-view',
    'itemView' => '_itemView',
    'layout' => "{items}\n{pager}",
    'pager' => [
    'class' => '\mata\widgets\InfiniteScrollPager\InfiniteScrollPager',
    'clientOptions' => [
    'pjax' => [
    'id' => $pjax->options['id'],
    ],
    'listViewId' => 'infinite-list-view',
    'itemSelector' => 'div[data-key]'
    ]
    ]
    ]);

    ?>
    <?php Pjax::end() ?>

    <?php

    if (count($searchModel->filterableAttributes()) > 0)
      $this->registerJs('
        $("#item-search").on("keyup", function() {
          var attrs = ' . json_encode($searchModel->filterableAttributes()) . ';
          var reqAttrs = []
          var value = $(this).val();
          $(attrs).each(function(i, attr) {
            reqAttrs.push({
              "name" : "' . $searchModel->formName() . '[" + attr + "]",
              "value" : value
            });
  });

    $.pjax.reload({container:"#w0", "url" : "?" + decodeURIComponent($.param(reqAttrs))});
  })
    ');

    ?>

    <script>

      parent.mata.simpleTheme.header
      .setText('YOU\'RE IN MANAGE <?= Inflector::camel2words($this->context->module->id) ?> MODULE')
      .hideBackToListView()
      .hideVersions()
      .show();

    </script>
