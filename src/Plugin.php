<?php
/**
 * Craft Commerce Shipping Rates Matrix plugin for Craft CMS 3.x
 *
 *  Configure shipping rates between multiple regions using a table matrix.
 *
 * @link      https://www.batch.nz
 * @copyright Copyright (c) 2019 Josh Smith
 */

namespace batchnz\ccshippingratesmatrix;

use batchnz\ccshippingratesmatrix\services\ShippingRates;
use batchnz\ccshippingratesmatrix\fields\ShippingRates as ShippingRatesField;
use batchnz\ccshippingratesmatrix\models\Settings;

use Craft;
use craft\base\Plugin as BasePlugin;
use craft\services\Plugins;
use craft\events\PluginEvent;
use craft\services\Fields;
use craft\events\RegisterComponentTypesEvent;
use craft\web\UrlManager;
use craft\events\RegisterUrlRulesEvent;

use Yii;
use yii\base\Event;

/**
 * @author    Josh Smith
 * @package   CraftCommerceShippingRatesMatrix
 * @since     1.0.0
 */
class Plugin extends BasePlugin
{
    // Static Properties
    // =========================================================================

    /**
     * @var CraftCommerceShippingRatesMatrix
     */
    public static $instance;

    // Public Properties
    // =========================================================================

    /**
     * @var string
     */
    public $schemaVersion = '1.0.1';

    /**
     * @var boolean
     */
    public $hasCpSection = true;

    // Public Methods
    // =========================================================================

    public function init()
    {
        parent::init();
        self::$instance = $this;

        Craft::setAlias('@batchnz', __DIR__);

        $this->registerComponents();
        $this->registerEvents();

        Craft::info(
            Craft::t(
                $this->handle,
                '{name} plugin loaded',
                ['name' => $this->name]
            ),
            __METHOD__
        );
    }

    public function getCpNavItem()
    {
        $item = parent::getCpNavItem();
        $item['label'] = 'Shipping';
        $item['url'] = $this->handle.'/settings';
        $item['subnav'] = [
            'settings' => ['label' => 'Settings', 'url' => $this->handle.'/settings'],
        ];
        return $item;
    }


    // Protected Methods
    // =========================================================================

    protected function createSettingsModel()
    {
        return new Settings();
    }

    protected function settingsHtml()
    {
        return \Craft::$app->getView()->renderTemplate($this->handle.'/settings', [
            'settings' => $this->getSettings()
        ]);
    }

    /**
     * Registers Plugin Components
     * @author Josh Smith <josh@batch.nz>
     * @return void
     */
    protected function registerComponents()
    {
        Craft::$app->setComponents(['shippingrates' => ShippingRates::class]);
    }

    /**
     * Registers plugin events
     * @author Josh Smith <josh@batch.nz>
     * @return void
     */
    protected function registerEvents()
    {
        Event::on(
            UrlManager::class,
            UrlManager::EVENT_REGISTER_CP_URL_RULES,
            function(RegisterUrlRulesEvent $event) {
                $event->rules[$this->handle.'/settings'] = $this->handle.'/shipping-rates/settings';
                $event->rules[$this->handle.'/save-settings'] = $this->handle.'/shipping-rates/save-settings';
            }
        );

        // Register our fields
        Event::on(
            Fields::class,
            Fields::EVENT_REGISTER_FIELD_TYPES,
            function (RegisterComponentTypesEvent $event) {
                $event->types[] = ShippingRatesField::class;
            }
        );
    }
}
