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
		'id' => 'vm',
		'title' => 'VirtueMart',
		'link' => JURI::base() . 'index.php?option=com_virtuemart',
		'type' => 'url',
		'parent_id' => $dynamicParentId,
		'params' => 'menu_image=-1',
		'access' => 0,
		'children' => array(
			array(
				'id' => 'vmProductList',
				'title' => 'Product List',
				'link' => JURI::base() . 'index.php?pshop_mode=admin&page=product.product_list&option=com_virtuemart',
				'type' => 'url',
				'parent_id' => $dynamicParentId + 1,
				'params' => 'menu_image=-1',
				'access' => 0,
				'children' => array()
			),
			array(
				'id' => 'vmCatTree',
				'title' => 'Category Tree',
				'link' => JURI::base() . 'index.php?pshop_mode=admin&page=product.product_category_list&option=com_virtuemart',
				'type' => 'url',
				'parent_id' => $dynamicParentId + 1,
				'params' => 'menu_image=-1',
				'access' => 0,
				'children' => array()
			),
			
			array(
				'id' => 'vmOrders',
				'title' => 'Orders',
				'link' => JURI::base() . 'index.php?pshop_mode=admin&page=order.order_list&option=com_virtuemart',
				'type' => 'url',
				'parent_id' => $dynamicParentId + 1,
				'params' => 'menu_image=-1',
				'access' => 0,
				'children' => array()
			),
			array(
				'id' => 'vmPaymentMethods',
				'title' => 'Payment methods',
				'link' => JURI::base() . 'index.php?pshop_mode=admin&page=store.payment_method_list&option=com_virtuemart',
				'type' => 'url',
				'parent_id' => $dynamicParentId + 1,
				'params' => 'menu_image=-1',
				'access' => 0,
				'children' => array()
			),
			array(
				'id' => 'vmVendors',
				'title' => 'Vendors',
				'link' => JURI::base() . 'index.php?pshop_mode=admin&page=vendor.vendor_list&option=com_virtuemart',
				'type' => 'url',
				'parent_id' => $dynamicParentId + 1,
				'params' => 'menu_image=-1',
				'access' => 0,
				'children' => array()
			),
			array(
				'id' => 'vmusers',
				'title' => 'Users',
				'link' => JURI::base() . 'index.php?pshop_mode=admin&page=admin.user_list&option=com_virtuemart',
				'type' => 'url',
				'parent_id' => $dynamicParentId + 1,
				'params' => 'menu_image=-1',
				'access' => 0,
				'children' => array()
			),
			array(
				'id' => 'vmConfig',
				'title' => 'Configuration',
				'link' => JURI::base() . 'index.php?pshop_mode=admin&page=admin.show_cfg&option=com_virtuemart',
				'type' => 'url',
				'parent_id' => $dynamicParentId + 1,
				'params' => 'menu_image=-1',
				'access' => 0,
				'children' => array()
			),
			array(
				'id' => 'vmEditStore',
				'title' => 'Edit Store',
				'link' => JURI::base() .  'index.php?pshop_mode=admin&page=store.store_form&option=com_virtuemart',
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