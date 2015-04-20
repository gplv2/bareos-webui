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

namespace ClientTest\Controller;

use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;

class ClientControllerTest extends AbstractHttpControllerTestCase
{
	protected $traceError = true;

	public function setUp()
	{
		$this->setApplicationConfig(
			include './config/application.config.php'
		);
	}

	public function testIndexActionCanBeAccessed()
	{
                $director = 'localhost-dir';
                $username = 'backup';
                $password = 'orchestra1.';
                // $routeMatch = $this->getApplication()->getMvcEvent()->getRouteMatch();

                $this->dispatch('/auth/login', 'POST', array('director' => $director, 'consolename' => $username , 'password' => $password, 'submit'=>'Login'));
                $this->assertRedirectRegex('/dashboard/');
                $this->assertResponseStatusCode(302);
                
		$this->reset(true);
		$this->dispatch('/client');
		$this->assertResponseStatusCode(200);
		$this->assertModuleName('Client');
		$this->assertControllerName('Client\Controller\Client');
		$this->assertControllerClass('ClientController');
		$this->assertMatchedRouteName('client');
	}
}
