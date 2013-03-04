<?php
/**
* $Id: time_tracking.php 899 2011-07-12 15:09:50Z eaxs $
* @package   Databeis
* @copyright Copyright (C) 2006-2010 DataBeis. All rights reserved.
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL, see LICENSE.php
*
* This file is part of Databeis.
*
* Databeis is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
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

$user 	= PFuser::GetInstance();
$lang 	= PFlanguage::GetInstance();
$com  	= Pfcomponent::GetInstance();
$config = PFconfig::GetInstance();

$date_format 	= $config->Get('date_format');
$now 			= strftime($date_format);

$form = new PFform('quick_track', 'index.php?option='.$com->Get('name').'&section=time');
$uri = JFactory::getURI();
$ret = $uri->toString();

if($user->Access('form_new', 'time')) {

    $html = $form->Start()
          . "<span>"
          . $form->SelectTask('time_task', NULL, 'id="pf_time_panel_task"');
	if ($now == $date_format){
		$html .= JHTML::calendar(JHTML::_('date', "", PFformat::JhtmlCalendarDateFormat()), 'cdate', 'cdate')."&nbsp;";
	}
	else {
		$html .= JHTML::calendar($now, 'cdate', 'cdate', $date_format)."&nbsp;";
	}
    $html .= $form->SelectHour('hours', NULL, 'id="pf_time_panel_hour"')."&nbsp;"
          . $form->SelectMinute('minutes', NULL, 'id="pf_time_panel_minute"')
          . "</span><br /><br /><span>"
          . $form->InputField('text', $lang->_('PFL_QUICK_NOTE'), 'id="pf_time_panel_note"')
          . "</span>"
          . "&nbsp; <input type='submit' value='".$lang->_('SAVE')."' class='pf_button' />"
          . $form->HiddenField("option", $com->Get('name'))
          . $form->HiddenField("section", 'time')
          . $form->HiddenField("task", 'task_save')
          . $form->HiddenField("return", base64_encode($ret))
          . $form->End();

    echo $html;
}
unset($user,$lang,$com,$form);
?>