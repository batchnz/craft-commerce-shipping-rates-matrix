<?php
/**
 * Craft Commerce Shipping Rates Matrix plugin for Craft CMS 3.x
 *
 *  Configure shipping rates between multiple regions using a table matrix.
 *
 * @link      https://www.batch.nz
 * @copyright Copyright (c) 2019 Josh Smith
 */

namespace batchnz\ccshippingratesmatrix\controllers;

use batchnz\ccshippingratesmatrix\Plugin;

use Craft;
use craft\web\Controller;

/**
 * Shipping Rates Controller
 *
 * @author    Josh Smith
 * @package   CraftCommerceShippingRatesMatrix
 * @since     1.0.0
 */
class ShippingRatesController extends Controller
{
    // Public Methods
    // =========================================================================

    /**
     * Allows editing of plugin settings
     * @author Josh Smith <josh@batch.nz>
     * @return void
     */
    public function actionSettings()
    {
        $plugin = Plugin::$instance;
        return $this->renderTemplate($plugin->handle.'/settings', [
            'plugin' => $plugin,
            'actionUrl' => 'admin/'.$plugin->handle.'/save-settings',
            'settings' => $plugin->getSettings()
        ]);
    }

    /**
     * Saves plugin settings
     * Blatantly stolen from the plugins controller
     * @author Josh Smith <josh@batch.nz>
     * @return void
     */
    public function actionSaveSettings()
    {
        $this->requirePostRequest();

        $plugin = Plugin::$instance;
        $settings = Craft::$app->getRequest()->getBodyParam('settings', []);

        if (!Craft::$app->getPlugins()->savePluginSettings($plugin, $settings)) {
            Craft::$app->getSession()->setError(Craft::t('app', 'Couldn’t save plugin settings.'));
            Craft::$app->getUrlManager()->setRouteParams(['plugin' => $plugin]);
            return null;
        }

        Craft::$app->getSession()->setNotice(Craft::t('app', 'Plugin settings saved.'));

        return $this->redirectToPostedUrl();
    }
}
