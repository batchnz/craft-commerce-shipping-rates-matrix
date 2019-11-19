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
     * Returns the validation rules for attributes.
     *
     * Validation rules are used by [[validate()]] to check if attribute values are valid.
     * Child classes may override this method to declare different validation rules.
     *
     * More info: http://www.yiiframework.com/doc-2.0/guide-input-validation.html
     *
     * @return array
     */
    public function rules()
    {
        return [
            [['fieldId', 'siteId', 'fromStateId', 'toStateId', 'rate'], 'required'],
            [['fieldId', 'siteId', 'fromStateId', 'toStateId'], 'int'],
            [['rate'], 'string'],
        ];
    }
}
