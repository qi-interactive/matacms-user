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

class ConfirmController extends Controller
{
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
     * Confirms a user by setting confirmed_at field to current time.
     *
     * @param string $search Email or username
     */
    public function actionIndex($search)
    {
        $user = $this->finder->findUserByUsernameOrEmail($search);
        if ($user === null) {
            $this->stdout(\Yii::t('user', 'User is not found') . "\n", Console::FG_RED);
        } else {
            if ($user->confirm()) {
                $this->stdout(\Yii::t('user', 'User has been confirmed') . "\n", Console::FG_GREEN);
            } else {
                $this->stdout(\Yii::t('user', 'Error occurred while confirming user') . "\n", Console::FG_RED);
            }
        }
    }
}
