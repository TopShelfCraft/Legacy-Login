<?php

/**
 * Legacy Login Config
 *
 * @author    Top Shelf Craft <michael@michaelrog.com>
 * @copyright Copyright (c) 2016, Michael Rog
 * @license   http://topshelfcraft.com/license
 * @see       http://topshelfcraft.com
 * @package   craft.plugins.legacylogin
 * @since     1.0
 */

return array(

	'allowedServices' => ['BigCommerce', 'EE2', 'Wellspring', 'WordPress'],
	'matchBy' => 'email',
	'setPassword' => true,
	'requirePasswordReset' => false,

);