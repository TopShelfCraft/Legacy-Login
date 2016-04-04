<?php
namespace Craft;

/**
 * LegacyLogin_Ee2UserDataRecord
 *
 * @author    Top Shelf Craft <michael@michaelrog.com>
 * @copyright Copyright (c) 2016, Michael Rog
 * @license   http://topshelfcraft.com/license
 * @see       http://topshelfcraft.com
 * @package   craft.plugins.legacylogin
 * @since     1.0
 */
class LegacyLogin_Ee2UserDataRecord extends BaseRecord
{

	/**
	 * @return string
	 */
	public function getTableName()
	{
		return 'legacylogin_data_ee2';
	}

	/**
	 * @access protected
	 * @return array
	 */
	protected function defineAttributes()
	{
		return array(
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