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
jimport('joomla.database.table');
jimport('joomla.error.log');

class adminpraiseDefaultMenu {

	private $path;

	public function __construct($path = null) {
		if ($path) {
			$this->path = $path;
		} else {
			$this->path = JPATH_ROOT;
		}

		JTable::addIncludePath($this->path . DS . 'administrator'
						. DS . 'components' . DS . 'com_adminpraise'
						. DS . 'library' . DS . 'joomla' . DS . 'database' . DS . 'table');
		
		//$this->log = &JLog::getInstance('com_adminpraise.menu.log.php');
	}

	public function logSuccess() {
		//$this->log->addEntry(array('comment' => 'Succeed'));
	}

	public function logFailure() {
		//$this->log->addEntry(array('comment' => 'Failed'));
	}

	public function saveRow(&$row) {
		if (!$row->check()) {
			$this->logFailure();
			return;
		}

		if (!$row->store()) {
			$this->logFailure();
			return;
		}

		$row->checkin();
		$this->logSuccess();

		return $row->id;
	}

	public function executeQuery($query) {
		$db = JFactory::getDBO();
		$db->setQuery($query);
		$db->query();
		$affectedRowsCount = $db->getAffectedRows();
		//$this->log->addEntry(array('comment' => $affectedRowsCount . ' rows affected.'));
	}

	public function deleteAdminPraiseMenu() {
		//$this->log->addEntry(array('comment' => 'Deleting AdminPraise Menu Type and Items ' ));
		$this->executeQuery("DELETE FROM #__adminpraise_menu");
		$this->executeQuery("DELETE FROM #__adminpraise_menu_types");
	}

	public function createMenuItem($title, $link, $type, $parentId, $ordering, $menutypeId, $access = '', $params = null) {

		//$this->log->addEntry(array('comment' => 'Creating AdminPraise Menu Item: ' . $name ));
		
		$menuRow = & JTable::getInstance('AdminpraiseMenu', 'adminpraiseTable');
		$menuRow->title = $title;
		$menuRow->link = $link;
		$menuRow->type = $type;
		$menuRow->published = 1;
		$menuRow->parent_id = $parentId;
		$menuRow->ordering = $ordering;
		$menuRow->access = $access;
		$menuRow->params = "menu_image=-1\n";
		if ($params != null) {
			$menuRow->params .= $params;
		}
		$menuRow->menutype = $menutypeId;
		return $this->saveRow($menuRow);
	}

	public function createAdminPraiseTopMenu() {

		$menuTypeRow = & JTable::getInstance('MenuTypes', 'AdminpraiseTable');

		$menuTypeRow->title = 'AdminPraiseTopMenu';
		$menuTypeRow->description = 'AdminPraise Menu';
		$menuTypeId = $this->saveRow($menuTypeRow);
		
		//$this->log->addEntry(array('comment' => 'Creating AdminPraise Top Menu Items '));

		$ordering = 0;
		$siteMenuId = $this->createMenuItem('Site', 'index.php', 'url', 0, $ordering++, $menuTypeId, '',  "menu_class=home-item\n");

		$menusMenuId = $this->createMenuItem('Menus', 'index.php?option=com_menus', 'url', 0, $ordering++, $menuTypeId);
		$articlesMenuId = $this->createMenuItem('Articles', 'index.php?option=com_content', 'url', 0, $ordering++, $menuTypeId);
		$componentsMenuId = $this->createMenuItem('Apps', 'index.php?ap_task=list_components', 'url', 0, $ordering++, $menuTypeId);
		$modulesMenuId = $this->createMenuItem('Modules', 'index.php?option=com_modules', 'url', 0, $ordering++, $menuTypeId);
		$templatesMenuId = $this->createMenuItem('Templates', 'index.php?option=com_templates', 'url', 0, $ordering++, $menuTypeId);
		$userMenuId = $this->createMenuItem('Users', 'modules/mod_adminpraise_menu/dynamic/user.php', 'dynamic', 0, $ordering++, $menuTypeId);

		// Site
		$ordering = 0;
		$this->createMenuItem('Dashboard', JURI::base(), 'url', $siteMenuId, $ordering++, $menuTypeId);
		$this->createMenuItem('Preview Site', JURI::root(), 'url', $siteMenuId, $ordering++, $menuTypeId);

		// Menus
		$ordering = 0;
		$this->createMenuItem('Menu Manager', 'index.php?option=com_menus', 'url', $menusMenuId, $ordering++, $menuTypeId);
		$this->createMenuItem('Menu Trash', 'index.php?option=com_menus&view=items&menutype=&filter_published=-2', 'url', $menusMenuId, $ordering++, $menuTypeId);
		$this->createMenuItem('Separator', '', 'separator', $menusMenuId, $ordering++, $menuTypeId);
		$this->createMenuItem('Menus List', 'modules/mod_adminpraise_menu/dynamic/menus.php', 'dynamic', $menusMenuId, $ordering++, $menuTypeId);
		$this->createMenuItem('Separator', '', 'separator', $menusMenuId, $ordering++, $menuTypeId);
		$this->createMenuItem('New Menu', 'index.php?option=com_menus&view=menu&layout=edit', 'url', $menusMenuId, $ordering++, $menuTypeId);

		// Articles
		$ordering = 0;
		$articlesId = $this->createMenuItem('Articles', 'index.php?option=com_content', 'url', $articlesMenuId, $ordering++, $menuTypeId);
		$this->createMenuItem('New Article', 'index.php?option=com_content&view=article&layout=edit', 'url', $articlesId, $ordering++, $menuTypeId);
		
		$categoryId = $this->createMenuItem('Categories', 'index.php?option=com_categories&scope=content', 'url', $articlesMenuId, $ordering++, $menuTypeId);
		$this->createMenuItem('New Category', 'index.php?option=com_categories&view=category&layout=edit&extension=com_content', 'url', $categoryId, $ordering++, $menuTypeId);

		$this->createMenuItem('Archived Articles', 'index.php?option=com_content&filter_published=2', 'url', $articlesMenuId, $ordering++, $menuTypeId);
		$this->createMenuItem('Featured Articles', 'index.php?option=com_content&view=featured', 'url', $articlesMenuId, $ordering++, $menuTypeId);
		$this->createMenuItem('Article Trash', 'index.php?option=com_content&filter_published=-2', 'url', $articlesMenuId, $ordering++, $menuTypeId);


		// Components
		$ordering = 0;
		$this->createMenuItem('Components', 'modules/mod_adminpraise_menu/dynamic/components.php', 'dynamic', $componentsMenuId, $ordering++, $menuTypeId);

		// Modules
		$ordering = 0;
		$siteModulesId = $this->createMenuItem('Site Modules', 'index.php?option=com_modules&filter_client_id=0', 'url', $modulesMenuId, $ordering++, $menuTypeId);

		$this->createMenuItem('Published', 'index.php?option=com_modules&filter_client_id=0&filter_state=1', 'url', $siteModulesId, $ordering++, $menuTypeId);
		$this->createMenuItem('Separator', '', 'separator', $modulesMenuId, $ordering++, 1);
		$adminModulesId = $this->createMenuItem('Admin Modules', 'index.php?option=com_modules&filter_client_id=1', 'url', $modulesMenuId, $ordering++, $menuTypeId);
		$this->createMenuItem('Published', 'index.php?option=com_modules&filter_client_id=1&filter_state=1', 'url', $adminModulesId, $ordering++, $menuTypeId);

		// Templates
		$ordering = 0;
		$this->createMenuItem('Site Templates', 'index.php?option=com_templates&filter_client_id=0', 'url', $templatesMenuId, $ordering++, $menuTypeId);
		$this->createMenuItem('Admin Templates', 'index.php?option=com_templates&filter_client_id=1', 'url', $templatesMenuId, $ordering++, $menuTypeId);
	}
	
