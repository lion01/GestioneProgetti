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
jimport( 'joomla.installer.installer' );
jimport( 'joomla.installer.helper' );
jimport( 'joomla.filesystem.folder' );

?>
<fieldset class="adminform">
<legend><?php echo JText::_( 'COM_ADMINPRAISE_INSTALL_COMPONENT' ); ?></legend>
<?php

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
				JError::raiseWarning(1, 'JInstaller::install: '.JText::_('SQL Error')." ".$db->stderr(true));
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

	/**
	 * Print 'next' button if everything is ok
	 */
	if (!in_array(false, $error)) {
		echo "<div class='pagination'><div class='numbers'><div class='button2-left'><div class='page'><span>3 of 7</span> <a href='".JRoute::_('index.php?option=com_adminpraise&task=step4')."'>".JText::_( 'COM_ADMINPRAISE_NEXT' )."</a>
		</div></div></div></div>";
	}
?>
</fieldset>
