<?php

/**
 * @link http://www.matacms.com/
 * @copyright Copyright (c) 2015 Qi Interactive Limited
 * @license http://www.matacms.com/license/
 */

namespace matacms\user\controllers;

use matacms\user\Finder;
use matacms\user\models\Account;
use matacms\user\models\LoginForm;
use yii\base\Model;
use yii\helpers\Url;
use mata\web\module\Controller;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\authclient\ClientInterface;
use yii\web\Response;
use mata\widgets\ActiveForm;

/**
 * Controller that manages user authentication process.
 *
 * @property \mata\user\Module $module
 */
class SecurityController extends Controller
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

    /** @inheritdoc */
    public function behaviors()
    {
        return [
        'access' => [
        'class' => AccessControl::className(),
        'rules' => [
        ['allow' => true, 'actions' => ['login', 'auth'], 'roles' => ['?', '@']],
        ['allow' => true, 'actions' => ['logout'], 'roles' => ['@']],
        ]
        ]
        ];
    }

    /** @inheritdoc */
    public function actions()
    {
        return [
        'auth' => [
        'class' => 'yii\authclient\AuthAction',
        'successCallback' => [$this, 'authenticate'],
        ]
        ];
    }

    /**
     * Displays the login page.
     * @return string|\yii\web\Response
     */
    public function actionLogin()
    {
        $this->layout = "@matacms/views/layouts/login";

        $model = \Yii::createObject(LoginForm::className());

        $this->performAjaxValidation($model);

        if ($model->load(\Yii::$app->getRequest()->post()) && $model->login()) {
            return $this->goBack();
        }

        return $this->render('login', [
            'model'  => $model,
            'module' => $this->module,
            ]);
    }

    /**
     * Logs the user out and then redirects to the homepage.
     * @return \yii\web\Response
     */
    public function actionLogout()
    {
        \Yii::$app->getUser()->logout();
        return $this->goHome();
    }

    /**
     * Logs the user in if this social account has been already used. Otherwise shows registration form.
     * @param  ClientInterface $client
     * @return \yii\web\Response
     */
    public function authenticate(ClientInterface $client)
    {
        $attributes = $client->getUserAttributes();
        $provider   = $client->getId();
        $clientId   = $attributes['id'];

        $account = $this->finder->findAccountByProviderAndClientId($provider, $clientId);

        if ($account === null) {
            $account = \Yii::createObject([
                'class'      => Account::className(),
                'provider'   => $provider,
                'client_id'  => $clientId,
                'data'       => json_encode($attributes),
                ]);
            $account->save(false);
        }

        if (null === ($user = $account->user)) {
            $this->action->successUrl = Url::to(['/user/registration/connect', 'account_id' => $account->id]);
        } else {
            \Yii::$app->user->login($user, $this->module->rememberFor);
        }
    }

    /**
     * Performs ajax validation.
     * @param Model $model
     * @throws \yii\base\ExitException
     */
    protected function performAjaxValidation(Model $model) {
        if (\Yii::$app->request->isAjax && $model->load(\Yii::$app->request->post())) {
            \Yii::$app->response->format = Response::FORMAT_JSON;
            echo json_encode(ActiveForm::validate($model));
            \Yii::$app->end();
        }
    }
}
