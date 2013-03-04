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
defined('_JEXEC') or die();

jimport('joomla.application.component.controller');

/**
 * The Live Update MVC controller
 */
class AdminpraiseInstallController extends JController
{
	private $jversion = '15';

	/**
	 * Object contructor 
	 * @param array $config
	 * 
	 * @return AdminpraiseInstallController
	 */
	public function __construct($config = array())
	{
		parent::__construct();

		// Do we have Joomla! 1.6?
		if( version_compare( JVERSION, '1.6.0', 'ge' ) ) {
			$this->jversion = '16';
		}
		
		$basePath = dirname(__FILE__);
		if($this->jversion == '15') {
			$this->_basePath = $basePath;
		} else {
			$this->basePath = $basePath;
		}

	}
	
	
	/**
	 * Displays the current view
	 * @param bool $cachable Ignored!
	 */
	public final function display($cachable = false)
	{
		$viewLayout	= JRequest::getCmd( 'layout', 'default' );

		$view = $this->getThisView();

		// Get/Create the model
		$model = $this->getThisModel();
		$view->setModel($model, true);

		// Set the layout
		$view->setLayout($viewLayout);

		// Display the view
		$view->display();
	}

	public final function getThisView()
	{
		static $view = null;
		
		if(is_null($view))
		{
			$basePath = ($this->jversion == '15') ? $this->_basePath : $this->basePath;
			$tPath = dirname(__FILE__).'/tmpl';
			
			require_once('view.php');
			$view = new AdminpraiseInstallView(array('base_path'=>$basePath, 'template_path'=>$tPath));
		}
		
		return $view;
	}
	
	public final function getThisModel()
	{
		static $model = null;
		
		if(is_null($model))
		{
			require_once('model.php');
			$model = new AdminpraiseInstallModel();
			$task = ($this->jversion == '15') ? $this->_task : $this->task;
			
			$model->setState( 'task', $task );
			
			$app	= JFactory::getApplication();
			$menu	= $app->getMenu();
			if (is_object( $menu ))
			{
				if ($item = $menu->getActive())
				{
					$params	=& $menu->getParams($item->id);
					// Set Default State Data
					$model->setState( 'parameters.menu', $params );
				}
			}
			
		}
		
		return $model;
	}

	/**
	 * Requirements
	 *
	 * @return	none
	 * @since	1.0.0
	 */
	function requirements()
	{
		$model = $this->getThisModel();

		$rq = $model->determineRequirements();

		// Init requirements array
		$error = array();

		/**
		 * Compare the PHP version
		 */
		if (version_compare($rq['phpMust'], $rq['phpIs'], '<')) {
			$error['php5'] = true;
		}else{
			$error['php5'] = false;
		}

		/**
		 * Compare the MYSQL version
		 */
		if (version_compare($rq['mysqlMust'], $rq['mysqlIs'])) {
			$error['mysql'] = true;
		}else{
			$error['mysql'] = false;
		}

		/**
		 * Check if fopen is enabled
		 */
		if (function_exists('fopen')) {
			$error['fopen'] = true;
		}else{
			$error['fopen'] = false;
		}

		/**
		 * Check if fopen is enabled for remote connections
		 */
		if( ini_get('allow_url_fopen') ) {
			$error['allow_url_fopen'] = true;
		}else{
			$error['allow_url_fopen'] = false;
		}

		$return = json_encode($error);
		echo $return;

	} //end function

