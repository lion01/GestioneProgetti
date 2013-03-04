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

jimport( 'joomla.filesystem.file' );

JFile::delete(JPATH_COMPONENT.DS.'installer.dummy.ini');

?>
<fieldset class="adminform">
<legend><?php echo JText::_( 'COM_ADMINPRAISE_INSTALL_SUCCESS' ); ?></legend>

<div class='success'><h3><?php echo JText::_( 'COM_ADMINPRAISE_INSTALL_COMPLETE' ); ?></h3>
</div>
<div class='pagination'><div class='numbers'><div class='button2-left'><div class='page'><span>7 of 7</span> <a href="<?php echo JRoute::_('index.php?option=com_adminpraise'); ?>" class='button'><?php echo JText::_( 'COM_ADMINPRAISE_NEXT' ); ?></a>
</div></div></div></div>

</fieldset>