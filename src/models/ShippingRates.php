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

use Craft;
use craft\base\Model;

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


    // Public Methods
    // =========================================================================

    /**
     * Returns the rate
     * @author Josh Smith <josh@batch.nz>
     * @return float
     */
    public function getRate(): float
    {
        return (float) $this->rate;
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
