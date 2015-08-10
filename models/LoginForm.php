<?php

/**
 * @link http://www.matacms.com/
 * @copyright Copyright (c) 2015 Qi Interactive Limited
 * @license http://www.matacms.com/license/
 */

namespace matacms\user\models;

use matacms\user\Finder;
use yii\base\Model;
use matacms\user\helpers\Password;

/**
 * LoginForm get user's login and password, validates them and logs the user in. If user has been blocked, it adds
 * an error to login form.
 */
class LoginForm extends Model
{
    /** @var string User's email or username */
    public $login;

    /** @var string User's plain password */
    public $password;

    /** @var string Whether to remember the user */
    public $rememberMe = false;

    /** @var \matacms\user\models\User */
    protected $user;

    /** @var \mata\user\Module */
    protected $module;

    /** @var Finder */
    protected $finder;

    /**
     * @param Finder $finder
     * @param array $config
     */
    public function __construct(Finder $finder, $config = [])
    {
        $this->finder = $finder;
        $this->module = \Yii::$app->getModule('user');
        parent::__construct($config);
    }

    /** @inheritdoc */
    public function attributeLabels()
    {
        return [
        'login'      => \Yii::t('user', 'Username / Email'),
        'password'   => \Yii::t('user', 'Password'),
        'rememberMe' => \Yii::t('user', 'Remember me'),
        ];
    }

    /** @inheritdoc */
    public function rules()
    {
        return [
        [['login', 'password'], 'required'],
        ['login', 'trim'],
        ['password', function ($attribute) {
            if ($this->user === null || !Password::validate($this->password, $this->user->password_hash)) {
                $this->addError($attribute, \Yii::t('user', 'Invalid login or password'));

                // Add error for login -- we don't want to indicate if the username failed or password
                $this->addError('login', \Yii::t('user', 'Invalid login or password'));
            }
        }],
        ['login', function ($attribute) {
            if ($this->user !== null) {
                $confirmationRequired = $this->module->enableConfirmation && !$this->module->enableUnconfirmedLogin;
                if ($confirmationRequired && !$this->user->getIsConfirmed()) {
                    $this->addError($attribute, \Yii::t('user', 'You need to confirm your email address'));
                }
                if ($this->user->getIsBlocked()) {
                    $this->addError($attribute, \Yii::t('user', 'Your account has been blocked'));
                }
            }
        }],
        ['rememberMe', 'boolean'],
        ];
    }

    /**
     * Validates form and logs the user in.
     * @return boolean whether the user is logged in successfully
     */
    public function login()
    {
        if ($this->validate()) {
            return \Yii::$app->getUser()->login($this->user, $this->rememberMe ? $this->module->rememberFor : 0);
        } else {
            return false;
        }
    }

    /** @inheritdoc */
    public function formName()
    {
        return 'login-form';
    }

    /** @inheritdoc */
    public function beforeValidate()
    {
        if (parent::beforeValidate()) {
            $this->user = $this->finder->findUserByUsernameOrEmail($this->login);
            return true;
        } else {
            return false;
        }
    }
}
