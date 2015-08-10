<?php

/**
 * @link http://www.matacms.com/
 * @copyright Copyright (c) 2015 Qi Interactive Limited
 * @license http://www.matacms.com/license/
 */

namespace matacms\user\helpers;

use yii\helpers\ArrayHelper;
use matacms\widgets\Selectize;
use matacms\helpers\Html as BaseHtml;
use yii\web\View;

class Html extends BaseHtml {

	public static function activeModuleAccessibilityField($model, $attribute, $options = []) {
        
        $items = ArrayHelper::map(\Yii::$app->moduleMenuManager->getMenuModules(), 'ModuleId', 'ModuleName');
		$value = $model->isNewRecord ? [] : ArrayHelper::getColumn(\Yii::$app->moduleAccessibilityManager->getModulesByUser($model->getId()), 'ModuleId');

		if ($value != null)
			$options["value"] = $value;

		if(!empty($_POST['ModuleAccessibility']))
			$options["value"] = $_POST['ModuleAccessibility'];

		$options["name"] = 'ModuleAccessibility';

		$options['id'] = self::getInputId($model, $attribute);

		$prompt = 'Select ' . $model->getAttributeLabel($attribute);
        if(isset($options['prompt'])) {
            $prompt = $options['prompt'];
            unset($options['prompt']);
        }

		$options = ArrayHelper::merge([
			'items' => $items,
			'options' => ['multiple'=>true, 'prompt' => $prompt],
			'clientOptions' => [
			'plugins' => ["remove_button", "drag_drop", "restore_on_backspace"],
			'create' => false,
			'persist' => false,
			]
			], $options);

		return Selectize::widget($options);
	}

}
