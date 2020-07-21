<?php
/**
 * Craft Commerce Shipping Rates Matrix plugin for Craft CMS 3.x
 *
 *  Configure shipping rates between multiple regions using a table matrix.
 *
 * @link      https://www.batch.nz
 * @copyright Copyright (c) 2019 Josh Smith
 */

namespace batchnz\ccshippingratesmatrix\models;

use batchnz\ccshippingratesmatrix\Plugin;
use batchnz\ccshippingratesmatrix\models\Settings;
use batchnz\ccshippingratesmatrix\records\Settings as SettingsRecord;

use Craft;
use craft\base\Model;

use Money\Money;
use Money\Formatter\DecimalMoneyFormatter;
use Money\Currencies\ISOCurrencies;

/**
 * CraftCommerceShippingRatesMatrixModel Model
 *
 * Models are containers for data. Just about every time information is passed
 * between services, controllers, and templates in Craft, it’s passed via a model.
 *
 * https://craftcms.com/docs/plugins/models
 *
 * @author    Josh Smith
 * @package   CraftCommerceShippingRatesMatrix
 * @since     1.0.0
 */
class ShippingRates extends Model
{
    // Public Properties
    // =========================================================================

    /**
     * Some model attribute
     *
     * @var int
     */
    public $id;

    /**
     * Some model attribute
     *
     * @var int
     */
    public $elementId;

    /**
     * Some model attribute
     *
     * @var int
     */
    public $fieldId;

    /**
     * Some model attribute
     *
     * @var int
     */
    public $siteId;

    /**
     * Some model attribute
     *
     * @var int
     */
    public $fromStateId;

    /**
     * Some model attribute
     *
     * @var int
     */
    public $toStateId;

    /**
     * Shipping rate
     * @var float
     */
    public $rate;

    /**
     * Date Created
     * @var string
     */
    public $dateCreated;

    /**
     * Date Updated
     * @var string
     */
    public $dateUpdated;

    /**
     * UID
     * @var string
     */
    public $uid;

    /**
     * Stores plugin settings
     * @var array
     */
    protected $pluginSettings;

    public function init() {
        parent::init();
        $record = SettingsRecord::find()->one() ?? [];
        $this->pluginSettings = new Settings($record);
    }


    // Public Methods
    // =========================================================================

    /**
     * Returns the rate
     * @author Josh Smith <josh@batch.nz>
     * @return float
     */
    public function getRate(): float
    {
        // Create a new money object in NZD
        $rate = Money::NZD($this->rate * 100);
        $modifier = $this->pluginSettings->fuelAdjustmentFactor;

        // Define currencies and a formatter
        $currencies = new ISOCurrencies();
        $moneyFormatter = new DecimalMoneyFormatter($currencies);

        return $moneyFormatter->format(
            $rate->multiply((1 + $modifier / 100), Money::ROUND_UP) // Always round up, to ensure shipping rates are covered.
        );
    }

    /**
     * Returns the validation rules for attributes.
     * @return array
     */
    public function rules()
    {
        return [
            [['elementId', 'fieldId', 'siteId', 'fromStateId', 'toStateId', 'rate'], 'required'],
            [['elementId', 'fieldId', 'siteId', 'fromStateId', 'toStateId'], 'int'],
            [['rate'], 'string'],
        ];
    }
}
