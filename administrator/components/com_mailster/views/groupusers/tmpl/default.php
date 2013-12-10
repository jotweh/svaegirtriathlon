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
	$mstUtils = & MstFactory::getUtils();
	$mstUtils->loadJavascript();
	$mstUtils->addTabs();
?>
	<script type="text/javascript">
		var $j = jQuery.noConflict();
		$j(document).ready(
		function () {							
			initPairList('users');
			prepareTabs();
		});
		function toggleTask(icon)
		{			
            if(icon == 'users')
            {            	
            	$j('#toolbar-removeUsers-mailster').show(600);
            }else
            {
            	$j('#toolbar-removeUsers-mailster').hide(600);
            }        
			return false;
		}
		function validateSubmittedForm(task)
		{
			var form = document.adminForm;
			// check we aren't cancelling
			if (task == 'cancel')
			{	// no need to validate, we are cancelling		
				submitform( task );
				return;
			}		
			if (task=='removeUsers'){
				var all_is_j_us_str = $j('#all_is_j_us_str').attr("value");
				var is_js_us_arr = all_is_j_us_str.split(";"); 
				var is_j_us_str = "";
				var ctr = 0;
				for(var i=0; i < <?php echo count( $this->rows ); ?>; i++)
				{
					if($j('#cb' + i).attr("checked"))
					{
						if(ctr>0)
							is_j_us_str = is_j_us_str + ";" + is_js_us_arr[i];
						else	
							is_j_us_str = is_js_us_arr[i];
						ctr++;
					}
				}
				$j('#is_j_us_str').attr("value", is_j_us_str);
			}
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
		<div id="tabContainer" class="tabs">
	        <ul class="tabNavigation">
	       		<li><a id="recipients" class="" href="#first" onclick="toggleTask('users');"><?php echo JText::_( 'COM_MAILSTER_GROUP_MEMBERS' ); ?></a></li>
	            <li><a class="" href="#second" onclick="toggleTask('addusers');">[ + ]</a></li>
	        </ul>
	        
	        
	    <div style="display: none;" id="first" class="tabDiv">
			<table class="adminlist" cellspacing="1">
				<thead>
					<tr>
						<th width="5"><?php echo JText::_( 'COM_MAILSTER_NUM' ); ?></th>
						<th width="5"><input type="checkbox" name="toggle" value="" onClick="checkAll(<?php echo count( $this->rows ); ?>);" /></th>
						<th class="title"><?php echo JText::_( 'COM_MAILSTER_NAME' ); ?></th>	
						<th class="title"><?php echo JText::_( 'COM_MAILSTER_EMAIL' ); ?></th>	
						<?php
						$mstConfig = &MstFactory::getConfig();
						if($mstConfig->showUserDescription()){ 
						?>	
						<th class="title"><?php echo JText::_( 'COM_MAILSTER_DESCRIPTION' ); ?></th>
						<?php
						} 
						?>							
						<th class="title"><?php echo JText::_( 'COM_MAILSTER_USER_DATA_ORIGIN' ); ?></th>	
						<th width="1%" nowrap="nowrap"><?php echo JText::_( 'COM_MAILSTER_JID' ); ?></th>
					</tr>
				</thead>	
	
				<tbody>
					<?php
					$is_joomla_user_tags = array();
					$k=0;
					for($i=0, $n=count( $this->rows ); $i < $n; $i++) {
						$row = &$this->rows[$i];
						$is_joomla_user_tags[] = $row->is_joomla_user;
						$userLink 			= 'index.php?option=com_mailster&view=user&amp;task=edit&amp;id='.$row->user_id . '&amp;isjuser='.$row->is_joomla_user;
						$checked = '<input type="checkbox" onclick="isChecked(this.checked);" value="' . $row->user_id . '" name="cid[]" id="cb' . $i . '">';
					?>
					<tr class="<?php echo "row$k"; ?>">
						<td><?php echo ($i+1); ?></td>
						<td><?php echo $checked; ?></td>
						
						<td>
						<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_MAILSTER_EDIT_USER' );?>: <?php echo $row->name; ?>">
						<a href="<?php echo $userLink; ?>"><?php echo $row->name != '' ? $row->name : '[ __________ ]'; ?></a></span>
						</td>	
						<td>
						<?php echo $row->email; ?>
						</td>
						<?php
						if($mstConfig->showUserDescription()){ 
						?>				
						<td>
						<?php echo $row->notes; ?>
						</td>	
						<?php
						} 
						?>	
						<td>
						<?php echo ($row->is_joomla_user == '1' ? JText::_( 'COM_MAILSTER_JOOMLA_USER_DATA' ) : JText::_( 'COM_MAILSTER_MAILSTER_USER_DATA' )); ?>
						</td>						
						<td align="center"><?php echo $row->user_id; ?></td>
					</tr>					
						<?php
						$k = 1 - $k; 
					} ?>	
					</tbody>
					<tfoot>
						<tr>
							<td colspan="7">&nbsp;</td>
						</tr>
					</tfoot>
				</table>	
			</div>
			<div style="display: block;" id="second" class="tabDiv">
				<?php 
				$listStrings = new stdClass;
				$listStrings->leftTitle = JText::_( 'COM_MAILSTER_CHOOSE_USERS_TO_ADD_TO_GROUP' );
				$listStrings->rightTitle =  JText::_( 'COM_MAILSTER_NEW_GROUP_USERS' );
				$listStrings->selectAll =  JText::_( 'COM_MAILSTER_SELECT_ALL' );
				$listStrings->selectNone =  JText::_( 'COM_MAILSTER_SELECT_NONE' );
				$listStrings->selectInv =  JText::_( 'COM_MAILSTER_SELECT_INVERSE' );
				$listStrings->submitButton =  JText::_( 'COM_MAILSTER_ADD_USERS_TO_GROUP' );		
				$listStrings->submitTitle =  JText::_( 'COM_MAILSTER_ADD_USERS_TO_GROUP' );		
				$mstUtils->insertPairlist("users", "newGroupUsers", $this->userEntries, $listStrings); 
				?>
			</div>
		</div>
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="option" value="com_mailster" />
		<input type="hidden" name="view" value="groupusers" />
		<input type="hidden" name="groupID" value="<?php echo $this->group_id; ?>" />
		<input type="hidden" name="controller" value="groupusers" />
		<input type="hidden" name="task" id="task" value="" />
		<input type="hidden" name="all_is_j_us_str" id="all_is_j_us_str" value="<?php echo implode(";", $is_joomla_user_tags); ?>" />
		<input type="hidden" name="is_j_us_str" id="is_j_us_str" value="" />
		<input type="hidden" name="pairlist_precodes" id="pairlist_precodes" value="users" />
	</form>
