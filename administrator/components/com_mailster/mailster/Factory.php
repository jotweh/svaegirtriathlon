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
	
	class MstFactory
	{
		private $instances = array();
		private $classMapping = array();
		private $prefix = 'Mst';
		private $basePath = 'mailster';
	
		/** Private constructor -> no direct class instanciation */
		private function __construct() {}
	
		/**
		 * Gets a single instance of our Factory
		 */
		protected static function &getInstance() {
			static $factory;
			if(!is_object($factory)){
					$factory = new self();										
					if(empty($factory->classMapping))
					{
						$factory->classMapping = array(
							'Application'			=> 'app'	.DS.	'Application',
							'Authorization'			=> 'app'	.DS.	'Authorization',
							'CacheUtils'			=> 'app'	.DS.	'CacheUtils',
							'Events'				=> 'app'	.DS.	'Events',
							'Notify'				=> 'app'	.DS.	'Notify',
							'NotifyUtils'			=> 'app'	.DS.	'NotifyUtils',
							'PluginUtils'			=> 'app'	.DS.	'PluginUtils',
							'Log'					=> 'app'	.DS.	'Log',
							'Configuration'			=> 'conf'	.DS.	'Configuration',
							'ConfIO'				=> 'conf'	.DS.	'ConfIO',
							'Parameter'				=> 'conf'	.DS.	'Parameter',
							'AttachmentsUtils'		=> 'mail'	.DS.	'AttachmentsUtils',
							'MailingList'			=> 'mail'	.DS.	'MailingList',
							'MailingListMailbox'	=> 'mail'	.DS.	'MailingListMailbox',
							'MailingListUtils'		=> 'mail'	.DS.	'MailingListUtils',
							'MailQueue'				=> 'mail'	.DS.	'MailQueue',						
							'MailRetriever'			=> 'mail'	.DS.	'MailRetriever',
							'MailSender'			=> 'mail'	.DS.	'MailSender',
							'MailUtils'				=> 'mail'	.DS.	'MailUtils',
							'Recipients'			=> 'mail'	.DS.	'Recipients',
							'ThreadUtils'			=> 'mail'	.DS.	'ThreadUtils',
							'SubscriberPlugin'		=> 'subscr'	.DS.	'SubscriberPlugin',
							'SubscribeUtils'		=> 'subscr'	.DS.	'SubscribeUtils',
							'CBUtils'				=> 'utils'	.DS.	'CBUtils',
							'ConverterUtils'		=> 'utils'	.DS.	'ConverterUtils',
							'DateUtils'				=> 'utils'	.DS.	'DateUtils',
							'DBUtils'				=> 'utils'	.DS.	'DBUtils',
							'Environment'			=> 'utils'	.DS.	'Environment',
							'HashUtils'				=> 'utils'	.DS.	'HashUtils',
							'Utils'					=> 'utils'	.DS.	'Utils'
						);
					}									
					if(empty($factory->scriptMapping))
					{
						$factory->scriptMapping = array(
							'lib_recaptcha'		=> 'lib'.DS.'recaptcha'.DS.'recaptchalib',
							'lib_mathcaptcha'	=> 'lib'.DS.'mathcaptcha'.DS.'MathCaptcha',
							'lib_html2text'		=> 'lib'.DS.'html2text'.DS.'html2text'
						);
					}
					
			}
			return $factory;
		}
	
		/**
		 * Gets/creates instances of classes
		 * @param string $class_name
		 * @return
		 */
		protected static function &getClassInstance($className) {
			$self = self::getInstance();
			if(!isset($self->objList[$className]))
			{
				foreach($self->classMapping as $classNameKey => $path)
				{
					if($classNameKey === $className){
						require_once($path . '.php');
						break;
					}
				}
				$className = $self->prefix.$className;
				$self->objList[$className] = new $className;
			}
			return $self->objList[$className];
		}
		
		/**
		 * Gets/includes scripts
		 * @param string script id
		 * @return
		 */
		protected static function getScript($scriptId) {
			$self = self::getInstance();			
			foreach($self->scriptMapping as $scriptKey => $path)
			{
				if($scriptKey === $scriptId){
					require_once($path . '.php');
					return true;
				}
			}
			return false;
		}
	
		/**
		 * Removes class instances
		 * @param string $class_name
		 */
		protected static function &unsetClassInstance($className) {
			$self = self::getInstance();
			if(isset($self->objList[$className]))
			{
				$self->objList[$className] = null;
				unset($self->objList[$className]);
			}
		}
		
		/* ******************* PUBLIC METHODS ******************* */
		public static function &getApplication(){
			return self::getClassInstance('Application');
		}
		
		public static function &getAuthorization(){
			return self::getClassInstance('Authorization');
		}
		
		public static function &getConfig(){
			return self::getClassInstance('Configuration');
		}
		
		public static function &getConfIO(){
			return self::getClassInstance('ConfIO');
		}	
		
		public static function &getCache(){			
			$cache = & JFactory::getCache();	
			$cache->setCaching( 1 );
			return $cache;	
		}
		
		public static function &getCacheUtils(){
			return self::getClassInstance('CacheUtils');
		}
		
		public static function &getLogger(){
			return self::getClassInstance('Log');
		}
		
		public static function &getMailUtils(){
			return self::getClassInstance('MailUtils');
		}
		
		public static function &getMailRetriever(){
			return self::getClassInstance('MailRetriever');
		}
		
		public static function &getMailSender(){
			return self::getClassInstance('MailSender');
		}
		
		public static function &getRecipients(){
			return self::getClassInstance('Recipients');
		}
		
		public static function &getSubscriberPlugin(){
			return self::getClassInstance('SubscriberPlugin');
		}
		
		public static function &getSubscribeUtils(){
			return self::getClassInstance('SubscribeUtils');
		}
		
		public static function &getThreadUtils(){
			return self::getClassInstance('ThreadUtils');
		}
		
		public static function &getUtils(){
			return self::getClassInstance('Utils');
		}
		
		public static function &getPluginUtils(){
			return self::getClassInstance('PluginUtils');
		}
		
		public static function &getAttachmentsUtils(){
			return self::getClassInstance('AttachmentsUtils');
		}
		
		public static function &getMailQueue(){
			return self::getClassInstance('MailQueue');
		}
		
		public static function &getMailingListMailbox(){
			return self::getClassInstance('MailingListMailbox');
		}
		public static function &getCBUtils(){
			return self::getClassInstance('CBUtils');
		}
		
		public static function &getConverterUtils(){
			return self::getClassInstance('ConverterUtils');
		}
		
		public static function &getDateUtils(){
			return self::getClassInstance('DateUtils');
		}
		
		public static function &getDBUtils(){
			return self::getClassInstance('DBUtils');
		}
		
		public static function &getEnvironment(){
			return self::getClassInstance('Environment');
		}
		
		public static function &getEvents(){
			return self::getClassInstance('Events');
		}
		
		public static function &getHashUtils(){
			return self::getClassInstance('HashUtils');
		}
		
		public static function &getMailingListUtils(){
			return self::getClassInstance('MailingListUtils');
		}
		
		public static function &getMailingList(){
			return self::getClassInstance('MailingList');
		}
		
		public static function &getNotify(){
			return self::getClassInstance('Notify');
		}
		
		public static function &getNotifyUtils(){
			return self::getClassInstance('NotifyUtils');
		}
		
		public static function getParameter($jParameter){
			$tmp = self::getClassInstance('Parameter');			
			return $tmp->getParameterFromJParameter($jParameter);
		}
	
		public static function loadLibrary($libName){
			return self::getScript('lib_' . strtolower($libName));
		}
		
		public static function &getModel($modelName){
			JLoader::import('joomla.application.component.model'); 
			$model  = JModel::getInstance( $modelName, 'MailsterModel' );
			if(is_null($model) || !$model){
				JLoader::import( $modelName, JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_mailster' . DS . 'models' );
				$model = JModel::getInstance( $modelName, 'MailsterModel' );
			}
			return $model;
		}
		
	}
	
	?>
