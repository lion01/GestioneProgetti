<?php
/**
 *  @package AdminTools
 *  @copyright Copyright (c)2010-2011 Nicholas K. Dionysopoulos
 *  @license GNU General Public License version 3, or later
 */

// Protect from unauthorized access
defined('_JEXEC') or die();

class AdmintoolsDispatcher extends FOFDispatcher
{
	public function onBeforeDispatch() {
		$result = parent::onBeforeDispatch();
		
		if($result) {
			// Merge the language overrides
			$paths = array(JPATH_ROOT, JPATH_ADMINISTRATOR);
			$jlang = JFactory::getLanguage();
			$jlang->load($this->component, $paths[0], 'en-GB', true);
			$jlang->load($this->component, $paths[0], null, true);
			$jlang->load($this->component, $paths[1], 'en-GB', true);
			$jlang->load($this->component, $paths[1], null, true);
			
			$jlang->load($this->component.'.override', $paths[0], 'en-GB', true);
			$jlang->load($this->component.'.override', $paths[0], null, true);
			$jlang->load($this->component.'.override', $paths[1], 'en-GB', true);
			$jlang->load($this->component.'.override', $paths[1], null, true);
			// Live Update translation
			$jlang->load('liveupdate', JPATH_COMPONENT_ADMINISTRATOR.DIRECTORY_SEPARATOR.'liveupdate', 'en-GB', true);
			$jlang->load('liveupdate', JPATH_COMPONENT_ADMINISTRATOR.DIRECTORY_SEPARATOR.'liveupdate', $jlang->getDefault(), true);
			$jlang->load('liveupdate', JPATH_COMPONENT_ADMINISTRATOR.DIRECTORY_SEPARATOR.'liveupdate', null, true);

			// Load Akeeba Strapper
			if(!defined('ADMINTOOLSMEDIATAG')) {
				$staticFilesVersioningTag = md5(ADMINTOOLS_VERSION.ADMINTOOLS_DATE);
				define('ADMINTOOLSMEDIATAG', $staticFilesVersioningTag);
			}
			include_once JPATH_ROOT.'/media/akeeba_strapper/strapper.php';
			AkeebaStrapper::$tag = ADMINTOOLSMEDIATAG;
			AkeebaStrapper::bootstrap();
			AkeebaStrapper::jQueryUI();
			AkeebaStrapper::addCSSfile('media://com_admintools/css/backend.css');
			
			// Control Check
			$view = FOFInflector::singularize(FOFInput::getCmd('view',$this->defaultView, $this->input));
			// ========== Master PW check ==========
			$model = FOFModel::getAnInstance('Masterpw','AdmintoolsModel');
			if(!$model->accessAllowed())
			{
				$url = ($viewName == 'cpanel') ? 'index.php' : 'index.php?option=com_admintools&view=cpanel';
				JFactory::getApplication()->redirect($url, JText::_('ATOOLS_ERR_NOTAUTHORIZED'), 'error');
				return;
			}
		}
		
		return $result;
	}
	
	public function dispatch() {
		// Handle Live Update requests
		if(!class_exists('LiveUpdate')) {
			require_once JPATH_ADMINISTRATOR.'/components/com_admintools/liveupdate/liveupdate.php';
			if((FOFInput::getCmd('view','',$this->input) == 'liveupdate')) {
				LiveUpdate::handleRequest();
				return;
			}
		}
		
		parent::dispatch();
	}
}