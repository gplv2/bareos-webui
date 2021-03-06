<?php

/**
 *
 * bareos-webui - Bareos Web-Frontend
 * 
 * @link      https://github.com/bareos/bareos-webui for the canonical source repository
 * @copyright Copyright (c) 2013-2014 Bareos GmbH & Co. KG (http://www.bareos.org/)
 * @license   GNU Affero General Public License (http://www.gnu.org/licenses/)
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 */
 
namespace Application\View\Helper;

use Zend\View\Helper\AbstractHelper;

class JobLevel extends AbstractHelper
{
	public function __invoke($jobLevel)
	{
	switch ($jobLevel)
	{
		case 'I':
		  $output = "Incremental";
		  break;
		case 'D':
		  $output = "Differential";
		  break;
		case 'F':
		  $output = "Full";
		  break;
		default:
		  $output = $jobLevel;
		  break;
	}
	
	return $output;
	
	}
}

