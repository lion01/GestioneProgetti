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
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');
require_once(JPATH_COMPONENT_ADMINISTRATOR . '/helpers/menu.php' );


class AdminpraiseModelMenu extends JModel {

	private $_table = null;
	private $_state = null;

	public function &getData() {
		$mainframe = JFactory::getApplication();

		static $items;

		if (isset($items)) {
			return $items;
		}

		$db = & $this->getDBO();

		$filter_order = $mainframe->getUserStateFromRequest('com_adminpraise.menu.filter_order', 'filter_order', 'm.ordering', 'cmd');
		$filter_order_Dir = $mainframe->getUserStateFromRequest('com_adminpraise.menu.filter_order_Dir', 'filter_order_Dir', 'ASC', 'word');
		$filter_state = $mainframe->getUserStateFromRequest('com_adminpraise.menu.filter_state', 'filter_state', '', 'word');
		$limit = $mainframe->getUserStateFromRequest('global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
		$limitstart = $mainframe->getUserStateFromRequest('com_adminpraise.menu.limitstart', 'limitstart', 0, 'int');
		$levellimit = $mainframe->getUserStateFromRequest('com_adminpraise.menu.levellimit', 'levellimit', 10, 'int');
		$search = $mainframe->getUserStateFromRequest('com_adminpraise.menu.search', 'search', '', 'string');
		if (strpos($search, '"') !== false) {
			$search = str_replace(array('=', '<'), '', $search);
		}
		$search = JString::strtolower($search);

		$and = '';
		if ($filter_state) {
			if ($filter_state == 'P') {
				$and = ' AND m.published = 1';
			} else if ($filter_state == 'U') {
				$and = ' AND m.published = 0';
			}
		}
		
		if(JRequest::getInt('menutype')) {
			$and .= ' AND m.menutype = ' . JRequest::getInt('menutype');
		} else {
			$justAMenu = $this->getAMenuType();
			if($justAMenu) {
				$and .= ' AND m.menutype = ' . $justAMenu ;
			}
		}

		// ensure $filter_order has a good value
		if (!in_array($filter_order, array('m.title', 'm.published', 'm.ordering', 'm.type', 'm.id'))) {
			$filter_order = 'm.ordering';
		}

		if (!in_array(strtoupper($filter_order_Dir), array('ASC', 'DESC', ''))) {
			$filter_order_Dir = 'ASC';
		}

		// just in case filter_order get's messed up
		if ($filter_order) {
			$orderby = ' ORDER BY ' . $filter_order . ' ' . $filter_order_Dir . ', m.parent_id, m.ordering';
		} else {
			$orderby = ' ORDER BY m.parent_id, m.ordering';
		}

		// select the records
		// note, since this is a tree we have to do the limits code-side
		if ($search) {
			$query = 'SELECT m.id' .
					' FROM #__adminpraise_menu AS m' .
					' WHERE LOWER( m.title ) LIKE ' . $db->Quote('%' . $db->getEscaped($search, true) . '%', false) .
					$and;
			$db->setQuery($query);
			$search_rows = $db->loadResultArray();
		}

		$query = 'SELECT m.*' .
				' FROM #__adminpraise_menu AS m' .
				' LEFT JOIN ' . $db->nameQuote('#__adminpraise_menu_types') . ' AS t' .
				' ON m.menutype = t.id' .
				' WHERE m.published != -2' .
				$and .
				$orderby;
		$db->setQuery($query);
		$rows = $db->loadObjectList();

		// establish the hierarchy of the menu
		$children = array();
		// first pass - collect children
		foreach ($rows as $v) {
			$pt = $v->parent_id;
			$list = @$children[$pt] ? $children[$pt] : array();
			array_push($list, $v);
			$children[$pt] = $list;
		}
		// second pass - get an indent list of the items
		$list = JHTML::_('menu.treerecurse', 0, '', array(), $children, max(0, $levellimit - 1));
		// eventually only pick out the searched items.
		if ($search) {
			$list1 = array();

			foreach ($search_rows as $sid) {
				foreach ($list as $item) {
					if ($item->id == $sid) {
						$list1[] = $item;
					}
				}
			}
			// replace full list with found items
			$list = $list1;
		}

		$total = count($list);

		jimport('joomla.html.pagination');
		$this->_pagination = new JPagination($total, $limitstart, $limit);

		// slice out elements based on limits
		$list = array_slice($list, $this->_pagination->limitstart, $this->_pagination->limit);

		$i = 0;
		$query = array();
		foreach ($list as $mitem) {
			$edit = '';
			switch ($mitem->type) {
				case 'separator':
					$list[$i]->descrip = JText::_('COM_ADMINPRAISE_SEPARATOR');
					break;

				case 'url':
					$list[$i]->descrip = JText::_('COM_ADMINPRAISE_URL');
					break;


				case 'dynamic':
					$list[$i]->descrip = JText::_('COM_ADMINPRAISE_DYNAMIC_LINK');
					break;
				default:
					$list[$i]->descrip = JText::_('COM_ADMINPRAISE_UNKNOWN');
					break;
			}
			$i++;
		}

		$items = $list;
		return $items;
	}
	
	/**
	 * This function tries to get an existing menu type and returns the id of the first element. 
	 * It is called each time
	 * the url doesn't have a menutype element
	 * @return mixed 
	 */
	public function getAMenuType() {
		$table = $this->getTable('MenuTypes', 'AdminpraiseTable');
		$allMenuTypes = $table->getAllMenuTypes();
		
		if(count($allMenuTypes)) {
			return $allMenuTypes[0]->id;
		}
		return 0;
	}
	
	public function getAllMenuTypes() {
		$table = $this->getTable('MenuTypes', 'AdminpraiseTable');
		
		return $table->getAllMenuTypes();
	}

	public function &getPagination() {
		if ($this->_pagination == null) {
			$this->getItems();
		}
		return $this->_pagination;
	}
	
	public function getMenuType() {
		static $menuItem;
		if (isset($menuItem)) {
			return $menuItem;
		}

		$table = $this->getTable('MenuTypes', 'AdminpraiseTable');

		// Load the current item if it has been defined
		$edit = JRequest::getVar('edit', true);
		$cid = JRequest::getVar('cid', array(0), '', 'array');
		JArrayHelper::toInteger($cid, array(0));
		if ($edit) {
			$table->load($cid[0]);
		}
		$menuItem = $table;
		return $menuItem;
		
	}

	public function getItem() {
		static $item;
		if (isset($item)) {
			return $item;
		}

		$table = & $this->_getTable();

		// Load the current item if it has been defined
		$edit = JRequest::getVar('edit', true);
		$cid = JRequest::getVar('cid', array(0), '', 'array');
		JArrayHelper::toInteger($cid, array(0));
		if ($edit) {
			$table->load($cid[0]);
		}


		// Override the current item's type field if defined in the request
		if ($type = JRequest::getString('type')) {
			$table->type = $type;
		}
		
		// Override the current item's menutype field if defined in the request
		if ($menutype = JRequest::getVar('menutype', '', '', 'menutype')) {
			$table->menutype = $menutype;
		}

		switch ($table->type) {
			case 'separator':
				$table->link = null;
				break;
			case 'dynamic':
				$dynamicType = JRequest::getVar('dynamicType', '', '', '0');
				if($dynamicType) {
					$dynamicLinks = AdminpraiseMenuHelper::getDynamicLinks();
					$table->link = $dynamicLinks[$dynamicType]['link'];
				}
				break;
		}

		$item = $table;

		return $item;
	}

	private function &_getTable() {
		if ($this->_table == null) {
			$this->_table = & $this->getTable('AdminpraiseMenu', 'AdminpraiseTable');
		}
		return $this->_table;
	}

	public function &getStateParams() {
		// Get the state parameters
		$item = & $this->getItem();
		$params = new JParameter($item->params);

		if ($state = & $this->_getStateXML()) {
			if (is_a($state, 'JSimpleXMLElement')) {
				$sp = & $state->getElementByPath('params');
				$params->setXML($sp);
			}
		}
		return $params;
	}

	/**
	 * Get the name of the current menu item
	 *
	 * @return	string
	 * @access	public
	 * @since	1.5
	 */
	public function getStateName() {
		$state = & $this->_getStateXML();

		if (!is_a($state, 'JSimpleXMLElement')) {
			return null;
		}

		$name = null;
		$sn = & $state->getElementByPath('name');
		if ($sn) {
			$name = $sn->data();
		}
		
		$dynamicType = JRequest::getVar('dynamicType', '', '', '0');
		if($dynamicType) {
			$dynamicLinks = AdminpraiseMenuHelper::getDynamicLinks();
			$name = $dynamicLinks[$dynamicType]['name'];
		}
				
		return JText::_($name);
	}

	/**
	 * Get the description of the current menu item
	 *
	 * @return	string
	 * @access	public
	 * @since	1.5
	 */
	public function getStateDescription() {
		$state = & $this->_getStateXML();


		if (!is_a($state, 'JSimpleXMLElement')) {
			return null;
		}

		$description = null;
		$sd = & $state->getElementByPath('description');
		if ($sd) {
			$description = $sd->data();
		}
		$dynamicType = JRequest::getVar('dynamicType', '', '', '0');
		if($dynamicType) {
			$dynamicLinks = AdminpraiseMenuHelper::getDynamicLinks();
			$description = $dynamicLinks[$dynamicType]['description'];
		}

		return JText::_($description);
	}

	public function &_getStateXML() {
		static $xml;

		if (isset($xml)) {
			return $xml;
		}
		$xml = null;
		$xmlpath = null;
		$item = &$this->getItem();

		switch ($item->type) {
			case 'separator':
				$xmlpath = JPATH_BASE . DS . 'components' . DS . 'com_adminpraise' . DS . 'models' . DS . 'metadata' . DS . 'separator.xml';
				break;
			case 'url':
				$xmlpath = JPATH_BASE . DS . 'components' . DS . 'com_adminpraise' . DS . 'models' . DS . 'metadata' . DS . 'url.xml';
				break;
			case 'dynamic':
			default:
				$xmlpath = JPATH_BASE . DS . 'components' . DS . 'com_adminpraise' . DS . 'models' . DS . 'metadata' . DS . 'dynamic.xml';
				break;
		}

		if (file_exists($xmlpath)) {
			$xml = & JFactory::getXMLParser('Simple');
			if ($xml->loadFile($xmlpath)) {
				$this->_xml = &$xml;
				$document = & $xml->document;

				/*
				 * HANDLE NO OPTION CASE
				 */
				$menus = & $document->getElementByPath('menu');
				if (is_a($menus, 'JSimpleXMLElement') && $menus->attributes('options') == 'none') {
					$xml = & $menus->getElementByPath('state');
				} else {
					$xml = & $document->getElementByPath('state');
				}

				// Handle error case... path doesn't exist
				if (!is_a($xml, 'JSimpleXMLElement')) {
					return $document;
				}

				/*
				 * HANDLE A SWITCH IF IT EXISTS
				 */
				if ($switch = $xml->attributes('switch')) {
					$default = $xml->attributes('default');
					// Handle switch
					$switchVal = (isset($item->linkparts[$switch])) ? $item->linkparts[$switch] : 'default';
					$found = false;

					foreach ($xml->children() as $child) {
						if ($child->name() == $switchVal) {
							$xml = & $child;
							$found = true;
							break;
						}
					}

					if (!$found) {
						foreach ($xml->children() as $child) {
							if ($child->name() == $default) {
								$xml = & $child;
								break;
							}
						}
					}
				}

				/*
				 * HANDLE INCLUDED PARAMS
				 */
				$children = $xml->children();
				if (count($children) == 1) {
					if ($children[0]->name() == 'include') {
						$ret = & $this->_getIncludedParams($children[0]);
						if ($ret) {
							$xml = & $ret;
						}
					}
				}

				if ($switch = $xml->attributes('switch')) {
					$default = $xml->attributes('default');
					// Handle switch
					$switchVal = ($item->linkparts[$switch]) ? $item->linkparts[$switch] : 'default';
					$found = false;

					foreach ($xml->children() as $child) {
						if ($child->name() == $switchVal) {
							$xml = & $child;
							$found = true;
							break;
						}
					}

					if (!$found) {
						foreach ($xml->children() as $child) {
							if ($child->name() == $default) {
								$xml = & $child;
								break;
							}
						}
					}
				}
			}
		}
		return $xml;
	}

	public function &_getIncludedParams($include) {
		$tags = array();
		$state = null;
		$source = $include->attributes('source');
		$path = $include->attributes('path');
		$item = &$this->getItem();

		preg_match_all("/{([A-Za-z\-_]+)}/", $source, $tags);
		if (isset($tags[1])) {
			for ($i = 0; $i < count($tags[1]); $i++) {
				$source = str_replace($tags[0][$i], @$item->linkparts[$tags[1][$i]], $source);
			}
		}

		// load the source xml file
		if (file_exists(JPATH_ROOT . $source)) {
			$xml = & JFactory::getXMLParser('Simple');

			if ($xml->loadFile(JPATH_ROOT . $source)) {
				$document = &$xml->document;
				$state = $document->getElementByPath($path);
			}
		}
		return $state;
	}

	/**
	 * Sets the sublevel for menu items
	 *
	 * @param array id values to set
	 * @param int level to assign to the sublevel
	 */
	public function _setSubLevel($cid, $level) {
		JArrayHelper::toInteger($cid, array(0));

		$ids = implode(',', $cid);

		$query = 'UPDATE #__adminpraise_menu SET sublevel = ' . (int) $level
				. ' WHERE id IN (' . $ids . ')';
		$this->_db->setQuery($query);
		$this->_db->query();

		$query = 'SELECT id FROM #__adminpraise_menu WHERE parent_id IN (' . $ids . ')';
		$this->_db->setQuery($query);
		$cids = $this->_db->loadResultArray(0);

		if (!empty($cids)) {
			$this->_setSubLevel($cids, $level + 1);
		}
	}

	public function remove($items) {
		$db = & $this->getDBO();
		$nd = $db->getNullDate();
		$state = -2;
		$row = & $this->getTable('AdminpraiseMenu', 'AdminpraiseTable');
		$default = 0;

		// Add all children to the list
		foreach ($items as $id) {
			//Check if it's the default item
			$row->load($id);

			$this->_addChildren($id, $items);
			$default++;
		}
		if (!empty($items)) {
			// Sent menu items to the trash
			JArrayHelper::toInteger($items, array(0));
			$where = ' WHERE (id = ' . implode(' OR id = ', $items) . ') AND home = 0';
			$query = 'DELETE FROM #__adminpraise_menu' .
					$where;
			$db->setQuery($query);
			if (!$db->query()) {
				$this->setError($db->getErrorMsg());
				return false;
			}
		}

		// clean cache
		AdminpraiseMenuHelper::cleanCache();

		return count($items);
	}

	public function _addChildren($id, &$list) {
		// Initialize variables
		$return = true;

		// Get all rows with parent_id of $id
		$db = & $this->getDBO();
		$query = 'SELECT id' .
				' FROM #__adminpraise_menu' .
				' WHERE parent_id = ' . (int) $id;
		$db->setQuery($query);
		$rows = $db->loadObjectList();

		// Make sure there aren't any errors
		if ($db->getErrorNum()) {
			$this->setError($db->getErrorMsg());
			return false;
		}

		// Recursively iterate through all children... kinda messy
		// TODO: Cleanup this method
		foreach ($rows as $row) {
			$found = false;
			foreach ($list as $idx) {
				if ($idx == $row->id) {
					$found = true;
					break;
				}
			}
			if (!$found) {
				$list[] = $row->id;
			}
			$return = $this->_addChildren($row->id, $list);
		}
		return $return;
	}

	/**
	 * Set the state of selected menu items
	 */
	public function setItemState($items, $state) {
		if (is_array($items)) {
			$row = & $this->getTable('AdminpraiseMenu', 'adminpraiseTable');
			foreach ($items as $id) {
				$row->load($id);

				$row->published = $state;

				if ($state != 1) {
					// Set any alias menu types to not point to unpublished menu items
					$db = &$this->getDBO();
					$query = 'UPDATE #__adminpraise_menu SET link = 0 WHERE type = \'menulink\' AND link = ' . (int) $id;
					$db->setQuery($query);
					if (!$db->query()) {
						$this->setError($db->getErrorMsg());
						return false;
					}
				}

				if (!$row->check()) {
					$this->setError($row->getError());
					return false;
				}
				if (!$row->store()) {
					$this->setError($row->getError());
					return false;
				}
			}
		}

		// clean cache
		AdminpraiseMenuHelper::cleanCache();

		return true;
	}

	public function orderItem($item, $movement) {
		$row = & $this->getTable('AdminpraiseMenu', 'adminpraiseTable');
		$row->load($item);
		if (!$row->move($movement, 'menutype = '.$this->_db->Quote($row->menutype).' AND  parent_id = ' . (int) $row->parent_id)) {
			$this->setError($row->getError());
			return false;
		}

		// clean cache
		AdminpraiseMenuHelper::cleanCache();

		return true;
	}

	public function setOrder($items) {
		$total = count($items);
		$row = & $this->getTable('AdminpraiseMenu', 'adminpraiseTable');
		$groupings = array();

		$order = JRequest::getVar('order', array(), 'post', 'array');
		JArrayHelper::toInteger($order);

		// update ordering values
		for ($i = 0; $i < $total; $i++) {
			$row->load($items[$i]);
			// track parents
			$groupings[] = $row->parent_id;
			if ($row->ordering != $order[$i]) {
				$row->ordering = $order[$i];
				if (!$row->store()) {
					$this->setError($row->getError());
					return false;
				}
			} // if
		} // for
		// execute updateOrder for each parent group
		$groupings = array_unique($groupings);
		foreach ($groupings as $group) {
			$row->reorder('menutype = '.$this->_db->Quote($menutype).' AND parent_id = ' . (int) $group . ' AND published >=0');
		}

		// clean cache
		AdminpraiseMenuHelper::cleanCache();

		return true;
	}

	/**
	 * Set the access of selected menu items
	 */
	public function setAccess($items, $access) {
		$row = & $this->getTable('AdminpraiseMenu', 'adminpraiseTable');
		foreach ($items as $id) {
			$row->load($id);
			$row->access = $access;

			// Set any alias menu types to not point to unpublished menu items
			$db = &$this->getDBO();
			$query = 'UPDATE #__adminpraise_menu SET link = 0 WHERE type = \'menulink\' AND access < ' . (int) $access . ' AND link = ' . (int) $id;
			$db->setQuery($query);
			if (!$db->query()) {
				$this->setError($db->getErrorMsg());
				return false;
			}

			if (!$row->check()) {
				$this->setError($row->getError());
				return false;
			}
			if (!$row->store()) {
				$this->setError($row->getError());
				return false;
			}
		}

		// clean cache
		AdminpraiseMenuHelper::cleanCache();

		return true;
	}

	public function store() {
		// Initialize variables
		$db = & JFactory::getDBO();
		$row = & $this->getItem();
//		$post = $this->_state->get('request');
		$post = $this->getState('request');

		if (!$row->bind($post)) {
			echo "<script> alert('" . $row->getError(true) . "'); window.history.go(-1); </script>\n";
			return false;
		}

		if ($row->id > 0) {

			$query = 'SELECT parent_id FROM #__adminpraise_menu WHERE id = ' . (int) $row->id;
			$this->_db->setQuery($query);
			$oldParent = $this->_db->loadResult();
			if ($oldParent != $row->parent_id) {
				// we have changed parents, so we have to fix the submenu values
				if ($row->parent_id != 0) {
					$query = 'SELECT sublevel FROM #__adminpraise_menu WHERE id = ' . (int) $row->parent_id;
					$this->_db->setQuery($query);
					$sublevel = $this->_db->loadResult() + 1;
				} else {
					$sublevel = 0;
				}
				$row->sublevel = $sublevel;
				$this->_setSubLevel(array((int) $row->id), $sublevel);
			}
		} else {
			// if new item order last in appropriate group
			$where = " published >= 0 AND parent_id = " . (int) $row->parent_id;
			$row->ordering = $row->getNextOrder($where);

			if ($row->parent_id != 0) {
				$query = 'SELECT sublevel FROM #__adminpraise_menu WHERE id = ' . (int) $row->parent_id;
				$this->_db->setQuery($query);
				$row->sublevel = $this->_db->loadResult() + 1;
			}
		}

		if (isset($post['urlparams']) && is_array($post['urlparams'])) {
			$pos = strpos($row->link, '?');
			if ($pos !== false) {
				$prefix = substr($row->link, 0, $pos);
				$query = substr($row->link, $pos + 1);

				$temp = array();
				if (strpos($query, '&amp;') !== false) {
					$query = str_replace('&amp;', '&', $query);
				}
				parse_str($query, $temp);
				$temp2 = array_merge($temp, $post['urlparams']);

				$temp3 = array();
				foreach ($temp2 as $k => $v) {
					if ($k && strlen($v)) {
						$temp3[] = $k . '=' . $v;
					}
				}
				$url = null;
				$row->link = $prefix . '?' . implode('&', $temp3);
			}
		}


		if (!$row->check()) {
			echo "<script> alert('" . $row->getError(true) . "'); window.history.go(-1); </script>\n";
			return false;
		}
		
		if (!$row->store()) {
			echo "<script> alert('" . $row->getError(true) . "'); window.history.go(-1); </script>\n";
			return false;
		}

		$row->checkin();
		$row->reorder('parent_id=' . (int) $row->parent_id);

		// clean cache
		AdminpraiseMenuHelper::cleanCache();

		return true;
	}
	
	public function storeMenuType() {
		// Initialize variables
		$db = & JFactory::getDBO();
		$row = & $this->getMenuType();
		$post = $this->_state->get('request');
		
		if (!$row->bind($post)) {
			echo "<script> alert('" . $row->getError(true) . "'); window.history.go(-1); </script>\n";
			return false;
		}
		
		if (!$row->check()) {
			echo "<script> alert('" . $row->getError(true) . "'); window.history.go(-1); </script>\n";
			return false;
		}
		
		if (!$row->store()) {
			echo "<script> alert('" . $row->getError(true) . "'); window.history.go(-1); </script>\n";
			return false;
		}

		$row->checkin();
		
		return true;
		
	}
	
	public function removeMenuType($id) {
		$db = & $this->getDBO();

		$query = 'DELETE FROM ' . $db->nameQuote('#__adminpraise_menu_types')
				. ' WHERE id = ' . $db->Quote($id);
		$db->setQuery($query);
		if($db->Query()) {
			$query = 'DELETE FROM ' . $db->nameQuote('#__adminpraise_menu')
					. ' WHERE menutype = ' . $db->Quote($id);
			$db->setQuery($query);
			if($db->Query()) {
				return true;
			} else {
				$this->setError($error);
				return false;
			}
		} else {
			$this->setError($db->getErrorMsg());
			return false;
		}

		// clean cache
		AdminpraiseMenuHelper::cleanCache();

		return count($items);
	}
	
	/**
	* Save the item(s) to the menu selected
	*/
	function copy( $items, $menu )
	{
		$curr =& $this->getTable('AdminpraiseMenu', 'AdminpraiseTable');
		$itemref = array();
		foreach ($items as $id)
		{
			$curr->load( $id );
			$curr->id	= NULL;
//			$curr->home	= 0;
			if ( !$curr->store() ) {
				$this->setError($curr->getError());
				return false;
			}
			$itemref[] = array($id, $curr->id);
		}
		foreach ($itemref as $ref)
		{
			$curr->load( $ref[1] );
			if ($curr->parent_id!=0) {
				$found = false;
				foreach ( $itemref as $ref2 )
				{
					if ($curr->parent_id == $ref2[0]) {
						$curr->parent_id = $ref2[1];
						$found = true;
						break;
					} // if
				}
				if (!$found && $curr->menutype!=$menu) {
					$curr->parent_id = 0;
				}
			}
			$curr->menutype = $menu;
			$curr->ordering = '9999';
//			$curr->home		= 0;
			if ( !$curr->store() ) {
				$this->setError($curr->getError());
				return false;
			}
			$curr->reorder( 'menutype = '.$this->_db->Quote($curr->menutype).' AND parent_id = '.(int) $curr->parent_id );
		} // foreach

		//Now, we need to rebuild sublevels...
		$this->_rebuildSubLevel();
		
		// clean cache
		AdminpraiseMenuHelper::cleanCache();
		
		return true;
	}
	
	/*
	 * Rebuild the sublevel field for items in the menu (if called with 2nd param = 0 or no params, it will rebuild entire menu tree's sublevel
	 * @param array of menu item ids to change level to
	 * @param int level to set the menu items to (based on parent
	 */
	function _rebuildSubLevel($cid = array(0), $level = 0)
	{
		JArrayHelper::toInteger($cid, array(0));
		$db =& $this->getDBO();
		$ids = implode( ',', $cid );
		$cids = array();
		if($level == 0) {
			$query 	= 'UPDATE #__adminpraise_menu SET sublevel = 0 WHERE parent_id = 0';
			$db->setQuery($query);
			$db->query();
			$query 	= 'SELECT id FROM #__adminpraise_menu WHERE parent_id = 0';
			$db->setQuery($query);
			$cids 	= $db->loadResultArray(0);
		} else {
			$query	= 'UPDATE #__adminpraise_menu SET sublevel = '.(int) $level
					.' WHERE parent_id IN ('.$ids.')';
			$db->setQuery( $query );
			$db->query();
			$query	= 'SELECT id FROM #__adminpraise_menu WHERE parent_id IN ('.$ids.')';
			$db->setQuery( $query );
			$cids 	= $db->loadResultArray( 0 );
		}
		if (!empty( $cids )) {
			$this->_rebuildSubLevel( $cids, $level + 1 );
		}
	}
	
	/**
	* Form for copying item(s) to a specific menu
	*/
	function getItemsFromRequest()
	{
		static $items;

		if (isset($items)) {
			return $items;
		}

		$cid = JRequest::getVar( 'cid', array(), 'post', 'array' );
		JArrayHelper::toInteger($cid);

		if (count($cid) < 1) {
			$this->setError(JText::_( 'COM_ADMINPRAISE_SELECT_ITEM_MOVE'));
			return false;
		}

		// Query to list the selected menu items
		$db =& $this->getDBO();
		$cids = implode( ',', $cid );
		$query = 'SELECT `id`, `title`' .
				' FROM `#__adminpraise_menu`' .
				' WHERE `id` IN ( '.$cids.' )';

		$db->setQuery( $query );
		$items = $db->loadObjectList();

		return $items;
	}
	
	public function move($items, $menu) {
		// Add all children to the list
		foreach ($items as $id)
		{
			$this->_addChildren($id, $items);
		}

		$row =& $this->getTable('AdminpraiseMenu', 'AdminpraiseTable');
		$ordering = 1000000;
		$firstroot = 0;
		foreach ($items as $id) {
			$row->load( $id );

			// is it moved together with his parent?
			$found = false;
			if ($row->parent_id != 0) {
				foreach ($items as $idx)
				{
					if ($idx == $row->parent_id) {
						$found = true;
						break;
					} // if
				}
			}
			if (!$found) {
				$row->parent_id = 0;
				$row->ordering = $ordering++;
				if (!$firstroot) $firstroot = $row->id;
			} // if

			$row->menutype = $menu;
			if ( !$row->store() ) {
				$this->setError($row->getError());
				return false;
			} // if
		} // foreach

		if ($firstroot) {
			$row->load( $firstroot );
			$row->reorder( 'menutype = '.$this->_db->Quote($row->menutype).' AND parent_id = '.(int) $row->parent_id );
		} // if
		
		//Rebuild sublevel
		$this->_rebuildSubLevel();
		
		// clean cache
		AdminpraiseMenuHelper::cleanCache();
		
		return true;
	}
	
	public function reset() {
		jimport('joomla.installer.helper');
		require_once(JPATH_COMPONENT_ADMINISTRATOR . DS . 'assets' . DS . 'menu' . DS . 'defaultMenu.php');
		$moduleInstalled = false;
		$appl = JFactory::getApplication();
		$menu = new adminpraiseDefaultMenu();
		$menu->deleteAdminPraiseMenu();
		$menu->createAdminPraiseTopMenu();
		$menu->createAdminpraiseToolsMenu();
		$menu->createAdminpraisePanelMenu();

		$db = JFactory::getDBO();

		$adminpraise_menu = JModuleHelper::getModule('adminpraise_menu');

		//check if the mod_adminpraise_menu is installed and if not install it
		if (!is_object($adminpraise_menu)) {
			require_once( JPATH_COMPONENT_ADMINISTRATOR
					. DS . 'library' . DS . 'joomla'
					. DS . 'installer' . DS . 'installer.php' );

			$this->installer = & AdminpraiseInstaller::getInstance();

			$menuModule = JPATH_COMPONENT.DS.'packages'.DS.'modules'.DS.'mod_adminpraise_menu.zip';
			$menuModule_package = JInstallerHelper::unpack($menuModule);
			$menuModulePath = $menuModule_package['extractdir'];

			if ($this->installer->install($menuModulePath)) {
				$moduleInstalled = true;
				$appl->enqueueMessage(JText::_('COM_ADMINPRAISE_MODULE_MENU_SUCCESS_INSTALLATION'));
			} else {
				$appl->enqueueMessage(JText::_('COM_ADMINPRAISE_MODULE_MENU_INSTALLATION_FAILURE'));
			}

		} else {
			$moduleInstalled = true;
		}

		if ($moduleInstalled == true) {

			// Setting the path for the module
			$menuModulePath = JPATH_ADMINISTRATOR.DS.'modules'.DS.'mod_adminpraise_menu'.DS.'mod_adminpraise_menu.xml';

			// Getting the menutypes
			$query = 'SELECT id, title FROM ' . $db->nameQuote('#__adminpraise_menu_types');
			$db->setQuery($query);
			$menuTypes = $db->loadObjectList('title');

			/*
       * Insert Adminpraise Panel Menu
			 */
			$data = array();
			$data['menutype'] = $menuTypes['AdminpraisePanelMenu']->id;
			$data['moduleid_css'] = 'ap-menu-panel';
			
			$params = json_encode($data);

			$this->_insertAPMenu('Adminpraise Panel Menu', 'adminpraise_panel', $params);

			/*
       * Insert Adminpraise Tools Menu
			 */
			$data = array();
			$data['menutype'] = $menuTypes['AdminpraiseToolsMenu']->id;
			$data['moduleid_css'] = 'ap-menu-tools';

			$params = json_encode($data);

			$this->_insertAPMenu('Adminpraise Tools Menu', 'adminpraise_tools', $params);

			/*
       * Insert Adminpraise Top Menu
			 */
			$data = array();
			$data['menutype'] = $menuTypes['AdminPraiseTopMenu']->id;
			$data['moduleid_css'] = 'ap-menu-top';

			$params = json_encode($data);

			$this->_insertAPMenu('Adminpraise Top Menu', 'adminpraise_menu', $params);

			return true;
		} else {
			return false;
		}
	}

	public function _insertAPMenu($title, $position, $params) {

		$db = JFactory::getDBO();

		$query = 'SELECT id FROM' . $db->nameQuote('#__modules')
				. ' WHERE module=' . $db->Quote('mod_adminpraise_menu')
				. ' AND position=' . $db->Quote($position);
		$db->setQuery($query, 0, 1);
		$id = $db->loadResult();

		if ($id) {
			$query = 'UPDATE ' . $db->nameQuote('#__modules')
					. ' SET params = ' . $db->Quote($params) . ','
					. ' published=' . $db->Quote(1)
					. ' WHERE module=' . $db->Quote('mod_adminpraise_menu')
					. ' AND position=' . $db->Quote($position);
			$db->setQuery($query);
			if (!$db->query()) {
					JError::raiseWarning( 500, $db->errorMsg() );
			}

		} else {
			$query = 'INSERT INTO ' . $db->nameQuote('#__modules')
					. ' (title, position, published, module, access, params, client_id, language)'
					. ' VALUES ('
					. $db->Quote($title) . ','
					. $db->Quote($position) . ','
					. $db->Quote(1) . ','
					. $db->Quote('mod_adminpraise_menu') . ','
					. $db->Quote(1) . ','
					. $db->Quote($params) . ','
					. $db->Quote(1) . ','
					. $db->Quote('*')
					. ')';
			$db->setQuery($query);
			if (!$db->query()) {
				JError::raiseWarning( 500, $db->errorMsg() );
			}

			$lastid = $db->insertid();

			$query = 'INSERT INTO ' . $db->nameQuote('#__modules_menu')
					. ' (moduleid, menuid)'
					. ' VALUES ('
					. $lastid . ', 0)';

			$db->setQuery($query);
			if (!$db->query()) {
				JError::raiseWarning( 500, $db->errorMsg() );
			}

		}
	}
}
