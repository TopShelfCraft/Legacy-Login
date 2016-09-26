<?php
namespace Craft;

/**
 * LegacyLogin_WordPressUserDataRecord
 *
 * @author    Top Shelf Craft <michael@michaelrog.com>
 * @copyright Copyright (c) 2016, Michael Rog
 * @license   http://topshelfcraft.com/license
 * @see       http://topshelfcraft.com
 * @package   craft.plugins.legacylogin
 * @since     1.0
 */
class LegacyLogin_WordPressUserDataRecord extends BaseRecord
{

	/**
	 * @return string
	 */
	public function getTableName()
	{
	    return 'legacylogin_data_wordpress';
	}

	/**
	 * @access protected
	 * @return array
	 */
	protected function defineAttributes()
	{
		return array(
            'id'                     => array(AttributeType::Number, 'required' => true),
            'user_login'             => array(AttributeType::String, 'required' => true),
            'user_pass'              => array(AttributeType::String, 'required' => true),
            'user_nicename'          => array(AttributeType::String, 'required' => true),
            'user_email'             => array(AttributeType::String, 'required' => true),
            'user_url'               => array(AttributeType::String, 'required' => false, 'default' => ''),
            'user_registered'        => array(AttributeType::DateTime, 'required' => false),
            'user_activation_key'    => array(AttributeType::String, 'required' => false),
            'user_status'            => array(AttributeType::Number, 'required' => false, 'column' => ColumnType::TinyInt, 'default' => 0),
            'display_name'           => array(AttributeType::String, 'required' => true),
		);
	}

	/**
	 * @return array
	 */
	public function defineRelations()
	{
		return array(
		);
	}

	/**
	 * @return array
	 */
	public function defineIndexes()
	{
		return array();
	}

	/**
	 * @return array
	 */
	public function scopes()
	{
		return array();
	}

}