<?php

namespace topshelfcraft\legacylogin\models;

use craft\base\Model;
use topshelfcraft\legacylogin\models\services\BaseLoginConfigModel;
use topshelfcraft\legacylogin\models\services\CraftLoginConfigModel;

/**
 * Class SettingsModel
 */
class SettingsModel extends Model
{
    /** @var array CONFIG_MODEL_NAMES */
    const CONFIG_MODEL_NAMES = [
        'ee2' => 'EE2LoginConfigModel',
        'wordPress' => 'WordPressLoginConfigModel',
        'bigCommerce' => 'BigCommerceLoginConfigModel',
        'wellspring' => 'WellspringLoginConfigModel',
    ];

    /** @var array $serviceConfig */
    public $serviceConfig = [];

    /**
     * Get service order
     * @return BaseLoginConfigModel[]
     */
    public function getServiceConfig() : array
    {
        // Get service config model names
        $configModelNames = self::CONFIG_MODEL_NAMES;

        // Start an array for our service models with craft as the first item
        $serviceModels = [
            new CraftLoginConfigModel([
                'configured' => true,
            ]),
        ];

        // Iterate through service config
        foreach ($this->serviceConfig as $key => $config) {
            // Make sure this is a service we can support and it's not craft
            if ($key === 'craft' || ! isset($configModelNames[$key])) {
                continue;
            }

            // Get the config settings model
            $serviceModels[] = $this->getConfigSettingsModel(
                $configModelNames[$key],
                $config
            );

            // Unset the config model name item
            unset($configModelNames[$key]);
        }

        // Get remainder of config items
        foreach ($configModelNames as $modelName) {
            $serviceModels[] = $this->getConfigSettingsModel($modelName);
        }

        // Return the service models
        return $serviceModels;
    }

    /**
     * Get config settings model
     * @param string $modelName,
     * @param array $config
     * @return BaseLoginConfigModel
     */
    private function getConfigSettingsModel(
        string $modelName,
        array $config = []
    ) : BaseLoginConfigModel {
        // Build the model class
        $class = "\\topshelfcraft\\legacylogin\\models\\services\\{$modelName}";

        // Return the model
        return new $class($config);
    }
}
