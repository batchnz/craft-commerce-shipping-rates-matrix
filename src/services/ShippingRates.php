<?php
/**
 * Craft Commerce Shipping Rates Matrix plugin for Craft CMS 3.x
 * Configure shipping rates between multiple regions using a table matrix.
 *
 * @link      https://www.batch.nz
 * @copyright Copyright (c) 2019 Josh Smith
 */

namespace batchnz\ccshippingratesmatrix\services;

use batchnz\ccshippingratesmatrix\Plugin;
use batchnz\ccshippingratesmatrix\fields\ShippingRates as ShippingRatesField;
use batchnz\ccshippingratesmatrix\models\ShippingRates as ShippingRatesModel;
use batchnz\ccshippingratesmatrix\records\ShippingRate;

use Craft;
use craft\base\Component;
use craft\base\Element;
use craft\base\Field;
use craft\db\Table;
use craft\commerce\db\Table as CommerceTable;
use craft\elements\User;
use craft\helpers\StringHelper;
use craft\commerce\Plugin as CommercePlugin;
use craft\commerce\elements\Variant;
use craft\commerce\models\Address;
use craft\commerce\models\Customer;

use yii\db\Query;

/**
 * Shipping Rates Service
 *
 * All of your plugin’s business logic should go in services, including saving data,
 * retrieving data, etc. They provide APIs that your controllers, template variables,
 * and other plugins can interact with.
 *
 * https://craftcms.com/docs/plugins/services
 *
 * @author    Josh Smith
 * @package   CraftCommerceShippingRatesMatrix
 * @since     1.0.0
 */
class ShippingRates extends Component
{
    /**
     * Saves the rates to a custom table from a populated element
     * @author Josh Smith <josh@batch.nz>
     * @param  Element $element Element containing shipping rate fields
     * @param  bool    $isNew   Whether the element is new or not
     * @return void
     */
    public function saveRates(Element $element, bool $isNew)
    {
        $fields = $element->getFieldLayout()->getFields();
        foreach ($fields as $field) {
            if( ! $field instanceof ShippingRatesField ) continue;
            $this->_saveRates($element, $field, $element->{$field->handle});
        }
    }

    /**
     * Returns a shipping rate for the variant and user
     * @author Josh Smith <josh@batch.nz>
     * @param  Variant $variant
     * @param  User    $user
     * @return ShippingRatesModel
     */
    public function getDeliveryRate(Variant $variant, Address $vendorShippingAddress, Address $customerShippingAddress): ShippingRatesModel
    {
        $shippingRateRecord = $this->_getRatesQuery($vendorShippingAddress, $customerShippingAddress)
            ->innerJoin(Table::RELATIONS.' r', 'r.targetId = '.ShippingRate::tableName().'.elementId')
            ->innerJoin(CommerceTable::VARIANTS.' cv', 'cv.id = r.sourceId')
            ->andWhere(['cv.id' => $variant->id])
        ->one();

        return new ShippingRatesModel($shippingRateRecord);
    }

    /**
     * Returns shipping rates for the element and field
     * @author Josh Smith <josh@batch.nz>
     * @param  Element  $element
     * @param  Field    $field
     * @param  int|null $siteId
     * @return array
     */
    public function getRatesMatrix(Element $element, Field $field, int $siteId = null): array
    {
        if( is_null($siteId) ){
            $siteId = Craft::$app->getSites()->getCurrentSite()->id;
        }

        $shippingRates = [];
        $rateRecords = ShippingRate::find()->where([
            'elementId' => $element->id,
            'fieldId' => $field->id,
            'siteId' => $siteId
        ])->all() ?? [];

        foreach ($rateRecords as $record) {
            $shippingRates[] = new ShippingRatesModel($record);
        }

        // Generate a matrix from the shipping rates
        $shippingRatesMatrix = $this->_createShippingRatesMatrix($shippingRates);

        // Fetch the shipping regions
        $shippingRegions = $this->getShippingRegions();

        // Convert the regions and rates into rows and cols
        $cols = $this->_createTableCols($shippingRegions);
        $rows = $this->_createTableRows($cols, $shippingRatesMatrix);

        return [$rows, $cols];
    }

