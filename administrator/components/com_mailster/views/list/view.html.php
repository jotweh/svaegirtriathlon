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

	jimport( 'joomla.application.component.view');

	/**
	 * HTML View class for the List View
	 *
	 * @package Joomla
	 * @subpackage Mailster
	 */
	class MailsterViewList extends JView
	{
		function display($tpl = null)
		{	
			//initialise variables
			$user 		= & JFactory::getUser();
			$document	= & JFactory::getDocument();
			$db  		= & JFactory::getDBO();
			$cid 		= JRequest::getVar( 'cid');
			$task 		= JRequest::getVar('task');
			
			// Get data from the model
			$row = &$this->get('Data'); 
			if ( $cid ) {
				JToolBarHelper::title( JText::_( 'COM_MAILSTER_EDIT_MAILING_LIST' ) . ' (' . $row->name . ')', 'list-mailster' );
			} else {
				JToolBarHelper::title( JText::_( 'COM_MAILSTER_ADD_MAILING_LIST' ), 'addList-mailster' );
			}
			
			$jUsers =  &$this->get('AllJoomlaUsers'); 
			
			$groupsModel = &$this->getModel('groups');
			$groups = $groupsModel->getData();
			
			//Create Submenu	
			JToolBarHelper::save();
			JToolBarHelper::apply();
			JToolBarHelper::cancel();

			$Lists = array();
			$listOptions = array();
			
			$smtpsecure = array (
			JHTML::_('select.option', '', JText::_( 'COM_MAILSTER_JNONE' )),
			JHTML::_('select.option', 'ssl', 'SSL'),
			JHTML::_('select.option', 'tls', 'TLS'));
			$Lists['smtpsecure_in'] = JHTML::_('select.genericlist',
									  $smtpsecure, 'mail_in_use_secure',
									   'class="inputbox hTip" title="'.JText::_( 'COM_MAILSTER_MAILING_LIST_USE_SECURE_MEASURES' ).'" size="1" style="width:80px;"',
									   'value', 'text', 
									  (isset($row->mail_in_use_secure) ? $row->mail_in_use_secure : ''));
									  
			$Lists['smtpsecure_out'] = JHTML::_('select.genericlist',
									  $smtpsecure, 
									  'mail_out_use_secure', 
									  'class="inputbox hTip" title="'.JText::_( 'COM_MAILSTER_MAILING_LIST_USE_SECURE_MEASURES' ).'" size="1" style="width:80px;"', 
									  'value', 'text', 
									  (isset($row->mail_out_use_secure) ? $row->mail_out_use_secure : ''));

			$protocol = array(
							//	JHTML::_('select.option', '', JText::_( 'COM_MAILSTER_NO_SPECIFIC' )),
								JHTML::_('select.option', 'pop3', 'POP3'),
								JHTML::_('select.option', 'imap', 'IMAP'),
								JHTML::_('select.option', 'nntp', 'NNTP')
							 );
			$Lists['protocol_in'] = JHTML::_('select.genericlist',  $protocol,
											 'mail_in_protocol', 
											 'class="inputbox hTip" title="'.JText::_( 'COM_MAILSTER_MAILING_LIST_PROTOCOL_SUPPORTED_BY_MAIL_SERVER' ).'" size="1" style="width:80px;"',
											 'value', 'text', 
											(isset($row->mail_in_protocol) ? $row->mail_in_protocol : ''));
			
			$mailFrom = array(
								JHTML::_('select.option', MstConsts::MAIL_FROM_MODE_GLOBAL, JText::_( 'COM_MAILSTER_USE_GLOBAL_SETTING_FROM_CONFIGURATION' )),
								JHTML::_('select.option', MstConsts::MAIL_FROM_MODE_SENDER_EMAIL, JText::_( 'COM_MAILSTER_SENDER_ADDRESS' )),
								JHTML::_('select.option', MstConsts::MAIL_FROM_MODE_MAILING_LIST, JText::_( 'COM_MAILSTER_MAILING_LIST_ADDRESS' ))
							  );
			$Lists['mail_from_mode'] = JHTML::_('select.genericlist',  $mailFrom,
											 'mail_from_mode', 
											 'class="hTipIB.mail_from_hTip" title="'.JText::_( 'COM_MAILSTER_THE_EMAILS_FORWARDED_USE_THIS_FOR_THE_FROM_FIELD' ).'" size="1" style="width:180px;"',
											 'value', 'text', 
											(isset($row->mail_from_mode) ? $row->mail_from_mode : 0));
											
			$nameFrom = array(
								JHTML::_('select.option', MstConsts::NAME_FROM_MODE_GLOBAL, JText::_( 'COM_MAILSTER_USE_GLOBAL_SETTING_FROM_CONFIGURATION' )),
								JHTML::_('select.option', MstConsts::NAME_FROM_MODE_SENDER_NAME, JText::_( 'COM_MAILSTER_SENDER_NAME' )),
								JHTML::_('select.option', MstConsts::NAME_FROM_MODE_MAILING_LIST_NAME, JText::_( 'COM_MAILSTER_MAILING_LIST_NAME' ))
							  );
			$Lists['name_from_mode'] = JHTML::_('select.genericlist',  $nameFrom,
											 'name_from_mode', 
											 'class="hTipIB.name_from_hTip" title="'.JText::_( 'COM_MAILSTER_THE_EMAILS_FORWARDED_USE_THIS_FOR_THE_FROM_FIELD_AS_NAME' ).'" size="1" style="width:180px;"',
											 'value', 'text', 
											(isset($row->name_from_mode) ? $row->name_from_mode : 0));
			
			$convOptions = array (
			JHTML::_('select.option', MstConsts::MAIL_FORMAT_CONVERT_NONE, JText::_( 'COM_MAILSTER_NO_CONVERSION' )),
			JHTML::_('select.option', MstConsts::MAIL_FORMAT_CONVERT_HTML, JText::_( 'COM_MAILSTER_HTML_MAIL' )),
			JHTML::_('select.option', MstConsts::MAIL_FORMAT_CONVERT_PLAIN, JText::_( 'COM_MAILSTER_PLAINTEXT_MAIL' )));
			
			$Lists['mail_format_conv'] = JHTML::_('select.genericlist', 
													 $convOptions, 'mail_format_conv', 
													 'class="inputbox hTip" title="'.JText::_( 'COM_MAILSTER_CONVERT_MAIL_FORMAT_TO_A_FIXED_FORMAT_OR_LEAVE_IT_UNTOUCHED' ).'" size="1" style="width:120px;"', 
													 'value', 'text', 
													 (isset($row->mail_format_conv) ? $row->mail_format_conv : MstConsts::MAIL_FORMAT_CONVERT_HTML));
																			 
			$notifyUtils 	= &MstFactory::getNotifyUtils(); 		
			$targetTypes 	= $notifyUtils->getAvailableTargetTypes();
			$triggerTypes 	= $notifyUtils->getAvailableTriggerTypes();	
			$notifies		= $notifyUtils->getNotifiesOfMailingList($cid[0]);
					
			$listOptions['trigger_types'] = JHTML::_('select.options', $triggerTypes, 'type', 'name');
			$listOptions['target_types'] = JHTML::_('select.options', $targetTypes, 'type', 'name');
			$listOptions['joomla_users'] = JHTML::_('select.options', $jUsers, 'id', 'name');
			$listOptions['user_groups'] = JHTML::_('select.options', $groups, 'id', 'name');
			
			for($i=0; $i<count($notifies); $i++){
				$notify = &$notifies[$i];
				$notify->triggerTypes = JHTML::_('select.options', $triggerTypes, 'type', 'name', $notify->trigger_type);
				$notify->targetTypes = JHTML::_('select.options', $targetTypes, 'type', 'name', $notify->target_type);
				if($notify->target_type == MstNotify::TARGET_TYPE_JOOMLA_USER){
					$notify->targetChoice =  JHTML::_('select.options', $jUsers, 'id', 'name', $notify->user_id);
				}elseif($notify->target_type == MstNotify::TARGET_TYPE_USER_GROUP){
					$notify->targetChoice =  JHTML::_('select.options', $groups, 'id', 'name', $notify->group_id);
				}
				$notifies[$i] = $notify;
			}
							
			$this->assignRef('row'      	, $row);
			$this->assignRef('user'			, $user);
			$this->assignRef('groups'		, $groups);
			$this->assignRef('notifies'		, $notifies);
			$this->assignRef('Lists'		, $Lists);
			$this->assignRef('listOptions'	, $listOptions);
			$this->assignRef('convOptions'	, $convOptions);
	 
			parent::display($tpl);
		}

	}
?>
