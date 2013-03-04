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
?>
<fieldset class="adminform">
<legend><?php echo JText::_( 'COM_ADMINPRAISE_REQUIREMENTS' ); ?></legend>
<?php
	//$this->rq['phpIs'] = '5.1';
	
	// Init requirements array
	$rq = array();

	/**
	 * Compare the PHP version
	 */
	if (version_compare($this->rq['phpMust'], $this->rq['phpIs'], '<')) {
		echo "<div class='rq'>".JText::_( 'COM_ADMINPRAISE_PHP_SUPPORTED' )."</div>";
	}else{
		echo "<div class='rq_false'>".JText::_( 'COM_ADMINPRAISE_PHP_UNSUPPORTED' )."</div>";
		$rq['php5'] = false;
	}

	/**
	 * Compare the MYSQL version
	 */
	if (version_compare($this->rq['mysqlMust'], $this->rq['mysqlIs'])) {
		echo "<div class='rq'>".JText::_( 'COM_ADMINPRAISE_MYSQL_SUPPORTED' )."</div>";
	}else{
		echo "<div class='rq_false'>".JText::_( 'COM_ADMINPRAISE_MYSQL_UNSUPPORTED' )."</div>";
		$rq['mysql'] = false;
	}

	/**
	 * Check if fopen is enabled
	 */
	if (function_exists('fopen')) {
		echo "<div class='rq'>".JText::_( 'COM_ADMINPRAISE_FOPEN_SUPPORTED' )."</div>";
	}else{
		echo "<div class='rq_false'>".JText::_( 'COM_ADMINPRAISE_FOPEN_UNSUPPORTED' )."</div>";
	}

	/**
	 * Check if fopen is enabled for remote connections
	 */
	if( ini_get('allow_url_fopen') ) {
		echo "<div class='rq'>".JText::_( 'COM_ADMINPRAISE_URL_FOPEN_SUPPORTED' )."</div>";
	}else{
		echo "<div class='rq_false'>".JText::_( 'COM_ADMINPRAISE_URL_FOPEN_UNSUPPORTED' )."</div>";
	}

	/**
	 * Print 'next' button if everything is ok
	 */
	if (!in_array(false, $rq)) {
		echo "<div class='pagination'><div class='numbers'><div class='button2-left'><div class='page'><span>1 of 7</span> <a href='".JRoute::_('index.php?option=com_adminpraise&task=step2')."'>".JText::_( 'COM_ADMINPRAISE_NEXT' )."</a>
		</div></div></div></div>";
	}
?>
</fieldset>
