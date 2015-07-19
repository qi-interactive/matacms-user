<?php

/**
 * @link http://www.matacms.com/
 * @copyright Copyright (c) 2015 Qi Interactive Limited
 * @license http://www.matacms.com/license/
 */

 namespace matacms\user\models;

 use mata\user\models\Token as BaseToken;

/**
 * Token Active Record model.
 *
 * @property integer $user_id
 * @property string  $code
 * @property integer $created_at
 * @property integer $type
 * @property string  $url
 * @property bool    $isExpired
 * @property User    $user
 */
class Token extends BaseToken
{
    
}
