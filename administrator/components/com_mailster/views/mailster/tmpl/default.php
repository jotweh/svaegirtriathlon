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
	
	$mstUtils 	= & MstFactory::getUtils();
	$mstApp 	= & MstFactory::getApplication();	
	$plgUtils 	= & MstFactory::getPluginUtils();
	
	$mstUtils->loadJavascript();
	
	$version 		= $mstApp->getVersionString(false);
	$fullVersion 	= $mstApp->getVersionString();
	$versionStr = JText::_( 'COM_MAILSTER_VERSION' ) . ': ' . $fullVersion;
	
	$imgPath = 'components/com_mailster/assets/images/';
	$updLink = "http://www.brandt-oss.com/index.php?option=com_versions&catid=1&myVersion=" . $version;
	$updateFeed = 'http://www.brandt-oss.com/component/versions/?catid=1&task=feed&tmpl=component';
	$queuedMailsLink = 'index.php?option=com_mailster&amp;view=queue&amp;controller=queue&amp;task=view';
	
	$updFeedLink = '<a href="' . $updateFeed .  '" title="' .  JText::_( 'COM_MAILSTER_SUBSCRIBE_TO_UDPATE_FEED' ) . '" target="_blank" >';
	$pHashOk = false;
	$aE = '</a>';
	?>
	<script language="javascript" type="text/javascript">
		var $j = jQuery.noConflict();
		$j(document).ready(function(){
			$j('#resetTimer').click(function(){
				$j('#progressIndicator1').removeClass('mtrActivityIndicator').addClass('mtrActivityIndicator');
				var data2send = '{ "task": "resetPlgTimer" }';
				var url = 'index.php?option=com_mailster&controller=plugins&task=resetplgtimer';
			    $j.post(url, { mtrAjaxData: data2send },
				    function(resultData){ 
				         if(resultData){				
					         var resultObject = eval(resultData)[0];
					         $j('#progressIndicator1').removeClass('mtrActivityIndicator');
				        	 $j('#timeResetContainer').text(resultObject.checkresult);
					         location.reload();
				         }else{ 
				        	 alert(<?php echo $mstUtils->jsonEncode(JText::_( 'COM_MAILSTER_COULD_NOT_COMPLETE_AJAX_REQUEST' )); ?>);				           
				         }
					}
				);  	
			    $j('#resetTimer').hide();	
				$j('#progressIndicator1').show();	      
			}); 
			$j('.activeToggler').click(function(){
				var data2send = '{ "task": "toggleActive" }';
				var selectNamePattern = 'activeToggler';
				var cid = (this.id).substr(selectNamePattern.length);
				var url = 'index.php?option=com_mailster&controller=lists&task=toggleActive&cid[]=' + cid;
			    $j.post(url, { mtrAjaxData: data2send },
				    function(resultData){ 
				         if(resultData){				
					         var resultObject = eval(resultData)[0];
							if(resultObject.res == 'true'){
				         		location.reload();
							}
				         }else{ 
				        	 alert(<?php echo $mstUtils->jsonEncode(JText::_( 'COM_MAILSTER_COULD_NOT_COMPLETE_AJAX_REQUEST' )); ?>);				           
				         }
					}
				); 
				return false; 
			}); 
		});
		function validateSubmittedForm(task)
		{
			if (task == 'updatecheck')
			{	// don't leave page, just check for updates...
				window.open('<?php echo $updLink; ?>');				
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
	<table style="width:100%">
		<tr>
			<td>
				<span style="display:block; float:right; text-align:right;">
				<?php echo $updFeedLink; ?>
				<img src="<?php echo $imgPath . '16-rss.png' ?>" alt="" style="vertical-align:middle;" />
				<?php echo $aE . $updFeedLink . $versionStr . $aE;?>
				</span>				
			</td>
		</tr>
		<?php 

			if($this->systemProblems->error){
				?>
				<tr><td style="background-color:red;font-weight:bold;text-align:center;height:30px;" colspan="2">
					<?php echo JText::_( 'COM_MAILSTER_PROBLEM_IDENTIFIED' ) . ': ' . $this->systemProblems->errorMsg; 
					if($this->systemProblems->autoFixAvailable){
					?>
						<a href="<?php echo $this->systemProblems->autoFixLink; ?>" title="<?php echo JText::_( 'COM_MAILSTER_ATTEMPTS_TO_FIX_THE_PROBLEM_FOR_YOU_IF_NOT_WORKING_PLEASE_CONTACT_SUPPORT_FOR_FURTHER_ADVISE' ); ?>" style="margin-left:10px;">
							<?php echo JText::_( 'COM_MAILSTER_AUTOMATICALLY_RESOLVE_ISSUE' ); ?>
						</a>
					<?php 
					}?>
					<a href="index.php?option=com_mailster&view=diagnosis" style="margin-left:10px;"><?php echo JText::_( 'COM_MAILSTER_OPEN_SYSTEM_DIAGNOSIS' ); ?></a>
				</td></tr>
				<?php
			}
		
			$mstApp = &MstFactory::getApplication();				
			$pHashOk = $mstApp->isProductHashCorrect('com_mailster', 'f7647518248eb8ef2b0a1e41b8a59f34');
			$plgHashOk = $mstApp->checkPluginProductHashes();
			if(!$pHashOk || !$plgHashOk){
				?>
				<tr><td style="background-color:red;font-weight:bold;text-align:center;height:30px;" colspan="2"><?php echo JText::_( 'COM_MAILSTER_PRODUCT_MODIFIED' ); ?></td></tr>
				<?php
			}
		?>
	</table>
	<div id="leftCol">
	<table id="startPageTable">
		<tr>			
			<td>
				<table id="generalStats">		
				<?php 
				
					$imgLink = 'components/com_mailster/assets/images/';
					$red_x = '16-publish_x.png';
					$green_t = '16-tick.png';
					$red_cross = '<img src="' . $imgLink . $red_x . '"';
					$green_tick = '<img src="' . $imgLink . $green_t . '"'; 
					$published = $green_tick . ' title="' . JText::_( 'COM_MAILSTER_PUBLISHED' ) . '" alt="" />';
					$unpublished = $red_cross . ' title="' . JText::_( 'COM_MAILSTER_PUBLISHED' ) . '" alt="" />';
					$active = $green_tick . ' title="' . JText::_( 'COM_MAILSTER_ACTIVE_LIST_FORWARD_MAILS' ) . '" alt="" />';
					$inactive = $red_cross . ' title="' . JText::_( 'COM_MAILSTER_INACTIVE_LIST_DO_NOT_RETRIEVE_OR_FORWARD_MAILS' ) . '" alt="" />';
					$editListImg = '<img src="' . $imgPath . '32-list.png' . '" alt="" />';
					$editListMembersImg = '<img src="' . $imgPath . '32-user.png' . '" alt="" />';
					$mailArchiveImg = '<img src="' . $imgPath . '32-mailArchive.png' . '" alt="" />';
					$csvImportImg = '<img src="' . $imgPath . '32-csv.png' . '" alt="" />';
					$newMailingListImg = '<img src="' . $imgPath . 'icon-32-newList-mailster.png' . '" alt="" />';
					$addUserImg = '<img src="' . $imgPath . '32-addUser.png' . '" alt="" />';
					$addGroupsImg = '<img src="' . $imgPath . '32-addGroup.png' . '" alt="" />';
				?>
					<tr>
						<th colspan="3"><?php echo JText::_( 'COM_MAILSTER_GENERAL_STATS' ); ?></th>
					</tr>
					<tr>
						<td style="text-align:right;"><?php echo $this->row->totalLists; ?></td>
						<td><?php echo JText::_( 'COM_MAILSTER_TOTAL_LIST_COUNT' ); ?></td>
						<td>&nbsp;</td>
					</tr>
					<!-- 
					<tr>
						<td style="text-align:right;"><?php echo $this->row->unpublishedLists; ?></td>
						<td><?php echo JText::_( 'COM_MAILSTER_UNPUBLISHED_LIST_COUNT' ); ?></td>
						<td>&nbsp;</td>
					</tr>
					 -->
					<tr>
						<td style="text-align:right;"><?php echo $this->row->inactiveLists; ?></td>
						<td><?php echo JText::_( 'COM_MAILSTER_INACTIVE_LIST_COUNT' ); ?></td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<td style="text-align:right;"><?php echo $this->row->totalMails; ?></td>
						<td><?php echo JText::_( 'COM_MAILSTER_TOTAL_MAIL_COUNT' ); ?></td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<td style="text-align:right;"><?php echo $this->row->queuedMails; ?></td>
						<td><a href="<?php echo $queuedMailsLink; ?>" ><?php echo JText::_( 'COM_MAILSTER_QUEUED_MAIL_COUNT' ); ?></a></td>
						<td>&nbsp;</td>
					</tr>
					<!--  
					<tr>
						<td style="text-align:right;"><?php echo $this->row->unsentMails; ?></td>
						<td><?php echo JText::_( 'COM_MAILSTER_UNSENT_MAIL_COUNT' ); ?></td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<td style="text-align:right;"><?php echo $this->row->errorMails; ?></td>
						<td><?php echo JText::_( 'COM_MAILSTER_ERROR_MAIL_COUNT' ); ?></td>	
						<td>&nbsp;</td>	
					</tr>
					-->
					<tr>
						<th colspan="3"><?php echo JText::_( 'COM_MAILSTER_PLUGIN_STATUS' ); ?></th>
					</tr>
					<?php 
					
						if(version_compare(JVERSION,'1.6.0','ge')) {
							// Joomla! 1.6 / 1.7 / ...
							$mailPluginId = empty($this->mailPlugin) ? 0 : $this->mailPlugin->extension_id;
							$subscrPluginId = empty($this->subscrPlugin) ? 0 : $this->subscrPlugin->extension_id;
							$profilePluginId = empty($this->profilePlugin) ? 0 : $this->profilePlugin->extension_id;
							$cbBridgePluginId = empty($this->cbBridgePlugin) ? 0 : $this->cbBridgePlugin->extension_id;
							$pluginBaseLink = 'index.php?option=com_plugins&task=plugin.edit&extension_id=';
							
						} else {
							// Joomla! 1.5 
							$mailPluginId = empty($this->mailPlugin) ? 0 : $this->mailPlugin->id;
							$subscrPluginId = empty($this->subscrPlugin) ? 0 : $this->subscrPlugin->id;
							$profilePluginId = empty($this->profilePlugin) ? 0 : $this->profilePlugin->id;
							$cbBridgePluginId = empty($this->cbBridgePlugin) ? 0 : $this->cbBridgePlugin->id;
							$pluginBaseLink = 'index.php?option=com_plugins&view=plugin&client=site&task=edit&cid[]=';							
						}
						
						$mailPlgLink = $pluginBaseLink . $mailPluginId;
						$subscrPlgLink = $pluginBaseLink . $subscrPluginId;
						$profilePluginLink = $pluginBaseLink . $profilePluginId;
						$cbBridgePluginLink = $pluginBaseLink . $cbBridgePluginId;
						
						$mailPlgLink = empty($this->mailPlugin) ? '' : '<a href="' . $mailPlgLink . '" target="_blank" >';
						$subscrPlgLink = empty($this->subscrPlugin) ? '' : '<a href="' . $subscrPlgLink . '" target="_blank" >';
						$profilePluginLink = empty($this->profilePlugin) ? '' : '<a href="' . $profilePluginLink . '" target="_blank" >';
						$cbBridgePluginLink = empty($this->cbBridgePlugin) ? '' : '<a href="' . $cbBridgePluginLink . '" target="_blank" >';
						
						$mailPlgLinkAE = empty($this->mailPlugin) ? '' : $aE;
						$subscrPlgLinkAE = empty($this->subscrPlugin) ? '' : $aE;
						$profilePluginLinkAE = empty($this->profilePlugin) ? '' : $aE;
						$cbBridgePluginLinkAE = empty($this->cbBridgePlugin) ? '' : $aE;
					?>
					<?php if($plgUtils->isMailPluginInstalled()){ ?>	
					<tr>
						<td style="text-align:right;"><?php echo $this->row->mailPluginStatus ? $active : $inactive; ?></td>
						<td><?php echo $mailPlgLink; ?><?php echo JText::_( 'COM_MAILSTER_MAIL_PLUGIN' ); ?><?php echo $mailPlgLinkAE; ?></td>
						<td>&nbsp;</td>
					</tr>
					<?php }else{ ?>
					<tr>
						<td style="text-align:right;"><?php echo $inactive; ?></td>
						<td><?php echo JText::_( 'COM_MAILSTER_MAIL_PLUGIN' ); ?></td>
						<td><span style="font-weight:bold; color:red;"><?php echo JText::_( 'COM_MAILSTER_ERRORS' ); ?></span></td>
					</tr>
					<?php } ?>
					<?php if($plgUtils->isSubscriberPluginInstalled()){ ?>	
					<tr>
						<td style="text-align:right;"><?php echo $this->row->subscriberPluginStatus ? $active : $inactive; ?></td>
						<td><?php echo $subscrPlgLink; ?><?php echo JText::_( 'COM_MAILSTER_SUBSCRIBER_PLUGIN' ); ?><?php echo $subscrPlgLinkAE; ?></td>	
						<td>&nbsp;</td>	
					</tr>
					<?php } ?>	
					<?php if($plgUtils->isProfilePluginInstalled()){ ?>	
					<tr>
						<td style="text-align:right;"><?php echo $this->row->profilePluginStatus ? $active : $inactive; ?></td>
						<td><?php echo $profilePluginLink; ?><?php echo JText::_( 'COM_MAILSTER_PROFILE_PLUGIN' ); ?><?php echo $profilePluginLinkAE; ?></td>	
						<td>&nbsp;</td>	
					</tr>
					<?php } ?>	
					<?php if($plgUtils->isCBPluginInstalled()){ ?>	
					<tr>
						<td style="text-align:right;"><?php echo $this->row->cbBridgePluginStatus ? $active : $inactive; ?></td>
						<td><?php echo $cbBridgePluginLink; ?><?php echo JText::_( 'COM_MAILSTER_CB_BRIDGE_PLUGIN' ); ?><?php echo $cbBridgePluginLinkAE; ?></td>	
						<td>&nbsp;</td>	
					</tr>	
					<?php } ?>	
					<tr>
						<th colspan="3" style="text-align:center">						
							<?php echo JText::_( 'COM_MAILSTER_PLUGIN_ACTIVITY' ); ?>
							<div id="timeResetContainer" style="float:right; margin-left:-30px; padding-right:3px; text-align:right;">
							<div id="progressIndicator1" style="display:none; margin:2px; padding-right:2px; min-height:16px;width:16px;">&nbsp;</div>
								<a id="resetTimer" href="#" title="<?php echo JText::_( 'COM_MAILSTER_RESET_PLUGIN_TIMER_RETRIEVE_AND_SEND_MAILS_NOW' ); ?>">
									<?php echo JText::_( 'COM_MAILSTER_MST_RESET' ); ?>
								</a>
							</div>
						</th>
					</tr>
					<tr>
						<td><?php echo JText::_( 'COM_MAILSTER_SERVER_TIME' ).': ';?></td>
						<td><?php echo $this->row->curTime; ?></td>
						<td>&nbsp;</td>	
					</tr>
					<tr>
						<td><?php echo JText::_( 'COM_MAILSTER_NEXT_CHECK_PLUGIN' ).': ';?></td>
						<td><?php echo $this->row->nextRetrieveRun; ?></td>
						<td>&nbsp;</td>	
					</tr>
					<tr>
						<td><?php echo JText::_( 'COM_MAILSTER_NEXT_SEND_PLUGIN' ).': ';?></td>
						<td><?php echo $this->row->nextSendRun; ?></td>
						<td>&nbsp;</td>	
					</tr>		
				</table>
			</td>
			<?php			
			if(count($this->row->lists) > 0){				
			?>
			<td>
				<table id="detailedStats">		
					<tr>
						<th colspan="3"><span><?php echo JText::_( 'COM_MAILSTER_MAILING_LISTS' ); ?></span></th>
					</tr>
					<?php
					
					$lists = &$this->row->lists;
					
					for($i=0; $i < count($lists); $i++)
					{
						$list = &$lists[$i];											
						$editListLink = 'index.php?option=com_mailster&amp;view=list&amp;controller=lists&amp;task=edit&amp;cid[]='.$list->id;
						$editListMembersLink = 'index.php?option=com_mailster&amp;controller=listmembers&amp;task=listmembers&amp;listID=' . $list->id;
						$mailArchiveLink = 'index.php?option=com_mailster&amp;controller=mails&amp;task=mails&amp;listID=' . $list->id;
						$editList = '<a href="' . $editListLink . '" title="' . JText::_( 'COM_MAILSTER_EDIT_MAILING_LIST' )  . '" >' . $editListImg . '</a>';
						$editMembers = '<a href="' . $editListMembersLink . '" title="' . JText::_( 'COM_MAILSTER_MANAGE_RECIPIENTS' )  . '" >' . $editListMembersImg . '</a>';
						$mailArchive = '<a href="' . $mailArchiveLink . '" title="' . JText::_( 'COM_MAILSTER_VIEW_MAIL_ARCHIVE' )  . '" >' . $mailArchiveImg . '</a>';						
					?>
					<tr class="detailedStatsHeader">
						<th>
							<a href="#" class="activeToggler" id="activeToggler<?php echo $list->id; ?>">
							<?php
									echo ($list->active == '1' ? $active : $inactive); 
							?></a>
						</th>
						<th>
						<?php 	echo $list->name; ?>
						</th>
						<th>
						<?php
							 	
								echo $editList;
								echo $editMembers;
								echo $mailArchive;
								 ?>
						</th>
					</tr>
					<tr>
						<td style="text-align:right;"><?php echo $list->recipients; ?></td>
						<td><?php echo JText::_( 'COM_MAILSTER_RECIPIENT_COUNT' ); ?></td>	
						<td>&nbsp;</td>	
					</tr>
					<tr>
						<td style="text-align:right;"><?php echo $list->totalMails; ?></td>
						<td><?php echo JText::_( 'COM_MAILSTER_TOTAL_MAIL_COUNT_FORWARDED' ); ?> (<?php echo $list->unsentMails; ?> <?php echo JText::_( 'COM_MAILSTER_TOTAL_MAIL_COUNT_UNSENT' ); ?>, <?php echo $list->errorMails; ?> <?php echo JText::_( 'COM_MAILSTER_TOTAL_MAIL_COUNT_SEND_ERRORS' ); ?>)</td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<td style="text-align:right;"><?php echo $list->blockedFilteredBounced; ?></td>
						<td><?php echo JText::_( 'COM_MAILSTER_TOTAL_MAIL_COUNT_NOT_FORWARDED' ); ?> (<?php echo $list->blockedMails . ' ' .JText::_( 'COM_MAILSTER_TOTAL_MAIL_COUNT_BLOCKED' ); ?>, <?php echo $list->filteredMails . ' ' .JText::_( 'COM_MAILSTER_TOTAL_MAIL_COUNT_FILTERED' ); ?>, <?php echo $list->bouncedMails . ' ' .JText::_( 'COM_MAILSTER_TOTAL_MAIL_COUNT_BOUNCED' ); ?>)</td>
						<td>&nbsp;</td>
					</tr>
					<?php 
					}
					?>
				</table>
			</td>
			<?php
			} 
				$addListLink = '<a href="index.php?option=com_mailster&amp;view=list&amp;controller=lists&amp;task=add" >';
				$addUsersLink = '<a href="index.php?option=com_mailster&amp;view=users#second" >';
				$addGroupsLink = '<a href="index.php?option=com_mailster&amp;view=groups#second" >';
				$importUsersLink = '<a href="index.php?option=com_mailster&amp;controller=csv&amp;task=import" >';
			?>
			<td>
				<table id="taskTable">		
					<tr>
						<th><?php echo JText::_( 'COM_MAILSTER_TASKS' ); ?></th>
					</tr>
					<tr>
						<td>
							<ul class="taskLinkList">
							<li><?php echo $addListLink.$newMailingListImg.$aE.' '.$addListLink.JText::_( 'COM_MAILSTER_ADD_MAILING_LIST' ).$aE; ?></li>
							<li><?php echo $addUsersLink.$addUserImg.$aE.' '.$addUsersLink.JText::_( 'COM_MAILSTER_ADD_USERS' ).$aE; ?></li>
							<li><?php echo $addGroupsLink.$addGroupsImg.$aE.' '.$addGroupsLink.JText::_( 'COM_MAILSTER_ADD_GROUPS' ).$aE; ?></li>
							<li><?php echo $importUsersLink.$csvImportImg.$aE.' '.$importUsersLink.JText::_( 'COM_MAILSTER_IMPORT_USERS' ).$aE; ?></li>
							</ul>
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
	</div>
	<div id="rightCol" style="text-align:center;">
		<table id="mailsterLogo">
			<tr>						
				<td>
					<?php
						$mailsterLogo = '<img class="maisterLogo" src="' . $imgPath . 'biglogo.png' . '" alt=""/>';
				 		echo $mailsterLogo;
					?>
				</td>
			</tr>
			<tr>
			<?php 
			$pHashOk = $mstApp->isProductHashCorrect('com_mailster', 'f7647518248eb8ef2b0a1e41b8a59f34');
			$plgHashOk = $mstApp->checkPluginProductHashes();
			$isFree = $mstApp->isFreeEdition('com_mailster', 'free', 'f7647518248eb8ef2b0a1e41b8a59f34');
			if(!$pHashOk || !$plgHashOk || $isFree){				
			?>	
				<td>
					<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
						<input type="hidden" name="cmd" value="_s-xclick">
						<input type="hidden" name="hosted_button_id" value="PLV4JQC8EHX3J">
						<input type="image" src="https://www.paypal.com/en_US/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
						<img alt="" border="0" src="https://www.paypal.com/de_DE/i/scr/pixel.gif" width="1" height="1">
					</form>
				</td>
			<?php 
			}else{
				?>	
				<td>&nbsp;</td>
			<?php 
			}
			?>
			</tr>			
		</table>
	</div>