	public function createAdminpraiseToolsMenu() {
		$menuTypeRow = & JTable::getInstance('MenuTypes', 'AdminpraiseTable');
		$menuTypeRow->title = 'AdminpraiseToolsMenu';
		$menuTypeRow->description = 'System tools menu';
		$menuTypeId = $this->saveRow($menuTypeRow);
		
		//$this->log->addEntry(array('comment' => 'Creating AdminPraise Tools Menu Items '));
		
		$ordering = 0;
		$systemId = $this->createMenuItem('System', 'index.php?option=com_config', 'url', 0, $ordering++, $menuTypeId, '',  "menu_class=admin-item\n");
		
		$this->createMenuItem('Global Config', 'index.php?option=com_config', 'url', $systemId, $ordering++, $menuTypeId);
		$this->createMenuItem('System Info', 'index.php?option=com_admin&view=sysinfo', 'url', $systemId, $ordering++, $menuTypeId);
		$this->createMenuItem('Adminpraise Settings', 'index.php?option=com_adminpraise&view=settings', 'url', $systemId, $ordering++, $menuTypeId);
		$this->createMenuItem('Admin Modules', 'index.php?option=com_modules&filter_client_id=1', 'url', $systemId, $ordering++, $menuTypeId);
	
		$ordering = 0;
		$toolsId = $this->createMenuItem('Tools', 'index.php?option=com_installer', 'url', 0, $ordering++, $menuTypeId, '',  "menu_class=tools-item\n");
		
		$this->createMenuItem('Installer', 'index.php?option=com_installer', 'url', $toolsId, $ordering++, $menuTypeId);
		$this->createMenuItem('Plugins', 'index.php?option=com_plugins', 'url', $toolsId, $ordering++, $menuTypeId);
		$this->createMenuItem('Mass Mail', 'index.php?option=com_users&view=mail', 'url', $toolsId, $ordering++, $menuTypeId);
		$this->createMenuItem('Cache', 'index.php?option=com_cache', 'url', $toolsId, $ordering++, $menuTypeId);
		$this->createMenuItem('Media Manager', 'index.php?option=com_media', 'url', $toolsId, $ordering++, $menuTypeId);
		$this->createMenuItem('Preview', JURI::root(), 'url', $toolsId, $ordering++, $menuTypeId);
		$this->createMenuItem('Global Check-in', 'index.php?option=com_checkin', 'url', $toolsId, $ordering++, $menuTypeId);
		
	}
	
	public function createAdminpraisePanelMenu() {
		$menuTypeRow = & JTable::getInstance('MenuTypes', 'AdminpraiseTable');
		$menuTypeRow->title = 'AdminpraisePanelMenu';
		$menuTypeRow->description = 'The Components Panel on the left';
		$menuTypeId = $this->saveRow($menuTypeRow);
		
		//$this->log->addEntry(array('comment' => 'Creating AdminPraise Panel Menu Items '));

		$ordering = 0;

		$this->createMenuItem('Apps', 'modules/mod_adminpraise_menu/dynamic/components.php', 'dynamic', 0, $ordering++, $menuTypeId);
	}

}

?>