    /**
     * Returns an array of shipping regions
     * This must return an array of objects with an ID/Name property
     * @author Josh Smith <josh@batch.nz>
     * @return array
     */
    public function getShippingRegions(): array
    {
        $commerce = CommercePlugin::getInstance();
        return $commerce->getStates()->getAllStates();
    }

    /**
     * Returns a generic query object for fetching rates data
     * @author Josh Smith <josh@batch.nz>
     * @param  Address $fromAddress
     * @param  Address $toAddress
     * @return Query
     */
    protected function _getRatesQuery(Address $fromAddress, Address $toAddress): Query
    {
        return ShippingRate::find()->where([
            ShippingRate::tableName().'.fromStateId' => $this->_parseAddressValue($fromAddress),
            ShippingRate::tableName().'.toStateId' => $this->_parseAddressValue($toAddress),
        ]);
    }

    /**
     * Returns the piece of information used to match rates to an address
     * @author Josh Smith <josh@batch.nz>
     * @param  Address $address
     * @return mixed
     */
    protected function _parseAddressValue(Address $address)
    {
        return $address->getState()->id;
    }

    /**
     * Creates a matrix array from the DB shipping rates
     * @author Josh Smith <josh@batch.nz>
     * @param  array  $shippingRates
     * @return array
     */
    protected function _createShippingRatesMatrix(array $shippingRates): array
    {
        $matrix = [];
        foreach ($shippingRates as $shippingRate) {
            $matrix[$shippingRate->fromStateId][$shippingRate->toStateId] = $shippingRate->rate;
        }
        return $matrix;
    }

    /**
     * Generates the table rows
     * @author Josh Smith <josh@batch.nz>
     * @param  array  $cols
     * @param  array  $matrix
     * @return array
     */
    protected function _createTableRows(array $cols, array $matrix): array
    {
        $rows = [];
        $regionIds = array_filter(array_keys($cols));

        // Loop over the cols to create each row
        // We can do this as the matrix will always have equal length columns and rows.
        foreach ($regionIds as $fromRegionId) {

            $row = [];
            foreach ($regionIds as $toRegionId) { // Now loop over the columns again to generate the cells

                // Generate the header cell
                if( empty($row) )
                    $row[] = ['value' => $cols[$fromRegionId]['heading']];

                // Determine the shipping rate
                $rate = (empty($matrix) ? null : ($matrix[$fromRegionId][$toRegionId] ?? '0.00'));

                // The cell position is 0 when toRegionId is 0
                // Determine whether to use the heading or rate as the cell value
                $row[$toRegionId] = ['value' => $rate];
            }

            $rows[$fromRegionId] = $row;
        }

        return $rows;
    }

    /**
     * Creates the table columns
     * @author Josh Smith <josh@batch.nz>
     * @param  array  $regions
     * @return array
     */
    protected function _createTableCols(array $regions): array
    {
        $cols = [[
            'heading' => '',
            'type' => 'heading',
            'info' => 'These are the states listed in the commerce settings.', // TODO: make a config setting?
        ]];

        foreach ($regions as $region) {
            $cols[$region->id] = [
                'heading' => $region->name,
                'type' => 'number',
            ];
        }

        return $cols;
    }

    /**
     * Performs the transformation of data and actual DB save
     * @author Josh Smith <josh@batch.nz>
     * @param  Element $element
     * @param  Field   $field
     * @param  array   $data
     * @return int
     */
    protected function _saveRates(Element $element, Field $field, array $data)
    {
        $site = Craft::$app->getSites()->getCurrentSite();

        $toInsert = [];
        foreach ($data as $rowId => $cols) {
            foreach ($cols as $colId => $value) {
                $toInsert[] = [
                    $element->id,
                    $field->id,
                    $rowId,
                    $colId,
                    $value,
                    $site->id
                ];
            }
        }

        // Delete existing rates for this element, field & site
        $deleteResult = ShippingRate::deleteAll([
            'elementId' => $element->id,
            'fieldId' => $field->id,
            'siteId' => $site->id
        ]);

        // Return at this point if no data exists
        if( empty($toInsert) ) return 0;

        // Batch insert the new rates
        return Craft::$app->db->createCommand()->batchInsert(ShippingRate::tableName(), ['elementId','fieldId','fromStateId','toStateId','rate','siteId'], $toInsert)->execute();
    }
}
