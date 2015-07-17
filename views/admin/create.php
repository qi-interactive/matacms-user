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
 * @var yii\web\View              $this
 * @var mata\user\models\User $user
 */

$this->title = Yii::t('user', 'Create a user account');
$this->params['breadcrumbs'][] = ['label' => Yii::t('user', 'Users'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="container alert-container">
    <?= $this->render('/_alert', [
        'module' => Yii::$app->getModule('user'),
        ]) ?>
    </div>

    <?php $form = ActiveForm::begin([
        'enableAjaxValidation'   => true,
        'enableClientValidation' => false
        ]); ?>

        <div class="panel panel-default">
            <div class="panel-heading">
                <?= Html::encode($this->title) ?>
            </div>
            <div class="panel-body">
                <div class="alert-section">
                    <div class="alert alert-info">
                        <?= Yii::t('user', 'Credentials will be sent to the user by email') ?>.
                        <?= Yii::t('user', 'A password will be generated automatically if not provided') ?>.
                    </div>
                </div>

                <?= $this->render('_user', ['form' => $form, 'user' => $user]) ?>
            </div>
        </div>

        <div class="form-group submit-form-group">
            <?= Html::submitButton(Yii::t('user', 'Save'), ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

        <script>

          parent.mata.simpleTheme.header
          .setText('YOU\'RE IN <?= Inflector::camel2words($this->context->module->id) ?> MODULE')
          .showBackToListView()
          .hideVersions()
          .show();

      </script>
