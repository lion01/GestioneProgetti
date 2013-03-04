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
defined( '_JEXEC' ) or die( 'Restricted access' );
?>

<h3><?php echo JText::_( 'EXTEND' );?></h3>
<ul>
	<li><a href="index.php?option=com_installer"><?php echo JText::_( 'INSTALL/UNINSTALL' );?></a></li>
	<li><a href="index.php?option=com_installer&task=manage&type=components"><?php echo JText::_( 'MANAGE COMPONENTS' );?></a></li>
	<li><a href="index.php?option=com_installer&task=manage&type=modules"><?php echo JText::_( 'MANAGE MODULES' );?></a></li>
	<li><a href="index.php?option=com_installer&task=manage&type=plugins"><?php echo JText::_( 'MANAGE PLUGINS' );?></a></li>
</ul>