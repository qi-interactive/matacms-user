<?php

/**
 * @link http://www.matacms.com/
 * @copyright Copyright (c) 2015 Qi Interactive Limited
 * @license http://www.matacms.com/license/
 */

namespace matacms\user\models;

use matacms\user\Finder;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * UserSearch represents the model behind the search form about User.
 */
class UserSearch extends Model
{
    /** @var string */
    public $username;

    /** @var string */
    public $email;

    /** @var integer */
    public $created_at;

    /** @var string */
    public $registration_ip;

    /** @var Finder */
    protected $finder;

    /**
     * @param Finder $finder
     * @param array $config
     */
    public function __construct(Finder $finder, $config = [])
    {
        $this->finder = $finder;
        parent::__construct($config);
    }

    /** @inheritdoc */
    public function rules()
    {
        return [
            [['created_at', 'username', 'email', 'registration_ip'], 'safe'],
        ];
    }

    /** @inheritdoc */
    public function attributeLabels()
    {
        return [
        'username'        => \Yii::t('user', 'Username'),
        'email'           => \Yii::t('user', 'Email'),
        'created_at'      => \Yii::t('user', 'Registration time'),
        'registration_ip' => \Yii::t('user', 'Registration ip'),
        ];
    }

    public function getProfile()
    {
        $userQuery = $this->finder->getUserQuery();
        $modelClass = $userQuery->modelClass;

        $query = \Yii::$app->getModule('user')->modelMap['Profile']::find();
        $query->primaryModel = $modelClass;
        $query->link = ['profile.user_id' => 'user.id'];
        $query->multiple = false;
        return $query;
    }

    /**
     * @param $params
     * @return ActiveDataProvider
     */
    public function search($params)
    {

        $userQuery = $this->finder->getUserQuery();
        $modelClass = $userQuery->modelClass;

        $query = $modelClass::find()->joinWith(['profile']);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere(['created_at'=> $this->created_at])
        ->orFilterWhere(['like', 'username', $this->username])
        ->orFilterWhere(['like', 'profile.name', isset($params['UserSearch']) ? $params['UserSearch']['profile.name'] : ''])
        ->orFilterWhere(['like', 'email', $this->email])
        ->orFilterWhere(['registration_ip' => $this->registration_ip]);

        return $dataProvider;
    }


    public function filterableAttributes() {
        return ["username", "email", "created_at", 'profile.name'];
    }
}
