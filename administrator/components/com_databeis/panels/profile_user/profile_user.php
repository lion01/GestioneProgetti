<?php
/**
* $Id: profile_user.php 837 2010-11-17 12:03:35Z eaxs $
* @package   Databeis
* @copyright Copyright (C) 2006-2009 DataBeis. All rights reserved.
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

// Load objects
$db     = PFdatabase::GetInstance();
$config = PFconfig::GetInstance();

// Get profile id
$id = (int) JRequest::getVar('id');

if($id) {
    $query = "SELECT * FROM #__users WHERE id = '$id'";
           $db->setQuery($query);
           $user = $db->loadObject();

    $avatar = PFavatar::Display($id, false);
         
    if(is_object($user)) {
        $html = '<table class="admintable">';
        if((int) $config->Get('display_avatar')) {
            $html .= '<tr>
                    <td class="key" width="100" valign="top">'.PFformat::Lang('AVATAR').'</td>
                    <td>'.$avatar.'</td>
                </tr>';
        }
        $html .= '         
            <tr>
                <td class="key" width="100">'.PFformat::Lang('NAME').'</td>
                <td>'.htmlspecialchars($user->name).'</td>
            </tr>
            <tr>
                <td class="key" width="100">'.PFformat::Lang('USERNAME').'</td>
                <td>'.htmlspecialchars($user->username).'</td>
            </tr>
        </table>';
        echo $html;
        unset($html,$user);
    }
}
unset($db);
?>