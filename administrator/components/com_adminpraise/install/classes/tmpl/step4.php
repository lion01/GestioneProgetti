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
?>
<fieldset class="adminform">
<legend><?php echo JText::_( 'COM_ADMINPRAISE_INSTALL_PLUGINS' ); ?></legend>
<?php

	/**
	 * Initialize
	 */
	$installer = & AdminpraiseInstaller::getInstance();
	$error = array();

	/**
	 * Install activityLogPlugin
	 */
	$activityLogPlugin = JPATH_COMPONENT.DS.'packages'.DS.'plugins'.DS.'plg_activitylog_pro.zip';
	$activityLogPlugin_package = JInstallerHelper::unpack($activityLogPlugin);

	if ($installer->install($activityLogPlugin_package['extractdir'])) {

		$installer->setPublished("activitylog_pro");

		echo "<div class='step'>".JText::_( 'COM_ADMINPRAISE_PLG_ACTIVITY_LOG_SUCCESS_INSTALLATION' )."</div>";
	}else{
		echo "<div class='step_false'>".JText::_( 'COM_ADMINPRAISE_PLG_ACTIVITY_LOG_ERROR_INSTALLATION' )."</div>";
		$error['activitylog'] = false;
	}

	/**
	 * Install scePlugin
	 */
	$scePlugin = JPATH_COMPONENT.DS.'packages'.DS.'plugins'.DS.'plg_adminpraise_sce.zip';
	$scePlugin_package = JInstallerHelper::unpack($scePlugin);
	
	if ($installer->install($scePlugin_package['extractdir'])) {

		$installer->setPublished("sce");

		echo "<div class='step'>".JText::_( 'COM_ADMINPRAISE_PLG_SCE_SUCCESS_INSTALLATION' )."</div>";
	}else{
		echo "<div class='step_false'>".JText::_( 'COM_ADMINPRAISE_PLG_SCE_ERROR_INSTALLATION' )."</div>";
		$error['sce'] = false;
	}

	/**
	 * Install autoeditorPlugin
	 */
	$autoeditorPlugin = JPATH_COMPONENT.DS.'packages'.DS.'plugins'.DS.'plg_adminpraise_autoeditor.zip';
	$autoeditorPlugin_package = JInstallerHelper::unpack($autoeditorPlugin);
	
	if ($installer->install($autoeditorPlugin_package['extractdir'])) {

		$installer->setPublished("adminpraise_autoeditor");

		echo "<div class='step'>".JText::_( 'COM_ADMINPRAISE_PLG_AUTOEDITOR_SUCCESS_INSTALLATION' )."</div>";
	}else{
		echo "<div class='step_false'>".JText::_( 'COM_ADMINPRAISE_PLG_AUTOEDITOR_ERROR_INSTALLATION' )."</div>";
		$error['autoeditor'] = false;
	}
	
	/**
	 * Install contentSearchPlugin
	 */
	$contentSearchPlugin = JPATH_COMPONENT.DS.'packages'.DS.'plugins'.DS.'plg_adminpraisesearch_content.zip';
	$contentSearchPlugin_package = JInstallerHelper::unpack($contentSearchPlugin);
	
	if ($installer->install($contentSearchPlugin_package['extractdir'])) {

		$installer->setPublished("adminpraise_search_content");

		echo "<div class='step'>".JText::_( 'COM_ADMINPRAISE_PLG_CONTENTSEARCH_SUCCESS_INSTALLATION' )."</div>";
	}else{
		echo "<div class='step_false'>".JText::_( 'COM_ADMINPRAISE_PLG_CONTENTSEARCH_ERROR_INSTALLATION' )."</div>";
		$error['autoeditor'] = false;
	}

	/**
	 * Install menuSearchPlugin
	 */
	$menuSearchPlugin = JPATH_COMPONENT.DS.'packages'.DS.'plugins'.DS.'plg_adminpraisesearch_menu.zip';
	$menuSearchPlugin_package = JInstallerHelper::unpack($menuSearchPlugin);
	
	if ($installer->install($menuSearchPlugin_package['extractdir'])) {

		$installer->setPublished("adminpraise_search_menu");

		echo "<div class='step'>".JText::_( 'COM_ADMINPRAISE_PLG_MENUSEARCH_SUCCESS_INSTALLATION' )."</div>";
	}else{
		echo "<div class='step_false'>".JText::_( 'COM_ADMINPRAISE_PLG_MENUSEARCH_ERROR_INSTALLATION' )."</div>";
		$error['autoeditor'] = false;
	}
	
		/**
	 * Install adminMenuSearchPlugin
	 */
	$adminMenuSearchPlugin = JPATH_COMPONENT.DS.'packages'.DS.'plugins'.DS.'plg_adminpraisesearch_admin_menu.zip';
	$adminMenuSearchPlugin_package = JInstallerHelper::unpack($adminMenuSearchPlugin);
	
	if ($installer->install($adminMenuSearchPlugin_package['extractdir'])) {

		$installer->setPublished("adminpraise_search__admin_menu");

		echo "<div class='step'>".JText::_( 'COM_ADMINPRAISE_PLG_ADMINMENUSEARCH_SUCCESS_INSTALLATION' )."</div>";
	}else{
		echo "<div class='step_false'>".JText::_( 'COM_ADMINPRAISE_PLG_ADMINMENUSEARCH_ERROR_INSTALLATION' )."</div>";
		$error['autoeditor'] = false;
	}
	
		/**
	 * Install plg_extension_adminpraise
	 */
	$extensionPlugin = JPATH_COMPONENT.DS.'packages'.DS.'plugins'.DS.'plg_extension_adminpraise.zip';
	$extensionPlugin_package = JInstallerHelper::unpack($extensionPlugin);
	
	if ($installer->install($extensionPlugin_package['extractdir'])) {

		$installer->setPublished("plg_extension_adminpraise");

		echo "<div class='step'>".JText::_( 'COM_ADMINPRAISE_PLG_EXTENSION_ADMINPRAISE_SUCCESS_INSTALLATION' )."</div>";
	}else{
		echo "<div class='step_false'>".JText::_( 'COM_ADMINPRAISE_PLG_EXTENSION_ADMINPRAISE_ERROR_INSTALLATION' )."</div>";
		$error['autoeditor'] = false;
	}
	
	/**
	 * Print 'next' button if everything is ok
	 */
	if (!in_array(false, $error)) {
		echo "<div class='pagination'><div class='numbers'><div class='button2-left'><div class='page'><span>4 of 7</span> <a href='".JRoute::_('index.php?option=com_adminpraise&task=step5')."'>".JText::_( 'COM_ADMINPRAISE_NEXT' )."</a>
		</div></div></div></div>";
	}
?>
</fieldset>
