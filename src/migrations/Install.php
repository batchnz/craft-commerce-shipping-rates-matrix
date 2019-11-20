<?php
/**
 * Craft Commerce Shipping Rates Matrix plugin for Craft CMS 3.x
 *
 *  Configure shipping rates between multiple regions using a table matrix.
 *
 * @link      https://www.batch.nz
 * @copyright Copyright (c) 2019 Josh Smith
 */

namespace batchnz\ccshippingratesmatrix\migrations;

use batchnz\ccshippingratesmatrix\Plugin;
use batchnz\ccshippingratesmatrix\records\ShippingRate;

use Craft;
use craft\config\DbConfig;
use craft\db\Migration;
use craft\db\Table as CraftTable;
use craft\commerce\db\Table as CommerceTable;

/**
 * Craft Commerce Shipping Rates Matrix Install Migration
 *
 * If your plugin needs to create any custom database tables when it gets installed,
 * create a migrations/ folder within your plugin folder, and save an Install.php file
 * within it using the following template:
 *
 * If you need to perform any additional actions on install/uninstall, override the
 * safeUp() and safeDown() methods.
 *
 * @author    Josh Smith
 * @package   CraftCommerceShippingRatesMatrix
 * @since     1.0.0
 */
class Install extends Migration
{
    // Public Properties
    // =========================================================================

    /**
     * @var string The database driver to use
     */
    public $driver;

    // Public Methods
    // =========================================================================

    /**
     * This method contains the logic to be executed when applying this migration.
     * This method differs from [[up()]] in that the DB logic implemented here will
     * be enclosed within a DB transaction.
     * Child classes may implement this method instead of [[up()]] if the DB logic
     * needs to be within a transaction.
     *
     * @return boolean return a false value to indicate the migration fails
     * and should not proceed further. All other return values mean the migration succeeds.
     */
    public function safeUp()
    {
        $this->driver = Craft::$app->getConfig()->getDb()->driver;
        if ($this->createTables()) {
            $this->createIndexes();
            $this->addForeignKeys();
            // Refresh the db schema caches
            Craft::$app->db->schema->refresh();
            $this->insertDefaultData();
        }

        return true;
    }

    /**
     * This method contains the logic to be executed when removing this migration.
     * This method differs from [[down()]] in that the DB logic implemented here will
     * be enclosed within a DB transaction.
     * Child classes may implement this method instead of [[down()]] if the DB logic
     * needs to be within a transaction.
     *
     * @return boolean return a false value to indicate the migration fails
     * and should not proceed further. All other return values mean the migration succeeds.
     */
    public function safeDown()
    {
        $this->driver = Craft::$app->getConfig()->getDb()->driver;
        $this->removeTables();

        return true;
    }

    // Protected Methods
    // =========================================================================

    /**
     * Creates the tables needed for the Records used by the plugin
     *
     * @return bool
     */
    protected function createTables()
    {
        $tablesCreated = false;

        // craftcommerceshippingratesmatrix_shipping_rates table
        $tableSchema = Craft::$app->db->schema->getTableSchema(ShippingRate::tableName());
        if ($tableSchema === null) {
            $tablesCreated = true;
            $this->createTable(
                ShippingRate::tableName(),
                [
                    'id' => $this->primaryKey()->unsigned(),
                    'elementId' => $this->integer()->notNull(),
                    'fieldId' => $this->integer()->notNull(),
                    'fromStateId' => $this->integer()->notNull(),
                    'toStateId' => $this->integer()->notNull(),
                    'rate' => $this->decimal('14,2'),
                    'siteId' => $this->integer()->notNull(),
                    'dateCreated' => $this->dateTime()->notNull(),
                    'dateUpdated' => $this->dateTime()->notNull(),
                    'uid' => $this->uid(),
                ]
            );
        }

        return $tablesCreated;
    }

    /**
     * Creates the indexes needed for the Records used by the plugin
     *
     * @return void
     */
    protected function createIndexes()
    {
        // craftcommerceshippingratesmatrix_shipping_rates table
        $this->createIndex(null, ShippingRate::tableName(), ['elementId','fieldId','fromStateId','toStateId','rate','siteId'], true);

        // Additional commands depending on the db driver
        switch ($this->driver) {
            case DbConfig::DRIVER_MYSQL:
                break;
            case DbConfig::DRIVER_PGSQL:
                break;
        }
    }

    /**
     * Creates the foreign keys needed for the Records used by the plugin
     *
     * @return void
     */
    protected function addForeignKeys()
    {
        $this->addForeignKey(
            $this->db->getForeignKeyName(ShippingRate::tableName(), 'siteId'),
            ShippingRate::tableName(),
            'siteId',
            CraftTable::SITES,
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            $this->db->getForeignKeyName(ShippingRate::tableName(), 'fieldId'),
            ShippingRate::tableName(),
            'fieldId',
            CraftTable::FIELDS,
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            $this->db->getForeignKeyName(ShippingRate::tableName(), 'elementId'),
            ShippingRate::tableName(),
            'elementId',
            CraftTable::ELEMENTS,
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            $this->db->getForeignKeyName(ShippingRate::tableName(), 'fromStateId'),
            ShippingRate::tableName(),
            'fromStateId',
            CommerceTable::STATES,
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            $this->db->getForeignKeyName(ShippingRate::tableName(), 'toStateId'),
            ShippingRate::tableName(),
            'toStateId',
            CommerceTable::STATES,
            'id',
            'CASCADE',
            'CASCADE'
        );
    }

    /**
     * Populates the DB with the default data.
     *
     * @return void
     */
    protected function insertDefaultData()
    {
    }

    /**
     * Removes the tables needed for the Records used by the plugin
     *
     * @return void
     */
    protected function removeTables()
    {
        // craftcommerceshippingratesmatrix_shipping_rates table
        $this->dropTableIfExists(ShippingRate::tableName());
    }
}
