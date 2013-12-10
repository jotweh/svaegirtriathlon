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

defined('_JEXEC') or die('Restricted access');

class MstMailRetriever
{

	function retrieveMailsOfActiveMailingLists($minDuration, $execEnd){
		$log 			= & MstFactory::getLogger();
		$listUtils		= & MstFactory::getMailingListUtils();
		$mailingLists 	= $listUtils->getActiveMailingLists();					
		$nrLists = count($mailingLists);	
		$log->debug('There are ' . $nrLists . ' active mailing lists', MstConsts::LOGENTRY_MAIL_RETRIEVE);		
		for($i = 0; $i < $nrLists; $i++) {		
			$timeLeft = $execEnd - time();
			if($timeLeft > $minDuration){	
				$this->retrieveMailsOfMailingList($mailingLists[$i], $minDuration, $execEnd); // store mails in DB
				$log->debug('Time left to run: ' . $timeLeft . ' sec after retrieving from ' . ($i+1) . '/' . $nrLists . ' lists');
			}else{
				$log->debug('Timeout while retrieving mails of active mailing lists, time left to run: ' . $timeLeft . ' sec');
				break;
			}
		}	
	}
	
	function retrieveMailsOfMailingList($mList, $minDuration, $execEnd){
		$log 		= & MstFactory::getLogger();
		$listUtils	= & MstFactory::getMailingListUtils();
		if($listUtils->lockMailingList($mList->id)){
			$log->debug('Successfully locked list ' . $mList->name . ' (id: ' . $mList->id . ')', MstConsts::LOGENTRY_MAIL_RETRIEVE);
			$mailbox 	= & MstFactory::getMailingListMailbox();
			if($mailbox->open($mList)){
				$mailbox->retrieveAllMessages($minDuration, $execEnd);
				$mailbox->close();
			}else{
				$log->error('Mailbox Connection NOT ok:  ' . $mList->name . ' (id: ' . $mList->id . ') Errors: ' . $mailbox->getErrors(), MstConsts::LOGENTRY_MAIL_RETRIEVE);
			}
			
			if($listUtils->unlockMailingList($mList->id)){				
				$log->debug('Successfully unlocked list ' . $mList->name . ' (id: ' . $mList->id . ')', MstConsts::LOGENTRY_MAIL_RETRIEVE);
			}else{		
				$listUtils->isListLocked($mList->id) ? 		$log->error('Could not unlock list ' . $mList->name . ' (id: ' . $mList->id . ')') 
													  	: 	$log->debug('List was already unlocked');
			}
		}else{
			$log->debug('List ' . $mList->name . ' (id: ' . $mList->id . ') could not be locked!', MstConsts::LOGENTRY_MAIL_RETRIEVE);
		}
	}
	
}
?>
