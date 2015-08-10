<?php

/**
 * @link http://www.matacms.com/
 * @copyright Copyright (c) 2015 Qi Interactive Limited
 * @license http://www.matacms.com/license/
 */

namespace matacms\user\controllers;

use Yii;
use yii\filters\AccessControl;
use matacms\user\controllers\AdminController;

class ReviewerController extends AdminController
{

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@']
                    ],
                ],
            ],
        ];
    }

    /**
     * Creates a new User model.
     * If creation is successful, the browser will be redirected to the 'index' page.
     * @return mixed
     */
    public function actionGetReviewers($containerId)
    {
        $reviewers = $this->finder->findUsersWithRole('reviewer');

        $reviewersMapped = [];
        if(!empty($reviewers)) {
            foreach($reviewers as $reviewer) {
                $reviewersMapped[] = ['id' => $reviewer['id'], 'profileName' => $reviewer['profile']['name']];
            }
        }

        return $this->renderAjax('reviewers', ["reviewers" => $reviewersMapped, 'containerId' => $containerId]);
    }

    public function getModel() {
        return new \matacms\user\models\User();
    }

}
