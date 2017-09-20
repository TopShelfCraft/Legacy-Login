<?php

namespace topshelfcraft\legacylogin\models;

use craft\base\Model;

/**
 * Class LoginModel
 */
class LoginModel extends Model
{
    /** @var string $loginName */
    public $username = '';

    /** @var string $password */
    public $password = '';

    /** @var bool $rememberMe */
    public $rememberMe = false;

    /**
     * @inheritdoc
     */
    public function rules() : array
    {
        return [
            [
                [
                    'username',
                    'password',
                ],
                'required',
            ],
        ];
    }
}
