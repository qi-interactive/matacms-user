<?php

/*
 * This file is part of the mata project.
 *
 * (c) mata project <http://github.com/mata>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

use matacms\user\widgets\ModuleAccessibility\ModuleAccessibility;

/**
 * @var yii\web\View 				$this
 * @var matacms\user\models\User 	$user
 */

?>

<?= ModuleAccessibility::widget(['form' => $form, 'userModel' => $userModel]) ?>
