<?php

use matacms\user\models\Profile;
use yii\helpers\Html;
use matacms\theme\simple\assets\ListAsset;

ListAsset::register($this);

$moduleBaseUrl = sprintf("/mata-cms/%s/%s", $this->context->module->id, $this->context->id);

$module = \Yii::$app->getModule("environment");

$profileAvatarURI = !empty($avatar = $model->profile->getMediaAvatar()) ? $avatar->URI : null ;

?>

<div class="list-container row <?= empty($model->filterableAttributes()) ? 'simple-list-container' : ''; ?>">
    <?php if ($uri = $profileAvatarURI) {
        $thumbnailActiveClass = "thumbnail-active";
        ?>

        <div class="list-thumbnail"><div class="list-thumbnail-img" style="background-image: url(<?=$uri ?>)"></div></div>

        <?php } else {
            $thumbnailActiveClass = " ";
        } ?>
        <a href='<?= sprintf("%s/update?id=%d", $moduleBaseUrl, $model->primaryKey );?>' class="list-link" title="Update" aria-label="Update" data-pjax="0">
            <div class="list-contents-container <?= $thumbnailActiveClass ?>">
                <div class="list-label">

                    <?php if ($model->isConfirmed): ?>
                        <div class="list-version-container confirm">
                            <div class="fadding-container"> </div>
                            <div class="list-version-inner-container">
                                <div class="version-status">
                                    <?= Yii::t('user', 'Confirmed') ?>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>


                    <?php if (!$model->isConfirmed): ?>
                     <?php   Html::a(Yii::t('user', 'Confirm'), ['confirm', 'id' => $model->id], [
                        'class' => 'btn btn-xs btn-success btn-block',
                        'data-method' => 'post',
                        'data-confirm' => Yii::t('user', 'Are you sure you want to confirm this user?'),
                        ]);

                        ?>

                    <?php endif; ?>

                    <span class='item-label'><?= $model->getLabel();?></span> </div>
                    <div class="list-sub-details row">
                        <?php
                        $columnsClass = '';
                        switch(count($model->filterableAttributes())) {
                            case 1:
                            $columnsClass = 'twelve';
                            break;
                            case 2:
                            $columnsClass = 'six';
                            break;
                            case 3:
                            $columnsClass = 'four';
                            break;
                            case 4:
                            $columnsClass = 'three';
                            break;

                        }

                        foreach ($model->filterableAttributes() as $attribute):

                            ?>

                        <div class="<?= $columnsClass?> columns">
                            <span class="label"><?= $model->getAttributeLabel($attribute).': '?></span><?= $model->$attribute ?> <div class="fadding-container"> </div>
                        </div>
                    <?php endforeach;
                    ?>

                </div>
            </div>
        </a>
        <a class='delete-btn' href="<?= sprintf("%s/delete?id=%d", $moduleBaseUrl, $model->primaryKey );?>" title="Delete" aria-label="Delete" data-confirm="Are you sure you want to delete this user?" data-method="post" data-pjax="0">
           <?php if(method_exists($model, 'canBeDeleted')) {
           // echo "data-delete-allowed=\"" . var_export($model->canBeDeleted(), true) . "\"";
            if(!$model->canBeDeleted()) {
             // echo " data-delete-alert=\"" . $model->deleteAlertMessage() . "\"";
            }
        }
        ?>
    </a>
</div>
