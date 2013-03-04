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
		'id' => 'tienda',
		'title' => 'Tienda',
		'link' => JURI::base() . 'index.php?option=com_tienda',
		'type' => 'url',
		'parent_id' => $dynamicParentId,
		'params' => 'menu_image=-1',
		'access' => 0,
		'children' => array(
			array(
				'id' => 'tiendaProducts',
				'title' => 'Products',
				'link' => JURI::base() . 'index.php?option=com_tienda&view=products',
				'type' => 'url',
				'parent_id' => $dynamicParentId + 1,
				'params' => 'menu_image=-1',
				'access' => 0,
				'children' => array()
			),
			array(
				'id' => 'tiendaCategories',
				'title' => 'Categories',
				'link' => JURI::base() . 'index.php?option=com_tienda&view=categories',
				'type' => 'url',
				'parent_id' => $dynamicParentId + 1,
				'params' => 'menu_image=-1',
				'access' => 0,
				'children' => array()
			),
			
			array(
				'id' => 'tiendaOrders',
				'title' => 'Orders',
				'link' => JURI::base() . 'index.php?option=com_tienda&view=orders',
				'type' => 'url',
				'parent_id' => $dynamicParentId + 1,
				'params' => 'menu_image=-1',
				'access' => 0,
				'children' => array()
			),
			array(
				'id' => 'tiendaUsers',
				'title' => 'Users',
				'link' => JURI::base() . 'index.php?option=com_tienda&view=users',
				'type' => 'url',
				'parent_id' => $dynamicParentId + 1,
				'params' => 'menu_image=-1',
				'access' => 0,
				'children' => array()
			),
			array(
				'id' => 'tiendaManufacturers',
				'title' => 'Manufacturers',
				'link' => JURI::base() . 'index.php?option=com_tienda&view=manufacturers',
				'type' => 'url',
				'parent_id' => $dynamicParentId + 1,
				'params' => 'menu_image=-1',
				'access' => 0,
				'children' => array()
			),
			array(
				'id' => 'tiendaLocalization',
				'title' => 'Localization',
				'link' => JURI::base() . 'index.php?option=com_tienda&view=localization',
				'type' => 'url',
				'parent_id' => $dynamicParentId + 1,
				'params' => 'menu_image=-1',
				'access' => 0,
				'children' => array()
			),
			array(
				'id' => 'tiendaTools',
				'title' => 'Tools',
				'link' => JURI::base() . 'index.php?option=com_tienda&view=tools',
				'type' => 'url',
				'parent_id' => $dynamicParentId + 1,
				'params' => 'menu_image=-1',
				'access' => 0,
				'children' => array()
			),
			array(
				'id' => 'tiendaConfig',
				'title' => 'Configuration',
				'link' => JURI::base() . 'index.php?option=com_tienda&view=config',
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