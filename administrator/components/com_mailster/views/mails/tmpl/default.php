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
	require_once('printMailListTab.php');	
	
	$list_id = $this->listID;
	
?>
	<form action="index.php" method="post" name="adminForm" id="adminForm">		
		<script type="text/javascript">
		var $j = jQuery.noConflict();
		$j(document).ready(
		function () {			
			var list_id = <?php echo $list_id; ?>;
			if(list_id > 0){
				$j('#listSelection option').each(function(){
					var value = $j(this).val();	
					if(value == list_id){							
						$j(this).attr("selected", "selected");
					}
				});	
			}
			

			$j("#listSelection").change(function() 
		    { 
		        var list_id = $j(this).val(); 			        
		        $j("#listID").attr("value", list_id);
		        document.adminForm.submit();		        			      
		    }); 

			prepareTabs();
		});

		function checkAllXT(checked, topId, rowCount)
		{
			$j('#' + topId + ' table.adminlist tbody [name=cid[]]').each(
					function() {
					 $j(this).attr('checked', checked);
			});
			if(checked){
				document.adminForm.boxchecked.value = rowCount;
			}else{
				document.adminForm.boxchecked.value = 0;
			}
				 
		}
		</script>
		<table border="0" style="margin-bottom:20px;">
			<tr>
				<td><?php echo JText::_( 'COM_MAILSTER_MAILING_LIST_FILTER' ); ?></td>
				<td width="200px">
					<select id="listSelection" name="listSelection" size="1" style="width:200px;margin-left:10px;">
						<option value="-1"><?php echo JText::_( 'COM_MAILSTER_ALL_MAILING_LISTS' ); ?></option>
					<?php 
						for($i=0, $n=count( $this->mailingLists ); $i < $n; $i++) {
							$mailingList = &$this->mailingLists[$i];
							echo '<option value="'. $mailingList->id . '">' . $mailingList->name . '</option>';
						}
					?>
					</select>
				</td>
			</tr>
		</table>
		<div id="tabContainer" class="tabs">
	        <ul class="tabNavigation">
	       		<li><a id="processed" class="" href="#first" onclick=""><?php echo JText::_( 'COM_MAILSTER_PROCESSED_MAILS' ); ?></a></li>    
	            <li><a id="blocked" class="" href="#second" onclick=""><?php echo JText::_( 'COM_MAILSTER_BLOCKED_AND_FILTERED_MAILS' ); ?></a></li>          
	            <li><a id="bounced" class="" href="#third" onclick=""><?php echo JText::_( 'COM_MAILSTER_BOUNCED_MAILS' ); ?></a></li>      
	        </ul>
	        <?php 		
	        	$blockedTxt = JText::_( 'COM_MAILSTER_SHOWS_BLOCKED_MAILS_SENT_FROM_UNKNOWN_AUTHORIZED_BLOCKED_PERSONS' );
	        	$bouncedTxt = JText::_( 'COM_MAILSTER_SHOWS_BOUNCED_MAILS_LIKE_FOR_EXAMPE_DELIVERY_STATUS_NOTIFICATIONS' );
				printMailListTab($this->rows, 		'first', 	false, 	'', 			true, 	$list_id, false);
				printMailListTab($this->blocked, 	'second', 	true, 	$blockedTxt, 	false, 	$list_id, true);   
				printMailListTab($this->bounced,	'third', 	true, 	$bouncedTxt, 	false, 	$list_id, false);     
	        ?>
			<div class="tabsFooter">
		        <?php 
		        	echo $this->pagination->getListFooter();
		        ?>
		    </div>
		</div>
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="option" value="com_mailster" />
		<input type="hidden" name="listID" value="<?php echo $list_id; ?>" id="listID" />
		<input type="hidden" name="view" value="mails" />
		<input type="hidden" name="controller" value="mails" />
		<input type="hidden" name="task" id="task" value="mails" />
	</form>
