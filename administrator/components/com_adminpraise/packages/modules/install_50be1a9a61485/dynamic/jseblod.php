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
		'id' => 'jseblod',
		'title' => 'jseblod Content',
		'link' => JURI::base() . 'index.php?option=com_cckjseblod',
		'type' => 'url',
		'parent_id' => $dynamicParentId,
		'params' => 'menu_image=-1',
		'access' => 0,
		'children' => array(
			array(
				'id' => 'jseblodAddItems',
				'title' => 'Add new content',
				'link' => JURI::base() . 'index.php?option=com_cckjseblod&controller=interface&act=-1&cck=1',
				'type' => 'url',
				'parent_id' => $dynamicParentId + 1,
				'params' => 'menu_image=-1',
				'access' => 0,
				'children' => array()
			),
			array(
				'id' => 'jseblodItems',
				'title' => 'Items',
				'link' => JURI::base() . 'index.php?option=com_content',
				'type' => 'url',
				'parent_id' => $dynamicParentId + 1,
				'params' => 'menu_image=-1',
				'access' => 0,
				'children' => array()
			),
			
			array(
				'id' => 'jseblodTemplates',
				'title' => 'Templates',
				'link' => JURI::base() . 'index.php?option=com_cckjseblod&controller=templates',
				'type' => 'url',
				'parent_id' => $dynamicParentId + 1,
				'params' => 'menu_image=-1',
				'access' => 0,
				'children' => array()
			),
			array(
				'id' => 'jseblodContentTypes',
				'title' => 'Content Types',
				'link' => JURI::base() . 'index.php?option=com_cckjseblod&controller=types',
				'type' => 'url',
				'parent_id' => $dynamicParentId + 1,
				'params' => 'menu_image=-1',
				'access' => 0,
				'children' => array()
			),
			array(
				'id' => 'jseblodFields',
				'title' => 'Fields',
				'link' => JURI::base() . 'index.php?option=com_cckjseblod&controller=items',
				'type' => 'url',
				'parent_id' => $dynamicParentId + 1,
				'params' => 'menu_image=-1',
				'access' => 0,
				'children' => array()
			),
			array(
				'id' => 'jseblodSearchTypes',
				'title' => 'Search types',
				'link' => JURI::base() . 'index.php?option=com_cckjseblod&controller=searchs',
				'type' => 'url',
				'parent_id' => $dynamicParentId + 1,
				'params' => 'menu_image=-1',
				'access' => 0,
				'children' => array()
			),
			array(
				'id' => 'jseblodPacks',
				'title' => 'Pack',
				'link' => JURI::base() . 'index.php?option=com_cckjseblod&controller=packs',
				'type' => 'url',
				'parent_id' => $dynamicParentId + 1,
				'params' => 'menu_image=-1',
				'access' => 0,
				'children' => array()
			),
			array(
				'id' => 'jseblodConfig',
				'title' => 'Config',
				'link' => JURI::base() . 'index.php?option=com_cckjseblod&controller=configuration',
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