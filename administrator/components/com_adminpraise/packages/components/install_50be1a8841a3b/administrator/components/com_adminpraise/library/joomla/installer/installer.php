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

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );
require_once(JPATH_ROOT . DS . 'libraries' . DS . 'joomla' . DS . 'installer' 
														. DS . 'installer.php');
class AdminpraiseInstaller extends JInstaller
{
	
	public static function &getInstance()
	{
		static $instance;

		if (!isset ($instance)) {
			$instance = new AdminpraiseInstaller();
		}
		return $instance;
	}
	
	/**
	 * Set an installer adapter by name
	 *
	 * @access	public
	 * @param	string	$name		Adapter name
	 * @param	object	$adapter	Installer adapter object
	 * @return	boolean True if successful
	 * @since	1.5
	 */
	function setAdapter($name, $adapter = null)
	{
		if (!is_object($adapter))
		{
			// Try to load the adapter object
			require_once(dirname(__FILE__).DS.'adapters'.DS.strtolower($name).'.php');
			$class = 'AdminpraiseInstaller'.ucfirst($name);
			if (!class_exists($class)) {
				return false;
			}

			$db = JFactory::getDBO();
			$adapter = new $class($this, $db);
		}
		$this->_adapters[$name] =& $adapter;
		return true;
	}

	/**
	 * Set module position
	 *
	 * @access	public
	 * @param	string	$name		Adapter name
	 * @param	object	$adapter	Installer adapter object
	 * @return	boolean True if successful
	 * @since	1.5
	 */
	public function setPosition($module, $position, $ordering)
	{
		require_once(JPATH_LIBRARIES.DS.'joomla'.DS.'database'.DS.'table'.DS.'module.php');
		$db = JFactory::getDBO();

		$mod = new JTableModule($db);
		$mod->load(array("module" => $module));
		$mod->position = (string)$position[0];
		$mod->published = 1;
		$mod->ordering = $ordering;
		if ($module = "mod_activitylog_pro") {
			$mod->params = '{"limit":25,"conf_name":"u.name","show_date":1,"dateformat":"m\/d Y, H:i","show_filter":1,"manager_access":1,"admin_access":1,"sadmin_access":1}';
		}
		$mod->store();

		$query = 'SELECT moduleid FROM' . $db->nameQuote('#__modules_menu')
				. ' WHERE moduleid=' . $db->Quote($mod->id);
		$db->setQuery($query, 0, 1);
		$menuid = $db->loadResult();

		if(!$menuid) {
			// Insert modules menu flag
			$query = 'INSERT INTO ' . $db->nameQuote('#__modules_menu')
					. ' (moduleid, menuid)'
					. ' VALUES ('	. $db->Quote($mod->id) . ','. $db->Quote(0) . ')';

			$db->setQuery($query);
			if($db->Query()) {
				return true;
			}
		} else {
			$query = 'UPDATE ' . $db->nameQuote('#__modules_menu')
					. ' SET menuid = ' . $db->Quote(0) 
					. ' WHERE moduleid = ' . $db->Quote($mod->id);

			$db->setQuery($query);
			if($db->Query()) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Autopublish plugins
	 *
	 * @access	public
	 * @param	string	$name		Adapter name
	 * @param	object	$adapter	Installer adapter object
	 * @return	boolean True if successful
	 * @since	1.5
	 */
	function setPublished($name)
	{
		require_once(JPATH_LIBRARIES.DS.'joomla'.DS.'database'.DS.'table'.DS.'extension.php');
		$db = JFactory::getDBO();

		$plg = new JTableExtension($db);
		$plg->load(array("name" => $name));
		$plg->enabled = 1;
		$plg->store();

		return true;
	}

	function insertActivityLogInIconPosition() {

		$db = JFactory::getDBO();

		$query = 'SELECT id FROM' . $db->nameQuote('#__modules')
				. ' WHERE module=' . $db->Quote('mod_activitylog_pro')
				. ' AND position=' . $db->Quote('adminpraise_cpanel_right');
		$db->setQuery($query, 0, 1);
		$id = $db->loadResult();

		if (!$id) {
			$params = '{"limit":25,"conf_name":"u.name","show_date":1,"dateformat":"m\/d Y, H:i","show_filter":1,"manager_access":1,"admin_access":1,"sadmin_access":1}';

			$query = 'INSERT INTO ' . $db->nameQuote('#__modules')
					. ' (title, ordering, position, published, module, params, client_id, access, language)'
					. ' VALUES ('
					. $db->Quote('Activitylog PRO') . ','
					. $db->Quote('2') . ','
					. $db->Quote('adminpraise_cpanel_right') . ','
					. $db->Quote(1) . ','
					. $db->Quote('mod_activitylog_pro') . ','
					. $db->Quote($params). ','
					. $db->Quote(1). ','
					. $db->Quote(1). ','
					. $db->Quote('*') . ')';
		
			$db->setQuery($query);
			$db->Query();

			$oldid = $db->insertid();

			// Insert modules menu flag
			$query = 'INSERT INTO ' . $db->nameQuote('#__modules_menu')
					. ' (moduleid, menuid)'
					. ' VALUES ('	. $db->Quote($oldid) . ','. $db->Quote(0) . ')';
		
			$db->setQuery($query);
			$db->Query();

		}
	

}

}
