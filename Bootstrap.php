<?php

/**
 * @link http://www.matacms.com/
 * @copyright Copyright (c) 2015 Qi Interactive Limited
 * @license http://www.matacms.com/license/
 */


namespace matacms\user;

use yii\authclient\Collection;
use yii\base\BootstrapInterface;
use yii\base\InvalidConfigException;
use yii\i18n\PhpMessageSource;
use yii\web\GroupUrlRule;
use yii\console\Application as ConsoleApplication;
use yii\web\User;
use matacms\user\behaviors\ModuleAccessibilityActiveFormBehavior;
use matacms\user\controllers\AdminController as AdminController;
use matacms\widgets\ActiveField;
use mata\base\MessageEvent;
use yii\base\Event;
use matacms\controllers\module\Controller;

/**
 * Bootstrap class registers module and user application component. It also creates some url rules which will be applied
 * when UrlManager.enablePrettyUrl is enabled.
 */
class Bootstrap implements BootstrapInterface
{
    /** @var array Model's map */
    private $_modelMap = [
        'User'             => 'matacms\user\models\User',
        'Account'          => 'matacms\user\models\Account',
        'Profile'          => 'matacms\user\models\Profile',
        'Token'            => 'matacms\user\models\Token',
        'RegistrationForm' => 'matacms\user\models\RegistrationForm',
        'ResendForm'       => 'matacms\user\models\ResendForm',
        'LoginForm'        => 'matacms\user\models\LoginForm',
        'SettingsForm'     => 'matacms\user\models\SettingsForm',
        'RecoveryForm'     => 'matacms\user\models\RecoveryForm',
        'UserSearch'       => 'matacms\user\models\UserSearch',
    ];

    /** @inheritdoc */
    public function bootstrap($app)
    {
        /** @var $module Module */
        if ($app->hasModule('user') && ($module = $app->getModule('user')) instanceof Module) {
            $this->_modelMap = array_merge($this->_modelMap, $module->modelMap);
            foreach ($this->_modelMap as $name => $definition) {
                $class = "matacms\\user\\models\\" . $name;
                \Yii::$container->set($class, $definition);
                $modelName = is_array($definition) ? $definition['class'] : $definition;
                $module->modelMap[$name] = $modelName;
                if (in_array($name, ['User', 'Profile', 'Token', 'Account'])) {
                    \Yii::$container->set($name . 'Query', function () use ($modelName) {
                        return $modelName::find();
                    });
                }
            }
            \Yii::$container->setSingleton(Finder::className(), [
                'userQuery'    => \Yii::$container->get('UserQuery'),
                'profileQuery' => \Yii::$container->get('ProfileQuery'),
                'tokenQuery'   => \Yii::$container->get('TokenQuery'),
                'accountQuery' => \Yii::$container->get('AccountQuery'),
            ]);

            if ($app instanceof ConsoleApplication) {
                $module->controllerNamespace = 'matacms\user\commands';
            } else {
                \Yii::$container->set('yii\web\User', [
                    'enableAutoLogin' => true,
                    'loginUrl'        => ['/user/security/login'],
                    'identityClass'   => $module->modelMap['User'],
                ]);

                $configUrlRule = [
                    'prefix' => $module->urlPrefix,
                    'rules'  => $module->urlRules
                ];

                if ($module->urlPrefix != 'user') {
                    $configUrlRule['routePrefix'] = 'user';
                }

                $app->get('urlManager')->rules[] = new GroupUrlRule($configUrlRule);

                if (!$app->has('authClientCollection')) {
                    $app->set('authClientCollection', [
                        'class' => Collection::className(),
                    ]);
                }
            }

            $app->get('i18n')->translations['user*'] = [
                'class'    => PhpMessageSource::className(),
                'basePath' => __DIR__ . '/messages',
            ];

            $defaults = [
                'welcomeSubject'        => \Yii::t('user', 'Welcome to {0}', \Yii::$app->name),
                'confirmationSubject'   => \Yii::t('user', 'Confirm account on {0}', \Yii::$app->name),
                'reconfirmationSubject' => \Yii::t('user', 'Confirm email change on {0}', \Yii::$app->name),
                'recoverySubject'       => \Yii::t('user', 'Complete password reset on {0}', \Yii::$app->name),
                'passwordChangedSubject'       => \Yii::t('user', 'Your password has been changed on {0}', \Yii::$app->name)
            ];

            \Yii::$container->set('matacms\user\Mailer', array_merge($defaults, $module->mailer));
        }

        Event::on(ActiveField::className(), ActiveField::EVENT_INIT_DONE, function(MessageEvent $event) {
            $event->getMessage()->attachBehavior('moduleAccessibility', new ModuleAccessibilityActiveFormBehavior());
        });

        Event::on(AdminController::class, Controller::EVENT_MODEL_UPDATED, function(\matacms\base\MessageEvent $event) {
            $this->processSave($event->getMessage());
        });

        Event::on(AdminController::class, Controller::EVENT_MODEL_CREATED, function(\matacms\base\MessageEvent $event) {
            $this->processSave($event->getMessage());
        });

    }

    private function processSave($model) {

        if (empty($modules = \Yii::$app->request->post('ModuleAccessibility')))
            return;

        $userId = $model->getId();

        \Yii::$app->moduleAccessibilityManager->deleteAllModulesByUser($userId);

        if(is_array($modules)) {
            foreach ($modules as $module) {
                $this->saveModuleAccess($module, $model, $userId);
            }
        } elseif(is_string($modules)) {
            $this->saveModuleAccess($modules, $model, $userId);
        }
    }

    private function saveModuleAccess($module, $model, $userId)
    {
        $moduleMenuManager = \Yii::$app->moduleAccessibilityManager;
        $moduleAccessible = $moduleMenuManager->getModuleByUser($module, $userId);

        if ($moduleAccessible == null) {

            $moduleAccessible = $moduleMenuManager->applyAccess($module, $userId);

            if(empty($moduleAccessible))
                throw new \yii\web\ServerErrorHttpException(\yii\helpers\CVarDumper::dumpAsString($moduleAccessible));

        }
    }
}
