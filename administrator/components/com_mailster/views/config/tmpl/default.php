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

$pane = JPane::getInstance('tabs');
$params = $this->params;
$groups = $params->getGroups();

$mstParams = MstFactory::getParameter($params);
?>
<form action="index.php?option=com_mailster" method="post" name="adminForm" id="adminForm">
	<?php
		echo $pane->startPane("menu-pane"); // Start config pane
		
		foreach (array_keys($groups) as $group)
		{
			$configTitle = 'COM_MAILSTER_CONFIGURATION_TAB_TITLE_';
			$configTitle .= strtoupper($group);
			echo $pane->startPanel(JText::_($configTitle), "param-page");
			?>
			<div class="col width-60"> 
			    <fieldset class="adminform">
			      <legend><?php echo JText::_( 'COM_MAILSTER_MAILSTER_CONFIGURATION' ); ?></legend>
	      			<?php 
	      				echo $mstParams->renderItOldSchool('params', $group);	      				
				 	?>
			    </fieldset>
		  	</div>
  			<div class="clr"></div>
	  		<?php 
			echo $pane->endPanel();
		}

		echo $pane->endPane(); // End config pane
  ?>

	<?php echo JHTML::_( 'form.token' ); ?>
	<input type="hidden" name="option" value="com_mailster" />
	<input type="hidden" name="controller" value="config" />
	<input type="hidden" name="view" value="config" />
	<input type="hidden" name="task" value="" />
</form>
