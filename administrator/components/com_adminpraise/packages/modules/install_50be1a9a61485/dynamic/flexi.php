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
		'id' => 'flexi',
		'title' => 'Flexi Content',
		'link' => JURI::base() . 'index.php?option=com_flexicontent',
		'type' => 'url',
		'parent_id' => $dynamicParentId,
		'params' => 'menu_image=-1',
		'access' => 0,
		'children' => array(
			array(
				'id' => 'flexiItems',
				'title' => 'Items',
				'link' => JURI::base() . 'index.php?option=com_flexicontent&view=items',
				'type' => 'url',
				'parent_id' => $dynamicParentId + 1,
				'params' => 'menu_image=-1',
				'access' => 0,
				'children' => array()
			),
			array(
				'id' => 'flexiTypes',
				'title' => 'Types',
				'link' => JURI::base() . 'index.php?option=com_flexicontent&view=types',
				'type' => 'url',
				'parent_id' => $dynamicParentId + 1,
				'params' => 'menu_image=-1',
				'access' => 0,
				'children' => array()
			),
			array(
				'id' => 'flexiCats',
				'title' => 'Categories',
				'link' => JURI::base() . 'index.php?option=com_flexicontent&view=categories',
				'type' => 'url',
				'parent_id' => $dynamicParentId + 1,
				'params' => 'menu_image=-1',
				'access' => 0,
				'children' => array()
			),
			array(
				'id' => 'flexiFields',
				'title' => 'Fields',
				'link' => JURI::base() . 'index.php?option=com_flexicontent&view=fields',
				'type' => 'url',
				'parent_id' => $dynamicParentId + 1,
				'params' => 'menu_image=-1',
				'access' => 0,
				'children' => array()
			),
			array(
				'id' => 'flexiTags',
				'title' => 'Tags',
				'link' => JURI::base() . 'index.php?option=com_flexicontent&view=tags',
				'type' => 'url',
				'parent_id' => $dynamicParentId + 1,
				'params' => 'menu_image=-1',
				'access' => 0,
				'children' => array()
			),
			array(
				'id' => 'flexiArchive',
				'title' => 'Archive',
				'link' => JURI::base() . 'index.php?option=com_flexicontent&view=archive',
				'type' => 'url',
				'parent_id' => $dynamicParentId + 1,
				'params' => 'menu_image=-1',
				'access' => 0,
				'children' => array()
			),
			array(
				'id' => 'flexiFiles',
				'title' => 'Files',
				'link' => JURI::base() . 'index.php?option=com_flexicontent&view=filemanager',
				'type' => 'url',
				'parent_id' => $dynamicParentId + 1,
				'params' => 'menu_image=-1',
				'access' => 0,
				'children' => array()
			),
			array(
				'id' => 'flexiTemplates',
				'title' => 'Templates',
				'link' => JURI::base() . 'index.php?option=com_flexicontent&view=templates',
				'type' => 'url',
				'parent_id' => $dynamicParentId + 1,
				'params' => 'menu_image=-1',
				'access' => 0,
				'children' => array()
			),
			array(
				'id' => 'flexiStats',
				'title' => 'Statistics',
				'link' => JURI::base() . 'index.php?option=com_flexicontent&view=stats',
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