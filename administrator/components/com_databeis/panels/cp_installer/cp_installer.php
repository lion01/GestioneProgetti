<?php
/**
* $Id: cp_installer.php 837 2010-11-17 12:03:35Z eaxs $
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

require_once(PFobject::GetHelper('config'));

$form = new PFform('pf_installer', NULL, 'post', 'enctype="multipart/form-data"');
$form->SetBind(false);

$return = base64_encode(PFformat::Link('section=controlpanel'));

$html = $form->Start()
      . '<table class="pf_table" width="100%"><thead>'
      . '<tr><th class="sectiontableheader title" width="100%">'.PFformat::Lang('CP_INSTALLER_DESC').'</th>'
      . '<th class="sectiontableheader title">'.PFconfigHelper::SelectAutoEnable('auto_enable', 1).'</th>'
      . '</tr></thead><tbody>'
      . '<tr><td colspan="2">'
      . $form->FileField('pack', '', 'size="20"')
      . '&nbsp;<input type="submit" class="pf_button" value="'.PFformat::Lang('INSTALL').'"/>'
      . $form->HiddenField('option', 'com_databeis')
      . $form->HiddenField('section', 'config')
      . $form->HiddenField('task', 'task_install')
      . $form->HiddenField('return', $return)
      . '</td></tr></tbody></table>'
      . $form->End();

if(!defined('PF_DEMO_MODE')) echo $html;
?>