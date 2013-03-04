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

// Borrowed from html/mod_menu/helper.php
$lang	= JFactory::getLanguage();
$db		= JFactory::getDbo();
$query	= $db->getQuery(true);
$result	= array();
$langs	= array();

$query->select('m.id, m.title, m.alias, m.link, m.parent_id as parent, m.img, e.element');
$query->from('#__menu AS m');

// Filter on the enabled states.
$query->leftJoin('#__extensions AS e ON m.component_id = e.extension_id');
$query->where('m.client_id = 1');
$query->where('e.enabled = 1');
$query->where('m.id > 1');

// Order by lft.
$query->order('m.lft');

$db->setQuery($query);
$components = $db->loadAssocList();

$menuItemsDynamic = array();
$menuDynamic = array();
for ($i = 0; $i < count($components); $i++) {
	$component = $components[$i];

		$menuItemDynamic = array(
						'id' => 'components' . $i,
						'option' => $component['element'],
						'title' => $component['title'],
						'link' => $component['link'],
						'type' => 'url',
						'parent_id' => $dynamicParentId,
						'params' => 'menu_image=-1',
						'access' => 0,
						'children' => array()
					);

		$parentId = $component['parent'];
		if ($parentId == 1) {
			
			if (!empty($component['element'])) {
			// Load the core file then
			// Load extension-local file.
				$lang->load($component['element'].'.sys', JPATH_BASE, null, false, false)
			||	$lang->load($component['element'].'.sys', JPATH_ADMINISTRATOR.'/components/'.$component['element'], null, false, false)
			||	$lang->load($component['element'].'.sys', JPATH_BASE, $lang->getDefault(), false, false)
			||	$lang->load($component['element'].'.sys', JPATH_ADMINISTRATOR.'/components/'.$component['element'], $lang->getDefault(), false, false);
			}
			
			$title = $lang->hasKey($menuItemDynamic['title']) ? JText::_($menuItemDynamic['title']) : $menuItemDynamic['title'];
			$menuItemDynamic['title'] = $title;
			$menuDynamic[] = &$menuItemDynamic;
		} else if (array_key_exists($parentId, $menuItemsDynamic)) {
			$menuItemDynamic['parent_id'] = $parentId;
			$title = $lang->hasKey($menuItemDynamic['title']) ? JText::_($menuItemDynamic['title']) : $menuItemDynamic['title'];

			$menuItemDynamic['name'] = $title;
			$menuItemsDynamic[$parentId]['children'][] = &$menuItemDynamic;
		}

		$menuItemsDynamic[$component['id']] = &$menuItemDynamic;
		unset($menuItemDynamic);
}
?>