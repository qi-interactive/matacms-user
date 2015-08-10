<?php

/**
 * @link http://www.matacms.com/
 * @copyright Copyright (c) 2015 Qi Interactive Limited
 * @license http://www.matacms.com/license/
 */

namespace matacms\user;

use mata\user\Module as BaseModule;

/**
 * This is the main module class for the Yii2-user.
 *
 * @property array $modelMap
 */
class Module extends BaseModule
{

    public $urlRules = [
        '<id:\d+>'                    => 'profile/show',
        '<action:(login|logout)>'     => 'security/<action>',
        '<action:(register|resend)>'  => 'registration/<action>',
        'confirm/<id:\d+>/<code:\w+>' => 'registration/confirm',
        'forgot'                      => 'recovery/request',
        'recover/<id:\d+>/<code:([0-9a-zA-Z\-_=]+)>' => 'recovery/reset',
        'settings/<action:\w+>'       => 'settings/<action>'
    ];

    public function getNavigation()
    {
        return [
            [
                "label" => "Your profile",
                "url" => "/mata-cms/user/settings",
                "icon" => "/images/user-profile-account-default.svg"
            ],
            [
                "label" => "Manage users",
                "url" => "/mata-cms/user/admin/index",
                "icon" => "/images/user-profile-account-default.svg"
            ],
            [
                "label" => "Logout",
                "url" => "/mata-cms/user/logout",
                "icon" => "/images/logout.svg",
                "class" => 'hard-link'
            ]
            ];
    }
}
