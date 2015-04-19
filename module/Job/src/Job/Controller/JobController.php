<?php

/**
 *
 * bareos-webui - Bareos Web-Frontend
 *
 * @link      https://github.com/bareos/bareos-webui for the canonical source repository
 * @copyright Copyright (c) 2013-2014 Bareos GmbH & Co. KG (http://www.bareos.org/)
 * @license   GNU Affero General Public License (http://www.gnu.org/licenses/)
 * @author    Frank Bergkemper
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

namespace Job\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class JobController extends AbstractActionController
{

	protected $jobTable;
	protected $logTable;
	protected $bconsoleOutput = array();
	protected $director = null;

	public function indexAction()
	{
		if ($_SESSION['bareos']['authenticated'] === true) {
				$order_by = $this->params()->fromRoute('order_by') ? $this->params()->fromRoute('order_by') : 'JobId';
				$order = $this->params()->fromRoute('order') ? $this->params()->fromRoute('order') : 'DESC';
				$limit = $this->params()->fromRoute('limit') ? $this->params()->fromRoute('limit') : '25';
				$paginator = $this->getJobTable()->fetchAll(true, $order_by, $order);
				$paginator->setCurrentPageNumber((int) $this->params()->fromQuery('page', 1));
				$paginator->setItemCountPerPage($limit);

				return new ViewModel(
					array(
						'paginator' => $paginator,
						'order_by' => $order_by,
						'order' => $order,
						'limit' => $limit,
						'allJobs' => $this->getJobTable()->fetchAll(),
						)
					);
		} else {
				return $this->redirect()->toRoute('auth', array('action' => 'login'));
		}
	}

	public function detailsAction()
	{
		if ($_SESSION['bareos']['authenticated'] === true) {
				$id = (int) $this->params()->fromRoute('id', 0);
				if (!$id) {
					return $this->redirect()->toRoute('job');
				}

				return new ViewModel(array(
						'job' => $this->getJobTable()->getJob($id),
						'log' => $this->getLogTable()->getLogsByJob($id),
					));
		} else {
		return $this->redirect()->toRoute('auth', array('action' => 'login'));
		}
	}

	public function runningAction()
	{
		if ($_SESSION['bareos']['authenticated'] === true) {
				$order_by = $this->params()->fromRoute('order_by') ? $this->params()->fromRoute('order_by') : 'JobId';
				$order = $this->params()->fromRoute('order') ? $this->params()->fromRoute('order') : 'DESC';
				$limit = $this->params()->fromRoute('limit') ? $this->params()->fromRoute('limit') : '25';
				$paginator = $this->getJobTable()->getRunningJobs(true, $order_by, $order);
				$paginator->setCurrentPageNumber((int) $this->params()->fromQuery('page', 1));
				$paginator->setItemCountPerPage($limit);

				return new ViewModel(
					array(
							'paginator' => $paginator,
						'order_by' => $order_by,
										'order' => $order,
										'limit' => $limit,
							'runningJobs' => $this->getJobTable()->getRunningJobs()
					)
				);
		} else {
				return $this->redirect()->toRoute('auth', array('action' => 'login'));
		}
	}

	public function waitingAction()
	{
		if ($_SESSION['bareos']['authenticated'] === true) {
				$order_by = $this->params()->fromRoute('order_by') ? $this->params()->fromRoute('order_by') : 'JobId';
				$order = $this->params()->fromRoute('order') ? $this->params()->fromRoute('order') : 'DESC';
				$limit = $this->params()->fromRoute('limit') ? $this->params()->fromRoute('limit') : '25';
				$paginator = $this->getJobTable()->getWaitingJobs(true, $order_by, $order);
				$paginator->setCurrentPageNumber((int) $this->params()->fromQuery('page', 1));
				$paginator->setItemCountPerPage($limit);

				return new ViewModel(
					array(
							'paginator' => $paginator,
						'order_by' => $order_by,
										'order' => $order,
										'limit' => $limit,
							'waitingJobs' => $this->getJobTable()->getWaitingJobs()
					)
				);
		} else {
				return $this->redirect()->toRoute('auth', array('action' => 'login'));
		}
	}

	public function unsuccessfulAction()
	{
		if ($_SESSION['bareos']['authenticated'] === true) {
				$order_by = $this->params()->fromRoute('order_by') ? $this->params()->fromRoute('order_by') : 'JobId';
				$order = $this->params()->fromRoute('order') ? $this->params()->fromRoute('order') : 'DESC';
				$limit = $this->params()->fromRoute('limit') ? $this->params()->fromRoute('limit') : '25';
				$paginator = $this->getJobTable()->getLast24HoursUnsuccessfulJobs(true, $order_by, $order);
				$paginator->setCurrentPageNumber((int) $this->params()->fromQuery('page', 1));
				$paginator->setItemCountPerPage($limit);

				return new ViewModel(
					array(
						'paginator' => $paginator,
						'order_by' => $order_by,
						'order' => $order,
						'limit' => $limit,
						'lastUnsuccessfulJobs' => $this->getJobTable()->getLast24HoursUnsuccessfulJobs(),
					)
				);
		} else {
				return $this->redirect()->toRoute('auth', array('action' => 'login'));
		}
	}

	public function successfulAction()
	{
		if ($_SESSION['bareos']['authenticated'] === true) {
				$order_by = $this->params()->fromRoute('order_by') ? $this->params()->fromRoute('order_by') : 'JobId';
				$order = $this->params()->fromRoute('order') ? $this->params()->fromRoute('order') : 'DESC';
				$limit = $this->params()->fromRoute('limit') ? $this->params()->fromRoute('limit') : '25';
				$paginator = $this->getJobTable()->getLast24HoursSuccessfulJobs(true, $order_by, $order);
				$paginator->setCurrentPageNumber((int) $this->params()->fromQuery('page', 1));
				$paginator->setItemCountPerPage($limit);

				return new ViewModel(
					array(
						'paginator' => $paginator,
						'order_by' => $order_by,
						'order' => $order,
						'limit' => $limit,
						'lastSuccessfulJobs' => $this->getJobTable()->getLast24HoursSuccessfulJobs(),
					)
				);
		} else {
				return $this->redirect()->toRoute('auth', array('action' => 'login'));
		}
	}

	public function rerunAction()
	{
		$flags = new \stdClass;
		$flags->keepalive = true;

		if ($_SESSION['bareos']['authenticated'] === true) {
				$jobid = (int) $this->params()->fromRoute('id', 0);
				// list job jobid=8477

				$cmd = "rerun jobid=" . $jobid . " yes";
				$this->director = $this->getServiceLocator()->get('director');
				$output = $this->director->send_command($cmd, $flags);

				if (preg_match('/rerun: is an invalid command/', $output, $match)) {
				   $cmd = "list jobid=" . $jobid;
				   $output = $this->director->send_command($cmd);
					// run job="Server017 - upload signatures" level=Full yes
				   foreach(preg_split("/((\r?\n)|(\r\n?))/", $subject) as $line){
						echo $line . PHP_EOL;
					} 	
               //debugbar_log($output);
				}
     				//print_r($output);exit;

				return new ViewModel(
						array(
							'bconsoleOutput' => $output,
							'jobid' => $jobid,
						)
				);
		} else {
				return $this->redirect()->toRoute('auth', array('action' => 'login'));
		}
	}

	public function cancelAction()
	{
		if ($_SESSION['bareos']['authenticated'] === true) {
				$jobid = (int) $this->params()->fromRoute('id', 0);
				$cmd = "cancel jobid=" . $jobid . " yes";
				$this->director = $this->getServiceLocator()->get('director');
				return new ViewModel(
						array(
							'bconsoleOutput' => $this->director->send_command($cmd)
						)
				);
		} else {
				return $this->redirect()->toRoute('auth', array('action' => 'login'));
		}
	}

	public function getJobTable()
	{
		if(!$this->jobTable)
		{
			$sm = $this->getServiceLocator();
			$this->jobTable = $sm->get('Job\Model\JobTable');
		}
		return $this->jobTable;
	}

	public function getLogTable()
	{
		if (!$this->logTable)
		{
			$sm = $this->getServiceLocator();
			$this->logTable = $sm->get('Log\Model\LogTable');
		}
		return $this->logTable;
	}

}

