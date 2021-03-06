<?php
/**
 *  @package FrameworkOnFramework
 *  @copyright Copyright (c)2010-2012 Nicholas K. Dionysopoulos
 *  @license GNU General Public License version 3, or later
 */

// Protect from unauthorized access
defined('_JEXEC') or die();

class FOFTemplateUtils
{
	public static function addCSS($path)
	{
		$url = self::parsePath($path);
		JFactory::getDocument()->addStyleSheet($url);
	}
	
	public static function addJS($path)
	{
		$url = self::parsePath($path);
		JFactory::getDocument()->addScript($url);
	}
	
	public static function parsePath($path)
	{
		$protoAndPath = explode('://', $path, 2);
		if(count($protoAndPath) < 2) {
			$protocol = 'media';
		} else {
			$protocol = $protoAndPath[0];
			$path = $protoAndPath[1];
		}
		
		$url = JURI::root();
		
		switch($protocol) {
			case 'media':
				// Do we have a media override in the template?
				$pathAndParams = explode('?', $path, 2);
				$altPath = JPATH_BASE.'/templates/'.JFactory::getApplication()->getTemplate().'/media/'.$pathAndParams[0];
				if(file_exists($altPath)) {
					$isAdmin = version_compare(JVERSION, '1.6.0', 'ge') ? (!JFactory::$application ? false : JFactory::getApplication()->isAdmin()) : JFactory::getApplication()->isAdmin();
					$url .= $isAdmin ? 'administrator/' : '';
					$url .= '/templates/'.JFactory::getApplication()->getTemplate().'/media/';
				} else {
					$url .= 'media/';
				}
				break;
			
			case 'admin':
				$url .= 'administrator/';
				break;
			
			default:
			case 'site':
				break;
		}
		
		$url .= $path;
		
		return $url;
	}
	
	public static function loadPosition($position, $style = -2)
	{
		$document	= JFactory::getDocument();
		$renderer	= $document->loadRenderer('module');
		$params		= array('style'=>$style);
		
		$contents = '';
		foreach (JModuleHelper::getModules($position) as $mod)  {
			$contents .= $renderer->render($mod, $params);
		}
		return $contents;
	}
	
        /**
         * Merges the current url with new or changed parameters.
         * 
         * This method merges the route string with the url parameters defined
         * in current url. The parameters defined in current url, but not given
         * in route string, will automatically reused in the resulting url. 
         * But only these following parameters will be reused:
         * 
         * option, view, layout, format
         * 
         * Example:
         * 
         * Assuming that current url is: 
         * http://fobar.com/index.php?option=com_foo&view=cpanel
         * 
         * <code>
         * <?php echo FOFTemplateutils::route('view=categories&layout=tree'); ?>
         * </code>
         * 
         * Result: 
         * http://fobar.com/index.php?option=com_foo&view=categories&layout=tree
         * 
         * @param string $route    The parameters string
         * @return string          The human readable, complete url
         */
	public static function route($route = '')
    {
        $route = trim($route);

        // Special cases
        if ($route == 'index.php' || $route == 'index.php?')
        {
            $result = $route;
        }
        else if (substr($route, 0, 1) == '&')
        {
            $url = JURI::getInstance();
            $vars = array();
            parse_str($route, $vars);

            $url->setQuery(array_merge($url->getQuery(true), $vars));

            $result = 'index.php?' . $url->getQuery();
        }
        else
        {

            $url = JURI::getInstance();
            $props = $url->getQuery(true);

            // Strip 'index.php?'
            if (substr($route, 0, 10) == 'index.php?')
            {
                $route = substr($route, 10);
            }

            // Parse route
            $parts = array();
            parse_str($route, $parts);
            $result = array();

            // Check to see if there is component information in the route if not add it
            if (!isset($parts['option']) && isset($props['option']))
            {
                $result[] = 'option=' . $props['option'];
            }

            // Add the layout information to the route only if it's not 'default'
            if (!isset($parts['view']) && isset($props['view']))
            {
                $result[] = 'view=' . $props['view'];
                if (!isset($parts['layout']) && isset($props['layout']))
                {
                    $result[] = 'layout=' . $props['layout'];
                }
            }

            // Add the format information to the URL only if it's not 'html'
            if (!isset($parts['format']) && isset($props['format']) && $props['format'] != 'html')
            {
                $result[] = 'format=' . $props['format'];
            }

            // Reconstruct the route
            if (!empty($route))
            {
                $result[] = $route;
            }

            $result = 'index.php?' . implode('&', $result);
        }

        return JRoute::_($result);
    }
}