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
		
	$importedUsers = $this->importedusers;
	$importTarget = $this->importtarget;
	?>
	<script type="text/javascript">
		var $j = jQuery.noConflict(); 
		$j(document).ready(
		function () {		
			var usersImported = '<?php echo ($importedUsers ? 'true' : 'false'); ?>';
			if(usersImported == 'true'){
				var importTask = '<?php echo $this->importtask; ?>';
				var duplicateopt = '<?php echo $this->duplicateopt; ?>';
				var selectCase = false;
				if(importTask == 'add2group'){
					$j('#add2group').attr('checked', 'checked');
					var targetValue = '<?php echo $this->targetgroup; ?>';	
					var targetElement = '#targetgroup';
					selectCase = true;		
				}else if(importTask == 'add2list'){
					$j('#add2list').attr('checked', 'checked');
					var targetValue = '<?php echo $this->targetlist; ?>';
					var targetElement = '#targetlist';
					selectCase = true;			
				}
				if(selectCase == true){					
					$j(targetElement + ' option').each(function(){	
						var value = $j(this).attr("value");
						if(value == targetValue)
						{							
							$j(this).attr("selected", "selected");
						}
					});		
				}
				if(duplicateopt == 'ignore'){
					$j('#ignore').attr('checked', 'checked');
				}else{
					$j('#merge').attr('checked', 'checked');
				}
			}else{
				var importTarget = '<?php echo ($importTarget ? $importTarget : ''); ?>';
				if(importTarget != ''){
					if(importTarget == 'add2group'){
						$j('#add2group').attr('checked', 'checked');
					}else if(importTarget == 'add2list'){
						$j('#add2list').attr('checked', 'checked');
					}else{
						$j('#importonly').attr('checked', 'checked');
					}
					toggleImportTarget(importTarget);
				}
			}
			toggleImportTarget(importTask);
			toggleImportSource('local_file');
			
			$j('#importonly').click(function () {
				if(this.checked == true){
					toggleImportTarget('importonly');
				}
			}); 

			$j('#add2group').click(function () {
				if(this.checked == true){
					toggleImportTarget('add2group');
				}
			});

			$j('#add2list').click(function () {
				if(this.checked == true){
					toggleImportTarget('add2list');
				}
			});
			
			$j('#targetgroup').click(function () {
				if(this.value == 0){
					$j('#newgroupname').attr('disabled',  '');
				}else{
					$j('#newgroupname').attr('disabled',  'disabled');
				}
			});

			$j('#local_file').click(function () {
				if(this.checked == true){
					toggleImportSource('local_file');
				}
			});
			
			$j('#server_file').click(function () {
				if(this.checked == true){
					toggleImportSource('server_file');
				}
			});
			
		});

		function toggleImportTarget(importTarget){
			if(importTarget == 'importonly'){
				$j('#newgroupname').attr('disabled',  'disabled');
				$j('#targetgroup').attr('disabled',  'disabled');
				$j('#targetlist').attr('disabled',  'disabled');
			}else if(importTarget == 'add2group'){
				if($j('#targetgroup').val() == 0){
					$j('#newgroupname').attr('disabled',  '');
				}else{
					$j('#newgroupname').attr('disabled',  'disabled');
				}
				$j('#targetgroup').attr('disabled',  '');
				$j('#targetlist').attr('disabled',  'disabled');				
			}else if(importTarget == 'add2list'){
				$j('#newgroupname').attr('disabled',  'disabled');
				$j('#targetgroup').attr('disabled',  'disabled');
				$j('#targetlist').attr('disabled',  '');
			}
		}

		function toggleImportSource(importSource){
			if(importSource == 'local_file'){
				$j('#filepath_local').attr('disabled',  '');
				$j('#filepath').attr('disabled',  'disabled');
			}else if(importSource == 'server_file'){	
				$j('#filepath_local').attr('disabled',  'disabled');
				$j('#filepath').attr('disabled',  '');			
			}
		}
		
		function submitbutton(task)
		{
			var form = document.adminForm;			
			if (task == 'cancel') // check we aren't cancelling
			{	// no need to validate, we are cancelling		
				submitform( task );
				return;
			}else{
				submitform( task );
			}
		}
	</script>
	<form enctype="multipart/form-data" action="index.php" method="post" name="adminForm" id="adminForm">	
	<table class="adminform">	
		<?php
			$task = 'saveimport';
			if(!$importedUsers){
				$task = 'startimport';
		?>
		<tr><th colspan="2"><?php echo JText::_( 'COM_MAILSTER_IMPORT_SOURCE' ); ?></th><th></th><th>&nbsp;</th></tr>
		<tr>
			<td width="15px" style="text-align:right;">
				<input type="radio" name="datasource" value="local_file" id="local_file" checked="checked" />
			</td>
			<td width="100px" style="text-align:left;">
				<label for="filepath_local">
					<?php echo JText::_( 'COM_MAILSTER_PATH_TO_FILE_LOCAL' ).':'; ?>
				</label>
			</td>
			<td width="200px">
				<input class="input_box" id="filepath_local" name="filepath_local" type="file" size="57" />
			</td>
			<td>&nbsp;</td>
		</tr>		
		<tr>
			<td width="15px" style="text-align:right;">
				<input type="radio" name="datasource" value="server_file" id="server_file" />
			</td>
			<td width="100px" style="text-align:left;">
				<label for="filepath" title="<?php echo JText::_( 'COM_MAILSTER_PATH_RELATIVE_TO_JOOMLA_BASE_DIRECTORY' );?>">
					<?php echo JText::_( 'COM_MAILSTER_PATH_TO_FILE_SERVER' ).':'; ?>
				</label>
			</td>
			<td width="200px" style="text-align:left;">
				<input class="inputbox" name="filepath" value="tmp/users.csv" size="30" maxlength="45" id="filepath" title="<?php echo JText::_( 'COM_MAILSTER_PATH_RELATIVE_TO_JOOMLA_BASE_DIRECTORY' );?>"/>
			</td>
			<td>&nbsp;</td>
		</tr>					
		<tr>
			<td width="100px" style="text-align:right;" colspan="2">
				<label for="delimiter" title="<?php echo JText::_( 'COM_MAILSTER_CHARACTER/LETTER_SEPARATING_NAME_AND_EMAIL_COLUMN' );?>">
					<?php echo JText::_( 'COM_MAILSTER_DELIMITER' ).':'; ?>
				</label>
			</td>
			<td width="200px">
				<input class="inputbox" name="delimiter" value="," size="3" maxlength="5" id="delimiter" title="<?php echo JText::_( 'COM_MAILSTER_CHARACTER_LETTER_SEPARATING_NAME_AND_EMAIL_COLUMN' );?>"/>
			</td>
			<td>&nbsp;</td>
		</tr>				
		<tr>
			<td width="100px" style="text-align:right;" colspan="2">
				<label for="dataorder" title="<?php echo JText::_( 'COM_MAILSTER_ORDER_OF_THE_COLUMNS_IN_CSV_FILE' );?>">
					<?php echo JText::_( 'COM_MAILSTER_DATA_ORDER_IN_CSV_FILE' ).':'; ?>
				</label>
			</td>
			<td width="200px">
				<input type="radio" name="dataorder" value="name_del_email" id="name_del_email" checked="checked" />
				<?php echo JText::_( 'COM_MAILSTER_NAME_DELIMITER_EMAIL' ); ?>
			</td>
			<td>&nbsp;</td>
		</tr>			
		<tr>
			<td width="100px" colspan="2">&nbsp;</td>
			<td width="200px">
				<input type="radio" name="dataorder" value="email_del_name" id="email_del_name" />
				<?php echo JText::_( 'COM_MAILSTER_EMAIL_DELIMITER_NAME' ); ?>
			</td>
			<td>&nbsp;</td>
		</tr>	
		<?php 
		}else{
		?>	
		<tr><th colspan="2"><?php echo JText::_( 'COM_MAILSTER_IMPORT_RESULT' ); ?></th><th></th><th>&nbsp;</th></tr>
		<tr>
			<td width="100px" style="text-align:right;">&nbsp;</td>
			<td width="200px"><?php echo count( $importedUsers ) . ' ' . JText::_( 'COM_MAILSTER_USER_DATA_SETS_FOUND' ); ?></td>
			<td>&nbsp;</td>
		</tr>			
		<?php 
		}
		?>	
		<tr><th colspan="2"><?php echo JText::_( 'COM_MAILSTER_IMPORT_OPTIONS' ); ?></th><th></th><th>&nbsp;</th></tr>		
		<tr>
			<td width="100px" style="text-align:right;" colspan="2">
				<label for="duplicateopt"  title="<?php echo JText::_( 'COM_MAILSTER_SET_HOW_DUPLICATE_USER_DATA_IS_HANDLED' );?>">
					<?php echo JText::_( 'COM_MAILSTER_OPTIONS_FOR_DUPLICATES' ).':'; ?>
				</label>
			</td>
			<td width="200px" colspan="2">
				<input type="radio" name="duplicateopt" value="merge" id="merge" checked="checked" />
				<?php echo JText::_( 'COM_MAILSTER_MERGE_USERS_NO_DUPLICATES' ); ?>
			</td>
			<td>&nbsp;</td>
		</tr>		
		<tr>
			<td width="100px" style="text-align:right;" colspan="2">&nbsp;</td>
			<td width="200px" colspan="2">
				<input type="radio" name="duplicateopt" value="ignore" id="ignore" />
				<?php echo JText::_( 'COM_MAILSTER_IGNORE_DUPLICATES_DONT_AVOID_DUPLICATES' ); ?>
			</td>
			<td>&nbsp;</td>
		</tr>	
		<tr>
			<td width="100px" style="text-align:right;" colspan="2">
				<label for="importtask"  title="<?php echo JText::_( 'COM_MAILSTER_OPTIONAL_SPECIFY_WHETHER_THE_USERS_SHOULD_BE_IMPORTED_IN_A_GROUP_OR_A_MAILING_LIST' );?>">
					<?php echo JText::_( 'COM_MAILSTER_IMPORT_USERS_AND' ).':'; ?>
				</label>
			</td>
			<td width="200px">
				<input type="radio" name="importtask" value="importonly" id="importonly" checked="checked" />
				<?php echo JText::_( 'COM_MAILSTER_NOTHING_ELSE_IMPORT_ONLY' ); ?>
			</td>
			<td>&nbsp;</td>
		</tr>		
		<tr>
			<td width="100px" colspan="2">&nbsp;</td>
			<td width="200px">
				<input type="radio" name="importtask" value="add2group" id="add2group" />
				<?php echo JText::_( 'COM_MAILSTER_AND_ADD_TO_GROUP' ); ?>
			</td>
			<td width="200px" rowspan="3"><?php echo JText::_( 'COM_MAILSTER_CHOOSE_GROUP' ); ?><br/>
			<select id="targetgroup" name="targetgroup" size="5" style="width:180px" disabled="disabled">
				<option value="0" selected="selected"><?php echo '< ' . JText::_( 'COM_MAILSTER_NEW_GROUP' ) . ' >'; ?></option>
			<?php
				for($i=0, $n=count( $this->groups ); $i < $n; $i++) {
					$group = &$this->groups[$i];
					?>
					<option value="<?php echo $group->id; ?>"><?php echo $group->name; ?></option>
					<?php
				}							
			?>
			</select>
			</td>
			<td><?php echo JText::_( 'COM_MAILSTER_NEW_GROUP_NAME' ); ?><br/><input type="text" name="newgroupname" value="<?php echo ($importedUsers ? $this->newgroupname : JText::_( 'COM_MAILSTER_NEW_GROUP' )); ?>" id="newgroupname" disabled="disabled" /></td>
		</tr>	
		<tr><td width="100px" colspan="2">&nbsp;</td></tr>
		<tr><td width="100px" colspan="2">&nbsp;</td></tr>
		<tr><td width="100px" colspan="2">&nbsp;</td></tr>		
		<tr>
			<td width="100px" colspan="2">&nbsp;</td>
			<td width="200px">
				<input type="radio" name="importtask" value="add2list" id="add2list" />
				<?php echo JText::_( 'COM_MAILSTER_AND_ADD_AS_RECIPIENTS' ); ?>
			</td>
			<td width="200px" rowspan="3"><?php echo JText::_( 'COM_MAILSTER_CHOOSE_MAILING_LIST' ); ?><br/>
			<select id="targetlist" name="targetlist" size="5" style="width:180px" disabled="disabled">					
			<?php
				for($i=0, $n=count( $this->mailLists ); $i < $n; $i++) {
					$mailingList = &$this->mailLists[$i];
					$selected = ($i==0 ? 'selected="selected"' : '');
					?>
					<option value="<?php echo $mailingList->id; ?>" <?php echo $selected; ?>><?php echo $mailingList->name; ?></option>
					<?php
				}							
			?>
			</select>
			</td>
			<td>&nbsp;</td>
		</tr>	
		<tr><td colspan="2">&nbsp;</td></tr>
		<tr><td colspan="2">&nbsp;</td></tr>
		<tr><td colspan="2">&nbsp;</td></tr>
		
		<?php 
			if(!$importedUsers){
		?>		
		<tr>
			<td colspan="3">&nbsp;</td>
			<td width="100px" style="text-align:right;">
				<input type="submit" name="submitbutton" value="<?php echo JText::_( 'COM_MAILSTER_IMPORT_NOW' ); ?>" id="submitbutton" class="submitButton" />
			</td>
			<td>&nbsp;</td>
		</tr>
		
		<?php 
			}else{
				?>
				<tr><td colspan="5">&nbsp;</td></tr>
				<tr><td colspan="3">&nbsp;</td><td><input type="submit" value="<?php echo JText::_( 'COM_MAILSTER_SAVE_NEW_USERS' ); ?>" class="submitButton" style="background-color:green;" /></td><td>&nbsp;</td></tr>
				<tr><th colspan="3"><?php echo JText::_( 'COM_MAILSTER_PREVIEW_OF_USERS_TO_IMPORT' ); ?></th><td>&nbsp;</td></tr>
				<tr><th>&nbsp;</th><th><?php echo JText::_( 'COM_MAILSTER_NAME' ); ?></th><th><?php echo JText::_( 'COM_MAILSTER_EMAIL' ); ?></th><th>&nbsp;</th></tr>
				<?php 
				for($i=0, $n=count( $importedUsers ); $i < $n; $i++) {
					$user = $importedUsers[$i];
					?>
					<tr><td style="text-align:right;"><?php echo ($i+1); ?></td><td><input name="name<?php echo $i; ?>" value="<?php echo $user['name']?>" size="50" maxlength="45" /></td><td><input name="email<?php echo $i; ?>" value="<?php echo $user['email']?>" size="50" maxlength="45" /></td><td>&nbsp;</td></tr>
					<?php
				}				
			}
		?>		
	</table>
	<?php echo JHTML::_( 'form.token' ); ?>
	<input type="hidden" name="option" value="com_mailster" />
	<input type="hidden" name="controller" value="csv" />
	<input type="hidden" name="view" value="csv" />
	<input type="hidden" name="task" value="<?php echo $task; ?>" />	
	<input type="hidden" name="usercount" value="<?php echo count( $importedUsers ); ?>" />
	</form>
	<?php
	//keep session alive while editing
	JHTML::_('behavior.keepalive');
	?>
