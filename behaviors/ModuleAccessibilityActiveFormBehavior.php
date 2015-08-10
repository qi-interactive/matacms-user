<?php

/**
 * @link http://www.matacms.com/
 * @copyright Copyright (c) 2015 Qi Interactive Limited
 * @license http://www.matacms.com/license/
 */

namespace matacms\user\behaviors;

use Yii;
use matacms\user\helpers\Html;
use yii\helpers\ArrayHelper;

class ModuleAccessibilityActiveFormBehavior extends \yii\base\Behavior {

	public function moduleAccessibility($options = []) {
		if(isset($this->owner->options['class'])) {
		    $this->owner->options['class'] .= ' multi-choice-dropdown partial-max-width-item';
		} else {
			$this->owner->options['class'] = ' multi-choice-dropdown partial-max-width-item';
		}

		$options = array_merge($this->owner->inputOptions, $options);

		$this->owner->adjustLabelFor($options);
		$this->owner->parts['{input}'] = Html::activeModuleAccessibilityField($this->owner->model, $this->owner->attribute, $options);

		return $this->owner;
	}

}
