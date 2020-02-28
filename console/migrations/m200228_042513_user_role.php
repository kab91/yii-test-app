<?php

use yii\db\Migration;

/**
 * Class m200228_042513_user_role
 */
class m200228_042513_user_role extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("ALTER TABLE `user`
	        ADD COLUMN `role` INT UNSIGNED NOT NULL DEFAULT '0' AFTER `verification_token`;");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200228_042513_user_role cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200228_042513_user_role cannot be reverted.\n";

        return false;
    }
    */
}
