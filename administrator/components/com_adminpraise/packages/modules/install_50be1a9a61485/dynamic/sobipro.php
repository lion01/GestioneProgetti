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
		'id' => 'sobiPro',
		'title' => 'SobiPro',
		'link' => JURI::base() . 'index.php?option=com_sobipro',
		'type' => 'url',
		'parent_id' => $dynamicParentId,
		'params' => 'menu_image=-1',
		'access' => 0,
		'children' => array(
			array(
				'id' => 'sobiProEntries',
				'title' => 'All Entries',
				'link' => JURI::base() . 'index.php?option=com_sobipro&task=section.entries&pid=1',
				'type' => 'url',
				'parent_id' => $dynamicParentId + 1,
				'params' => 'menu_image=-1',
				'access' => 0,
				'children' => array()
			),
			array(
				'id' => 'sobiProEntry',
				'title' => 'Add Entry',
				'link' => JURI::base() . 'index.php?option=com_sobipro&task=entry.add&pid=1',
				'type' => 'url',
				'parent_id' => $dynamicParentId + 1,
				'params' => 'menu_image=-1',
				'access' => 0,
				'children' => array()
			),
			
			array(
				'id' => 'sobiProCats',
				'title' => 'Categories',
				'link' => JURI::base() . 'index.php?option=com_sobipro&sid=1',
				'type' => 'url',
				'parent_id' => $dynamicParentId + 1,
				'params' => 'menu_image=-1',
				'access' => 0,
				'children' => array()
			),
			array(
				'id' => 'sobiProAcl',
				'title' => 'ACL',
				'link' => JURI::base() . 'index.php?option=com_sobipro&task=acl',
				'type' => 'url',
				'parent_id' => $dynamicParentId + 1,
				'params' => 'menu_image=-1',
				'access' => 0,
				'children' => array()
			),
			array(
				'id' => 'sobiProTemplates',
				'title' => 'Templates',
				'link' => JURI::base() . 'index.php?option=com_sobipro&task=template.edit',
				'type' => 'url',
				'parent_id' => $dynamicParentId + 1,
				'params' => 'menu_image=-1',
				'access' => 0,
				'children' => array()
			),
			array(
				'id' => 'sobiProConfig',
				'title' => 'Configuration',
				'link' => JURI::base() . 'index.php?option=com_sobipro&task=config.general',
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