<?php
/**
 * Craft Commerce Shipping Rates Matrix plugin for Craft CMS 3.x
 *
 *  Configure shipping rates between multiple regions using a table matrix.
 *
 * @link      https://www.batch.nz
 * @copyright Copyright (c) 2019 Josh Smith
 */

namespace batchnz\ccshippingratesmatrix\services;

use batchnz\ccshippingratesmatrix\Plugin;

use Craft;
use craft\base\Component;

/**
 * CraftCommerceShippingRatesMatrixService Service
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
class CraftCommerceShippingRatesMatrixService extends Component
{
    // Public Methods
    // =========================================================================

    /**
     * This function can literally be anything you want, and you can have as many service
     * functions as you want
     *
     * From any other plugin file, call it like this:
     *
     *     CraftCommerceShippingRatesMatrix::$plugin->craftCommerceShippingRatesMatrixService->exampleService()
     *
     * @return mixed
     */
    public function exampleService()
    {
        $result = 'something';

        return $result;
    }
}
