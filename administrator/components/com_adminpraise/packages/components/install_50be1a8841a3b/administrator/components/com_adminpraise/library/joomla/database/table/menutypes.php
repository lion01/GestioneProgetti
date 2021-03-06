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

class AdminpraiseTableMenuTypes extends JTable {
	/**
	 *
	 * @var int Primary key
	 */
	public $id = null;
	
	/**
	 *
	 * @var string
	 */
	public $title = null;
	
	/**
	 *
	 * @var string
	 */
	public $description = null;
	
	
	
	public function __construct (&$db) {
		parent::__construct('#__adminpraise_menu_types', 'id', $db);
	}
	
	public function getAllMenuTypes() {
		$query = 'SELECT * FROM '. $this->getTableName();
		
		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}
}
?>
