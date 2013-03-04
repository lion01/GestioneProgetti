<?php
/**
 * @package		AdminPraise3
 * @author		AdminPraise http://www.adminpraise.com
 * @copyright	Copyright (c) 2008 - 2011 Pixel Praise LLC. All rights reserved.
 * @license		GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 */
 
 /**
 *    This file is part of AdminPraise.
 *    
 *    AdminPraise is distributed in the hope that it will be useful,
 *    but WITHOUT ANY WARRANTY; without even the implied warranty of
 *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *    GNU General Public License for more details.
 *
 *    You should have received a copy of the GNU General Public License
 *    along with AdminPraise.  If not, see <http://www.gnu.org/licenses/>.
 *
 **/
defined('_JEXEC') or die('Restricted access');

$db = &JFactory::getDBO();
$sql = 
	"SELECT menutype, ".
	"	title ".
	"FROM #__menu_types ".
	"ORDER BY title";
$db->setQuery($sql);
$menuTypes = $db->loadAssocList();

$menuDynamic = array();
for($i = 0; $i < count($menuTypes); $i++)
{
	$menuType = $menuTypes[$i];

	$menuDynamic[] = 
		array(
			'id' => 'menus'.$i, 
			'title' => $menuType['title'],
			'link' => 'index.php?option=com_menus&view=items&menutype='.$menuType['menutype'],
			'type' => 'url', 
			'parent_id' => $dynamicParentId, 
			'params' => 'menu_image=-1', 
			'access' => 0,
			'children' => array()
		);
}

?>

