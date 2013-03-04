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

// Requires
jimport('joomla.installer.helper');
require_once(JPATH_COMPONENT.DS.'library'.DS.'joomla'.DS.'installer'.DS.'installer.php');
require_once(JPATH_COMPONENT.DS.'models'.DS.'menu.php');

?>
<fieldset class="adminform">
<legend><?php echo JText::_( 'COM_ADMINPRAISE_INSTALL_MODULES' ); ?></legend>
<?php

	/**
	 * Initialize
	 */
	$db = JFactory::getDBO();
	$installer = & AdminpraiseInstaller::getInstance();
	$firstTimeInstall = false;
	$error = array();

	/**
	 * Install activityLogModule
	 */
	$activityLogModule = JPATH_COMPONENT.DS.'packages'.DS.'modules'.DS.'mod_activitylog_pro.zip';
	$activityLogModule_package = JInstallerHelper::unpack($activityLogModule);

	if ($installer->install($activityLogModule_package['extractdir'])) {

		$manifest = $installer->getManifest();
		$position = $manifest->attributes()->position;
		$installer->setPosition("mod_activitylog_pro", $position, 1);

		echo "<div class='step'>".JText::_( 'COM_ADMINPRAISE_MODULE_ACTIVITYLOG_SUCCESS_INSTALLATION' )."</div>";
	}else{
		echo "<div class='step_false'>".JText::_( 'COM_ADMINPRAISE_MODULE_ACTIVITYLOG_ERROR_INSTALLATION' )."</div>";
		$error['activitylog'] = false;
	}

	if(!isset($error['activitylog'])) {
		$installer->insertActivityLogInIconPosition();
	}

	/**
	 * Install cpanelModule
	 */
	$cpanelModule = JPATH_COMPONENT.DS.'packages'.DS.'modules'.DS.'mod_adminpraise_cpanel.zip';
	$cpanelModule_package = JInstallerHelper::unpack($cpanelModule);

	if ($installer->install($cpanelModule_package['extractdir'])) {

		$manifest = $installer->getManifest();
		$position = $manifest->attributes()->position;
		$installer->setPosition("mod_adminpraise_cpanel", $position, 1);

		echo "<div class='step'>".JText::_( 'COM_ADMINPRAISE_MODULE_CPANEL_SUCCESS_INSTALLATION' )."</div>";
	}else{
		echo "<div class='step_false'>".JText::_( 'COM_ADMINPRAISE_MODULE_CPANEL_ERROR_INSTALLATION' )."</div>";
		$error['cpanel'] = false;
	}

	/**
	 * Install quickItemModule
	 */
	$quickItemModule = JPATH_COMPONENT.DS.'packages'.DS.'modules'.DS.'mod_quickitem_pro.zip';
	$quickItemModule_package = JInstallerHelper::unpack($quickItemModule);

	if ($installer->install($quickItemModule_package['extractdir'])) {

		$manifest = $installer->getManifest();
		$position = $manifest->attributes()->position;
		$installer->setPosition("mod_quickitem_pro", $position, 1);

		echo "<div class='step'>".JText::_( 'COM_ADMINPRAISE_MODULE_QUICKITEM_SUCCESS_INSTALLATION' )."</div>";
	}else{
		echo "<div class='step_false'>".JText::_( 'COM_ADMINPRAISE_MODULE_QUICKITEM_ERROR_INSTALLATION' )."</div>";
		$error['quickitem'] = false;
	}
	
	/**
	 * Install spotlight
	 */
	$spotlightModule = JPATH_COMPONENT.DS.'packages'.DS.'modules'.DS.'mod_adminpraise_spotlight.zip';
	$spotlightModule_package = JInstallerHelper::unpack($spotlightModule);

	if ($installer->install($spotlightModule_package['extractdir'])) {

		$manifest = $installer->getManifest();
		$position = $manifest->attributes()->position;
		$installer->setPosition("mod_adminpraise_spotlight", $position, 1);

		echo "<div class='step'>".JText::_( 'COM_ADMINPRAISE_MODULE_SPOTLIGHT_SUCCESS_INSTALLATION' )."</div>";
	}else{
		echo "<div class='step_false'>".JText::_( 'COM_ADMINPRAISE_MODULE_SPOTLIGHT_ERROR_INSTALLATION' )."</div>";
		$error['spotlight'] = false;
	}

	/**
	 * Install spotlight
	 */
	$myeditorModule = JPATH_COMPONENT.DS.'packages'.DS.'modules'.DS.'mod_myeditor.zip';
	$myeditorModule_package = JInstallerHelper::unpack($myeditorModule);

	if ($installer->install($myeditorModule_package['extractdir'])) {

		$manifest = $installer->getManifest();
		$position = $manifest->attributes()->position;
		$installer->setPosition("mod_myeditor", $position, 1);

		echo "<div class='step'>".JText::_( 'COM_ADMINPRAISE_MODULE_MYEDITOR_SUCCESS_INSTALLATION' )."</div>";
	}else{
		echo "<div class='step_false'>".JText::_( 'COM_ADMINPRAISE_MODULE_MYEDITOR_ERROR_INSTALLATION' )."</div>";
		$error['myeditor'] = false;
	}

	/**
	 * Install menu system
	 */
	$query = 'SELECT count(id) as count FROM ' . $db->nameQuote('#__adminpraise_menu');
	$db->setQuery($query);

	if (!$db->loadObject()->count) {
		$firstTimeInstall = true;

		$menu = new AdminpraiseModelMenu();
		$menu->reset();
	}

	/**
	 * Print 'next' button if everything is ok
	 */
	if (!in_array(false, $error)) {
		echo "<div class='pagination'><div class='numbers'><div class='button2-left'><div class='page'><span>5 of 7</span> <a href='".JRoute::_('index.php?option=com_adminpraise&task=step6')."'>".JText::_( 'COM_ADMINPRAISE_NEXT' )."</a>
		</div></div></div></div>";
	}
?>
</fieldset>
