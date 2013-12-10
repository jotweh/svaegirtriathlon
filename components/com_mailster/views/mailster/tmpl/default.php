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
	
	$adminImgPath = 'administrator/components/com_mailster/assets/images/';
	$subscriberTutLink = 'http://www.brandt-oss.com/products/mailster/doc/27-using-the-subscriber-plugin';
	$docLink = 'http://www.brandt-oss.com/products/mailster/doc';
	?>
	<script language="javascript" type="text/javascript">
		
	</script>
	<div id="mailsterContainer">
		<hr/>
		<img src="<?php echo $adminImgPath . 'biglogo.png';?>" width="200px" style="float:right;"/>
		<h1><?php echo JText::_( 'COM_MAILSTER_WELCOME_TO_MAILSTER' ); ?></h1>
		<h3>...<?php echo JText::_( 'COM_MAILSTER_BUT_SORRY_FOR_THE_BAD_NEWS' ); ?></h3>
		<p><?php echo JText::_( 'COM_MAILSTER_MAILSTER_DOES_NOT_HAVE_FRONTEND' ); ?></p>
		<p><?php echo JText::_( 'COM_MAILSTER_USE_SUBSCRIBER_PLUGIN_IN_NORMAL_ARTICLE' ); ?><br/>
		<br/>
		 <?php echo JText::_( 'COM_MAILSTER_TUTORIAL' ); ?>: <a href="<?php echo $subscriberTutLink; ?>" target="blank"><?php echo JText::_( 'COM_MAILSTER_USING_THE_SUBSCRIBER_PLUGIN' ); ?></a><br/>
		 <?php echo JText::_( 'COM_MAILSTER_DOCUMENTATION' ); ?>: <a href="<?php echo $docLink; ?>" target="blank"><?php echo JText::_( 'COM_MAILSTER_COMPLETE_DOCUMENTATION' ); ?></a>
		 </p>
		<hr/>
	</div>
