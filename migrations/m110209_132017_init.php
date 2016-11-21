<?php

use yii\db\Schema;
use yii\db\Migration;

class m110209_132017_init extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }
        $this->createTable('{{%storagefiles}}', [
            
            'id'            => $this->primaryKey(),
            'parent_name'   => $this->string(30)->notNull()->comment("name model"),
            'parent_id'     => $this->integer()->notNull()->comment("pid model"), 
//            'ptag'       => $this->string(30)->defaultValue(null)->comment("tag model"),  
            'path'       => $this->string()->notNull()->comment("path to file "),   
            'type'       => $this->string(20)->notNull()->comment("type file"), 
            'size'       => $this->integer()->notNull()->comment("size file"),  
            'name'       => $this->string()->notNull()->comment("name file "), 
            'origin_name'=> $this->string()->notNull()->comment("origin name file "),  
//            'order'      => $this->integer()->notNull()->defaultValue(0)->comment("Order by"),
            'ip'         => $this->string()->notNull()->comment("ip address client"),   
            'created_at' => $this->timestamp()->defaultValue(null)->comment("Created date"),    
            
        ]);
    }

    public function down()
    {
            $this->dropTable('{{%storagefiles}}');
            return true;
    }
}
