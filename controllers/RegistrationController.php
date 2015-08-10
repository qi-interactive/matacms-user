<?php

/**
 * @link http://www.matacms.com/
 * @copyright Copyright (c) 2015 Qi Interactive Limited
 * @license http://www.matacms.com/license/
 */

namespace matacms\user\controllers;

use mata\user\controllers\RegistrationController as BaseRegistrationController;
use matacms\user\Finder;
use matacms\user\models\RegistrationForm;
use matacms\user\models\ResendForm;
use matacms\user\models\User;

class RegistrationController extends BaseRegistrationController
{
	public $layout = "@matacms/views/layouts/login";
}
