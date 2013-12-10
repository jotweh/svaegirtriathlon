<?php
	/**
	 * @package Joomla
	 * @subpackage Mailster
	 * @copyright (C) 2010 Holger Brandt IT Solutions
	 * @license GNU/GPL, see license.txt
	 * Mailster is free software; you can redistribute it and/or
	 * modify it under the terms of the GNU General Public License 2
	 * as published by the Free Software Foundation.
	 * 
	 * Mailster is distributed in the hope that it will be useful,
	 * but WITHOUT ANY WARRANTY; without even the implied warranty of
	 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	 * GNU General Public License for more details.
	 * 
	 * You should have received a copy of the GNU General Public License
	 * along with Mailster; if not, write to the Free Software
	 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA
	 * or see http://www.gnu.org/licenses/.
	 */
	
	defined( '_JEXEC' ) or die( 'Restricted access' );
	
	class MstConsts
	{
				
		const SUBSCR_SUBSCRIBER_TMPL = '{mailster_subscriber}';
		const SUBSCR_UNSUBSCRIBER_TMPL = '{mailster_unsubscriber}';
		const SUBSCR_PARAM_START = '[';
		const SUBSCR_PARAM_END = ']';
		const SUBSCR_KEY_VALUE_DELIMITER = ':';
		const SUBSCR_KEY_VALUE_PAIR_DELIMITER = '|';
		const SUBSCR_LIST_KEY = 'list';
		const SUBSCR_ID_KEY = 'id';
		const SUBSCR_SUBMIT_TEXT = 'submitTxt';
		const SUBSCR_HEADER_TEXT = 'headerTxt';
		const SUBSCR_BUTTON_TEXT = 'buttonTxt';
		const SUBSCR_CSS_PREFIX = 'css';
		const SUBSCR_LIST_LABEL = 'listLabel';
		const SUBSCR_NAME_LABEL = 'nameLabel';
		const SUBSCR_EMAIL_LABEL = 'emailLabel';
		const SUBSCR_DESIGN_CHOICE = 'designChoice';
		const SUBSCR_CAPTCHA = 'captcha';
		const SUBSCR_NAME_FIELD = 'subscriber_name';
		const SUBSCR_EMAIL_FIELD = 'subscriber_email';
		const SUBSCR_ID_FIELD = 'listId';
		const SUBSCR_NO_NAME_FIELD = 'hideName';
		const SUBSCR_NO_LIST_NAME = 'hideList';
		const SUBSCR_POST_IDENTIFIER = 'postId';
		const SUBSCR_SMART_HIDE = 'smartHide';
		const SUBSCR_CSS_DEFAULT = 'mailster_subscriber_';
		
		const MAIL_HEADER_CC ='Cc';
		const MAIL_HEADER_IN_REPLY_TO = 'In-Reply-To';
		const MAIL_HEADER_REFERENCES = 'References';
		const MAIL_HEADER_SENDER = 'Sender';
		const MAIL_HEADER_AUTO_SUBMITTED = 'Auto-Submitted';
		const MAIL_HEADER_RETURN_PATH = 'Return-Path';
		const MAIL_HEADER_ERRORS_TO = 'Errors-To';
		const MAIL_HEADER_PRECEDENCE = 'Precedence';
		const MAIL_HEADER_LIST_ARCHIVE = 'List-Archive';
		const MAIL_HEADER_LIST_HELP = 'List-Help';
		const MAIL_HEADER_LIST_ID = 'List-Id';
		const MAIL_HEADER_LIST_POST = 'List-Post';
		const MAIL_HEADER_LIST_SUBSCRIBE = 'List-Subscribe';
		const MAIL_HEADER_LIST_UNSUBSCRIBE = 'List-Unsubscribe';
		const MAIL_HEADER_BEEN_THERE = 'X-BeenThere';
		const MAIL_HEADER_MSG_ID = 'X-MstMessageID';
		const MAIL_HEADER_MAILSTER_TAG = 'X-PostMailer: Mailster';
		const MAIL_HEADER_MAILSTER_DEBUG = 'X-Mailster-Debug';
		
		const CAPTCHA_ID_RECAPTCHA = 'recaptcha';
		const CAPTCHA_ID_MATH = 'mathcaptcha';
		
		const LIB_HTML2TEXT = 'html2text';
		
		const MAIL_TYPE_PLAIN = 0;
		const MAIL_TYPE_MULTIPART = 1;
		const MAIL_TYPE_MESSAGE = 2;
		const MAIL_TYPE_APPLICATION = 3;
		const MAIL_TYPE_AUDIO = 4;
		const MAIL_TYPE_IMAGE = 5;
		const MAIL_TYPE_VIDEO = 6;
		const MAIL_TYPE_OTHER = 7;
		
		const MAIL_TYPE_PLAIN_STR = 'text';	
		const MAIL_TYPE_MULTIPART_STR = 'multipart';
		const MAIL_TYPE_MESSAGE_STR = 'message';
		const MAIL_TYPE_APPLICATION_STR = 'application';
		const MAIL_TYPE_AUDIO_STR = 'audio';
		const MAIL_TYPE_IMAGE_STR = 'image';
		const MAIL_TYPE_VIDEO_STR = 'video';
		const MAIL_TYPE_OTHER_STR = 'other';	
		
		const MAIL_HEADER_MAILSTER_REFERENCE_DOMAIN = 'brandt-solutions.de';
		
		const DISPOSITION_TYPE_ATTACH = 0;
		const DISPOSITION_TYPE_INLINE = 1;
		
		const ADDRESSING_MODE_TO = 0;
		const ADDRESSING_MODE_BCC = 1;
		const ADDRESSING_MODE_CC = 2;
		
		const PLUGIN_TRIGGER_SRC_ALL = 'all';
		const PLUGIN_TRIGGER_SRC_BACKEND = 'admin';
		const PLUGIN_TRIGGER_SRC_CRONJOB = 'cron';
		
		const TEXT_VARIABLES_EMAIL = '{email}';
		const TEXT_VARIABLES_NAME = '{name}';
		const TEXT_VARIABLES_DATE = '{date}';
		const TEXT_VARIABLES_LIST = '{list}';
		const TEXT_VARIABLES_SITE = '{site}';
		const TEXT_VARIABLES_UNSUBSCRIBE_URL = '{unsubscribe}';
		
		const TEXT_VARIABLES_EMAIL_ALT = 'mailster_var_email';
		const TEXT_VARIABLES_NAME_ALT = 'mailster_var_name';
		const TEXT_VARIABLES_DATE_ALT = 'mailster_var_date';
		const TEXT_VARIABLES_LIST_ALT = 'mailster_var_list';
		const TEXT_VARIABLES_SITE_ALT = 'mailster_var_site';
		const TEXT_VARIABLES_UNSUBSCRIBE_URL_ALT = 'mailster_var_unsubscribe';
		
		const PLUGIN_FLAG_NO_EXECUTION = 'no_mst_plg_exec';
		
		const NO_PARAMETER_SUPPLIED_FLAG = '---...---...---...---...---';
		
		const MAIL_FORMAT_CONVERT_NONE = 0;
		const MAIL_FORMAT_CONVERT_HTML = 1;
		const MAIL_FORMAT_CONVERT_PLAIN = 2;
		
		const MAIL_FORMAT_ALTBODY_NO = 0;
		const MAIL_FORMAT_ALTBODY_YES = 1;
		
		const BOUNCE_MODE_LIST_ADDRESS = 0;
		const BOUNCE_MODE_DEDICATED_ADDRESS = 1;
		
		const ARCHIVE_MODE_ALL = 0;
		const ARCHIVE_MODE_NO_CONTENT = 1;
		
		const MAIL_FROM_MODE_GLOBAL = 0;
		const MAIL_FROM_MODE_SENDER_EMAIL = 1;
		const MAIL_FROM_MODE_MAILING_LIST = 2;
		
		const NAME_FROM_MODE_GLOBAL = 0;
		const NAME_FROM_MODE_SENDER_NAME = 1;
		const NAME_FROM_MODE_MAILING_LIST_NAME = 2;
		
		const MAIL_FLAG_BLOCKED_NOT_BLOCKED = 0;
		const MAIL_FLAG_BLOCKED_BLOCKED = 1;
		const MAIL_FLAG_BLOCKED_FILTERED = 2;
		const MAIL_FLAG_BOUNCED_NOT_BOUNCED = 0;
		const MAIL_FLAG_BOUNCED_BOUNCED = 1;
		
		const CUSTOM_HTML_MAIL_HEADER_START = '<span id="mstHeaderStart"></span>';
		const CUSTOM_HTML_MAIL_HEADER_STOP = '<span id="mstHeaderStop"></span>';
		const CUSTOM_HTML_MAIL_FOOTER_START = '<span id="mstFooterStart"></span>';
		const CUSTOM_HTML_MAIL_FOOTER_STOP = '<span id="mstFooterStop"></span>';
		
		const ATTACHMENT_NO_FILENAME_FOUND = 'mst_no_filename';
		
		const DB_QUEUED_INSERTS_PER_QUERY = 30;
			
		// Logging Level (0 = logging off)
		const LOG_LEVEL_ERROR = 1;
		const LOG_LEVEL_WARNING = 2;
		const LOG_LEVEL_INFO = 3;
		const LOG_LEVEL_DEBUG = 4;
		
		// Log Entry Types (0 = undefined/general entry)
		const LOGENTRY_INSTALLER = 1;
		const LOGENTRY_PLUGIN = 2;
		const LOGENTRY_MAIL_RETRIEVE = 3;
		const LOGENTRY_MAIL_SEND = 4;
		
		// Log Destination
		const LOG_DEST_FILE = 0;
		const LOG_DEST_DB = 1;
		const LOG_DEST_DB_AND_FILE = 2;
	}
	
	?>
