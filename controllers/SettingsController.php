<?php

/**
 * @link http://www.matacms.com/
 * @copyright Copyright (c) 2015 Qi Interactive Limited
 * @license http://www.matacms.com/license/
 */

namespace matacms\user\controllers;

use matacms\user\Finder;
use matacms\user\models\Account;
use matacms\user\models\SettingsForm;
use matacms\user\Module;
use yii\authclient\ClientInterface;
use yii\base\Model;
use yii\helpers\Url;
use mata\web\module\Controller;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use matacms\widgets\ActiveForm;
use matacms\controllers\module\Controller as BaseController;
use matacms\base\MessageEvent;
use yii\base\Event;

/**
 * SettingsController manages updating user settings (e.g. profile, email and password).
 *
 * @property \matacms\user\Module $module
 *
 */
class SettingsController extends Controller
{
    /** @inheritdoc */
    public $defaultAction = 'account';

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

    /** @inheritdoc */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'disconnect' => ['post']
                ],
            ],
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow'   => true,
                        'actions' => ['profile', 'account', 'confirm', 'networks', 'connect', 'disconnect'],
                        'roles'   => ['@']
                    ],
                ]
            ]
        ];
    }

    /** @inheritdoc */
    public function actions()
    {
        return [
            'connect' => [
                'class'           => 'yii\authclient\AuthAction',
                'successCallback' => [$this, 'connect'],
            ]
        ];
    }

    /**
     * Displays page where user can update account settings (username, email or password).
     * @return string|\yii\web\Response
     */
     public function actionAccount()
     {
         $profileModel = $this->finder->findProfileById(\Yii::$app->user->identity->getId());
         $accountModel = \Yii::createObject(SettingsForm::className());

         $this->performAjaxValidation($profileModel);
         $this->performAjaxValidation($accountModel);

         if ($profileModel->load(\Yii::$app->request->post()) && $accountModel->load(\Yii::$app->request->post())) {

             if($profileModel->validate() && $accountModel->validate() && $profileModel->save() && $accountModel->save()) {
                 Event::trigger(BaseController::class, BaseController::EVENT_MODEL_UPDATED, new MessageEvent($profileModel));
                 \Yii::$app->getSession()->setFlash('success', \Yii::t('user', 'Your account has been updated'));
                 return $this->refresh();
             }

         }

         return $this->render('account', [
             'profileModel' => $profileModel,
             'accountModel' => $accountModel,
         ]);
     }

    /**
     * Attempts changing user's password.
     * @param  integer $id
     * @param  string  $code
     * @return string
     * @throws \yii\web\HttpException
     */
    public function actionConfirm($id, $code)
    {
        $user = $this->finder->findUserById($id);

        if ($user === null || $this->module->emailChangeStrategy == Module::STRATEGY_INSECURE) {
            throw new NotFoundHttpException;
        }

        $user->attemptEmailChange($code);

        return $this->redirect(['account']);
    }

    /**
     * Displays list of connected network accounts.
     * @return string
     */
    public function actionNetworks()
    {
        return $this->render('networks', [
            'user' => \Yii::$app->user->identity
        ]);
    }

    /**
     * Disconnects a network account from user.
     * @param  integer $id
     * @return \yii\web\Response
     * @throws \yii\web\NotFoundHttpException
     * @throws \yii\web\ForbiddenHttpException
     */
    public function actionDisconnect($id)
    {
        $account = $this->finder->findAccountById($id);
        if ($account === null) {
            throw new NotFoundHttpException;
        }
        if ($account->user_id != \Yii::$app->user->id) {
            throw new ForbiddenHttpException;
        }
        $account->delete();

        return $this->redirect(['networks']);
    }

    /**
     * Connects social account to user.
     * @param  ClientInterface $client
     * @return \yii\web\Response
     */
    public function connect(ClientInterface $client)
    {
        $attributes = $client->getUserAttributes();
        $provider   = $client->getId();
        $clientId   = $attributes['id'];

        $account = $this->finder->findAccountByProviderAndClientId($provider, $clientId);

        if ($account === null) {
            $account = \Yii::createObject([
                'class'     => Account::className(),
                'provider'  => $provider,
                'client_id' => $clientId,
                'data'      => json_encode($attributes),
                'user_id'   => \Yii::$app->user->id,
            ]);
            $account->save(false);
            \Yii::$app->session->setFlash('success', \Yii::t('user', 'Your account has been connected'));
        } else {
            \Yii::$app->session->setFlash('error', \Yii::t('user', 'This account has already been connected to another user'));
        }

        $this->action->successUrl = Url::to(['/user/settings/networks']);
    }

    /**
     * Performs ajax validation.
     * @param Model $model
     * @throws \yii\base\ExitException
     */
    protected function performAjaxValidation(Model $model)
    {
        if (\Yii::$app->request->isAjax && $model->load(\Yii::$app->request->post())) {
            \Yii::$app->response->format = Response::FORMAT_JSON;
            echo json_encode(ActiveForm::validate($model));
            \Yii::$app->end();
        }
    }
}
