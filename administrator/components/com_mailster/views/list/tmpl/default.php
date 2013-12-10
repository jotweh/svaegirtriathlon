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
	
	$mstConfig 	= & MstFactory::getConfig();
	$mstUtils = & MstFactory::getUtils();
	$mstUtils->loadJavascript();
	$mstUtils->addTabs();
	$mstUtils->addTable();
	$mstUtils->addTips();
	$mstUtils->addToggler();
	$jEditor = &JFactory::getEditor();
	?>
	<script language="javascript" type="text/javascript">
		var $j = jQuery.noConflict();
		var rowCounter = <?php echo count($this->notifies);	?>;
		$j(document).ready(function(){
			$j('#inboxConnectionCheck, #inboxConnectionCheckImg').click(function(){
				var in_user = $j('#mail_in_user').val();
				var in_pw = $j('#mail_in_pw').val();
				var in_host = $j('#mail_in_host').val();
				var in_port = $j('#mail_in_port').val();
				var in_secure = $j('#mail_in_use_secure').val();
				var in_sec_auth = $j('input:radio[name=mail_in_use_sec_auth]:checked').val();
				var in_protocol = $j('#mail_in_protocol').val();
				var in_params = $j('#mail_in_params').val();
				var data2send = '{ "task": "inboxConnCheck", "in_user": "' + in_user + '", "in_pw": "'+ in_pw + '", "in_host": "'+ in_host + '", "in_port": "'+ in_port + '", "in_secure": "'+ in_secure+ '", "in_sec_auth": "' + in_sec_auth + '", "in_protocol": "' + in_protocol + '", "in_params": "' + in_params + '" }';
				
				$j('#progressIndicator1').removeClass('mtrActivityIndicator').addClass('mtrActivityIndicator');
				
			    var url = 'index.php?option=com_mailster&controller=conncheck&task=chk';
			    $j.post(url, { mtrAjaxData: data2send },
				    function(resultData){ 
			         	 $j('#progressIndicator1').removeClass('mtrActivityIndicator');
						 $j('#inboxConnectionCheck').show();
						 $j('#inboxConnectionCheckImg').show();
				         if(resultData){				
					         var resultObject = eval(resultData)[0];
					         alert(resultObject.checkresult);
				         }else{ 
				        	 alert(<?php echo $mstUtils->jsonEncode(JText::_( 'COM_MAILSTER_COULD_NOT_COMPLETE_AJAX_REQUEST' )); ?>);				           
				         }
					}
				); 
			    $j('#inboxConnectionCheck').hide();
			    $j('#inboxConnectionCheckImg').hide(); 			      
			}); 
			
			$j('#outboxConnectionCheck, #outboxConnectionCheckImg').click(function(){

				var list_name = $j('#name').val();
				var admin_email = $j('#admin_mail').val();
				var use_j_mailer = $j('input:radio[name=use_joomla_mailer]:checked').val();
				var out_email = $j('#list_mail').val();
				var out_user = $j('#mail_out_user').val();
				var out_pw = $j('#mail_out_pw').val();
				var out_host = $j('#mail_out_host').val();
				var out_port = $j('#mail_out_port').val();
				var out_secure = $j('#mail_out_use_secure').val();
				var out_sec_auth = $j('input:radio[name=mail_out_use_sec_auth]:checked').val();
				var data2send = '{ "task": "outboxConnCheck", "out_user": "' + out_user + '", "out_pw": "'+ out_pw + '", "out_email": "'+ out_email + '", "out_host": "'+ out_host + '", "out_port": "'+ out_port + '", "out_secure": "'+ out_secure+ '", "out_sec_auth": "' + out_sec_auth + '", "list_name": "'+ list_name + '", "admin_email": "'+ admin_email+ '", "use_j_mailer": "' + use_j_mailer+ '" }';
				
				$j('#progressIndicator2').removeClass('mtrActivityIndicator').addClass('mtrActivityIndicator');
				
				var url = 'index.php?option=com_mailster&controller=conncheck&task=chk';
			    				 
			   $j.post(url, { mtrAjaxData: data2send },
				    function(resultData){    
		       			$j('#progressIndicator2').removeClass('mtrActivityIndicator');	
						$j('#outboxConnectionCheck').show();
						$j('#outboxConnectionCheckImg').show();
				        if(resultData){				
					    	var resultObject = eval(resultData)[0];
					    	alert(resultObject.checkresult);
				        }else{ 
				        	 alert(<?php echo $mstUtils->jsonEncode(JText::_( 'COM_MAILSTER_COULD_NOT_COMPLETE_AJAX_REQUEST' )); ?>);				           
				        }
					}
				); 
				$j('#outboxConnectionCheck').hide();
				$j('#outboxConnectionCheckImg').hide();   
			}); 
						
			prepareTabs();
			prepareTips();
			
			$j('#use_joomla_mailer0').click(function () {
				if(this.checked == true){
					toggleMailer(false);
				}
			}); 
			$j('#use_joomla_mailer1').click(function () {
				if(this.checked == true){
					toggleMailer(true);
				}
			}); 
			$j('#use_to_alibi_true').click(function () {
				if(this.checked == true){
					toggleAlibiTo(true);
				}
			}); 	
			$j('#use_to_alibi_false').click(function() {
				if(this.checked == true){
					toggleAlibiTo(false);
				}
			}); 
			$j('#allow_registration1').change(function() {
				toggleRegistration(this.checked);
			}); 	
			$j('#allow_registration0').change(function() {
				toggleRegistration(!this.checked);
			}); 	
			$j('#sending_public1').change(function() {
				toggleSending(this.checked);
			}); 			
			$j('#sending_public0').change(function() {
				toggleSending(!this.checked);
			}); 			
			$j("#sending_group").change(function() {
				toggleSendingGroup(this.checked);
			}); 			
			$j("input[name=replyRecipient]").change(function () {
				recipientVal = this.value;
				$j('#reply_to_sender').attr('value', recipientVal);
			}); 			
			$j("input[name=bounceModeSettings]").change(function () {
				recipientVal = this.value;
				$j('#bounce_mode').attr('value', recipientVal);
				bounceModeSettingsChanged();
			}); 			
			$j("input[name=addressing_mode]").change(function () {
				addressingModeChanged();
			}); 			
			$j("#mail_format_conv").change(function () {
				var disabled = '';
				if($j("#mail_format_conv").val() == 2){
					disabled = 'disabled'; // convert to text email -> don't need HTML altbody setting...
				}
				$j("#mail_format_altbody0").attr('disabled', disabled);
				$j("#mail_format_altbody1").attr('disabled', disabled);
			}); 

			addToggler(false, '#headerToggleKnob', '.headerOption', <?php echo $mstUtils->jsonEncode(JText::_( 'COM_MAILSTER_EDIT_CUSTOM_HEADER' )); ?>, <?php echo $mstUtils->jsonEncode(JText::_( 'COM_MAILSTER_HIDE_CUSTOM_HEADER' )); ?>, 400);
			addToggler(false, '#footerToggleKnob', '.footerOption', <?php echo $mstUtils->jsonEncode(JText::_( 'COM_MAILSTER_EDIT_CUSTOM_FOOTER' )); ?>, <?php echo $mstUtils->jsonEncode(JText::_( 'COM_MAILSTER_HIDE_CUSTOM_FOOTER' )); ?>, 400);
			
			$j(".notifierRemoverClass").click(function(event){
				removeTableRow(event.target.id);
			}); 
			
			$j('#removeFirstMailLink').click(function(){
				var url = 'index.php?option=com_mailster&controller=lists&task=removeFirstMailFromMailbox&<?php echo MstConsts::PLUGIN_FLAG_NO_EXECUTION; ?>=true';	
				var data2send = '{ "task": "removeFirstMailFromMailbox", "listId": "<?php echo $this->row->id; ?>"}';
				$j('#removeFirstMailLinkProgressIndicator').removeClass('mtrActivityIndicator').addClass('mtrActivityIndicator');	
			    $j.post(url, { mtrAjaxData: data2send },
				    function(resultData){
				         if(resultData){
					        var resultObject = eval(resultData)[0];
							if(resultObject.res == 'true'){
				       			$j('#removeFirstMailLinkProgressIndicator').removeClass('mtrActivityIndicator');	
								alert(<?php echo $mstUtils->jsonEncode(JText::_( 'COM_MAILSTER_DELETED_FIRST_MAIL_IN_MAILBOX' )); ?>);	
							}
				         }else{
				        	 alert(<?php echo $mstUtils->jsonEncode(JText::_( 'COM_MAILSTER_COULD_NOT_COMPLETE_AJAX_REQUEST' )); ?>);	           
				         }
					}
				);
			});
			
			$j('#removeAllMailsLink').click(function(){
				var url = 'index.php?option=com_mailster&controller=lists&task=removeAllMailsFromMailbox&<?php echo MstConsts::PLUGIN_FLAG_NO_EXECUTION; ?>=true';	
				var data2send = '{ "task": "removeAllMailsFromMailbox", "listId": "<?php echo $this->row->id; ?>"}';
				$j('#removeAllMailsLinkProgressIndicator').removeClass('mtrActivityIndicator').addClass('mtrActivityIndicator');	
			    $j.post(url, { mtrAjaxData: data2send },
				    function(resultData){
				         if(resultData){
					        var resultObject = eval(resultData)[0];
							if(resultObject.res == 'true'){
				       			$j('#removeAllMailsLinkProgressIndicator').removeClass('mtrActivityIndicator');	
								alert(<?php echo $mstUtils->jsonEncode(JText::_( 'COM_MAILSTER_DELETED_ALL_MAILS_IN_MAILBOX' )); ?>);	
							}
				         }else{
				        	 alert(<?php echo $mstUtils->jsonEncode(JText::_( 'COM_MAILSTER_COULD_NOT_COMPLETE_AJAX_REQUEST' )); ?>);	           
				         }
					}
				);
			});
			
			$j('#removeAllMailsInSendQueue').click(function(){
				var url = 'index.php?option=com_mailster&controller=lists&task=removeAllMailsInSendQueue&<?php echo MstConsts::PLUGIN_FLAG_NO_EXECUTION; ?>=true';	
				var data2send = '{ "task": "removeAllMailsInSendQueue", "listId": "<?php echo $this->row->id; ?>"}';
				$j('#removeAllMailsInSendQueueProgressIndicator').removeClass('mtrActivityIndicator').addClass('mtrActivityIndicator');	
			    $j.post(url, { mtrAjaxData: data2send },
				    function(resultData){
				         if(resultData){
					        var resultObject = eval(resultData)[0];
							if(resultObject.res == 'true'){
				       			$j('#removeAllMailsInSendQueueProgressIndicator').removeClass('mtrActivityIndicator');	
								alert(<?php echo $mstUtils->jsonEncode(JText::_( 'COM_MAILSTER_REMOVED_ALL_MAILS_IN_THE_SEND_QUEUE' )); ?>);	
							}
				         }else{
				        	 alert(<?php echo $mstUtils->jsonEncode(JText::_( 'COM_MAILSTER_COULD_NOT_COMPLETE_AJAX_REQUEST' )); ?>);		           
				         }
					}
				);
			});
			
			$j('#addNotifyButton').click(function () {
				rowCounter++;
				var cols = new Array();
				var buttonId = 'removeNotifyButton' + rowCounter;
				var targetType = 'targetType' + rowCounter;
				<?php
					$targetTypesCleaned = $this->listOptions['target_types'];
					$targetTypesCleaned = (string)str_replace(array("\r", "\r\n", "\n"), '', $targetTypesCleaned);
					$targetTypesCleaned = (string)str_replace(array("'"), "\'", $targetTypesCleaned);
					
					$triggerTypesCleaned = $this->listOptions['trigger_types'];
					$triggerTypesCleaned = (string)str_replace(array("\r", "\r\n", "\n"), '', $triggerTypesCleaned);
					$triggerTypesCleaned = (string)str_replace(array("'"), "\'", $triggerTypesCleaned);
				?>
				cols[0] = rowCounter + '*';
				cols[1] = '<select id="triggerType' + rowCounter +'" name="triggerType';
				cols[1] = cols[1] + rowCounter +'" class="triggerTypeClass" style="width:130px;">';
				cols[1] = cols[1] + '<?php echo  $triggerTypesCleaned; ?>' + '</select>';
				cols[2] = '<select id="targetType' + rowCounter +'" name="targetType';
				cols[2] = cols[2] + rowCounter +'" class="targetTypeClass" style="width:130px;">';
				cols[2] = cols[2] + '<?php echo $targetTypesCleaned; ?>' + '</select>';
				cols[3] = '<select id="targetId' + rowCounter +'"name="targetId';
				cols[3] = cols[3] + rowCounter +'" class="targetIdClass" style="width:130px;"></select>';
				cols[4] = '<a id="' + buttonId +'" href="#" class="notifierRemoverClass">';
				cols[4] = cols[4] + '<img src="components/com_mailster/assets/images/16-remove.png" style="vertical-align:middle;" />';
				cols[4] = cols[4] + '<?php echo JText::_( 'COM_MAILSTER_UNDO_ADD' ); ?>';
				cols[4] = cols[4] + '</a>';
				cols[4] = cols[4] + '<input type="hidden" id="notifyId' + rowCounter +'" name="notifyId' + rowCounter +'" value="0" />';
				var rows = new Array();
				rows[0] = cols;
				addRow('notifiesTbl', rows, rowCounter);
				$j('#'+buttonId).click(function(event) {
					removeTableRow(event.target.id);
				}); 
				$j('#'+targetType).change(function(event) {
					var selectNamePattern = 'targetType';
					var rowNr = (this.name).substr(selectNamePattern.length);
					targetTypeChanged(rowNr);
				}); 
				$j('#'+targetType).change();
			});

			$j('.targetTypeClass').change(function (event) {
				var selectNamePattern = 'targetType';
				var rowNr = (this.name).substr(selectNamePattern.length);
				targetTypeChanged(rowNr);
			}); 	
			
			<?php 
				if($this->row->use_joomla_mailer === '1'){
					$enabled = 'disabled';
				}else{
					$enabled = '';
				}
				
				if($this->row->reply_to_sender > 0){
					if($this->row->reply_to_sender == 1){
						$cmdOutput =  '$' . 'j(\'#replyToSender\').attr(\'checked\',  \'checked\');';	
					}elseif($this->row->reply_to_sender == 2){
						$cmdOutput =  '$' . 'j(\'#replyToSenderAndList\').attr(\'checked\',  \'checked\');';	
					}
				}else{
					$cmdOutput =  '$' . 'j(\'#replyToList\').attr(\'checked\',  \'checked\');';
				}
				echo $cmdOutput;				
				
				if($this->row->bounce_mode > 0){
					if($this->row->bounce_mode == 1){
						$cmdOutput =  '$' . 'j(\'#useBounceAddress\').attr(\'checked\',  \'checked\');';	
					}
				}else{
					$cmdOutput =  '$' . 'j(\'#useNoBounceAddress\').attr(\'checked\',  \'checked\');';
				}
				echo $cmdOutput;
				
				if($this->row->addressing_mode == MstConsts::ADDRESSING_MODE_TO){
					$cmdOutput =  '$' . 'j(\'#use_to\').attr(\'checked\',  \'checked\');';	
				}elseif($this->row->addressing_mode == MstConsts::ADDRESSING_MODE_BCC){
					$cmdOutput =  '$' . 'j(\'#use_bcc\').attr(\'checked\',  \'checked\');';	
				}elseif($this->row->addressing_mode == MstConsts::ADDRESSING_MODE_CC){
					$cmdOutput =  '$' . 'j(\'#use_cc\').attr(\'checked\',  \'checked\');';	
				}
				echo $cmdOutput;
				
				if($this->row->sending_public > 0){
					echo 'toggleSending(true);';
					echo 'toggleSendingGroup(false);';
				}else{
					echo 'toggleSending(false);';
					if($this->row->sending_group > 0){
						echo 'toggleSendingGroup(true);';
					}else{
						echo 'toggleSendingGroup(false);';
					}
				}
				
				if($this->row->allow_registration > 0){
					echo 'toggleRegistration(true);';
				}else{
					echo 'toggleRegistration(false);';
				}
				
				
				if($this->row->id <= 0){ // init for new mailing lists
					echo 'toggleMailer(true);';
					echo 'toggleSending(true);';
					echo 'toggleSendingGroup(false);';
					$enabled = 'disabled';
				}
				
			?>
			$j('#mail_out_use_secure').attr('disabled',  '<?php echo $enabled; ?>');
			$j('#mail_out_use_sec_auth0').attr('disabled', '<?php echo $enabled; ?>');
			$j('#mail_out_use_sec_auth1').attr('disabled', '<?php echo $enabled; ?>');
			addressingModeChanged();
			bounceModeSettingsChanged();
			$j("#mail_format_conv").change();
			<?php 				
			$mstApp = & MstFactory::getApplication();	
			$pHashOk = $mstApp->isProductHashCorrect('com_mailster', 'f7647518248eb8ef2b0a1e41b8a59f34');
			$isFree = $mstApp->isFreeEdition('com_mailster', 'free', 'f7647518248eb8ef2b0a1e41b8a59f34');
			$plgHashOk = $mstApp->checkPluginProductHashes();
			if($pHashOk && $plgHashOk && !$isFree){
				// Pro
				echo ' $' . 'j(\'#mail_format_altbody0\').attr(\'disabled\', false); ' . '$' . 'j(\'#mail_format_altbody1\').attr(\'disabled\', false);';
			}else{ // Free
				echo ' $' . 'j(\'#mail_format_altbody0\').attr(\'disabled\',  \'disabled\'); ' . '$' . 'j(\'#mail_format_altbody1\').attr(\'disabled\', \'disabled\');';	
			}
			?>
		});
		function removeTableRow(sourceId){
			var buttonNamePattern = 'removeNotifyButton';
			var rowNr = (sourceId).substr(buttonNamePattern.length);
			removeTableRowWithRowNr(rowNr);
		}

		function removeTableRowWithRowNr(rowNr){
			var notifyId = $j('#notifyId' + rowNr).val();
			if(notifyId == 0){
				// not in DB, can be deleted from DOM right away
				$j('#notifiesTbl_row' + rowNr).remove();
			}else{
				// has to be deleted from DB
				var url = 'index.php?option=com_mailster&controller=lists&task=removeNotify';	
				var data2send = '{ "task": "removeNotify", "notifyId": "' + notifyId + '", "rowNr": "' + rowNr + '"}';
				$j(('#removeNotifyButtonProgressIndicator'+rowNr)).removeClass('mtrActivityIndicator').addClass('mtrActivityIndicator');	
			    	
			    $j.post(url, { mtrAjaxData: data2send },
				    function(resultData){ 				         	 
				         if(resultData){				
					         var resultObject = eval(resultData)[0];
								if(resultObject.res == 'true'){
									$j(('#removeNotifyButtonProgressIndicator'+rowNr)).removeClass('mtrActivityIndicator');
									alert(<?php echo $mstUtils->jsonEncode(JText::_( 'COM_MAILSTER_DELETED_NOTIFICATION' )); ?>);	
									var rowNr = resultObject.rowNr;
									$j('#notifyId' + rowNr).val('0');
									removeTableRowWithRowNr(rowNr);
								}
				         }else{
				        	 alert(<?php echo $mstUtils->jsonEncode(JText::_( 'COM_MAILSTER_COULD_NOT_COMPLETE_AJAX_REQUEST' )); ?>);        
				         }
					}
				); 
			}
		}

		function targetTypeChanged(rowNr){
			var targetType = $j('#targetType'+rowNr).val();
			if(targetType == 0){ // List administrator
				$j('#targetId'+rowNr).children().remove();
				$j('#targetId'+rowNr).hide();
			}else if(targetType == 1){ // JOOMLA USER
				$j('#targetId'+rowNr).children().remove();
				$j('#copyStationUsers').children().clone().appendTo('#targetId'+rowNr);
				$j('#targetId'+rowNr).show();
			}else if(targetType == 2){ // User group
				$j('#targetId'+rowNr).children().remove();
				$j('#copyStationGroups').children().clone().appendTo('#targetId'+rowNr);
				$j('#targetId'+rowNr).show();
			} 
		}

		function toggleMailer(useJMailer){
			var disabled = '';
			if(useJMailer == true){
				disabled = 'disabled';
			}
			$j('#mail_out_host').attr('disabled', disabled);
			$j('#mail_out_user').attr('disabled', disabled);
			$j('#mail_out_pw').attr('disabled', disabled);
			$j('#mail_out_port').attr('disabled', disabled);
			$j('#mail_out_use_secure').attr('disabled', disabled);
			$j('#mail_out_use_sec_auth0').attr('disabled', disabled);
			$j('#mail_out_use_sec_auth1').attr('disabled', disabled);
			
		}
		function toggleSending(sendingPublic){
			var disabled = '';
			if(sendingPublic == true){
				disabled = 'disabled';
			}
			
			$j('#sending_admin').attr('disabled', disabled);
			$j('#sending_recipients').attr('disabled', disabled);
			$j('#sending_group').attr('disabled', disabled);
			
			var sendingGroup = (($j('#sending_group').val()==1) && !(sendingPublic));
			toggleSendingGroup(sendingGroup);		
		}
		function toggleRegistration(registrationActive){
			var disabled = '';
			if(registrationActive == false){
				disabled = 'disabled';
			}
			
			$j('#public_registration0').attr('disabled', disabled);
			$j('#public_registration1').attr('disabled', disabled);
		}
		function toggleSendingGroup(sendingGroup){
			var disabled = '';
			if(sendingGroup == false){
				disabled = 'disabled';
			}
			$j('#sending_group_id').attr('disabled', disabled);	
		}
		function addressingModeChanged(){
			var disabled = '';
			var useBCC	= ($j('#use_bcc:checked').val()	== 1);
			var useCC 	= ($j('#use_cc:checked').val() 	== 1);
			var useTo 	= ($j('#use_to:checked').val()	== 1);
			if(useBCC == false){
				disabled = 'disabled';
			}
			$j('#bcc_count').attr('disabled', disabled);
		}	
		function bounceModeSettingsChanged(){
			var disabled = '';
			var noBounce	= ($j('#useNoBounceAddress:checked').val()	== 1);
			var useBounce 	= ($j('#useBounceAddress:checked').val() 	== 1);
			if(useBounce == false){
				disabled = 'disabled';
			}
			$j('#bounce_mail').attr('disabled', disabled);
		}	
		
		function validateSubmittedForm(task){
			chkError = false;

			var form = document.adminForm;			
			if (task == 'cancel'){ // check we aren't cancelling
				submitform( task ); // no need to validate, we are cancelling		
				return;
			}	
			if($j.trim(form.name.value) == ""){
				alert( <?php echo $mstUtils->jsonEncode(JText::_( 'COM_MAILSTER_PROVIDE_MAILING_LIST_NAME')); ?> );
				forceTab('first');
				form.name.focus();
				chkError = true;
			}
			if(($j.trim(form.list_mail.value) == "") && (chkError == false)){
				alert( <?php echo $mstUtils->jsonEncode(JText::_( 'COM_MAILSTER_PROVIDE_MAILING_LIST_E-MAIL_ADRESS')); ?> );
				forceTab('first');
				form.list_mail.focus();
				chkError = true;
			}
			if(($j.trim(form.admin_mail.value) == "") && (chkError == false)){
				alert( <?php echo $mstUtils->jsonEncode(JText::_( 'COM_MAILSTER_PROVIDE_ADMIN_E-MAIL_ADRESS')); ?> );
				forceTab('first');
				form.admin_mail.focus();
				chkError = true;
			}
			if(($j.trim(form.admin_mail.value) == $j.trim(form.list_mail.value)) && (chkError == false)){
				alert( <?php echo $mstUtils->jsonEncode(JText::_( 'COM_MAILSTER_PROVIDE_NOT_SAME_ADMIN_E-MAIL_ADRESS')); ?> );
				forceTab('first');
				form.admin_mail.focus();
				chkError = true;
			}
			if(($j.trim(form.mail_in_host.value) == "") && (chkError == false)){
				alert( <?php echo $mstUtils->jsonEncode(JText::_( 'COM_MAILSTER_PROVIDE_MAILING_LIST_HOST')); ?> );
				forceTab('second');
				form.mail_in_host.focus();
				chkError = true;
			}
			if(((IsNumeric(form.mail_in_port.value) == false) || ($j.trim(form.mail_in_port.value) == "") || (form.mail_in_port.value <= 0)) && (chkError == false)){
				alert( <?php echo $mstUtils->jsonEncode(JText::_( 'COM_MAILSTER_PROVIDE_MAILING_LIST_PORT')); ?> );
				forceTab('second');
				form.mail_in_port.focus();
				chkError = true;
			}
			if(($j('#use_joomla_mailer1').attr('checked') == false)  && (chkError == false)){
				if($j.trim(form.mail_out_host.value) == ""){
					alert( <?php echo $mstUtils->jsonEncode(JText::_( 'COM_MAILSTER_PROVIDE_MAILING_LIST_SENDER_HOST')); ?> );
					forceTab('third');
					form.mail_out_host.focus();
					chkError = true;
				}else if((IsNumeric(form.mail_out_port.value) == false) || ($j.trim(form.mail_out_port.value) == "") || (form.mail_out_port.value <= 0)){
					alert( <?php echo $mstUtils->jsonEncode(JText::_( 'COM_MAILSTER_PROVIDE_MAILING_LIST_SENDER_PORT')); ?> );
					forceTab('third');
					form.mail_out_port.focus();
					chkError = true;
				}
			}
			if($j('#use_bcc').attr('checked') == true){
				if( (IsNumeric($j('#bcc_count').val()) == false) || ($j('#bcc_count').val() < 1) ){
					alert( <?php echo $mstUtils->jsonEncode(JText::_( 'COM_MAILSTER_AT_LEAST_ONE_BCC_RECIPIENT_NEEDED')); ?> );
					forceTab('sixth');
					form.bcc_count.focus();
					chkError = true;
				}
			}
			if(((IsNumeric(form.max_send_attempts.value) == false) || ($j.trim(form.max_send_attempts.value) == "") || (form.max_send_attempts.value <= 0)) && (chkError == false)){
				alert( <?php echo $mstUtils->jsonEncode(JText::_( 'COM_MAILSTER_MAX_SEND_ATTEMPTS_HAS_TO_BE_AT_LEAST_ONE')); ?> );
				forceTab('sixth');
				form.max_send_attempts.focus();
				chkError = true;
			}

			if( (($j('#sending_public1').attr('checked') == false) && 
					($j('#sending_recipients').attr('checked') == false) &&
					($j('#sending_admin').attr('checked') == false) &&
					($j('#sending_group').attr('checked') == false))
				 && (chkError == false) ){
					alert( <?php echo $mstUtils->jsonEncode(JText::_( 'COM_MAILSTER_CHOOSE_SENDERS_THAT_ARE_ALLOWED_TO_SEND_TO_THE_LIST')); ?> );
					forceTab('fifth');
					form.sending_recipients.focus();
					chkError = true;
			}

			if(chkError == false){
				$j('#sending_recipients, #sending_admin, #sending_group').attr('checked', 'checked');
				submitform( task );
			}else{
				return false;
			}			
		}
		function submitbutton(task){
			validateSubmittedForm(task);
		}
		<?php
		if(version_compare(JVERSION,'1.6.0','ge')) {
			// Joomla! 1.6 / 1.7 / ...
			echo 'Joomla.submitbutton = function(pressbutton) { validateSubmittedForm(pressbutton); }';					
		}
		?>
	</script>

	<form action="index.php" method="post" name="adminForm" id="adminForm">
	<div id="tabContainer" class="tabs">
        <ul class="tabNavigation">
       		<li><a id="first" 	class="" 	href="#first" 	onclick=""><?php echo JText::_( 'COM_MAILSTER_GENERAL_SETTINGS' ); ?></a></li>
            <li><a id="second" 	class="" 	href="#second" 	onclick=""><?php echo JText::_( 'COM_MAILSTER_MAILBOX_SETTINGS' ); ?></a></li>
            <li><a id="third" 	class="" 	href="#third" 	onclick=""><?php echo JText::_( 'COM_MAILSTER_SENDER_SETTINGS' ); ?></a></li>
            <li><a id="fourth" 	class="" 	href="#fourth" 	onclick=""><?php echo JText::_( 'COM_MAILSTER_MAIL_CONTENT' ); ?></a></li>
            <li><a id="fifth" 	class="" 	href="#fifth" 	onclick=""><?php echo JText::_( 'COM_MAILSTER_LIST_BEHAVIOUR' ); ?></a></li>
            <li><a id="sixth" 	class="" 	href="#sixth" 	onclick=""><?php echo JText::_( 'COM_MAILSTER_SENDING_BEHAVIOUR' ); ?></a></li>
            <li><a id="seventh" class="" 	href="#seventh" onclick=""><?php echo JText::_( 'COM_MAILSTER_NOTIFICATIONS' ); ?></a></li>
            <li><a id="eigth" 	class="" 	href="#eigth"	onclick=""><?php echo JText::_( 'COM_MAILSTER_TOOLS' ); ?></a></li>
        </ul>
        <?php require_once('tab01.php'); ?>
        <?php require_once('tab02.php'); ?>
        <?php require_once('tab03.php'); ?>
        <?php require_once('tab04.php'); ?>
        <?php require_once('tab05.php'); ?>
        <?php require_once('tab06.php'); ?>
        <?php require_once('tab07.php'); ?>
        <?php require_once('tab08.php'); ?>
	</div>

	<?php echo JHTML::_( 'form.token' ); ?>
	<input type="hidden" name="option" value="com_mailster" />
	<input type="hidden" name="controller" value="lists" />
	<input type="hidden" name="view" value="list" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="tab" value="" id="tab" />
	<input type="hidden" name="id" value="<?php echo $this->row->id; ?>" />
	</form>
	<?php	
		JHTML::_('behavior.keepalive'); //keep session alive while editing
	?>
