<?php
/**
* $Id: config.helper.php 531 2010-05-02 01:24:07Z eaxs $
* @package    Databeis
* @subpackage Config
* @copyright  Copyright (C) 2006-2010 DataBeis. All rights reserved.
* @license    http://www.gnu.org/licenses/gpl.html GNU/GPL, see LICENSE.php
*
* This file is part of Databeis.
*
* Databeis is free software: you can redistribute it and/or modify
* it under the terms of the GNU General Public License License as published by
* the Free Software Foundation, either version 3 of the License,
* or any later version.
*
* Databeis is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License
* along with Databeis.  If not, see <http://www.gnu.org/licenses/gpl.html>.
**/

defined( '_JEXEC' ) or die( 'Restricted access' );

class PFconfigHelper
{
    public function HTMLparams($params, $cfg_scope)
    {
        if(!is_object($params)) {
			$html = '<fieldset class="adminform">
                         <legend>'.PFformat::Lang('PARAMETERS').'</legend>
                         <table class="admintable" width="100%">
                             <tbody>
                                 <tr>
                                     <td align="center">'.PFformat::Lang('PARAMS_NA').'</td>
                                 </tr>
                             </tbody>
                         </table>
                     </fieldset>';
		
		    return $html;
		}
		
		return "";
		// TODO
		
		// let see if we need the sliders
		$sliders = false;
		jimport('joomla.html.pane');
		$pane    =& JPane::getInstance('sliders');
		
		foreach ($params AS $param)
		{
			if($param['type'] == 'slider_start') {
				$sliders = true;
				
			}
		}
		
		$html = "<fieldset class=\"adminform\"><legend>".PFL_PARAMETERS."</legend>";
		if($sliders) {
			$html .= $pane->startPane("content-pane");
		}
		$html .= "<table class='admintable' width='100%'><tbody>";
		
		foreach ($params AS $param)
		{
			JFilterInput::clean($param, 'array');
			switch ($param['type']) 
			{
				case 'text':
					$v  = $param['value'];
					$v2 = $this->_config->get($param['name'], $scope);
					$size = $param['size'];
					
					if(!$size) { $size = "50"; }
					if(!is_null($v2)) { $v = $v2; }

					$html .= "<tr>";
					$html .= "<td class='key' width='150'>".strtoconst(htmlspecialchars($param['title']))."</td>";
				    $html .= "<td valign='top'><input type='text' value='".htmlspecialchars(strtoconst($v), ENT_QUOTES)."' name='params[".htmlspecialchars($param['name'], ENT_QUOTES)."]' size='$size'/></td>";
				    $html .= "<td valign='top'>".htmlspecialchars(strtoconst($param['desc']), ENT_QUOTES)."</td>";
				    $html .= "</tr>";
				    break;
				    
				case 'textarea':
					$v  = $param['value'];
					$v2 = $this->_config->get($param['name'], $scope);
					$rows = $param['rows'];
					$cols = $param['cols'];
					
					if(!$rows) { $rows = 10; }
					if(!$cols) { $cols = 30; }
					if(!is_null($v2)) { $v = $v2; }
					$html .= "<tr>";
					$html .= "<td class='key' width='150' valign='top'>".strtoconst(htmlspecialchars($param['title']))."</td>";
				    $html .= "<td valign='top'><textarea name='params[".htmlspecialchars(strtoconst($param['name']), ENT_QUOTES)."]' rows='$rows' cols='$cols'>".$v."</textarea></td>";
				    $html .= "<td valign='top'>".htmlspecialchars(strtoconst($param['desc']), ENT_QUOTES)."</td>";
				    $html .= "</tr>";
					break;
					
				case 'select':
					$options = $param['options'];
					$v       = $this->_config->get($param['name'], $scope);
					$size    = "";
					
					if($param['multiple']) {
						$multiple = "multiple='multiple'";
					}
					else {
						$multiple = "";
					}
					
					if($param['size']) { $size = "size='$size'"; }
					
					$html .= "<tr>";
					$html .= "<td class='key' width='150' valign='top'>".strtoconst(htmlspecialchars($param['title']))."</td>";
				    $html .= "<td valign='top'>";
				    $html .= "<select name='params[".htmlspecialchars($param['name'], ENT_QUOTES)."]' $multiple $size>";
				    foreach ($options AS $option)
				    {
                        $ps = "";
                        if($v == $option['value']) { $ps = "selected='selected'"; }
				    	$html .= "<option value='".htmlspecialchars($option['value'], ENT_QUOTES)."' $ps>".strtoconst($option['title'])."</option>";
				    }
				    $html .= "</select>";
				    $html .= "</td>";
				    $html .= "<td valign='top'>".htmlspecialchars(strtoconst($param['desc']), ENT_QUOTES)."</td>";
				    $html .= "</tr>";
					break;	

				case 'separator':
					$html .= "<tr>";
					$html .= "<td class='key' width='150'>".strtoconst(htmlspecialchars($param['title']))."</td>";
				    $html .= "<td><hr/></td>";
				    $html .= "</tr>";
					break;
					
				case 'slider_start':
					$html .= "<tr>";
					$html .= "<td colspan='3'>".$pane->startPanel( strtoconst(htmlspecialchars($param['title'])), 'slider'.htmlspecialchars($param['id']) );
				    $html .= "<table class='admintable' width='100%'><tbody>";
					break;
					
				case 'slider_end':
					$html .= "</tbody></table>";
					$html .= $pane->endPanel();
					$html .= "</td></tr>";
					break;		
			}
		}
		
		
		$html .= "</tbody></table>";
		
		if($sliders) {
			$html .= $pane->endPane();
		}
		
		$html .= "</fieldset>";
		
		return $html;
    }
}
?>