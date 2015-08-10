<?php

/**
 * @link http://www.matacms.com/
 * @copyright Copyright (c) 2015 Qi Interactive Limited
 * @license http://www.matacms.com/license/
 */

namespace matacms\user\widgets\ModuleAccessibility;

use Yii;
use yii\base\InvalidConfigException;
use yii\base\Widget;

class ModuleAccessibility extends Widget
{

    public $userModel;

    public $form;

    /** @inheritdoc */
    public function init()
    {
        parent::init();

        if ($this->userModel === null) {
            throw new InvalidConfigException('You should set ' . __CLASS__ . '::$userModel');
        }

        if ($this->form === null) {
            throw new InvalidConfigException('You should set ' . __CLASS__ . '::$form');
        }
    }

    /** @inheritdoc */
    public function run()
    {
        return $this->form->field($this->userModel, 'ModuleAccessibility')->moduleAccessibility();
    }
}
