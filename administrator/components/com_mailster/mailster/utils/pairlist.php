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
	
	function pairList($pairListId, $submitTask, $selectArray, $listStrings)
	{
		$listsJS = 'lists.js';
		$listsCSS = 'lists.css';
		$document = &JFactory::getDocument();
		$document->addScript('../administrator/components/com_mailster/assets/js/' . $listsJS);
		$document->addStyleSheet('../administrator/components/com_mailster/assets/css/' . $listsCSS,'text/css','screen');
		$ID = $pairListId . '_'; // unique pair list identifier and precode
		?>
		<div id="<?php echo $ID;?>outerPairListContainer" class="outerPairListContainer">
			<div id="<?php echo $ID;?>selectListPair" class="selectListPair">
				<div id="<?php echo $ID;?>leftSelectList" class="leftSelectList">
				<div id="<?php echo $ID;?>leftSelectListTitle" class="leftSelectListTitle"><?php echo $listStrings->leftTitle; ?></div>			
				  <select id="<?php echo $ID;?>selectLeft" class="pairListSelection" multiple="multiple" >     
					<?php
						
						for($i=0, $n=count( $selectArray ); $i < $n; $i++) {
							$entry = $selectArray[$i];
							?>
							<option value="<?php echo $entry->value; ?>"><?php echo $entry->text; ?></option>
							<?php
						}
					?>
				  </select>
				  <div class="selectListFooter"><ul><li><a id="<?php echo $ID;?>leftSelectAll" href="#"><?php echo $listStrings->selectAll; ?></a></li><li><a id="<?php echo $ID;?>leftSelectInv" href="#"><?php echo $listStrings->selectInv; ?></a></li><li><a id="<?php echo $ID;?>leftSelectNone" href="#"><?php echo $listStrings->selectNone; ?></a></li></ul></div>		
				</div>
				<div id="<?php echo $ID;?>selectListControl" class="selectListControl">
				<ul>
				  <li><input id="<?php echo $ID;?>MoveAllRight" type="button" value=" >> " title="Add all" class="selListButton" /></li>
				  <li><input id="<?php echo $ID;?>MoveRight" type="button" value=" > " title="Add Selected" class="selListButton" /></li>
				  <li>&nbsp;</li>
				  <li><input id="<?php echo $ID;?>MoveLeft" type="button" value=" < " title="Remove Selected" class="selListButton" /></li>
				  <li><input id="<?php echo $ID;?>MoveAllLeft" type="button" value=" << " title="Remove all" class="selListButton" /></li>
				  </ul>
				</div>
				<div id="<?php echo $ID;?>rightSelectList" class="rightSelectList">  
				<div id="<?php echo $ID;?>rightSelectListTitle" class="rightSelectListTitle"><?php echo $listStrings->rightTitle; ?></div>
				  <select name="<?php echo $ID;?>selectRight[]" id="<?php echo $ID;?>selectRight" class="pairListSelection" multiple="multiple" >          
				  </select>
				 <div class="selectListFooter"><ul><li><a id="<?php echo $ID;?>rightSelectAll" href="#"><?php echo $listStrings->selectAll; ?></a></li><li><a id="<?php echo $ID;?>rightSelectInv" href="#"><?php echo $listStrings->selectInv; ?></a></li><li><a id="<?php echo $ID;?>rightSelectNone" href="#"><?php echo $listStrings->selectNone; ?></a></li></ul></div>
				</div>			
			</div>
			<div id="<?php echo $ID;?>selListPairSubmit" class="submitContainer">
					<input id="<?php echo $ID;?>selListPairSubmitButton" type="submit" value="<?php echo $listStrings->submitButton; ?>" title="<?php echo $listStrings->submitTitle; ?>" class="submitButton" onclick="document.getElementById('task').value='<?php echo $submitTask; ?>';"/>
			</div>
		</div>
		<?php
	}

?>
