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
class AdminpraiseTableMenu extends JTable {

	/**
	 *
	 * @var int Primary key
	 */
	public $id = null;
	/**
	 *
	 * @var string Link name
	 */
	public $name = null;
	/**
	 *
	 * @var string $link
	 */
	public $link = null;
	/**
	 *
	 * @var int 
	 */
	public $published = null;
	/**
	 *
	 * @var int
	 */
	public $type = null;
	/**
	 *
	 * @var int
	 */
	public $parent = null;
	/**
	 *
	 * @var int
	 */
	public $ordering = null;
	/**
	 *
	 * @var int
	 */
	public $access = null;
	/**
	 *
	 * @var string
	 */
	public $params = null;
	/**
	 *
	 * @var int
	 */
	public $home = null;
	/**
	 *
	 * @var int
	 */
	public $checked_out = null;
	/**
	 *
	 * @param int 
	 */
	public $browserNav = null;
	/**
	 *
	 * @var string
	 */
	public $note = null;
	
	/**
	 *
	 * @var int
	 */
	public $menutype = null;


	public function __construct(&$db) {
		parent::__construct('#__adminpraise_menu', 'id', $db);
	}

	/**
	 * Overloaded bind function
	 *
	 * @access public
	 * @param array $hash named array
	 * @return null|string	null is operation was satisfactory, otherwise returns an error
	 * @see JTable:bind
	 * @since 1.5
	 */
	function bind($array, $ignore = '') {
		if (is_array($array['params'])) {
			$registry = new JRegistry();
			$registry->loadArray($array['params']);
			$array['params'] = $registry->toString();
		}

		return parent::bind($array, $ignore);
	}

}

?>
