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
		'id' => 'phocagallery',
		'title' => 'PhocaGallery',
		'link' => JURI::base() . 'index.php?option=com_phocagallery',
		'type' => 'url',
		'parent_id' => $dynamicParentId,
		'params' => 'menu_image=-1',
		'access' => 0,
		'children' => array(
			array(
				'id' => 'phocagalleryImages',
				'title' => 'Images',
				'link' => JURI::base() . 'index.php?option=com_phocagallery&view=phocagallerys',
				'type' => 'url',
				'parent_id' => $dynamicParentId + 1,
				'params' => 'menu_image=-1',
				'access' => 0,
				'children' => array()
			),
			array(
				'id' => 'phocagalleryCats',
				'title' => 'Categories',
				'link' => JURI::base() . 'index.php?option=com_phocagallery&view=phocagallerycs',
				'type' => 'url',
				'parent_id' => $dynamicParentId + 1,
				'params' => 'menu_image=-1',
				'access' => 0,
				'children' => array()
			),
			
			array(
				'id' => 'phocagalleryThemes',
				'title' => 'Themes',
				'link' => JURI::base() .  'index.php?option=com_phocagallery&view=phocagalleryt',
				'type' => 'url',
				'parent_id' => $dynamicParentId + 1,
				'params' => 'menu_image=-1',
				'access' => 0,
				'children' => array()
			),
			array(
				'id' => 'phocagalleryCatRating',
				'title' => 'Category rating',
				'link' => JURI::base() . 'index.php?option=com_phocagallery&view=phocagalleryra',
				'type' => 'url',
				'parent_id' => $dynamicParentId + 1,
				'params' => 'menu_image=-1',
				'access' => 0,
				'children' => array()
			),
			array(
				'id' => 'phocagalleryImgRatings',
				'title' => 'Image ratings',
				'link' => JURI::base() . 'index.php?option=com_phocagallery&view=phocagalleryraimg',
				'type' => 'url',
				'parent_id' => $dynamicParentId + 1,
				'params' => 'menu_image=-1',
				'access' => 0,
				'children' => array()
			),
			array(
				'id' => 'phocagalleryCatComments',
				'title' => 'Category Comments',
				'link' => JURI::base() . 'index.php?option=com_phocagallery&view=phocagallerycos',
				'type' => 'url',
				'parent_id' => $dynamicParentId + 1,
				'params' => 'menu_image=-1',
				'access' => 0,
				'children' => array()
			),
			array(
				'id' => 'phocagalleryImgComments',
				'title' => 'Image Comments',
				'link' => JURI::base() . 'index.php?option=com_phocagallery&view=phocagallerycoimgs',
				'type' => 'url',
				'parent_id' => $dynamicParentId + 1,
				'params' => 'menu_image=-1',
				'access' => 0,
				'children' => array()
			),
			array(
				'id' => 'phocagalleryUsers',
				'title' => 'Users',
				'link' => JURI::base() . 'index.php?option=com_phocagallery&view=phocagalleryusers',
				'type' => 'url',
				'parent_id' => $dynamicParentId + 1,
				'params' => 'menu_image=-1',
				'access' => 0,
				'children' => array()
			),
			array(
				'id' => 'phocagalleryInfo',
				'title' => 'Info',
				'link' => JURI::base() . 'index.php?option=com_phocagallery&view=phocagalleryin',
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