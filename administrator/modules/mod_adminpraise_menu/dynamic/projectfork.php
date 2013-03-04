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
		'id' => 'projectfork',
		'title' => 'ProjectFork',
		'link' => JURI::base() . 'index.php?option=com_projectfork',
		'type' => 'url',
		'parent_id' => $dynamicParentId,
		'params' => 'menu_image=-1',
		'access' => 0,
		'children' => array(
			array(
				'id' => 'projectforkControlPanel',
				'title' => 'Control Panel',
				'link' => JURI::base() . 'index.php?option=com_projectfork&amp;section=controlpanel',
				'type' => 'url',
				'parent_id' => $dynamicParentId + 1,
				'params' => 'menu_image=-1',
				'access' => 0,
				'children' => array()
			),
			array(
				'id' => 'projectforkProjects',
				'title' => 'Projects',
				'link' => JURI::base() . 'index.php?option=com_projectfork&amp;section=projects',
				'type' => 'url',
				'parent_id' => $dynamicParentId + 1,
				'params' => 'menu_image=-1',
				'access' => 0,
				'children' => array()
			),
			array(
				'id' => 'projectforkTasks',
				'title' => 'Tasks',
				'link' => JURI::base() . 'index.php?option=com_projectfork&amp;section=tasks',
				'type' => 'url',
				'parent_id' => $dynamicParentId + 1,
				'params' => 'menu_image=-1',
				'access' => 0,
				'children' => array()
			),
			
			array(
				'id' => 'projectforkTime',
				'title' => 'Time',
				'link' => JURI::base() . 'index.php?option=com_projectfork&amp;section=time',
				'type' => 'url',
				'parent_id' => $dynamicParentId + 1,
				'params' => 'menu_image=-1',
				'access' => 0,
				'children' => array()
			),
			array(
				'id' => 'projectforkFiles',
				'title' => 'Files',
				'link' => JURI::base() . 'index.php?option=com_projectfork&amp;section=filemanager',
				'type' => 'url',
				'parent_id' => $dynamicParentId + 1,
				'params' => 'menu_image=-1',
				'access' => 0,
				'children' => array()
			),
			array(
				'id' => 'projectforkCalendar',
				'title' => 'Calendar',
				'link' => JURI::base() . 'index.php?option=com_projectfork&amp;section=calendar',
				'type' => 'url',
				'parent_id' => $dynamicParentId + 1,
				'params' => 'menu_image=-1',
				'access' => 0,
				'children' => array()
			),
			array(
				'id' => 'projectforkMessages',
				'title' => 'Messages',
				'link' => JURI::base() . 'index.php?option=com_projectfork&amp;section=board',
				'type' => 'url',
				'parent_id' => $dynamicParentId + 1,
				'params' => 'menu_image=-1',
				'access' => 0,
				'children' => array()
			),
			array(
				'id' => 'projectforkProfile',
				'title' => 'Profile',
				'link' => JURI::base() . 'index.php?option=com_projectfork&amp;section=profile',
				'type' => 'url',
				'parent_id' => $dynamicParentId + 1,
				'params' => 'menu_image=-1',
				'access' => 0,
				'children' => array()
			),
			array(
				'id' => 'projectforkUsers',
				'title' => 'Users',
				'link' => JURI::base() . 'index.php?option=com_projectfork&amp;section=users',
				'type' => 'url',
				'parent_id' => $dynamicParentId + 1,
				'params' => 'menu_image=-1',
				'access' => 0,
				'children' => array()
			),
			array(
				'id' => 'projectforkGroups',
				'title' => 'Groups',
				'link' => JURI::base() . 'index.php?option=com_projectfork&amp;section=groups',
				'type' => 'url',
				'parent_id' => $dynamicParentId + 1,
				'params' => 'menu_image=-1',
				'access' => 0,
				'children' => array()
			),
			array(
				'id' => 'projectforkConfig',
				'title' => 'Config',
				'link' => JURI::base() . 'index.php?option=com_projectfork&amp;section=config',
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