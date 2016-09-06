<?php
namespace Craft;

/**
 * LegacyLoginVariable returns the loging type that is stored in the session

 * {@link WebApp::userSession `craft()->legacyLogin`}.
 *
 * @author    Top Shelf Craft <michael@michaelrog.com>
 * @copyright Copyright (c) 2016, Michael Rog
 * @license   http://topshelfcraft.com/license
 * @see       http://topshelfcraft.com
 * @package   craft.plugins.legacylogin
 * @since     1.0
 */
class LegacyLoginVariable
{
    /**
     *
     * @return Craft|BigCommerce|EE2|null
     */
    public function loginType(){
        return craft()->httpSession->get("legacyLoginType");
    }

}
