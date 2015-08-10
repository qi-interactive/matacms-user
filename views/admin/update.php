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
 * @var yii\web\View                 $this
 * @var matacms\user\models\User    $user
 * @var matacms\user\models\Profile $profile
 * @var matacms\user\Module         $module
 */

$this->title = Yii::t('user', 'Update user account');
$this->params['breadcrumbs'][] = ['label' => Yii::t('user', 'Users'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="container alert-container">
    <?= $this->render('/_alert', [
        'module' => Yii::$app->getModule('user'),
        ]) ?>
    </div>
    <?php if(!empty($user->created_at)) : ?>
        <div class="user-registered-date">
            Registered on <?php
            echo  date('F d, Y H:i', strtotime($user->created_at));
            ?>
        </div>
    <?php endif; ?>

    <?php $form = ActiveForm::begin([
        'enableAjaxValidation'   => true,
        'enableClientValidation' => false
        ]); ?>


        <div class="panel panel-default">
            <div class="panel-heading">
                <?= Html::encode($this->title) ?>
            </div>
            <div class="panel-body">
                <?= $this->render('_user', ['form' => $form, 'user' => $user]) ?>
            </div>
        </div>

        <div class="panel panel-default">
            <div class="panel-heading">
                <?= Yii::t('user', 'Update user profile') ?>
            </div>
            <div class="panel-body">
                <?= $this->render('_profile', ['form' => $form, 'profile' => $profile]) ?>
            </div>
        </div>

       <div class="panel panel-default">
            <div class="panel-heading">
                <?= Yii::t('user', 'Update user permissions') ?>
            </div>
            <div class="panel-body">
                <?php if(isset(Yii::$app->extensions['matacms/matacms-rbac'])): ?>
                <?= $this->render('_roleAssignments', ['form' => $form, 'userModel' => $user]) ?>
                <?php endif; ?>
                <?= $this->render('_moduleAccessibility', ['form' => $form, 'userModel' => $user]) ?>
            </div>
        </div>

        <div class="form-group submit-form-group">
            <?= Html::submitButton(Yii::t('user', 'Save'), ['class' => 'btn btn-primary btn-sm']) ?>
            <?php if (!$user->getIsConfirmed()): ?>
                <?= Html::a(Yii::t('user', 'Confirm'), ['confirm', 'id' => $user->id, 'back' => 'update'], ['class' => 'btn btn-success btn-sm', 'data-method' => 'post']) ?>
            <?php endif; ?>
            <?php if ($user->getIsBlocked()): ?>
                <?= Html::a(Yii::t('user', 'Unblock'), ['block', 'id' => $user->id, 'back' => 'update'], ['class' => 'btn btn-success btn-sm', 'data-method' => 'post', 'data-confirm' => Yii::t('user', 'Are you sure you want to block this user?')]) ?>
            <?php else: ?>
                <?= Html::a(Yii::t('user', 'Block'), ['block', 'id' => $user->id, 'back' => 'update'], ['class' => 'btn btn-danger btn-sm', 'data-method' => 'post', 'data-confirm' => Yii::t('user', 'Are you sure you want to block this user?')]) ?>
            <?php endif; ?>
        </div>

        <?php ActiveForm::end(); ?>

        <script>

          parent.mata.simpleTheme.header
          .setText('UPDATE USER: <span> <?=$profile->name ?></span>')
          .showBackToListView()
          .hideVersions()
          .show();

      </script>
