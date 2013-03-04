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

// No direct access.
defined('_JEXEC') or die;

jimport('joomla.plugin.plugin');

/**
 * Adminpraise extension plugin.
 *
 */
class plgExtensionAdminpraise extends JPlugin
{
	/**
	 * @var		integer Extension Identifier
	 * @since	1.6
	 */
	private $eid = 0;

	/**
	 * @var		JInstaller Installer object
	 * @since	1.6
	 */
	private $installer = null;

	/**
	 * Constructor
	 *
	 * @access      protected
	 * @param       object  $subject The object to observe
	 * @param       array   $config  An array that holds the plugin configuration
	 * @since       1.5
	 */
	public function __construct(& $subject, $config)
	{

		parent::__construct($subject, $config);
		$this->loadLanguage();
		$this->loadLanguage('com_adminpraise');
	}

	/**
	 * Called before uninstall
	 *
	 * @param	string	the extension identifier
	 */
	public function onExtensionBeforeUninstall($eid)
	{
		$db = JFactory::getDBO();
		$query = 'SELECT element FROM ' . $db->nameQuote('#__extensions')
				. ' WHERE extension_id = ' .$db->Quote($eid);
		$db->setQuery($query, 0, 1);
		
		$extension = $db->loadObject();
		
		if($extension->element == 'com_adminpraise') {
			$query = 'SELECT home FROM ' . $db->nameQuote('#__template_styles')
					. ' WHERE template = ' . $db->Quote('adminpraise3');
			$db->setQuery($query,0,1);
			
			$template = $db->loadObject();
			
			if(is_object($template)) {
				if($template->home == 1) {
					$appl = JFactory::getApplication();
					$appl->enqueueMessage(JText::_('COM_ADMINPRAISE_CHANGE_TEMPLATE_FIRST'));
					$appl->enqueueMessage(JText::_('COM_ADMINPRAISE_UNINSTALL_FAILED'));
					$appl->redirect('index.php?option=com_installer&view=manage');
					return false;
				}
			}
		}
		return false;
		
	}

	
}
