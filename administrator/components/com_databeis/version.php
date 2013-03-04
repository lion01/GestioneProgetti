<?php
/**
* $Id: version.php 928 2012-06-25 15:58:55Z eaxs $
* @package   Databeis
* @copyright Copyright (C) 2006-2010 DataBeis. All rights reserved.
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL, see LICENSE.php
*
* This file is part of Databeis.
*
* Databeis is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
*
* Databeis is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License
* along with Databeis.  If not, see <http://www.gnu.org/licenses/gpl.html>.
**/

defined( '_JEXEC' ) or die( 'Restricted access' );

// Databeis version number
if(!defined('PF_VERSION_NUM'))   define('PF_VERSION_NUM', 3010);

// Databeis version state
if(!defined('PF_VERSION_STATE')) define('PF_VERSION_STATE', 'Stable');

// Databeis version string
if(!defined('PF_VERSION_STRING')) define('PF_VERSION_STRING', 'Version 3.0.10 Stable');

// Databeis installer version
if(!defined('PF_VERSION_EXTINS')) define('PF_VERSION_EXTINS', '2.2,3.0');

// Databeis content/index class
if(!defined('PF_CONTENT_CLASS')) {
    define('PF_CONTENT_CLASS', 1);

    class PFcontent
    {
        public function Sections()
        {
            $data = array('board', 'calendar', 'config', 'controlpanel',
            'filemanager', 'groups', 'profile', 'projects', 'tasks',
            'time', 'users');

            return $data;
        }

        public function Panels()
        {
            $data = array('cp_events', 'cp_news', 'cp_tasks', 'cp_installer', 'cp_weblinks',
            'cp_welcome', 'nav_section', 'nav_section_subnav', 'note_comments',
            'note_details', 'note_info', 'profile_contact', 'profile_location',
            'profile_networks', 'profile_user', 'project_details', 'project_desc',
            'project_join', 'project_logo', 'project_tasks', 'quicklink_project',
            'quicklink_version', 'system_console', 'system_messages', 'task_attachments',
            'task_comments', 'task_content', 'task_details', 'theme_logo', 'time_tracking');

            return $data;
        }

        public function Processes()
        {
            $data = array('avatar', 'comments', 'dbopt', 'demo', 'projects_mailer',
            'tasks_mailer');

            return $data;
        }

        public function Themes()
        {
            $data = array('default');

            return $data;
        }

        public function Languages()
        {
            $data = array('english');

            return $data;
        }
    }
}
?>