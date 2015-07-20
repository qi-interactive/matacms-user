<?php

/**
 * @link http://www.matacms.com/
 * @copyright Copyright (c) 2015 Qi Interactive Limited
 * @license http://www.matacms.com/license/
 */

namespace matacms\user\controllers;

use mata\user\controllers\AdminController as BaseAdminController;
use matacms\user\Finder;
use matacms\user\models\User;
use matacms\user\models\UserSearch;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

/**
 * AdminController allows you to administrate users.
 *
 * @property \mata\user\Module $module
 */
class AdminController extends BaseAdminController
{

    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete'  => ['post'],
                    'confirm' => ['post'],
                    'block'   => ['post'],
                ],
            ],
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function () {
                            return Yii::$app->user->identity->getIsAdmin();
                        },
                    ],
                ],
            ],
        ];
    }

    public function getModel() {
        return new \matacms\user\models\User();
    }

}
