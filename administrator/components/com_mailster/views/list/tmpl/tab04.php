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
?>	
<div style="display: none; padding-left:20px; padding-top:5px;" id="fourth" class="tabDiv">				
	<table class="adminform tabContentTbl" style="float:left;">
		<tr>
			<td width="150px">
				<label for="subject_prefix">
					<?php echo JText::_( 'COM_MAILSTER_SUBJECT_PREFIX_TEXT' ).':'; ?>
				</label>
			</td>
			<td width="350px">
				<input class="inputbox hTip" name="subject_prefix" 
				value="<?php echo $this->row->subject_prefix; ?>" 
				size="50" maxlength="45" id="subject_prefix" 
				title="<?php echo JText::_( 'COM_MAILSTER_SUBJECT_PREFIX_TEXT_WILL_BE_PUT_IN_FRONT_OF_EACH_SUBJECT_INSERT_A_WHITESPACE_IF_WANTED_CAN_CONTAIN_TEXT_VARIABLES' ); ?>" />
			</td>
			<td colspan="1">&nbsp;</td>
		</tr>	
		<tr>
			<td width="150px">
				<label for="clean_up_subject">
					<?php echo JText::_( 'COM_MAILSTER_CLEAN_UP_SUBJECT' ).':'; ?>
				</label>
			</td>
			<td>
				<?php
					echo JHTML::_('select.booleanlist', 'clean_up_subject',
								 'class="inputbox hTip" title="' . JText::_( 'COM_MAILSTER_CLEAN_UP_SUBJECT_FOR_REPLIES_TO_LOOK_UNIFIED' ) . '"',
								 $this->row->clean_up_subject );
				?>
			</td>
			<td colspan="1">&nbsp;</td>
		</tr>	
		<?php 				
		$mstApp = & MstFactory::getApplication();	
		$pHashOk = $mstApp->isProductHashCorrect('com_mailster', 'f7647518248eb8ef2b0a1e41b8a59f34');
		$isFree = $mstApp->isFreeEdition('com_mailster', 'free', 'f7647518248eb8ef2b0a1e41b8a59f34');
		$plgHashOk = $mstApp->checkPluginProductHashes();
		?>
		<tr>
			<td width="150px">
				<label for="mail_format_conv">
					<?php echo JText::_( 'COM_MAILSTER_CONVERT_MAIL_FORMAT_TO' ).':'; ?>
				</label>
			</td>
			<td>
					<?php
					if($pHashOk && $plgHashOk && !$isFree){
						echo $this->Lists['mail_format_conv']; ;
					}else{
						echo JHTML::_('select.genericlist',  
									$this->convOptions, 
									'mail_format_conv', 
									'disabled="disabled" class="inputbox hTip" size="1" style="width:120px;" title="'.JText::_( 'COM_MAILSTER_CONVERT_MAIL_FORMAT_TO_A_FIXED_FORMAT_OR_LEAVE_IT_UNTOUCHED' ).'"', 
									'value', 'text', MstConsts::MAIL_FORMAT_CONVERT_HTML);			
						echo ' (' . JText::_( 'COM_MAILSTER_AVAILABLE_IN_PRO_EDITION' ) . ')';
					}
					?>
				<?php ?>
			</td>
			<td colspan="1">&nbsp;</td>
		</tr>		
		<tr>
			<td width="150px">
				<label for="mail_format_altbody">
					<?php echo JText::_( 'COM_MAILSTER_INCLUDE_HTML_AND_PLAINTEXT_VERSION' ).':'; ?>
				</label>
			</td>
			<td>
				<?php
				if($pHashOk && $plgHashOk && !$isFree){
					echo JHTML::_('select.booleanlist', 'mail_format_altbody',
								'class="inputbox hTip" title="' . JText::_( 'COM_MAILSTER_INCLUDE_HTML_AND_PLAINTEXT_VERSION_IN_HTML_EMAILS' ) . '"', 
								$this->row->mail_format_altbody );
				}else{
					echo JHTML::_('select.booleanlist', 'mail_format_altbody',
								'class="inputbox hTip" title="' . JText::_( 'COM_MAILSTER_INCLUDE_HTML_AND_PLAINTEXT_VERSION_IN_HTML_EMAILS' ) . '"', 
								 MstConsts::MAIL_FORMAT_ALTBODY_YES );
					echo ' (' . JText::_( 'COM_MAILSTER_AVAILABLE_IN_PRO_EDITION' ) . ')';
				}
				?>
			</td>
			<td colspan="1">&nbsp;</td>	
		</tr>	
		<tr>
			<td width="150px" style="vertical-align:top;">
				<?php echo JText::_( 'COM_MAILSTER_CUSTOM_HEADER' ).':'; ?>
			</td>
			<td width="350px">
				<a id="headerToggleKnob" href="#" class="hTip" title="<?php echo JText::_( 'COM_MAILSTER_CUSTOM_HEADER_PUT_IN_FRONT_OF_BODY_CAN_CONTAIN_TEXT_VARIABLES' ); ?>">
					<?php echo JText::_( 'COM_MAILSTER_WILL_BE_REPLACED' ); ?>
				</a>
			</td>								
			<td colspan="1">&nbsp;</td>
		</tr>	
		<tr class="headerOption">	
			<td width="150px" style="vertical-align:top;">
				<label for="custom_header_plain">
					<?php echo JText::_( 'COM_MAILSTER_CUSTOM_PLAINTEXT_HEADER' ).':'; ?>
				</label>
			</td>
			<td width="350px">
				<textarea id="custom_header_plain" style="width:350px; height:80px" name="custom_header_plain"><?php echo $this->row->custom_header_plain; ?></textarea>
			</td>				
			<td>&nbsp;</td>
		</tr>	
		<tr class="headerOption">
			<td width="150px" style="vertical-align:top;">
				<label for="custom_header_html" class="headerOption">
					<?php echo JText::_( 'COM_MAILSTER_CUSTOM_HTML_HEADER' ).':'; ?>
				</label>
			</td>
			<td width="350px">
				<?php echo $jEditor->display("custom_header_html", $this->row->custom_header_html, 350, 80, 80, 5); ?>
			</td>						
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td width="150px" style="vertical-align:top;">
				<?php echo JText::_( 'COM_MAILSTER_CUSTOM_FOOTER' ).':'; ?>
			</td>
			<td width="350px">
				<a id="footerToggleKnob" href="#" class="hTip" title="<?php echo JText::_( 'COM_MAILSTER_CUSTOM_FOOTER_APPENDED_TO_BODY_CAN_CONTAIN_TEXT_VARIABLES' ); ?>">
					<?php echo JText::_( 'COM_MAILSTER_WILL_BE_REPLACED' ); ?>
				</a>
			</td>						
			<td>&nbsp;</td>
		</tr>	
		<tr class="footerOption">
			<td width="150px" style="vertical-align:top;">
				<label for="custom_footer_plain">
					<?php echo JText::_( 'COM_MAILSTER_CUSTOM_PLAINTEXT_FOOTER' ).':'; ?>
				</label>
			</td>
			<td width="350px">
				<textarea id="custom_footer_plain" style="width:350px; height:80px" name="custom_footer_plain"><?php echo $this->row->custom_footer_plain; ?></textarea>
			</td>						
			<td>&nbsp;</td>
		</tr>		
		<tr class="footerOption">
			<td width="150px" style="vertical-align:top;">
				<label for="custom_footer_html" class="footerOption">
					<?php echo JText::_( 'COM_MAILSTER_CUSTOM_HTML_FOOTER' ).':'; ?>
				</label>
			</td>
			<td width="350px">
				<?php echo $jEditor->display("custom_footer_html", $this->row->custom_footer_html, 350, 80, 80, 5); ?>
			</td>						
			<td>&nbsp;</td>
		</tr>
		<?php 				
		$mstApp = & MstFactory::getApplication();	
		$pHashOk = $mstApp->isProductHashCorrect('com_mailster', 'f7647518248eb8ef2b0a1e41b8a59f34');
		$isFree = $mstApp->isFreeEdition('com_mailster', 'free', 'f7647518248eb8ef2b0a1e41b8a59f34');
		$plgHashOk = $mstApp->checkPluginProductHashes();
		?>
		<tr>
			<td width="150px">
				<label for="disable_mail_footer">
					<?php echo JText::_( 'COM_MAILSTER_DISABLE_MAIL_FOOTER' ).':'; ?>
				</label>
			</td>
			<td>
				<?php
				if($pHashOk && $plgHashOk && !$isFree){
					echo JHTML::_('select.booleanlist', 
									'disable_mail_footer', 
									'class="inputbox hTip" title="'.JText::_( 'COM_MAILSTER_DISABLE_MAIL_FOOTER_POWERED_BY_PRO_EDITION' ).'"',
									$this->row->disable_mail_footer );
				}else{
					echo '<span class="hTip" title="'.JText::_( 'COM_MAILSTER_DISABLE_MAIL_FOOTER_POWERED_BY_PRO_EDITION' ).'">' . JText::_( 'COM_MAILSTER_AVAILABLE_IN_PRO_EDITION' ) . '</span>';
				
				} ?>
			</td>
			<td colspan="1">&nbsp;</td>
		</tr>
		<tr>
			<td colspan="3">&nbsp;</td>
		</tr>
		<tr>	
			<td colspan="3">&nbsp;</td>
		</tr>			
	</table>
	<?php 
		$altTextVars = $mstConfig->isUseAlternativeTextVars();
		
		$txt_email = MstConsts::TEXT_VARIABLES_EMAIL;
		$txt_name = MstConsts::TEXT_VARIABLES_NAME;
		$txt_date = MstConsts::TEXT_VARIABLES_DATE;
		$txt_list = MstConsts::TEXT_VARIABLES_LIST;
		$txt_site = MstConsts::TEXT_VARIABLES_SITE;
		$txt_unsub_url = MstConsts::TEXT_VARIABLES_UNSUBSCRIBE_URL;
		$txt_example = JText::_( 'COM_MAILSTER_FROM_NAME_EMAIL_ON_DATE_EXAMPLE' );
		if($altTextVars){
			$txt_email = MstConsts::TEXT_VARIABLES_EMAIL_ALT;
			$txt_name = MstConsts::TEXT_VARIABLES_NAME_ALT;
			$txt_date = MstConsts::TEXT_VARIABLES_DATE_ALT;
			$txt_list = MstConsts::TEXT_VARIABLES_LIST_ALT;
			$txt_site = MstConsts::TEXT_VARIABLES_SITE_ALT;
			$txt_unsub_url = MstConsts::TEXT_VARIABLES_UNSUBSCRIBE_URL_ALT;
			$txt_example = JText::_( 'COM_MAILSTER_FROM_NAME_EMAIL_ON_DATE_EXAMPLE_ALTERNATIVE_VARIABLES' );
		}
	?>
	<div style="float:right;margin-left:5px;margin-right:5px;border:2px solid darkgrey;padding:5px;">
		<strong><?php echo JText::_( 'COM_MAILSTER_TEXT_VARIABLES' ) . ':'; ?></strong><br/>
		<pre style="display:inline"><?php echo $txt_email; ?></pre> - <?php echo JText::_( 'COM_MAILSTER_SENDER_MAIL' ); ?><br/>
		<pre style="display:inline"><?php echo $txt_name; ?></pre> - <?php echo JText::_( 'COM_MAILSTER_SENDER_NAME' ); ?><br/>	
		<pre style="display:inline"><?php echo $txt_date; ?></pre> - <?php echo JText::_( 'COM_MAILSTER_SEND_TIME' ); ?><br/>	
		<pre style="display:inline"><?php echo $txt_list; ?></pre> - <?php echo JText::_( 'COM_MAILSTER_MAILING_LIST_NAME' ); ?><br/>
		<pre style="display:inline"><?php echo $txt_site; ?></pre> - <?php echo JText::_( 'COM_MAILSTER_JOOMLA_SITE_NAME' ); ?><br/>
		<pre style="display:inline"><?php echo $txt_unsub_url; ?></pre> - <?php echo JText::_( 'COM_MAILSTER_UNSUBSCRIBE_URL' ); ?><br/>
		<br/>
		<strong><?php echo JText::_( 'COM_MAILSTER_EXAMPLE' ) . ':'; ?></strong><br/>
		<pre style="display:inline"><?php echo $txt_example; ?></pre><br/>
	</div>
</div>
	
