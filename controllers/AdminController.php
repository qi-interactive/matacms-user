<?php

/**
 * @link http://www.matacms.com/
 * @copyright Copyright (c) 2015 Qi Interactive Limited
 * @license http://www.matacms.com/license/
 */

namespace matacms\user\controllers;

use matacms\user\Finder;
use matacms\user\models\User;
use matacms\user\models\Profile;
use matacms\user\models\UserSearch;
use yii\base\Model;
use mata\web\module\Controller;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use mata\widgets\ActiveForm;
use yii\data\Sort;
use mata\helpers\BehaviorHelper;
use matacms\controllers\module\Controller as BaseController;
use matacms\base\MessageEvent;
use Yii;

/**
 * AdminController allows you to administrate users.
 *
 * @property \mata\user\Module $module
 */
class AdminController extends Controller
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
        'verbs' => [
        'class' => VerbFilter::className(),
        'actions' => [
        'delete'  => ['post'],
        'confirm' => ['post'],
        'block'   => ['post']
        ],
        ],
            // 'access' => [
            //     'class' => AccessControl::className(),
            //     'rules' => [
            //         [
            //             'actions' => ['index', 'create', 'update', 'delete', 'block', 'confirm'],
            //             'allow' => true,
            //             'roles' => ['@'],
            //             'matchCallback' => function ($rule, $action) {
            //                 return \Yii::$app->user->identity->getIsAdmin();
            //             }
            //         ],
            //     ]
            // ]
        ];
    }

    /**
     * Lists all User models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel  = \Yii::createObject(UserSearch::className());
        $dataProvider = $searchModel->search($_GET);

            // Remove any default orderings
            // $dataProvider->query->orderBy = null;

        $sort = new Sort([
            'attributes' => $searchModel->filterableAttributes()
            ]);

        if(!empty($sort->orders)) {
            $dataProvider->query->orderBy = null;
        } else {

            if(BehaviorHelper::hasBehavior($searchModel, \mata\arhistory\behaviors\HistoryBehavior::class)) {
                $dataProvider->query->select('*');
                $reflection =  new \ReflectionClass($searchModel);
                $parentClass = $reflection->getParentClass();

                $alias = $searchModel->getTableSchema()->name;
                $pk = $searchModel->getTableSchema()->primaryKey;


                if (is_array($pk)) {
                    if(count($pk) > 1)
                        throw new NotFoundHttpException('Combined primary keys are not supported.');
                    $pk = $pk[0];
                }

                $aliasWithPk = $alias . '.' . $pk;

                $dataProvider->query->join('INNER JOIN', 'arhistory_revision', 'arhistory_revision.DocumentId = CONCAT(:class, '.$aliasWithPk.')', [':class' => $parentClass->name . '-']);
                $dataProvider->query->andWhere('arhistory_revision.Revision = (SELECT MAX(Revision) FROM `arhistory_revision` WHERE arhistory_revision.`DocumentId` = CONCAT(:class, '.$aliasWithPk.'))', [':class' => $parentClass->name . '-']);
                $dataProvider->query->orderBy('arhistory_revision.DateCreated DESC');
            }
        }

        $dataProvider->setSort($sort);

        return $this->render("index", [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'sort' => $sort
            ]);
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

        $profile = \Yii::createObject([
            'class'    => Profile::className(),
        ]);
        $r = \Yii::$app->request;

        $this->performAjaxValidation($user);

        if ($user->load($r->post()) && $user->save()) {
            $profile = $this->finder->findProfileById($user->id);
            $profile->load($r->post());
            $profile->save();
            $this->trigger(BaseController::EVENT_MODEL_CREATED, new MessageEvent($user));
            \Yii::$app->getSession()->setFlash('success', \Yii::t('user', 'User has been created'));
            return $this->redirect(['index']);
        }

        return $this->render('create', [
            'user' => $user,
            'profile' => $profile
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

    /**
     * Confirms the User.
     * @param integer $id
     * @param string  $back
     * @return \yii\web\Response
     */
    public function actionConfirm($id, $back = 'index')
    {
        $this->findModel($id)->confirm();
        \Yii::$app->getSession()->setFlash('success', \Yii::t('user', 'User has been confirmed'));
        $url = $back == 'index' ? ['index'] : ['update', 'id' => $id];
        return $this->redirect($url);
    }

    /**
     * Deletes an existing User model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param  integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        if ($id == \Yii::$app->user->getId()) {
            \Yii::$app->getSession()->setFlash('danger', \Yii::t('user', 'You can not remove your own account'));
        } else {
            $this->findModel($id)->delete();
            \Yii::$app->getSession()->setFlash('success', \Yii::t('user', 'User has been deleted'));
        }
        return $this->redirect(['index']);
    }

    /**
     * Blocks the user.
     * @param  integer $id
     * @param  string  $back
     * @return \yii\web\Response
     */
    public function actionBlock($id, $back = 'index')
    {
        if ($id == \Yii::$app->user->getId()) {
            \Yii::$app->getSession()->setFlash('danger', \Yii::t('user', 'You can not block your own account'));
        } else {
            $user = $this->findModel($id);
            if ($user->getIsBlocked()) {
                $user->unblock();
                \Yii::$app->getSession()->setFlash('success', \Yii::t('user', 'User has been unblocked'));
            } else {
                $user->block();
                \Yii::$app->getSession()->setFlash('success', \Yii::t('user', 'User has been blocked'));
            }
        }
        $url = $back == 'index' ? ['index'] : ['update', 'id' => $id];
        return $this->redirect($url);
    }

    /**
     * Finds the User model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param  integer               $id
     * @return User                  the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        $user = $this->finder->findUserById($id);
        if ($user === null) {
            throw new NotFoundHttpException('The requested page does not exist');
        }
        return $user;
    }

    public function getModel() {
        return new \matacms\user\models\User();
    }

    /**
     * Performs AJAX validation.
     * @param array|Model $models
     * @throws \yii\base\ExitException
     */
    protected function performAjaxValidation($models)
    {
        if (\Yii::$app->request->isAjax) {
            if (is_array($models)) {
                $result = [];
                foreach ($models as $model) {
                    if ($model->load(\Yii::$app->request->post())) {
                        \Yii::$app->response->format = Response::FORMAT_JSON;
                        $result = array_merge($result, ActiveForm::validate($model));
                    }
                }
                echo json_encode($result);
                \Yii::$app->end();
            } else {
                if ($models->load(\Yii::$app->request->post())) {
                    \Yii::$app->response->format = Response::FORMAT_JSON;
                    echo json_encode(ActiveForm::validate($models));
                    \Yii::$app->end();
                }
            }
        }
    }
}
