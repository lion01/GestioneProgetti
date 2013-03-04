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
if(file_exists( JPATH_COMPONENT_ADMINISTRATOR . DS . 'installer.dummy.ini') ) {
	require_once (JPATH_COMPONENT_ADMINISTRATOR.DS.'install'.DS.'adminpraiseinstall.php');
	AdminpraiseInstall::handleRequest();
	return;
}
/**
 * Calling ALU to live updating
 * @since	0.3.3
 */
require_once JPATH_COMPONENT_ADMINISTRATOR.DS.'library'.DS.'liveupdate'.DS.'liveupdate.php';
if(JRequest::getCmd('view','') == 'liveupdate') {
    LiveUpdate::handleRequest();
    return;
}

require_once(JPATH_COMPONENT . DS . 'library' . DS . 'joomla' . DS .'application' . DS . 'component' . DS . 'controller.php');
require_once(JPATH_COMPONENT . DS . 'library' . DS . 'joomla' . DS .'application' . DS . 'component' . DS . 'view.php');
require_once(JPATH_COMPONENT_ADMINISTRATOR . DS . 'helpers' . DS .'helper.php');

// check if the adminpraise3 template is on and output a message if not - 
// should be displayed all over the component, that is why we put it here
// not a nice OOP, but works..
if(JRequest::getCmd('view','') != 'activity') {
	AdminpraiseHelper::turnAdminpraiseOn();
}
$controller = JRequest::getCmd('view');
if ($controller) {
	$path = JPATH_COMPONENT . DS . 'controllers' . DS . $controller . '.php';
	if (file_exists($path)) {
		require_once $path;
	} else {
		$controller = '';
	}
}

if ($controller == '') {
	require_once(JPATH_COMPONENT . DS . 'controllers' . DS . 'cpanel.php');
	$controller = 'Cpanel';
}

// Create the controller
$classname = 'AdminpraiseController' . ucfirst($controller);
$controller = new $classname( );

// Perform the Request task
$controller->execute(JRequest::getVar('task'));

// Redirect if set by the controller
$controller->redirect();
?>
