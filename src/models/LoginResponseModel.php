<?php

namespace topshelfcraft\legacylogin\models;

use craft\base\Model;

/**
 * Class LoginResponseModel
 */
class LoginResponseModel extends Model
{
    /** @var bool $success */
    public $success = false;

    /** @var string $type */
    public $type = '';

    /** @var string $errorCode */
    public $errorCode = '';

    /** @var string $error */
    public $error = '';
}
