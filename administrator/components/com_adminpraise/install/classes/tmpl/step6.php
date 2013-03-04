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
<legend><?php echo JText::_( 'COM_ADMINPRAISE_INSTALL_TEMPLATE' ); ?></legend>
<?php

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

	/**
	 * Print 'next' button if everything is ok
	 */
	if (!in_array(false, $error)) {
		echo "<div class='pagination'><div class='numbers'><div class='button2-left'><div class='page'><span>6 of 7</span> <a href='".JRoute::_('index.php?option=com_adminpraise&task=step7')."'>".JText::_( 'COM_ADMINPRAISE_NEXT' )."</a>
		</div></div></div></div>";
	}
?>
</fieldset>