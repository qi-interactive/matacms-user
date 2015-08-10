<?php

use yii\helpers\ArrayHelper;
use matacms\widgets\Selectize;
use matacms\theme\simple\assets\ReviewerAsset;
use yii\helpers\Html as BaseHtml;
use yii\web\JsExpression;

$items = ArrayHelper::map($reviewers, 'id', 'profileName');

$prompt = 'Select Reviewer';

$options = [
    'items' => $items,
    'options' => ['multiple'=>false, 'prompt' => $prompt, 'id' => 'select-reviewer'],
    'clientOptions' => [
        'maxItems' => 1,
        'onChange' => new JsExpression("
                function (value) {
	                if(value != '') {
                        $('#choose-reviewer-modal #choose-reviewer-group').removeClass('has-error');
                        $('#choose-reviewer-modal #choose-reviewer-group .help-block').text('');
                    }
                }
            ")
    ]
];

ReviewerAsset::register($this);

?>
<div class="form-row">
    <div class="form-group single-choice-dropdown" id="choose-reviewer-group">
        <label class="control-label">Reviewer</label>
        <?= Selectize::widget($options); ?>
        <div class="help-block"></div>
    </div>
</div>
<div class="form-row">
    <?= BaseHtml::button('SUBMIT FOR REVIEW', ['id' => 'btn-send-to-review', 'class' => 'btn btn-primary review-btn']); ?>
</div>

<?php
\Yii::$app->view->registerJs("

    $('#btn-send-to-review').on('click', function() {

        var value = $('#select-reviewer').selectize()[0].selectize.getValue();
        if(value == '') {
            $('#choose-reviewer-modal #choose-reviewer-group').addClass('has-error');
            $('#choose-reviewer-modal #choose-reviewer-group .help-block').text('Reviewer cannot be blank');
            return;
        }

        $('#" . $containerId . " input#reviewer-hidden-input').val(value);
        $('#choose-reviewer-modal').modal('hide');
        $('#" . $containerId . " button.review-btn').trigger('click', [{isReviewerSet : true}]);
    });
", $this::POS_READY);

?>
