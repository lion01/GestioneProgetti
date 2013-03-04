<?php
/**
* $Id: project_desc.php 837 2010-11-17 12:03:35Z eaxs $
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

$id = (int) JRequest::getVar('id');

if($id) {
    $db = PFdatabase::GetInstance();
    $config = PFconfig::GetInstance();
    
    $query = "SELECT content FROM #__pf_projects"
           . "\n WHERE id = '$id'";
           $db->setQuery($query);
           $desc = $db->loadResult();
           
    if($config->Get('use_editor', 'projects') == '0') {
        echo nl2br($desc);
    }
    else {
        echo $desc;
    }
    unset($db,$config,$desc);
}
?>