<?php
namespace TopShelfCraft\LegacyLogin\login;

/**
 * @author Michael Rog <michael@michaelrog.com>
 */
class LoginRequest
{

    /**
	 * @var string $loginName
	 */
    public $loginName;

    /**
	 * @var string $password
	 */
    public $password;

    /**
	 * @var bool $rememberMe
	 */
    public $rememberMe;

    public function __construct(string $loginName = null, string $password = null, bool $rememberMe = false)
	{
		$this->loginName = $loginName;
		$this->password = $password;
		$this->rememberMe = $rememberMe;
	}

}
