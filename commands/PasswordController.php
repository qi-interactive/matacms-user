<?php

/**
 * @link http://www.matacms.com/
 * @copyright Copyright (c) 2015 Qi Interactive Limited
 * @license http://www.matacms.com/license/
 */

namespace matacms\user\commands;

use matacms\user\Finder;
use yii\console\Controller;
use yii\helpers\Console;

class PasswordController extends Controller
{
    /** @var Finder */
    protected $finder;

    /**
     * @param string $id
     * @param \yii\base\Module $module
     * @param Finder $finder
     * @param array $config
     */
    public function __construct($id, $module, Finder $finder, $config = [])
    {
        $this->finder = $finder;
        parent::__construct($id, $module, $config);
    }

    /**
     * Updates user's password to given.
     *
     * @param string $search   Email or username
     * @param string $password New password
     */
    public function actionIndex($search, $password)
    {
        $user = $this->finder->findUserByUsernameOrEmail($search);
        if ($user === null) {
            $this->stdout(\Yii::t('user', 'User is not found') . "\n", Console::FG_RED);
        } else {
            if ($user->resetPassword($password)) {
                $this->stdout(\Yii::t('user', 'Password has been changed') . "\n", Console::FG_GREEN);
            } else {
                $this->stdout(\Yii::t('user', 'Error occurred while changing password') . "\n", Console::FG_RED);
            }
        }
    }
}
