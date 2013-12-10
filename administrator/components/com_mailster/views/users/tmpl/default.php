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
	$csvImg = '<img src="' . $imgPath . '32-csv.png' . '" alt="" style="vertical-align:middle;" />';
?>
	<form action="index.php" method="post" name="adminForm">		
		<script type="text/javascript">
			var $j = jQuery.noConflict();
			$j(document).ready(
			function () {		
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
		</script>
		<div id="tabContainer" class="tabs">
	        <ul class="tabNavigation">
	       		<li><a id="first" class="" href="#first" onclick="toggleTask('users');"><?php echo JText::_( 'COM_MAILSTER_USERS' ); ?></a></li>
	            <li><a id="second" class="" href="#second" onclick="toggleTask('addusers');">[ + ]</a></li>
	        </ul>
	        
	        
	        <div style="display: none;" id="first" class="tabDiv">
				<table class="adminlist" cellspacing="1">
					<thead>
						<tr>
							<th width="5"><?php echo JText::_( 'COM_MAILSTER_NUM' ); ?></th>
							<th width="5"><input type="checkbox" name="toggle" value="" onClick="checkAll(<?php echo count( $this->rows ); ?>);" /></th>
							<th class="title"><?php echo JText::_( 'COM_MAILSTER_NAME' ); ?></th>	
							<th><?php echo JText::_( 'COM_MAILSTER_EMAIL' ); ?></th>					
							<th><?php echo JText::_( 'COM_MAILSTER_DESCRIPTION' ); ?></th>				
							<th width="1%" nowrap="nowrap"><?php echo JText::_( 'COM_MAILSTER_JID' ); ?></th>
						</tr>
					</thead>
	
					<tbody>
						<?php
						$k=0;
						for($i=0, $n=count( $this->rows ); $i < $n; $i++) {
							$row = &$this->rows[$i];
							$userLink = 'index.php?option=com_mailster&amp;controller=users&amp;task=edit&amp;id='.$row->id;
							$checked = '<input type="checkbox" onclick="isChecked(this.checked);" value="' . $row->id . '" name="cid[]" id="cb' . $i . '">';
						?>
							<tr class="<?php echo "row$k"; ?>">
								<td><?php echo ($i+1); ?></td>
								<td><?php echo $checked; ?></td>
								
								<td>
								<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_MAILSTER_EDIT_USER' );?>:<?php echo $row->name; ?>">
								<a href="<?php echo $userLink; ?>">
								<?php echo $row->name != '' ? $row->name : '[ _ _ _ _ _ ]'; ?>
								</a></span>
								</td>
								<td>
								<?php echo $row->email; ?>
								</td>				
								<td>
								<?php echo $row->notes; ?>
								</td>					
								<td align="center"><?php echo $row->id; ?></td>
							</tr>
						<?php
							$k = 1 - $k;  
						} ?>
	
					</tbody>
					<tfoot>
						<tr>
							<td colspan="12">&nbsp;</td>
						</tr>
					</tfoot>
					
				</table>
			</div>
			<div style="display: block;" id="second" class="tabDiv">
				<table id="importCSVForm" class="adminform">
					<thead>
						<tr>
							<th width="5" colspan="3"><?php echo JText::_( 'COM_MAILSTER_METHOD' ) . ' 1: ' . JText::_( 'COM_MAILSTER_CSV_IMPORT' ); ?></th>
						</tr>
					</thead>
					<tr>
						<td>&nbsp;</td>
						<?php 
						$csvImportLink = 'index.php?option=com_mailster&controller=csv&task=import';
						?>
						<td colspan="2">
							<a href="<?php echo $csvImportLink; ?>"><?php echo $csvImg; ?></a>
							<a href="<?php echo $csvImportLink; ?>"><?php echo JText::_( 'COM_MAILSTER_CSV_IMPORT' ); ?></a>
						</td>
						<td width="100%">&nbsp;</td>
					</tr>
					<tr>
						<td>&nbsp;</td>
						<td width="100%">&nbsp;</td>
					</tr>
				</table>
				<table id="addUserForm" class="adminform">
					<thead>
						<tr>
							<th width="5" colspan="3"><?php echo JText::_( 'COM_MAILSTER_METHOD' ) . ' 2: ' . JText::_( 'COM_MAILSTER_ENTER_USER_DETAILS' ); ?></th>							
						</tr>
					</thead>
					<?php
					$userCount = 5;
					for($i=0; $i < $userCount; $i++)
					{
						$name = "name-" . ($i+1);
						$email = "email-" . ($i+1);
						?>
					<tr>
						<td>
							<?php echo ($i+1) . "."; ?>
						</td>
						<td>
							<label for="<?php echo $name;?>">
								<?php echo JText::_( 'COM_MAILSTER_NAME' ).':'; ?>
							</label>
						</td>
						<td width="180px">
							<input class="inputbox" name="<?php echo $name;?>" value="" style="width:180px" maxlength="45" id="<?php echo $name;?>" />
						</td>
						<td>
							<label for="<?php echo $email;?>">
								<?php echo JText::_( 'COM_MAILSTER_EMAIL' ).':'; ?>
							</label>
						</td>
						<td width="190px">
							<input class="inputbox" name="<?php echo $email;?>" value="" style="width:180px"  maxlength="45" id="<?php echo $email;?>" /> <span style="font-weight:bold;color:red;">*</span>
						</td>
						<td width="100%">&nbsp;</td>
					</tr><?php
					}
					?>
					<tr>
						<td colspan="4">&nbsp;</td>
						<td><div id="selListPairSubmit" class="submitContainer">
							<input type="submit" value="<?php echo JText::_( 'COM_MAILSTER_SAVE_NEW_USERS' ); ?>" class="submitButton" onclick="document.getElementById('task').value='newUsers'; this.form.submit();"/>
						</div></td>
						<td width="100%">&nbsp;</td>
					</tr>
					<tr>
						<td>&nbsp;</td>
					</tr>
				</table> 
			</div>
		</div>
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="option" value="com_mailster" />
		<input type="hidden" name="view" value="users" />
		<input type="hidden" name="controller" value="users" />
		<input type="hidden" name="task" id="task" value="" />
		<input type="hidden" name="userCount" value="<?php echo $userCount; ?>" />
	</form>

