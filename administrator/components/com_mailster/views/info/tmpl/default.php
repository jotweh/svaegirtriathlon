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
		
	$imgLink = 'components/com_mailster/assets/images/';
	
	$moreLink = "http://www.brandt-oss.com/products";
	$updLink = "http://www.brandt-oss.com/index.php?option=com_versions&tmpl=component&catid=1&myVersion=" . $this->version;
	$trackLink = "http://www.brandt-oss.com/support";
	$docLink = "http://www.brandt-oss.com/products/mailster/doc";
	$upgrLink = "http://www.brandt-oss.com/products/mailster/pro";
	$diagnosisLink = 'index.php?option=com_mailster&view=diagnosis';	
	$logLink = 'index.php?option=com_mailster&view=log';
	$moreLink = '<a href="' . $moreLink . '" target="_blank" >';	
	$updLink = '<a href="' . $updLink . '" target="_blank" >';
	$docLink = '<a href="' . $docLink . '" target="_blank" >';
	$upgrLink = '<a href="' . $upgrLink . '" target="_blank" >';
	$trackLink = '<a href="' . $trackLink . '" target="_blank" >';
	$diagnosisLink = '<a href="' . $diagnosisLink . '" >';
	$logLink = '<a href="' . $logLink . '" >';
	$aE = '</a>';
	$updImg = '<img src="' . $imgLink . 'icon-32-updatecheck-mailster.png" alt="" />';
	$upgrImg = '<img src="' . $imgLink . 'icon-32-upgrade-mailster.png" alt="" />';
	$docImg = '<img src="' . $imgLink . 'icon-32-help-mailster.png" alt="" />';
	$diagnosisImg = '<img src="' . $imgLink . 'icon-32-check-mailster.png" alt="" />';
	$logImg  = '<img src="' . $imgLink . 'icon-32-log-mailster.png" alt="" />';
	$trackImg = '<img src="' . $imgLink . 'icon-32-track-mailster.png" alt="" />';
	$moreImg  = '<img src="' . $imgLink . 'icon-32-blocks-mailster.png" alt="" />';
?>

<div style="width:100%;">
	<div style="float:left;" class="col width-60">
	    <fieldset class="adminform">
	      <legend><?php echo JText::_( 'COM_MAILSTER_VERSION' ); ?></legend>
	      			<?php
		echo $this->appName . '<br/><br/>' . $this->versionStr . ' (' . $this->compInfos->creationdate . ')<br/>';
	 	?>
	    </fieldset>
	    <fieldset class="adminform">
	    	<legend><?php echo JText::_( 'COM_MAILSTER_MORE_MAILSTER' ); ?></legend>
			<ul id="infoLinkList">
				<li><?php echo $updLink.$updImg.$aE.' '.$updLink.JText::_( 'COM_MAILSTER_CHECK_FOR_UPDATES' ).$aE; ?></li>
				<?php 
				
					$mstApp = &MstFactory::getApplication();			
					$pHashOk = $mstApp->isProductHashCorrect('com_mailster', 'f7647518248eb8ef2b0a1e41b8a59f34');
					$plgHashOk = $mstApp->checkPluginProductHashes();
					$isFree = $mstApp->isFreeEdition('com_mailster', 'free', 'f7647518248eb8ef2b0a1e41b8a59f34');								
					if(!$pHashOk || !$plgHashOk || $isFree){
				?>
					<li><?php echo $upgrLink.$upgrImg.$aE.' '.$upgrLink.JText::_( 'COM_MAILSTER_UPGRADE_TO_PRO_EDITION' ).$aE; ?></li>
				<?php
					 }?>
				<li><?php echo $docLink.$docImg.$aE.' '.$docLink.JText::_( 'COM_MAILSTER_VIEW_DOCUMENTATION' ).$aE; ?></li>
				<li><?php echo $diagnosisLink.$diagnosisImg.$aE.' '.$diagnosisLink.JText::_( 'COM_MAILSTER_SYSTEM_DIAGNOSIS' ).$aE; ?></li>
				<li><?php echo $logLink.$logImg.$aE.' '.$logLink.JText::_( 'COM_MAILSTER_SHOW_LOG' ).$aE; ?></li>
				<li><?php echo $trackLink.$trackImg.$aE.' '.$trackLink.JText::_( 'COM_MAILSTER_REPORT_ISSUE' ).$aE; ?></li>
			</ul>
		</fieldset>
	    <fieldset class="adminform">
	    	<legend><?php echo JText::_( 'COM_MAILSTER_MORE_PRODUCTS' ); ?></legend>
			<ul class="taskLinkList">
				<li><?php echo $moreLink.$moreImg.$aE.' '.$moreLink.JText::_( 'COM_MAILSTER_CHECKOUT_OTHER_COMPONENTS' ).$aE; ?></li>
			</ul>
		</fieldset>
	    <fieldset class="adminform">
	    	<legend><?php echo JText::_( 'COM_MAILSTER_MAILSTER_COPYRIGHT' ); ?></legend>
			<table cellpadding="4" cellspacing="0" border="0">
				<tr>
					<td valign="top" colspan="2">
					 &copy; 2010-2012 Holger Brandt IT Solutions<br/>
					 Mailster is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License 2
					 as published by the Free Software Foundation. <br/>
					 <br/>
					 Mailster is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of 
					 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.<br/>
					 <br/>
					 You should have received a copy of the GNU General Public License along with Mailster; if not, write to the Free Software
					 Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA or see <a href="http://www.gnu.org/licenses/">http://www.gnu.org/licenses/</a>.<br/><br/>
					 </td>
				</tr>
				<tr>
					<td valign="top">
				    		<a href="http://www.brandt-oss.com" target="_blank"><img src="<?php echo $imgLink . 'logo_boss.png'; ?>" height="80" alt="Brandt OSS Logo" align="left"></a>
					</td>
					<td valign="top" width="100%">
			       	 	<strong>a project hosted at <a href="http://www.brandt-oss.com" target="_blank">Brandt OSS (Open Source Software)</a></strong>
					</td>
				</tr>	
				<tr>
					<td valign="top">
			    		<a href="http://www.brandt-solutions.de" target="_blank"><img src="<?php echo $imgLink . 'logo_hbit.png'; ?>" height="80" alt="Brandt IT Solutions Logo" align="left"></a>
					</td>
					<td valign="top" width="100%">
			       	 	<strong>a product of <a href="http://www.brandt-solutions.de" target="_blank">Brandt IT Solutions</a></strong>
					</td>
				</tr>	
			</table>
		</fieldset>
  	</div>
	<div style="float:right;" class="col width-40">
   		<img src="<?php echo 'components/com_mailster/assets/images/biglogo.png'; ?>" alt="Mailster Logo" align="right">
	</div>
	<div class="clr"></div>
</div> 			
