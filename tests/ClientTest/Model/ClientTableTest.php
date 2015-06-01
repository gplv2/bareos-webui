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

namespace ClientTest\Model;

use Jobtest\BootStrap;
use Client\Controller\ClientController;
use Client\Model\ClientTable;
use Client\Model\Client;
use Zend\Db\ResultSet\ResultSet;
use PHPUnit_Framework_TestCase;
//use Zend\ServiceManager\ServiceLocatorAwareInterface;
//use Zend\ServiceManager\ServiceLocatorInterface;


class ClientTableTest extends PHPUnit_Framework_TestCase 
{
	protected $controller; 

	protected function setUp()
	{   
		$serviceManager = Bootstrap::getServiceManager();
		$this->controller = new ClientController();  
		$config = $serviceManager->get('Config');
		$this->controller->setServiceLocator($serviceManager);
	}
	
	public function testFetchAllReturnsAllClients() 
	{
		$resultSet = new ResultSet();
		$mockTableGateway = $this->getMock('Zend\Db\TableGateway\TableGateway', array('select'), array(), '', false);
		$mockTableGateway->expects($this->once())
			->method('select')
			->with()
			->will($this->returnValue($resultSet));

		$clientTable = new ClientTable($mockTableGateway);

		$this->assertSame($resultSet, $clientTable->fetchAll());

	}

}
