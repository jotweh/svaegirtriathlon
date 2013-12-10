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
	
	
	$tipsJS = 'tips.js';
	$tipTipJS = 'jquery.tipTip.minified.js';
	$tipsCSS = 'tips.css';
	$document = & JFactory::getDocument();
	$imgPath = 'components/com_mailster/assets/images/'; 
	?>
	<div style="display:none;">
		<img id="infoIconZero" class="infoIcon" src="<?php echo $imgPath;?>16-info.png" width="16px" />
	</div>	
	<?php 
	$document->addScript('../administrator/components/com_mailster/assets/js/' . $tipTipJS);
	$document->addScript('../administrator/components/com_mailster/assets/js/' . $tipsJS);
	$document->addStyleSheet('../administrator/components/com_mailster/assets/css/' . $tipsCSS ,'text/css','screen');
	
?>
