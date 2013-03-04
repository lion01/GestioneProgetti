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
		'id' => 'k2',
		'title' => 'K2',
		'link' => JURI::base() . 'index.php?option=com_k2',
		'type' => 'url',
		'parent_id' => $dynamicParentId,
		'params' => 'menu_image=-1',
		'access' => 0,
		'children' => array(
			array(
				'id' => 'k2AddItems',
				'title' => 'Add new item',
				'link' => JURI::base() . 'index.php?option=com_k2&view=item',
				'type' => 'url',
				'parent_id' => $dynamicParentId + 1,
				'params' => 'menu_image=-1',
				'access' => 0,
				'children' => array()
			),
			array(
				'id' => 'k2Items',
				'title' => 'Items',
				'link' => JURI::base() . 'index.php?option=com_k2&view=items&filter_trash=0',
				'type' => 'url',
				'parent_id' => $dynamicParentId + 1,
				'params' => 'menu_image=-1',
				'access' => 0,
				'children' => array()
			),
			
			array(
				'id' => 'k2FeaturedItems',
				'title' => 'Featured Items',
				'link' => JURI::base() . 'index.php?option=com_k2&view=items&filter_featured=1',
				'type' => 'url',
				'parent_id' => $dynamicParentId + 1,
				'params' => 'menu_image=-1',
				'access' => 0,
				'children' => array()
			),
			array(
				'id' => 'k2TrashedItems',
				'title' => 'Trashed Items',
				'link' => JURI::base() . 'index.php?option=com_k2&view=items&filter_trash=1',
				'type' => 'url',
				'parent_id' => $dynamicParentId + 1,
				'params' => 'menu_image=-1',
				'access' => 0,
				'children' => array()
			),
			array(
				'id' => 'k2Cats',
				'title' => 'Categories',
				'link' => JURI::base() . 'index.php?option=com_k2&view=categories&filter_trash=0',
				'type' => 'url',
				'parent_id' => $dynamicParentId + 1,
				'params' => 'menu_image=-1',
				'access' => 0,
				'children' => array()
			),
			array(
				'id' => 'k2TrashedCategories',
				'title' => 'Trashed Categories',
				'link' => JURI::base() . 'index.php?option=com_k2&view=categories&filter_trash=1',
				'type' => 'url',
				'parent_id' => $dynamicParentId + 1,
				'params' => 'menu_image=-1',
				'access' => 0,
				'children' => array()
			),
			array(
				'id' => 'k2Tags',
				'title' => 'Tags',
				'link' => JURI::base() . 'index.php?option=com_k2&view=tags',
				'type' => 'url',
				'parent_id' => $dynamicParentId + 1,
				'params' => 'menu_image=-1',
				'access' => 0,
				'children' => array()
			),
			array(
				'id' => 'k2Comments',
				'title' => 'Comments',
				'link' => JURI::base() . 'index.php?option=com_k2&view=comments',
				'type' => 'url',
				'parent_id' => $dynamicParentId + 1,
				'params' => 'menu_image=-1',
				'access' => 0,
				'children' => array()
			),
			array(
				'id' => 'k2ExtraFields',
				'title' => 'Extra Fields',
				'link' => JURI::base() . 'index.php?option=com_k2&view=extraFields',
				'type' => 'url',
				'parent_id' => $dynamicParentId + 1,
				'params' => 'menu_image=-1',
				'access' => 0,
				'children' => array()
			),
			array(
				'id' => 'k2ExtraFieldGroups',
				'title' => 'Extra Field Groups',
				'link' => JURI::base() . 'index.php?option=com_k2&view=extraFieldsGroups',
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