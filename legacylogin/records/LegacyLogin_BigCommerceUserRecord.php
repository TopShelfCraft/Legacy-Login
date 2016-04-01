<?php
namespace Craft;

/**
 * LegacyLogin_BigCommerceUserDataRecord
 *
 * @author    Top Shelf Craft <michael@michaelrog.com>
 * @copyright Copyright (c) 2016, Michael Rog
 * @license   http://topshelfcraft.com/license
 * @see       http://topshelfcraft.com
 * @package   craft.plugins.legacylogin
 * @since     1.0
 */
class LegacyLogin_BigCommerceUserDataRecord extends BaseRecord
{

	/**
	 * @return string
	 */
	public function getTableName()
	{
		return 'legacylogin_data_bigcommerce';
	}

	/**
	 * @access protected
	 * @return array
	 */
	protected function defineAttributes()
	{
		return array(

			'customerid' => array(AttributeType::Number, 'required' => true),
			'custpassword' => array(AttributeType::String, 'required' => true),

			'custconcompany' => array(AttributeType::String, 'required' => false),
			'custconfirstname' => array(AttributeType::String, 'required' => false),
			'custconlastname' => array(AttributeType::String, 'required' => false),
			'custconemail' => array(AttributeType::String, 'required' => false),
			'custconphone' => array(AttributeType::String, 'required' => false),
			'customertoken' => array(AttributeType::String, 'required' => false),
			'customerpasswordresettoken' => array(AttributeType::String, 'required' => false),
			'customerpasswordresetemail' => array(AttributeType::String, 'required' => false),
			'custdatejoined' => array(AttributeType::Number, 'required' => false),
			'custlastmodified' => array(AttributeType::Number, 'required' => false),
			'custimportpassword' => array(AttributeType::String, 'required' => false),
			'custstorecredit' => array(AttributeType::Number, 'required' => false, 'column' => ColumnType::Decimal),
			'custregipaddress' => array(AttributeType::String, 'required' => false),
			'custgroupid' => array(AttributeType::Number, 'required' => false),
			'custnotes' => array(AttributeType::String, 'required' => false, 'column' => ColumnType::Text),
			'custformsessionid' => array(AttributeType::Number, 'required' => false),

		);
	}

	/**
	 * @return array
	 */
	public function defineRelations()
	{
		return array();
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