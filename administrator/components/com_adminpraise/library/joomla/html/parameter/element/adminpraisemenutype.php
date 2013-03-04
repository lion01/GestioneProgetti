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
defined('_JEXEC') or die('Restricted Access');

class JElementAdminpraiseMenuType extends JElement {

	/**
	 * Element name
	 *
	 * @var		string
	 */
	public $_name = 'AdminpraiseMenuType';

	public function fetchElement($name, $value, &$node, $control_name) {
		$db = & JFactory::getDBO();

		require_once( JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_adminpraise' . DS . 'helpers' . DS . 'menu.php' );
		$menuTypes = AdminpraiseMenuHelper::getMenuTypes();


		$menuList = JHTML::_('select.genericlist', $menuTypes, '' . $control_name . '[' . $name . ']', 'class="inputbox"', 'id', 'title', $value, $control_name . $name);

		return $menuList;
	}

}
