<?php

/**
 * @link http://www.matacms.com/
 * @copyright Copyright (c) 2015 Qi Interactive Limited
 * @license http://www.matacms.com/license/
 */

namespace matacms\user\controllers;

use mata\user\controllers\SettingsController as BaseSettingsController;
use matacms\user\Finder;
use matacms\user\models\Account;
use matacms\user\models\SettingsForm;
use matacms\user\Module;
use matacms\widgets\ActiveForm;
use matacms\controllers\module\Controller as BaseController;
use matacms\base\MessageEvent;

class SettingsController extends BaseSettingsController
{

    public function actionAccount()
    {
        $profileModel = $this->finder->findProfileById(\Yii::$app->user->identity->getId());
        $accountModel = \Yii::createObject(SettingsForm::className());

        $this->performAjaxValidation($profileModel);
        $this->performAjaxValidation($accountModel);

        if ($profileModel->load(\Yii::$app->request->post()) && $accountModel->load(\Yii::$app->request->post())) {

            if($profileModel->validate() && $accountModel->validate() && $profileModel->save() && $accountModel->save()) {
                $this->trigger(BaseController::EVENT_MODEL_CREATED, new MessageEvent('Your account has been updated'));
                \Yii::$app->getSession()->setFlash('success', \Yii::t('user', 'Your account has been updated'));
                return $this->refresh();
            }

        }

        return $this->render('account', [
            'profileModel' => $profileModel,
            'accountModel' => $accountModel,
        ]);
    }

}
