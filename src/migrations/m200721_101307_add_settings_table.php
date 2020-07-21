<?php

namespace batchnz\ccshippingratesmatrix\migrations;

use batchnz\ccshippingratesmatrix\records\Settings;

use Craft;
use craft\db\Migration;

/**
 * m200721_101307_add_settings_table migration.
 */
class m200721_101307_add_settings_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $tableSchema = Craft::$app->db->schema->getTableSchema(Settings::tableName());
        if ($tableSchema === null) {
            $tablesCreated = true;
            $this->createTable(
                Settings::tableName(),
                [
                    'id' => $this->primaryKey()->unsigned(),
                    'fuelAdjustmentFactor' => $this->decimal('4,2')->defaultValue('0.00')->notNull(),
                    'dateCreated' => $this->dateTime()->notNull(),
                    'dateUpdated' => $this->dateTime()->notNull(),
                    'uid' => $this->uid(),
                ]
            );
        }
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        echo "m200721_101307_add_settings_table cannot be reverted.\n";
        return false;
    }
}
