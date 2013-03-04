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

jimport('joomla.html.parameter');
require_once (dirname(__FILE__) . DS . 'helper.php');

class modAPMenuHelper {

	public static $class;

	public function setParams($params) {
		self::$class = $params->get('moduleclass_sfx');
	}

	function getMenu($menuType) {
		$db = &JFactory::getDBO();

		$sql = "SELECT m.id, " .
				"	m.title, " .
				"	m.link, " .
				"	m.type, " .
				"	m.parent_id, " .
				"	m.params, " .
				"	m.access " .
				"FROM #__adminpraise_menu AS m " .
				"WHERE m.menutype = " . $db->quote($menuType) .
				" AND m.published = 1 " .
				"ORDER BY m.parent_id, m.ordering ";

		$db->setQuery($sql);

		$menuRows = $db->loadAssocList();

		$menu = modAPMenuHelper::_buildMenu($menuRows);

		/*
		  $menu = array(
		  array('id' => 1, 'name' => 'Site', 'url' => 'index.php', 'children' => array()),
		  array('id' => 1, 'name' => 'Site', 'url' => 'index.php', 'children' => array()),
		  array('id' => 1, 'name' => 'Site', 'url' => 'index.php', 'children' => array()),
		  );
		 */

		return $menu;
	}

	function &_buildMenu($menuRows) {
		$menuItems = array();
		$menu = array();
		$dynamic = false;
		$user = &JFactory::getUser();
		$userGroups = $user->getAuthorisedGroups();
		foreach ($menuRows as $menuRow) {
			// This makes a copy
			$menuItem = $menuRow;

//			if no access value is saved in the database - allow everyone
//			else make the check if the user has the rights
			if(strlen($menuItem['access'])) {
				$groups = explode(',', $menuItem['access']);
				if(!array_intersect($groups, $userGroups)) {
					$access = false;
				} else {
					$access = true;
				}
			} else {
				$access = true;
			}

			if ($access) {

				$menuItem['children'] = array();

				// get the dynamic menu and add it to the array later on
				if ($menuItem['type'] == 'dynamic') {
					$dynamicParentId = $menuItem['parent_id'];
					require($menuItem['link']);
					$dynamicMenuItems = $menuDynamic;
					$dynamic = true;
				}

				$parentId = $menuItem['parent_id'];
				if ($parentId == 0) {
					if ($dynamic) {
						foreach ($dynamicMenuItems as $value) {
							$menu[] = $value;
						}
					} else {
						$menu[] = &$menuItem;
					}
				} else if (array_key_exists($parentId, $menuItems)) {
					if ($dynamic) {
						foreach ($dynamicMenuItems as $value) {
							$menuItems[$parentId]['children'][] = $value;
						}
					} else {
						$menuItems[$parentId]['children'][] = &$menuItem;
					}
				}

				$menuItems[$menuItem['id']] = &$menuItem;
				unset($menuItem);
				$dynamic = false;
				$dynamicMenuItems = '';
			}
		}

		return $menu;
	}

	function renderMenu($menu, $top = false) {
		if ($top) {
			if (self::$class) {
				echo '<ul class="' . self::$class . '">';
			} else {
				echo "<ul>\n";
			}
		}

		$childCount = count($menu);

		for ($i = 0; $i < $childCount; $i++) {
			modAPMenuHelper::renderMenuItem($menu[$i]);
		}

		if ($top) {
			echo "</ul>\n";
		}
	}

	function renderMenuItem($menuItem) {
		$classes = array();
		$childCount = count($menuItem['children']);
		$menuItemParams = new JParameter($menuItem['params']);
		$menuImage = $menuItemParams->get('menu_image');
		$menuClass = $menuItemParams->get('menu_class');

		if ($menuClass) {
			$classes[] = $menuClass;
		}
		if ($childCount > 0) {
			$classes[] = 'parent';
		}
		if (strpos($menuItem['id'], 'components') === 0) {
			$classes[] = $menuItem['option'];
		}

		if ($menuItem['type'] == 'separator') {
			$classes[] = 'separator';
			echo '<li class="' . implode(' ', $classes) . '"><a><span>' . $menuItem['title'] . '</span></a>';

			if ($childCount > 0) {
				self::submenu($childCount, $menuItem['children']);
			}
			echo '</li>';
		} else {

			$anchorExtra = "";

			// Use onclick for any javascript
			if (strpos($menuItem['link'], "javascript:") === 0) {
				$href = "#";
				$anchorExtra = "onclick=\"" . $menuItem['link'] . "\"";
			} else {
				$href = $menuItem['link'];
			}

			echo "<li class=\"" . implode(' ', $classes) . "\">\n";
			echo "<a class=\"" . str_replace(' ', '', $menuItem['title']) . "\" " . $anchorExtra . " href=\"" . $href . "\">";

			if ($menuImage == -1) {
				echo '<span class="component-image"></span>';
			} else {
				echo '<img src="' . JURI::root() . '/media/com_adminpraise/images/menu/' . $menuImage . '" class="menu-image" />';
			}


			if ($childCount > 0) {
				echo '<span class="parent-name">' . JText::_($menuItem['title']) . '</span>';
			} else {
				if ($menuItem['parent_id'] == 0) {
					echo '<span class="no-parent">';
				}
				echo JText::_($menuItem['title']);
				if ($menuItem['parent_id'] == 0) {
					echo '</span>';
				}
			}

			if ($childCount > 0) {
				echo '<span class="subarrow"></span>';
			}
			echo '</a>';

			if ($childCount > 0) {
				self::submenu($childCount, $menuItem['children']);
			}

			echo '</li>';
		}
	}

	public function submenu($childCount, $menuItemChildren) {
		echo '<ul class="submenu">';
		echo '<li class="submenu-arrow"></li>';
		for ($i = 0; $i < $childCount; $i++) {
			modAPMenuHelper::renderMenuItem($menuItemChildren[$i]);
		}
		echo '</ul>';
	}

}