	/**
	 * Compatibility
	 *
	 * @return	none
	 * @since	1.0.0
	 */
	function compatibility()
	{
		//$model = $this->getThisModel();

		jimport('joomla.installer.installer');

		// Init
		$db =& JFactory::getDBO();
		$appl = JFactory::getApplication();
		$installer = & JInstaller::getInstance();

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
				$db->query();
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
				$db->query();
			}
		}	

		echo true;

	} //end function

	/**
	 * Install component
	 *
	 * @return	none
	 * @since	1.0.0
	 */
	function component()
	{
		jimport( 'joomla.installer.installer' );
		jimport( 'joomla.installer.helper' );
		jimport( 'joomla.filesystem.folder' );

		/**
		 * Initialize
		 */
		$db =& JFactory::getDBO();
		$installer =& JInstaller::getInstance();
		$error = array();

		/**
		 * Decompress component files
		 */
		$component = JPATH_COMPONENT.DS.'packages'.DS.'components'.DS.'com_adminpraise.zip';
		$component_package = JInstallerHelper::unpack($component);

		/**
		 * Inserting install database
		 */
		$sqlfile = $component_package['extractdir'].DS.'administrator'.DS.'components'.DS. 'com_adminpraise'.DS.'sql'.DS.'install.mysql.sql';

		// Check that sql files exists before reading. Otherwise raise error for rollback
		if ( !file_exists( $sqlfile ) ) {
			return false;
		}
		$buffer = file_get_contents($sqlfile);

		$queries = JInstallerHelper::splitSql($buffer);

		// Process each query in the $queries array (split out of sql file).
		foreach ($queries as $query)
		{
			$query = trim($query);
			if ($query != '' && $query{0} != '#') {
				$db->setQuery($query);
				if (!$db->query()) {
					echo JText::_('SQL Error')." ".$db->stderr(true);
					//JError::raiseWarning(1, 'JInstaller::install: '.JText::_('SQL Error')." ".$db->stderr(true));
					return false;
				}
			}
		}

		/**
		 * Copying files
		 */
		$srcdir = $component_package['extractdir'].DS.'administrator'.DS.'components'.DS. 'com_adminpraise';

		if (!JFolder::copy($srcdir.DS.'assets', JPATH_COMPONENT.DS.'assets', '', true)) {
			$error[] = false;
		}
		if (!JFolder::copy($srcdir.DS.'controllers', JPATH_COMPONENT.DS.'controllers', '', true)) {
			$error[] = false;
		}
		if (!JFolder::copy($srcdir.DS.'help', JPATH_COMPONENT.DS.'help', '', true)) {
			$error[] = false;
		}
		if (!JFolder::copy($srcdir.DS.'helpers', JPATH_COMPONENT.DS.'helpers', '', true)) {
			$error[] = false;
		}
		if (!JFolder::copy($srcdir.DS.'library', JPATH_COMPONENT.DS.'library', '', true)) {
			$error[] = false;
		}
		if (!JFolder::copy($srcdir.DS.'models', JPATH_COMPONENT.DS.'models', '', true)) {
			$error[] = false;
		}
		if (!JFolder::copy($srcdir.DS.'sql', JPATH_COMPONENT.DS.'sql', '', true)) {
			$error[] = false;
		}
		if (!JFolder::copy($srcdir.DS.'tables', JPATH_COMPONENT.DS.'tables', '', true)) {
			$error[] = false;
		}
		if (!JFolder::copy($srcdir.DS.'views', JPATH_COMPONENT.DS.'views', '', true)) {
			$error[] = false;
		}

	} //end function

	/**
	 * Install plugins
	 *
	 * @return	none
	 * @since	1.0.0
	 */
	function plugins()
	{
		jimport('joomla.installer.helper');
		require_once(JPATH_COMPONENT.DS.'library'.DS.'joomla'.DS.'installer'.DS.'installer.php');

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

			$installer->setPublished("adminpraise_search_admin_menu");

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

	} //end function

	/**
	 * Install modules
	 *
	 * @return	none
	 * @since	1.0.0
	 */
	function modules()
	{
		jimport('joomla.installer.helper');
		jimport('joomla.application.module.helper');
		require_once(JPATH_COMPONENT.DS.'library'.DS.'joomla'.DS.'installer'.DS.'installer.php');
		require_once(JPATH_COMPONENT.DS.'models'.DS.'menu.php');

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

	} //end function

	/**
	 * Install template
	 *
	 * @return	none
	 * @since	1.0.0
	 */
	function template()
	{
		jimport('joomla.installer.helper');
		require_once(JPATH_COMPONENT.DS.'library'.DS.'joomla'.DS.'installer'.DS.'installer.php');

		/**
		 * Initialize
		 */
		$installer = & AdminpraiseInstaller::getInstance();
		$error = array();

		/**
		 * Install adminpraiseTemplate
		 */
		$adminpraiseTemplate = JPATH_COMPONENT.DS.'packages'.DS.'templates'.DS.'tpl_adminpraise.zip';
		$adminpraiseTemplate_package = JInstallerHelper::unpack($adminpraiseTemplate);

		if ($installer->install($adminpraiseTemplate_package['extractdir'])) {
			echo "<div class='step'>".JText::_( 'COM_ADMINPRAISE_TEMPLATE_SUCCESS_INSTALLATION' )."</div>";
		}else{
			echo "<div class='step_false'>".JText::_( 'COM_ADMINPRAISE_TEMPLATE_ERROR_INSTALLATION' )."</div>";
			$error['activitylog'] = false;
		}

	} //end function

	/**
	 * Install done
	 *
	 * @return	none
	 * @since	1.0.0
	 */
	function done()
	{
		jimport( 'joomla.filesystem.file' );
		JFile::delete(JPATH_COMPONENT.DS.'installer.dummy.ini');
	}
}
