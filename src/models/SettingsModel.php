<?php

namespace topshelfcraft\legacylogin\models;

use craft\base\Model;

/**
 * Class SettingsModel
 */
class SettingsModel extends Model
{
    public static $defaultServiceOrder = [
        'ee2',
        'wordPress',
        'bigCommerce',
        'wellspring'
    ];

    /** @var array $serviceOrder */
    public $serviceOrder = [
        'ee2',
        'wordPress',
        'bigCommerce',
        'wellspring'
    ];

    /** @var array $drivers */
    public $drivers = [];
}
