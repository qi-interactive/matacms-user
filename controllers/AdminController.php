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
use matacms\controllers\module\Controller as BaseController;
use matacms\base\MessageEvent;
use Yii;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

/**
 * AdminController allows you to administrate users.
 *
 * @property \mata\user\Module $module
 */
class AdminController extends BaseAdminController
{

    protected $finder;

    public function __construct($id, $module, Finder $finder, $config = [])
    {
        $this->finder = $finder;
        parent::__construct($id, $module, $finder, $config);
    }

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

    /**
     * Creates a new User model.
     * If creation is successful, the browser will be redirected to the 'index' page.
     * @return mixed
     */
    public function actionCreate()
    {
        /** @var User $user */
        $user = \Yii::createObject([
            'class'    => User::className(),
            'scenario' => 'create',
            ]);

        $this->performAjaxValidation($user);

        if ($user->load(\Yii::$app->request->post()) && $user->create()) {
            $this->trigger(BaseController::EVENT_MODEL_CREATED, new MessageEvent($user));
            \Yii::$app->getSession()->setFlash('success', \Yii::t('user', 'User has been created'));
            return $this->redirect(['index']);
        }

        return $this->render('create', [
            'user' => $user
            ]);
    }

    /**
     * Updates an existing User model.
     * If update is successful, the browser will be redirected to the 'index' page.
     * @param  integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $user = $this->findModel($id);
        $user->scenario = 'update';
        $profile = $this->finder->findProfileById($id);
        $r = \Yii::$app->request;

        $this->performAjaxValidation([$user, $profile]);

        if ($user->load($r->post()) && $profile->load($r->post()) && $user->save() && $profile->save()) {
            $this->trigger(BaseController::EVENT_MODEL_UPDATED, new MessageEvent($user));
            \Yii::$app->getSession()->setFlash('success', \Yii::t('user', 'User has been updated'));
            return $this->redirect(['index']);
        }

        return $this->render('update', [
            'user'    => $user,
            'profile' => $profile,
            'module'  => $this->module,
            ]);
    }

    public function getModel() {
        return new \matacms\user\models\User();
    }

}
