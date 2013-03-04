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
		'id' => 'sobi2',
		'title' => 'Sobi2 directory',
		'link' => JURI::base() . 'index.php?option=com_sobi2&task=listing&catid=-1',
		'type' => 'url',
		'parent_id' => $dynamicParentId,
		'params' => 'menu_image=-1',
		'access' => 0,
		'children' => array(
			array(
				'id' => 'sobi2All',
				'title' => 'Entries awaiting approval',
				'link' => JURI::base() . 'index.php?option=com_sobi2&task=getUnapproved',
				'type' => 'url',
				'parent_id' => $dynamicParentId + 1,
				'params' => 'menu_image=-1',
				'access' => 0,
				'children' => array()
			),
			array(
				'id' => 'sobi2GenConf',
				'title' => 'General Configuration',
				'link' => JURI::base() . 'index.php?option=com_sobi2&task=genConf',
				'type' => 'url',
				'parent_id' => $dynamicParentId + 1,
				'params' => 'menu_image=-1',
				'access' => 0,
				'children' => array()
			),
			
			array(
				'id' => 'sobi2CustomFields',
				'title' => 'Custom Fields Manager',
				'link' => JURI::base() . 'index.php?option=com_sobi2&task=editFields',
				'type' => 'url',
				'parent_id' => $dynamicParentId + 1,
				'params' => 'menu_image=-1',
				'access' => 0,
				'children' => array()
			),
			array(
				'id' => 'sobi2AddEntry',
				'title' => 'Add Entry',
				'link' => JURI::base() . 'index2.php?option=com_sobi2&task=addItem&returnTask=',
				'type' => 'url',
				'parent_id' => $dynamicParentId + 1,
				'params' => 'menu_image=-1',
				'access' => 0,
				'children' => array()
			),
			array(
				'id' => 'sobi2AddCat',
				'title' => 'Add Category',
				'link' => JURI::base() . 'index2.php?option=com_sobi2&task=addCat&returnTask=',
				'type' => 'url',
				'parent_id' => $dynamicParentId + 1,
				'params' => 'menu_image=-1',
				'access' => 0,
				'children' => array()
			),
			array(
				'id' => 'sobi2Templates',
				'title' => 'Template Manager',
				'link' => JURI::base() . 'index2.php?option=com_sobi2&task=templates',
				'type' => 'url',
				'parent_id' => $dynamicParentId + 1,
				'params' => 'menu_image=-1',
				'access' => 0,
				'children' => array()
			),
			array(
				'id' => 'sobi2PluginManager',
				'title' => 'Plugin Manager',
				'link' => JURI::base() . 'index2.php?option=com_sobi2&task=pluginsManager',
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