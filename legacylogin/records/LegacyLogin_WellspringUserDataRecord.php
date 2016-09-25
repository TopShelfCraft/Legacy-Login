<?php
namespace Craft;

/**
 * LegacyLogin_WellspringUserDataRecord
 *
 * @author    Top Shelf Craft <michael@michaelrog.com>
 * @copyright Copyright (c) 2016, Michael Rog
 * @license   http://topshelfcraft.com/license
 * @see       http://topshelfcraft.com
 * @package   craft.plugins.legacylogin
 * @since     1.0
 */
class LegacyLogin_WellspringUserDataRecord extends BaseRecord
{

	/**
	 * @return string
	 */
	public function getTableName()
	{
	    return 'legacylogin_data_wellspring';
	}

	/**
	 * @access protected
	 * @return array
	 */
	protected function defineAttributes()
	{
		return array(
			'user_id'                => array(AttributeType::Number, 'required' => true),
			'user_name'              => array(AttributeType::String, 'required' => true),
			'user_email'             => array(AttributeType::String, 'required' => true),
			'user_pass'              => array(AttributeType::String, 'required' => true),
			'user_isActive'          => array(AttributeType::Number, 'required' => false, 'column' => ColumnType::TinyInt, 'default' => 0),
			'user_dateRegistered'    => array(AttributeType::DateTime, 'required' => false),
			'user_dateModified'      => array(AttributeType::DateTime, 'required' => false),
			'user_dateExpires'       => array(AttributeType::DateTime, 'required' => false)
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