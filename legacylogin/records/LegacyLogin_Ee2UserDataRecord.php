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

			'member_id' => array(AttributeType::Number, 'required' => true),
			'group_id' => array(AttributeType::Number, 'required' => false),
			'username' => array(AttributeType::String, 'required' => true),
			'screen_name' => array(AttributeType::String, 'required' => false),
			'password' => array(AttributeType::String, 'required' => true),
			'unique_id' => array(AttributeType::String, 'required' => false),
			'crypt_key' => array(AttributeType::String, 'required' => false),
			'authcode' => array(AttributeType::String, 'required' => false),
			'email' => array(AttributeType::String, 'required' => false),
			'url' => array(AttributeType::String, 'required' => false),
			'location' => array(AttributeType::String, 'required' => false),
			'occupation' => array(AttributeType::String, 'required' => false),
			'interests' => array(AttributeType::String, 'required' => false),
			'bday_d' => array(AttributeType::Number, 'required' => false),
			'bday_m' => array(AttributeType::Number, 'required' => false),
			'bday_y' => array(AttributeType::Number, 'required' => false),
			'aol_im' => array(AttributeType::String, 'required' => false),
			'yahoo_im' => array(AttributeType::String, 'required' => false),
			'msn_im' => array(AttributeType::String, 'required' => false),
			'icq' => array(AttributeType::String, 'required' => false),
			'bio' => array(AttributeType::String, 'required' => false, 'column' => ColumnType::Text),
			'signature' => array(AttributeType::String, 'required' => false, 'column' => ColumnType::Text),
			'avatar_filename' => array(AttributeType::String, 'required' => false),
			'avatar_width' => array(AttributeType::Number, 'required' => false),
			'avatar_height' => array(AttributeType::Number, 'required' => false),
			'photo_filename' => array(AttributeType::String, 'required' => false),
			'photo_width' => array(AttributeType::Number, 'required' => false),
			'photo_height' => array(AttributeType::Number, 'required' => false),
			'sig_img_filename' => array(AttributeType::String, 'required' => false),
			'sig_img_width' => array(AttributeType::Number, 'required' => false),
			'sig_img_height' => array(AttributeType::Number, 'required' => false),
			'ignore_list' => array(AttributeType::String, 'required' => false, 'column' => ColumnType::Text),
			'private_messages' => array(AttributeType::Number, 'required' => false),
			'accept_messages' => array(AttributeType::String, 'required' => false),
			'last_view_bulletins' => array(AttributeType::Number, 'required' => false),
			'last_bulletin_date' => array(AttributeType::Number, 'required' => false),
			'ip_address' => array(AttributeType::String, 'required' => false),
			'join_date' => array(AttributeType::Number, 'required' => false),
			'last_visit' => array(AttributeType::Number, 'required' => false),
			'last_activity' => array(AttributeType::Number, 'required' => false),
			'total_entries' => array(AttributeType::Number, 'required' => false, 'column' => ColumnType::MediumInt),
			'total_comments' => array(AttributeType::Number, 'required' => false, 'column' => ColumnType::MediumInt),
			'total_forum_topics' => array(AttributeType::Number, 'required' => false, 'column' => ColumnType::MediumInt),
			'total_forum_posts' => array(AttributeType::Number, 'required' => false, 'column' => ColumnType::MediumInt),
			'last_entry_date' => array(AttributeType::Number, 'required' => false),
			'last_comment_date' => array(AttributeType::Number, 'required' => false),
			'last_form_post_date' => array(AttributeType::Number, 'required' => false),
			'last_email_date' => array(AttributeType::Number, 'required' => false),
			'in_authorlist' => array(AttributeType::String, 'required' => false),
			'accept_admin_email' => array(AttributeType::String, 'required' => false),
			'accept_user_email' => array(AttributeType::String, 'required' => false),
			'notify_by_default' => array(AttributeType::String, 'required' => false),
			'notify_of_pm' => array(AttributeType::String, 'required' => false),
			'display_avatars' => array(AttributeType::String, 'required' => false),
			'display_signatures' => array(AttributeType::String, 'required' => false),
			'parse_smileys' => array(AttributeType::String, 'required' => false),
			'smart_notifications' => array(AttributeType::String, 'required' => false),
			'language' => array(AttributeType::String, 'required' => false),
			'timezone' => array(AttributeType::String, 'required' => false),
			'time_format' => array(AttributeType::String, 'required' => false),
			'include_seconds' => array(AttributeType::String, 'required' => false),
			'date_format' => array(AttributeType::String, 'required' => false),
			'cp_theme' => array(AttributeType::String, 'required' => false),
			'profile_theme' => array(AttributeType::String, 'required' => false),
			'forum_theme' => array(AttributeType::String, 'required' => false),
			'tracker' => array(AttributeType::String, 'required' => false, 'column' => ColumnType::Text),
			'template_size' => array(AttributeType::String, 'required' => false),
			'notepad' => array(AttributeType::String, 'required' => false, 'column' => ColumnType::Text),
			'notepad_size' => array(AttributeType::String, 'required' => false),
			'quick_links' => array(AttributeType::String, 'required' => false, 'column' => ColumnType::Text),
			'quick_tabs' => array(AttributeType::String, 'required' => false, 'column' => ColumnType::Text),
			'show_sidebar' => array(AttributeType::String, 'required' => false),
			'pmember_id' => array(AttributeType::Number, 'required' => false),
			'salt' => array(AttributeType::String, 'required' => false),

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