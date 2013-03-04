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

jimport( 'joomla.plugin.helper' );
jimport( 'joomla.filesystem.folder ');
require_once(JPATH_LIBRARIES.DS.'joomla'.DS.'installer'.DS.'installer.php');

?>
<fieldset class="adminform">
<legend><?php echo JText::_( 'COM_ADMINPRAISE_COMPATIBILITY' ); ?></legend>
<?php
	// Init
	$db =& JFactory::getDBO();
	$appl = JFactory::getApplication();
	$installer = & JInstaller::getInstance();
	$error = array();
	$error[] = true;

  /**
  * Uninstall activityLogPlugin
  */
	$query = 'SELECT * FROM ' . $db->nameQuote('#__extensions')
			. ' WHERE element = ' . $db->Quote('activitylog_pro');
	$db->setQuery($query);

	$plugins = $db->loadObjectList();

	if (count($plugins)) {
		foreach ($plugins as $key => $value) {
			$uninstallStatus = $installer->uninstall('plugin', $value->extension_id, 1);
			$appl->enqueueMessage(JText::_('COM_ADMINPRAISE_UALOG_REMOVED'));
		}
	}

	/**
	 * Check if AdminPraise2 is enabled
	 */	
	$path = JPATH_ADMINISTRATOR."/templates/adminpraise2";
	$ap2 = JFolder::exists($path);

	if ($ap2) {
		$query = "SELECT template FROM #__templates_menu WHERE client_id = 1 LIMIT 1";
		$db->setQuery($query);
		$template = $db->loadResult();

		if ($template == "adminpraise2") {
			$query = "UPDATE `#__templates_menu` SET `template` = 'khepri' WHERE client_id = 1";
			$db->setQuery($query);
			if($db->query()) {
				$appl->enqueueMessage(JText::_('COM_ADMINPRAISE_TEMPLATE_CHANGED'));
			}
		}
	}	

	/**
	 * Check if AdminPraiseLite is enabled
	 */	
	$path = JPATH_ADMINISTRATOR."/templates/aplite";
	$aplite = JFolder::exists($path);

	if ($aplite) {
		$query = "SELECT template FROM #__templates_menu WHERE client_id = 1 LIMIT 1";
		$db->setQuery($query);
		$template = $db->loadResult();

		if ($template == "aplite") {
			$query = "UPDATE `#__templates_menu` SET `template` = 'khepri' WHERE client_id = 1";
			$db->setQuery($query);
			if($db->query()) {
				$appl->enqueueMessage(JText::_('COM_ADMINPRAISE_TEMPLATE_CHANGED'));
			}
		}
	}	

	/**
	 * Print 'next' button if everything is ok
	 */
	if (!in_array(false, $error)) {
		echo "<div id='pagination' class='pagination'><div class='numbers'><div class='button2-left'><div class='page'><span>2 of 7</span> <a href='".JRoute::_('index.php?option=com_adminpraise&task=step3')."'>".JText::_( 'COM_ADMINPRAISE_NEXT' )."</a>
		</div></div></div></div>";
	}
?>
</fieldset>
