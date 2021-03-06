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

jimport('joomla.application.component.model');

/**
 * The Live Update MVC model
 */
class AdminpraiseInstallModel extends JModel
{
	public function determineRequirements() {
		$requirements = array();
		
		$requirements['phpMust'] = '5.2';
		$requirements['phpIs'] = PHP_VERSION;
		
		$requirements['mysqlMust'] = '5.1';
		$requirements['mysqlIs'] = @mysql_get_server_info();
		
		return $requirements;
	}
}
