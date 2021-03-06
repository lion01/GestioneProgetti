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
JHTML::_('script', 'spotlight.js', 'media/mod_adminpraise_spotlight/js/');
JHTML::_('stylesheet', 'spotlight.css', 'media/mod_adminpraise_spotlight/css/');
?>

<div class="ap-spotlight">
	<form action="<?php echo JRoute::_('index.php?option=com_adminpraise&view=search&controller=search&task=search&format=raw'); ?>" method="post" id="ap-spotlight">
		<input type="text" name="ap-search" id="ap-search" results="5" placeholder="Search..." autocomplete="on"/>

	</form>
</div>
