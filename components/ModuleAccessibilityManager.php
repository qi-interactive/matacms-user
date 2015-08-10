<?php

/**
 * @link http://www.matacms.com/
 * @copyright Copyright (c) 2015 Qi Interactive Limited
 * @license http://www.matacms.com/license/
 */

namespace matacms\user\components;

use Yii;
use yii\base\Component;
use yii\db\Query;
use yii\db\Connection;
use yii\db\Expression;
use yii\di\Instance;

/**
 * This Auth manager changes visibility and signature of some methods from \yii\rbac\DbManager.
 */
class ModuleAccessibilityManager extends Component
{

	public $db = 'db';

	public $moduleAccessibilityTable = '{{%matacms_module_accessbility}}';

	public function init()
    {
        parent::init();
        $this->db = Instance::ensure($this->db, Connection::className());
    }

    public function getModulesByUser($userId)
    {
        if (empty($userId)) {
            return [];
        }

        $query = (new Query)->select('a.*')
            ->from(['a' => $this->moduleAccessibilityTable])
            ->where(['a.UserId' => (string) $userId]);

        $modules = [];
        foreach ($query->all($this->db) as $row) {
            $modules[] = $row;
        }
        return $modules;
    }

    public function deleteAllModulesByUser($userId)
    {
        if (empty($userId)) {
            return [];
        }

        $command = \Yii::$app->getDb()->createCommand();
        $command->delete($this->moduleAccessibilityTable, 'UserId = :userId', ['userId' => $userId]);
        return $command->execute();
    }

    public function getModuleByUser($moduleId, $userId)
    {
        if (empty($userId)) {
            return [];
        }

        $query = (new Query)->select('a.*')
            ->from(['a' => $this->moduleAccessibilityTable])
            ->where(['{{a}}.[[ModuleId]]' => (string) $moduleId, 'a.UserId' => $userId]);

        $moduleAccessible = $query->one($this->db);

        if(!empty($moduleAccessible))
            $moduleAccessible = $this->populateItem($moduleAccessible);

        return $moduleAccessible;
    }

    public function applyAccess($moduleId, $userId)
    {
        $moduleAccessible = \Yii::$app->getDb()->createCommand()
            ->insert($this->moduleAccessibilityTable, [
                'UserId' => $userId,
                'ModuleId' => $moduleId,
                'DateCreated' => new Expression('NOW()'),
            ])->execute();

        return $moduleAccessible;
    }

}
