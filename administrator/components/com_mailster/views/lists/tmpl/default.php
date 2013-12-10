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
	$dateUtils = & MstFactory::getDateUtils();
	$mstUtils 	= & MstFactory::getUtils();
	
	$mstUtils->loadJavascript();
?>
	<script language="javascript" type="text/javascript">
		var $j = jQuery.noConflict();
		$j(document).ready(function(){
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
		function validateSubmittedForm(task){		
			submitform( task );
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
	<form action="index.php" method="post" name="adminForm">
		
		<table class="adminlist" cellspacing="1">
			<thead>
				<tr>
					<th width="5"><?php echo JText::_( 'COM_MAILSTER_NUM' ); ?></th>
					<th width="10" title="<?php echo JText::_( 'COM_MAILSTER_MAILING_LIST_ACTIVE' ); ?>"><?php echo JText::_( 'COM_MAILSTER_ACTIVE_UC' ); ?></th>
					<th width="5"><input type="checkbox" name="toggle" value="" onClick="checkAll(<?php echo count( $this->rows ); ?>);" /></th>
					<th class="title"><?php echo JText::_( 'COM_MAILSTER_NAME' ); ?></th>
					<th width="200"><?php echo JText::_( 'COM_MAILSTER_MAILING_LIST_E-MAIL_ADRESS' ); ?></th>
					<th width="100"><?php echo JText::_( 'COM_MAILSTER_LAST_CHECK' ); ?></th>			
					<th width="100"><?php echo JText::_( 'COM_MAILSTER_RECIPIENT_COUNT' ); ?></th>			
					<th width="150"><?php echo JText::_( 'COM_MAILSTER_MANAGE_RECIPIENTS' ); ?></th>			
					<th width="100"><?php echo JText::_( 'COM_MAILSTER_MAIL_COUNT' ); ?></th>	
					<th width="150"><?php echo JText::_( 'COM_MAILSTER_VIEW_MAIL_ARCHIVE' ); ?></th>		
					<th width="1%" nowrap="nowrap"><?php echo JText::_( 'COM_MAILSTER_JID' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php
				$imgPath = 'components/com_mailster/assets/images/';
				$editListMembersImg = '<img src="' . $imgPath . '32-user.png' . '" style="vertical-align:middle;" alt="" />';
				$mailArchiveImg = '<img src="' . $imgPath . '32-mailArchive.png' . '" style="vertical-align:middle" alt="" />';
				$listImg = '<img src="' . $imgPath . '32-list.png' . '" style="vertical-align:middle" alt="" />';
				$jImgPath = 'components/com_mailster/assets/images/';
				$red_x = '16-publish_x.png';
				$green_t = '16-tick.png';
				$red_cross = '<img src="' . $jImgPath . $red_x . '"';
				$green_tick = '<img src="' . $jImgPath . $green_t . '"'; 
				$active = $green_tick . ' title="' . JText::_( 'COM_MAILSTER_ACTIVE_LIST_FORWARD_MAILS' ) . '" alt="" />';
				$inactive = $red_cross . ' title="' . JText::_( 'COM_MAILSTER_INACTIVE_LIST_DO_NOT_RETRIEVE_OR_FORWARD_MAILS' ) . '" alt="" />';
			
				$k = 0;	
				for($i=0, $n=count( $this->rows ); $i < $n; $i++) {
					$row = &$this->rows[$i];
					
					$link 			= 'index.php?option=com_mailster&amp;view=list&amp;controller=lists&amp;task=edit&amp;cid[]='.$row->id;
					$userLink 		= 'index.php?option=com_mailster&amp;controller=listmembers&amp;task=listmembers&amp;listID='.$row->id;
					$mailLink		= 'index.php?option=com_mailster&amp;controller=mails&amp;task=mails&amp;listID='.$row->id;
					$checked 		= '<input type="checkbox" onclick="isChecked(this.checked);" value="' .$row->id. '" name="cid[]" id="cb' . $i . '">';
				?>
				<tr class="<?php echo "row$k"; ?>">
					<td><?php echo ($i+1); ?></td>
					<td align="center">
						<a href="#" class="activeToggler" id="activeToggler<?php echo $row->id; ?>">
							<?php echo ($row->active == '1' ? $active : $inactive);  ?>
						</a>
					</td>
					<td><?php echo $checked; ?></td>					
					<td>
						<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_MAILSTER_EDIT_MAILING_LIST' );?>: <?php echo $row->name; ?>">
						<a href="<?php echo $link; ?>"><?php echo $listImg; ?></a>
						<a href="<?php echo $link; ?>"><?php echo $row->name; ?></a>
						</span>
					</td>
					<td><?php echo $row->list_mail; ?></td>
					<td><?php echo $dateUtils->getTimeAgo($row->last_check, JText::_( 'COM_MAILSTER_NEVER' )); ?></td>
					<td align="center"><?php echo $row->nrMembers; ?></td>
					<td align="center">
						<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_MAILSTER_MANAGE_RECIPIENTS' );?>">
						<a href="<?php echo $userLink; ?>"><?php echo $editListMembersImg; ?></a>
						<a href="<?php echo $userLink; ?>"><?php echo JText::_( 'COM_MAILSTER_EDIT_RECIPIENTS' ); ?></a>
						</span>
					</td>
					<td align="center"><?php echo $row->nrMails; ?></td>
					<td align="center">
						<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_MAILSTER_VIEW_MAILING_LIST_MAIL' );?>">
						<a href="<?php echo $mailLink; ?>"><?php echo $mailArchiveImg; ?></a>
						<a href="<?php echo $mailLink; ?>"><?php echo JText::_( 'COM_MAILSTER_VIEW_MAILS' ); ?></a>
						</span>
					</td>
					<td align="center"><?php echo $row->id; ?></td>
				</tr>
				<?php
				 	$k = 1 - $k;  
				} ?>

			</tbody>			
			<tfoot>
				<tr>
					<td colspan="11">&nbsp;</td>
				</tr>
			</tfoot>
		</table>

		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="option" value="com_mailster" />
		<input type="hidden" name="view" value="lists" />
		<input type="hidden" name="controller" value="lists" />
		<input type="hidden" name="task" value="" />
	</form>
