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
	$imgPath = 'components/com_mailster/assets/images/';
	$groupUsersImg = '<img src="' . $imgPath . '32-group.png' . '" alt="" style="vertical-align:middle;" />';	
?>
	<script type="text/javascript">
		var $j = jQuery.noConflict();
		$j(document).ready(function(){
			initPairList('users');
			initPairList('groups');
			prepareTabs();
		});
		function checkAllXT(checked, topId, rowCount)
		{
			$j('#' + topId + ' table.adminlist tbody [name=cid[]]').each(
					function() {
					 $j(this).attr('checked', checked);
			});
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
						if(ctr>0){
							is_j_us_str = is_j_us_str + ";" + is_js_us_arr[i];
						}else{	
							is_j_us_str = is_js_us_arr[i];
						}
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

		function toggleTask(icon)
		{			
            if(icon == 'users')
            {            	
            	$j('#toolbar-removeUsers-mailster').show(600);
            	$j('#toolbar-removeGroups-mailster').hide(600);
            }else if(icon == 'groups')
            {
            	$j('#toolbar-removeUsers-mailster').hide(600);
            	$j('#toolbar-removeGroups-mailster').show(600);
            }else
            {
            	$j('#toolbar-removeUsers-mailster').hide(600);
            	$j('#toolbar-removeGroups-mailster').hide(600);
            }        
			return false;
		}
		
	</script>
	<form action="index.php" method="post" name="adminForm">
	 <div id="tabContainer" class="tabs">
        <ul class="tabNavigation">
       		<li><a id="recipients" class="" href="#first" onclick="toggleTask('recipients');"><?php echo JText::_( 'COM_MAILSTER_ALL_RECIPIENTS' ); ?></a></li>
            <li class="tabNavigationSpacer">&nbsp;</li>
            <li><a id="users" class="" href="#second" onclick="toggleTask('users');"><?php echo JText::_( 'COM_MAILSTER_LIST_MEMBERS' ); ?></a></li>
            <li><a class="" href="#third" onclick="toggleTask('addusers');">[ + ]</a></li>
            <li class="tabNavigationSpacer">&nbsp;</li>
            <li><a id="groups" class="" href="#fourth" onclick="toggleTask('groups');"><?php echo JText::_( 'COM_MAILSTER_LIST_GROUPS' ); ?></a></li>
            <li><a class="" href="#fifth" onclick="toggleTask('addgroups');">[ + ]</a></li>
        </ul>
        
        
        <div style="display: none;" id="first" class="tabDiv">
        	<table class="adminlist" cellspacing="1">
			<thead>
				<tr>
					<th width="5"><?php echo JText::_( 'COM_MAILSTER_NUM' ); ?></th>
					<th class="title"><?php echo JText::_( 'COM_MAILSTER_NAME' ); ?></th>	
					<th class="title"><?php echo JText::_( 'COM_MAILSTER_EMAIL' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php
				$k = 0;
				for($i=0, $n=count( $this->recipients ); $i < $n; $i++) {
					$recipient = &$this->recipients[$i];
					
				?>
				<tr class="<?php echo "row$k"; ?>">
					<td><?php echo ($i+1); ?></td>					
					<td><?php echo $recipient->name; ?></td>	
					<td><?php echo $recipient->email; ?></td>
				</tr>
				<?php
				$k = 1 - $k; 
				}
				if($n==0){
					?>
					<tr class="row0">				
						<td> </td>	
						<td><?php echo JText::_( 'COM_MAILSTER_CURRENTLY_NO_RECIPIENTS' ); ?></td>	
						<td> </td>
					</tr>
				<?php
				}
				?>
			</tbody>
			<tfoot>
				<tr>
					<td colspan="12"></td>
				</tr>
			</tfoot>
		</table>
        </div>
        <div style="display: block;" id="second" class="tabDiv">
		<table class="adminlist" cellspacing="1">
			<thead>
				<tr>
					<th width="5"><?php echo JText::_( 'COM_MAILSTER_NUM' ); ?></th>
					<th width="5"><input type="checkbox" name="toggle" value="" onClick="checkAllXT(this.checked, 'second', <?php echo count( $this->rows ); ?>);" /></th>
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
				$k = 0;
				$is_joomla_user_tags = array();
				for($i=0, $n=count( $this->rows ); $i < $n; $i++) {
					$row = &$this->rows[$i];
					$is_joomla_user_tags[] = $row->is_joomla_user;
					$userLink = 'index.php?option=com_mailster&view=user&amp;task=edit&amp;id='.$row->user_id . '&amp;isjuser='.$row->is_joomla_user;					
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
					<?php if($mstConfig->showUserDescription()){ ?>	
					<td><?php echo $row->notes; ?></td>
					<?php }  ?>							
					<td>
					<?php echo ($row->is_joomla_user == '1' ? JText::_( 'COM_MAILSTER_JOOMLA_USER_DATA' ) : JText::_( 'COM_MAILSTER_MAILSTER_USER_DATA' )); ?>
					</td>						
					<td align="center"><?php echo $row->user_id; ?></td>
				</tr>
				<?php
				$k = 1 - $k; 
				}
				if($n==0){
					?>
					<tr class="row0">				
						<td colspan="2"> </td>	
						<td><?php echo JText::_( 'COM_MAILSTER_NO_USERS_ADDED' ); ?></td>	
						<td colspan="3"> </td>
					</tr>
				<?php
				}
				?>
			</tbody>
			<tfoot>
				<tr>
					<td colspan="7">&nbsp;</td>
				</tr>
			</tfoot>
		</table>
            
        </div>
        <div style="display: none;" id="third" class="tabDiv">
		<?php
		$listStrings = new stdClass;
		$listStrings->leftTitle = JText::_( 'COM_MAILSTER_CHOOSE_USERS_TO_ADD_TO_RECIPIENTS' );
		$listStrings->rightTitle =  JText::_( 'COM_MAILSTER_NEW_LIST_USERS' );
		$listStrings->selectAll =  JText::_( 'COM_MAILSTER_SELECT_ALL' );
		$listStrings->selectNone =  JText::_( 'COM_MAILSTER_SELECT_NONE' );
		$listStrings->selectInv =  JText::_( 'COM_MAILSTER_SELECT_INVERSE' );
		$listStrings->submitButton =  JText::_( 'COM_MAILSTER_ADD_USERS_TO_RECIPIENTS' );
		$listStrings->submitTitle =  JText::_( 'COM_MAILSTER_ADD_USERS_TO_RECIPIENTS' );		
		$mstUtils->insertPairlist("users", "newListMembers", $this->userEntries, $listStrings); 
		?>
        </div>
        <div style="display: none;" id="fourth" class="tabDiv">
		<table class="adminlist" cellspacing="1">
			<thead>
				<tr>
					<th width="5"><?php echo JText::_( 'COM_MAILSTER_NUM' ); ?></th>
					<th width="5"><input type="checkbox" name="toggle" value="" onClick="checkAllXT(this.checked, 'fourth', <?php echo count( $this->rows ); ?>);"  /></th>
					<th class="title"><?php echo JText::_( 'COM_MAILSTER_NAME' ); ?></th>
					<th class="title"><?php echo JText::_( 'COM_MAILSTER_MANAGE_MEMBERS' ); ?></th>
					<th width="1%" nowrap="nowrap"><?php echo JText::_( 'COM_MAILSTER_JID' ); ?></th>
				</tr>
			</thead>

			<tbody>
				<?php
				$k = 0;
				for($i=0, $n=count( $this->groupRows ); $i < $n; $i++) {
					$groupRow = &$this->groupRows[$i];
					$groupLink = 'index.php?option=com_mailster&view=group&amp;task=edit&amp;cid[]='.$groupRow->id;
					$userLink = 'index.php?option=com_mailster&amp;controller=groupusers&amp;task=groupusers&amp;groupID='.$groupRow->id;
					$checked = '<input type="checkbox" onclick="isChecked(this.checked);" value="' . $groupRow->id . '" name="cid[]" id="cb' . $i . '">';
				?>
				<tr class="<?php echo "row$k"; ?>">
					<td><?php echo ($i+1); ?></td>
					<td><?php echo $checked; ?></td>		
					<td>
						<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_MAILSTER_EDIT_GROUP' );?>: <?php echo $groupRow->name; ?>">
						<a href="<?php echo $groupLink; ?>"><?php echo $groupRow->name; ?></a></span>
					</td>
					<td align="center">
						<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_MAILSTER_MANAGE_MEMBERS' );?>">							
							<a href="<?php echo $userLink; ?>"><?php echo $groupUsersImg; ?></a>
							<a href="<?php echo $userLink; ?>"><?php echo JText::_( 'COM_MAILSTER_GROUP_MEMBERS' );?></a>
						</span>
					</td>						
					<td align="center"><?php echo $groupRow->id; ?></td>
				</tr>
				<?php
					$k = 1 - $k;
				 }
				if($n==0){
					?>
					<tr class="row0">				
						<td colspan="3"> </td>	
						<td><?php echo JText::_( 'COM_MAILSTER_NO_GROUPS_ADDED' ); ?></td>	
						<td> </td>
					</tr>
				<?php
				}
				?>
			</tbody>
			<tfoot>
				<tr>
					<td colspan="5">&nbsp;</td>
				</tr>
			</tfoot>
		</table>
                
            
        </div>
        <div style="display: none;" id="fifth" class="tabDiv">
            <?php
			$listStrings = new stdClass;
			$listStrings->leftTitle = JText::_( 'COM_MAILSTER_CHOOSE_GROUPS_TO_ADD_TO_RECIPIENTS' );
			$listStrings->rightTitle =  JText::_( 'COM_MAILSTER_NEW_LIST_GROUPS' );
			$listStrings->selectAll =  JText::_( 'COM_MAILSTER_SELECT_ALL' );
			$listStrings->selectNone =  JText::_( 'COM_MAILSTER_SELECT_NONE' );
			$listStrings->selectInv =  JText::_( 'COM_MAILSTER_SELECT_INVERSE' );
			$listStrings->submitButton =  JText::_( 'COM_MAILSTER_ADD_GROUPS_TO_RECIPIENTS' );	 
			$listStrings->submitTitle =  JText::_( 'COM_MAILSTER_ADD_GROUPS_TO_RECIPIENTS' );
			$mstUtils->insertPairlist("groups", "newListGroups", $this->groupsEntries, $listStrings);	
             ?>
		</div>
        
    </div>

		
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="option" value="com_mailster" />
		<input type="hidden" name="view" value="listmembers" />
		<input type="hidden" name="listID" value="<?php echo $this->list_id; ?>" />
		<input type="hidden" name="controller" value="listmembers" />
		<input type="hidden" name="task" id="task" value="" />
		<input type="hidden" name="all_is_j_us_str" id="all_is_j_us_str" value="<?php echo implode(";", $is_joomla_user_tags); ?>" />
		<input type="hidden" name="is_j_us_str" id="is_j_us_str" value="" />
		<input type="hidden" name="pairlist_precodes" id="pairlist_precodes" value="users" />
	</form>
	<?php	
		JHTML::_('behavior.keepalive'); //keep session alive while editing
	?>

