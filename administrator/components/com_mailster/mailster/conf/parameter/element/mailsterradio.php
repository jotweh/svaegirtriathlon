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
	
	class MstElementMailsterRadio extends JElement
	{
		/**
		 * Element name
		 *
		 * @access	protected
		 * @var		string
		 */
		var	$_name = 'radio';
	
		function fetchElement($name, $value, &$node, $control_name)
		{
			$options = array ();
			foreach ($node->children() as $option)
			{
				$val	= $option->attributes('value');
				$text	= $option->data();
				$options[] = JHTML::_('select.option', $val, JText::_($text));
			}
	
			return $this->getRadioButtonHtml($options, ''.$control_name.'['.$name.']', '', 'value', 'text', $value, $control_name.$name );
		}
		
		function getRadioButtonHtml( $arr, $name, $attribs = null, $key = 'value', $text = 'text', $selected = null, $idtag = false, $translate = false )
		{
			reset( $arr );
			$html = '';
	
			if (is_array($attribs)) {
				$attribs = JArrayHelper::toString($attribs);
			 }
	
			$id_text = $name;
			if ( $idtag ) {
				$id_text = $idtag;
			}
	
			for ($i=0, $n=count( $arr ); $i < $n; $i++ )
			{
				$k	= $arr[$i]->$key;
				$t	= $translate ? JText::_( $arr[$i]->$text ) : $arr[$i]->$text;
				$id	= ( isset($arr[$i]->id) ? @$arr[$i]->id : null);
	
				$extra	= '';
				$extra	.= $id ? " id=\"" . $arr[$i]->id . "\"" : '';
				if (is_array( $selected ))
				{
					foreach ($selected as $val)
					{
						$k2 = is_object( $val ) ? $val->$key : $val;
						if ($k == $k2)
						{
							$extra .= " selected=\"selected\"";
							break;
						}
					}
				} else {
					$extra .= ((string)$k == (string)$selected ? " checked=\"checked\"" : '');
				}
				$html .= "\n\t<input type=\"radio\" name=\"$name\" id=\"$id_text$k\" value=\"".$k."\"$extra style=\"display:inline;float:none;\" $attribs />";
				$html .= "\n\t<label for=\"$id_text$k\" style=\"min-width:0px;display:inline;float:none;\">$t</label>";
			}
			$html .= "\n";
			return $html;
		}
	}

?>