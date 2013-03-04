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

$menuDynamic = array(
	array(
		'id' => 'ninjaboard',
		'title' => 'Ninjaboard',
		'link' => JURI::base() . 'index.php?option=com_ninjaboard&view=dashboard',
		'type' => 'url',
		'parent_id' => $dynamicParentId,
		'params' => 'menu_image=-1',
		'access' => 0,
		'children' => array(
			array(
				'id' => 'ninjaboardForums',
				'title' => 'Forums',
				'link' => JURI::base() . 'index.php?option=com_ninjaboard&view=forums',
				'type' => 'url',
				'parent_id' => $dynamicParentId + 1,
				'params' => 'menu_image=-1',
				'access' => 0,
				'children' => array()
			),
			array(
				'id' => 'ninjaboardNew',
				'title' => 'New forum',
				'link' => JURI::base() . 'index.php?option=com_ninjaboard&view=forum',
				'type' => 'url',
				'parent_id' => $dynamicParentId + 1,
				'params' => 'menu_image=-1',
				'access' => 0,
				'children' => array()
			),
			
			array(
				'id' => 'ninjaboardUsers',
				'title' => 'Users',
				'link' => JURI::base() . 'index.php?option=com_ninjaboard&view=users',
				'type' => 'url',
				'parent_id' => $dynamicParentId + 1,
				'params' => 'menu_image=-1',
				'access' => 0,
				'children' => array()
			),
			array(
				'id' => 'ninjaboardUserGroups',
				'title' => 'User groups',
				'link' => JURI::base() . 'index.php?option=com_ninjaboard&view=usergroups',
				'type' => 'url',
				'parent_id' => $dynamicParentId + 1,
				'params' => 'menu_image=-1',
				'access' => 0,
				'children' => array()
			),
			array(
				'id' => 'ninjaboardRanks',
				'title' => 'Ranks',
				'link' => JURI::base() . 'index.php?option=com_ninjaboard&view=ranks',
				'type' => 'url',
				'parent_id' => $dynamicParentId + 1,
				'params' => 'menu_image=-1',
				'access' => 0,
				'children' => array()
			),
			array(
				'id' => 'ninjaboardTools',
				'title' => 'Tools',
				'link' => JURI::base() . 'index.php?option=com_ninjaboard&view=tools',
				'type' => 'url',
				'parent_id' => $dynamicParentId + 1,
				'params' => 'menu_image=-1',
				'access' => 0,
				'children' => array()
			),
			array(
				'id' => 'ninjaboardThemes',
				'title' => 'Themes',
				'link' => JURI::base() . 'index.php?option=com_ninjaboard&view=themes',
				'type' => 'url',
				'parent_id' => $dynamicParentId + 1,
				'params' => 'menu_image=-1',
				'access' => 0,
				'children' => array()
			),
			array(
				'id' => 'ninjaboardConfig',
				'title' => 'Configuration',
				'link' => JURI::base() . 'index.php?option=com_ninjaboard&view=settings',
				'type' => 'url',
				'parent_id' => $dynamicParentId + 1,
				'params' => 'menu_image=-1',
				'access' => 0,
				'children' => array()
			)
		)
	)
);
?>