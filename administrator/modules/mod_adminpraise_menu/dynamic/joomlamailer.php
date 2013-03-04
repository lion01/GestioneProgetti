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
		'id' => 'joomlamailer',
		'title' => 'JoomlaMailer',
		'link' => JURI::base() . 'index.php?option=com_joomailermailchimpintegration&view=main',
		'type' => 'url',
		'parent_id' => $dynamicParentId,
		'params' => 'menu_image=-1',
		'access' => 0,
		'children' => array(
			array(
				'id' => 'joomlamailerLists',
				'title' => 'Lists',
				'link' => JURI::base() . 'index.php?option=com_joomailermailchimpintegration&view=joomailermailchimpintegrations',
				'type' => 'url',
				'parent_id' => $dynamicParentId + 1,
				'params' => 'menu_image=-1',
				'access' => 0,
				'children' => array()
			),
			array(
				'id' => 'joomlamailerCampaigns',
				'title' => 'Campaigns',
				'link' => JURI::base() . 'index.php?option=com_joomailermailchimpintegration&view=campaignlist',
				'type' => 'url',
				'parent_id' => $dynamicParentId + 1,
				'params' => 'menu_image=-1',
				'access' => 0,
				'children' => array()
			),
			
			array(
				'id' => 'joomlamailerCreate',
				'title' => 'Create Campaign',
				'link' => JURI::base() . 'index.php?option=com_joomailermailchimpintegration&view=create',
				'type' => 'url',
				'parent_id' => $dynamicParentId + 1,
				'params' => 'menu_image=-1',
				'access' => 0,
				'children' => array()
			),
			array(
				'id' => 'joomlamailerReports',
				'title' => 'Reports',
				'link' => JURI::base() . 'index.php?option=com_joomailermailchimpintegration&view=campaigns',
				'type' => 'url',
				'parent_id' => $dynamicParentId + 1,
				'params' => 'menu_image=-1',
				'access' => 0,
				'children' => array()
			),
			array(
				'id' => 'joomlamailerTemplates',
				'title' => 'Templates',
				'link' => JURI::base() . 'index.php?option=com_joomailermailchimpintegration&view=templates',
				'type' => 'url',
				'parent_id' => $dynamicParentId + 1,
				'params' => 'menu_image=-1',
				'access' => 0,
				'children' => array()
			),
			array(
				'id' => 'joomlamailerExtensions',
				'title' => 'Exensions',
				'link' => JURI::base() . 'index.php?option=com_joomailermailchimpintegration&view=extensions',
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