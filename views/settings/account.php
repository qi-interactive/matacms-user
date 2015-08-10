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
use matacms\widgets\ActiveForm;
use yii\helpers\Inflector;

\matacms\theme\simple\assets\UserAsset::register($this);

/**
 * @var $this  yii\web\View
 * @var $form  yii\widgets\ActiveForm
 * @var $model mata\user\models\SettingsForm
 */

$this->title = Yii::t('user', 'Account settings');
$this->params['breadcrumbs'][] = $this->title;
?>

<?= $this->render('/_alert', ['module' => Yii::$app->getModule('user')]) ?>

<div class="account-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php $form = ActiveForm::begin([
        'id' => 'account-form',
        'enableAjaxValidation' => true,
        'enableClientValidation' => false,
        ]); ?>

        <div class="panel panel-default">
            <div class="panel-heading">Account details</div>
            <div class="panel-body">
                <?= $form->field($accountModel, 'email') ?>

                <?= $form->field($accountModel, 'username') ?>

                <?= $form->field($accountModel, 'new_password')->passwordInput() ?>

                <?= $form->field($accountModel, 'current_password')->passwordInput() ?>
            </div>
        </div>

        <div class="panel panel-default">
            <div class="panel-heading">User Profile details </div>
            <div class="panel-body">
                <?= $form->field($profileModel, 'name') ?>

                <?= $form->field($profileModel, 'Avatar')->media() ?>
            </div>
        </div>


        <?= $form->submitButton($profileModel) ?>

        <?php ActiveForm::end(); ?>
    </div>

    <script>

      parent.mata.simpleTheme.header
      .setText('YOU\'RE IN <?= Inflector::camel2words($this->context->module->id) ?> MODULE')
      .hideBackToListView()
      .hideVersions()
      .show();

  </script>
