<?php
namespace Craft;

/**
 * LegacyLogin_MatchedUserModel
 *
 * @author    Top Shelf Craft <michael@michaelrog.com>
 * @copyright Copyright (c) 2016, Michael Rog
 * @license   http://topshelfcraft.com/license
 * @see       http://topshelfcraft.com
 * @package   craft.plugins.legacylogin
 * @since     1.0
 */
class LegacyLogin_MatchedUserModel extends BaseModel
{

    /**
     * @access protected
     * @return array
     */
    protected function defineAttributes()
    {
        return array(
            'craftUserId' => array(AttributeType::Number, 'required' => true),
            'legacyUserType' => array(
                AttributeType::Enum,
                'values' => [LegacyLoginPlugin::EE2LegacyUserType, LegacyLoginPlugin::BigCommerceLegacyUserType],
                'required' => true,
            ),
            'legacyRecordId' => array(AttributeType::Number, 'required' => false),
            'legacyUserId' => array(AttributeType::Number, 'required' => true),
            'legacyUsername' => array(AttributeType::String, 'required' => false),
            'legacyEmail' => array(AttributeType::String, 'required' => true),
            'passwordReset' => array(AttributeType::Bool, 'required' => true),
        );
    }

    /**
     * @return UserModel|null
     */
    public function getCraftUser()
    {
        return craft()->users->getUserById($this->craftUserId);
    }

}
