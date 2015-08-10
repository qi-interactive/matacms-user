<?php

/**
 * @link http://www.matacms.com/
 * @copyright Copyright (c) 2015 Qi Interactive Limited
 * @license http://www.matacms.com/license/
 */

use yii\db\Schema;
use yii\db\Migration;

class m150729_155415_init extends Migration {

	public function safeUp() {
        $this->createTable('{{%matacms_module_accessbility}}', [
            'ModuleId' => Schema::TYPE_STRING . '(128) NOT NULL',
            'UserId'    => Schema::TYPE_INTEGER .'(11) UNSIGNED NOT NULL',
            'DateCreated'  => Schema::TYPE_TIMESTAMP . ' NOT NULL DEFAULT CURRENT_TIMESTAMP'
            ]);
        $this->addPrimaryKey('PK_ModuleId_UserId', '{{%matacms_module_accessbility}}', ['ModuleId', 'UserId']);
    }

    public function safeDown() {
        $this->dropTable('{{%matacms_module_accessbility}}');
    }
}
