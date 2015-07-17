<?php

/*
 * This file is part of the mata project.
 *
 * (c) mata project <http://github.com/mata>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

/**
 * @var matacms\widgets\ActiveForm       $form
 * @var mata\user\models\Profile $profile
 */

?>

<?= $form->field($profile, 'name') ?>
<?= $form->field($profile, 'Avatar')->media() ?>
