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
use batchnz\ccshippingratesmatrix\models\Settings;
use batchnz\ccshippingratesmatrix\records\Settings as SettingsRecord;

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
        $params = Craft::$app->getUrlManager()->getRouteParams();
        $settings = SettingsRecord::find()->one();
        return $this->renderTemplate($plugin->handle.'/settings', array_merge($params, [
            'plugin' => $plugin,
            'actionUrl' => 'admin/'.$plugin->handle.'/save-settings',
            'settings' => $settings
        ]));
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
        $data = Craft::$app->getRequest()->getBodyParam('settings', []);

        $settings = new Settings($data);

        if (!$settings->validate()) {
            Craft::$app->getSession()->setError(Craft::t('app', 'Couldn’t save plugin settings.'));
            Craft::$app->getUrlManager()->setRouteParams(['plugin' => $plugin, 'errors' => $settings->getErrors()]);
            return null;
        }

        $settingsRecord = new SettingsRecord($settings);

        // Truncate the table as there's only one record
        Craft::$app->db->createCommand()->truncateTable(SettingsRecord::tableName())->execute();
        $settingsRecord->save();

        Craft::$app->getSession()->setNotice(Craft::t('app', 'Plugin settings saved.'));

        return $this->redirectToPostedUrl();
    }
}
