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
 
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
/**
 * Script file of HelloWorld component
 */
class com_adminpraiseInstallerScript
{
  /**
   * method to install the component
   *
   * @return void
   */
  function install($parent) 
  {
		// Import filesystem libraries
		jimport('joomla.filesystem.file');
		 
		$buffer = 'installing';
		// Create the install.dummy file
		$file = JFile::write(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_adminpraise'.DS.'installer.dummy.ini', $buffer);
  }

  /**
   * method to uninstall the component
   *
   * @return void
   */
  function uninstall($parent) 
  {
		require_once (JPATH_ADMINISTRATOR.DS.'components'.DS.'com_adminpraise'.DS.'uninstall.adminpraise.php');

		$appl = JFactory::getApplication();
		$lang = JFactory::getLanguage();
		$lang->load('com_adminpraise', JPATH_BASE, null, true);

		$uninstaller = new adminPraiseUninstallation();

		$uninstaller->dropTables();

		$template = $uninstaller->uninstallTemplate();

		if ($template) {
			$appl->enqueueMessage(JText::_('COM_ADMINPRAISE_UNINSTALL_TEMPLATE_SUCCESS'));
		} else {
			$appl->enqueueMessage(JText::_('COM_ADMINPRAISE_UNINSTALL_TEMPLATE_FAILURE'));
		}

		$modules = $uninstaller->uninstallModules();
		if (!in_array(false, $modules)) {
			$appl->enqueueMessage(JText::_('COM_ADMINPRAISE_UNINSTALL_MODULES_SUCCESS'));
			$modules = true;
		} else {
			$appl->enqueueMessage(JText::_('COM_ADMINPRAISE_UNINSTALL_MODULES_FAILURE'));
			$modules = false;
		}

		$plugins = $uninstaller->uninstallPlugins();
		if (!in_array(false, $plugins)) {
			$appl->enqueueMessage(JText::_('COM_ADMINPRAISE_UNINSTALL_PLUGINS_SUCCESS'));
			$plugins = true;
		} else {
			$appl->enqueueMessage(JText::_('COM_ADMINPRAISE_UNINSTALL_PLUGINS_FAILURE'));
			$plugins = false;
		}

		if ($template && $modules && $plugins) {
			return true;
		}

		return false;
  }

  /**
   * method to update the component
   *
   * @return void
   */
  function update($parent) 
  {
		// Import filesystem libraries
		jimport('joomla.filesystem.file');
		 
		$buffer = 'installing';
		// Create the install.dummy file
		$file = JFile::write(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_adminpraise'.DS.'installer.dummy.ini', $buffer);
  }

  /**
   * method to run before an install/update/uninstall method
   *
   * @return void
   */
  function preflight($type, $parent) 
  {
    // $parent is the class calling this method
    // $type is the type of change (install, update or discover_install)
    //echo '<p>' . JText::_('COM_ADMINPRAISE_PREFLIGHT_' . $type . '_TEXT') . '</p>';
  }

  /**
   * method to run after an install/update/uninstall method
   *
   * @return void
   */
  function postflight($type, $parent) 
  {
		$appl = JFactory::getApplication();
		$appl->redirect('index.php?option=com_adminpraise');
  }
}

