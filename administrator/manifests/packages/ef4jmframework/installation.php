<?php
/**
 * @package JMFramework
 * @copyright Copyright (C) 2012 DJ-Extensions.com LTD, All rights reserved.
 * @license http://www.gnu.org/licenses GNU/GPL
 * @author url: http://dj-extensions.com
 * @author email contact@dj-extensions.com
 * @developer Michal Olczyk - michal.olczyk@design-joomla.eu
 *
 * JMFramework is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * JMFramework is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with JMFramework. If not, see <http://www.gnu.org/licenses/>.
 *
 */
 
defined('_JEXEC') or die('Restricted access');


class pkg_ef4jmframeworkInstallerScript {
	function postflight($type, $parent) 
	{
		if ($type == 'install') {
			$results = $parent->get('results');
			if (count($results)) {
				$db = JFactory::getDbo();
				foreach ($results as $result) {
					if ($result['result']) {
						$db->setQuery('UPDATE #__extensions SET enabled=1 WHERE name='.$db->quote($result['name']));
						$db->query();
					}
				}
			}
		}
	}
}