<?php

namespace lucasbares\craftflowplayerdrive\migrations;

use Craft;
use craft\db\Migration;

/**
 * Install migration.
 */
class Install extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        if (!$this->db->tableExists('{{%craftflowplayerdrive}}')) {

            // create the craftflowplayerdrive table
            $this->createTable('{{%craftflowplayerdrive}}', [
                'id' => $this->integer()->notNull(),

                'video_id' => $this->char(36)->notNull(),
                'name' => $this->char(255)->notNull(),
                'description' => $this->string()->notNull(),
                'published' => $this->boolean()->notNull(),
                'views' => $this->integer()->notNull(),

                'adtag' => $this->char(255),
                'categoryid' =>  $this->char(36),
                'created_at' => $this->dateTime()->notNull(),
                'duration' => $this->integer()->notNull(),
                'episode' => $this->boolean()->notNull(),
                'externalvideoid' => $this->char(255),
                'thumbnail_url' => $this->string()->notNull(),
                'normal_image_url' => $this->string()->notNull(),
                'mediafiles' => $this->json()->notNull(),
                'noads' => $this->boolean()->notNull(),
                'published_at' => $this->dateTime(),
                'siteid' => $this->char(36)->notNull(),
                'use_unpublish_date' => $this->boolean()->notNull(),
                'unpublished_at' => $this->dateTime(),
                'state' => $this->char(16)->notNull(),
                'tags' => $this->char(255),
                'updated_at' => $this->dateTime(),
                'userid' => $this->char(36)->notNull(),

                'uid' => $this->uid(),
                'dateCreated' => $this->dateTime(),
                'dateUpdated' => $this->dateTime(),
                'PRIMARY KEY(id)',
            ]);

            // give it a FK to the elements table
            $this->addForeignKey(
                $this->db->getForeignKeyName('{{%craftflowplayerdrive}}', 'id'),
                '{{%craftflowplayerdrive}}', 'id', '{{%elements}}', 'id', 'CASCADE', null);
        }
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        if ($this->db->tableExists('{{%craftflowplayerdrive}}')) {            
            $this->dropTable('{{%craftflowplayerdrive}}');
        }
    }
}

