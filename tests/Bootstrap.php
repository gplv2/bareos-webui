<?php

namespace JobTest;
ob_start();

use Zend\Loader\AutoloaderFactory;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Mvc\Service\ServiceManagerConfig;
use Zend\ServiceManager\ServiceManager;
use Zend\Stdlib\ArrayUtils;

use RuntimeException;

error_reporting(E_ALL | E_STRICT);
chdir(__DIR__);
define ('APPLICATION_ENV', 'development');
define ('APP_ENV', 'development');

class Bootstrap 
{
	protected static $serviceManager;
 	protected static $config;
    	protected static $bootstrap;

	public static function init()
	{

		// Load the user-defined test configuration file, if it exists; otherwise, load
		// echo __DIR__ . '/../config/autoload/local.php' . PHP_EOL; exit;
		if (file_exists(__DIR__ . '/../config/autoload/local.php.dist')) {
			$testConfig = include __DIR__ . '/../config/autoload/local.php.dist';
		}

		$zf2ModulePaths = array(dirname(dirname(__DIR__)));
		if (($path = static::findParentPath('vendor'))) {
			$zf2ModulePaths[] = $path;
		}
		if (($path = static::findParentPath('module')) !== $zf2ModulePaths[0]) {
			$zf2ModulePaths[] = $path;
		}

		static::initAutoloader();

		// use ModuleManager to load this module and it's dependencies
		$config = array(
			'modules' => array('Application','Job','Auth','Client'),
			'module_listener_options' => array('module_paths' => $zf2ModulePaths,
				'module_paths' => array(
					'../module',
					'../vendor',
					),
				// An array of paths from which to glob configuration files after
				// modules are loaded. These effectively override configuration
				// provided by modules themselves. Paths may use GLOB_BRACE notation.
				'config_glob_paths' => array(
					'../config/autoload/{,*.}{global,local}.php',
					),
				)

		);

		//$config = ArrayUtils::merge($baseConfig, $testConfig);

		$serviceManager = new ServiceManager(new ServiceManagerConfig());
		$serviceManager->setService('ApplicationConfig', $config);
		$serviceManager->get('ModuleManager')->loadModules();

		static::$serviceManager = $serviceManager;
		static::$config = $config;
	}

	public static function chroot()
	{
		$rootPath = dirname(static::findParentPath('module'));
		chdir($rootPath);
	}

	public static function getServiceManager() 
	{
		return static::$serviceManager;
	}

	public static function getConfig()
	{
		return static::$config;
	}

	protected static function initAutoloader() 
	{
		$vendorPath = static::findParentPath('vendor');

	        $zf2Path = getenv('ZF2_PATH');
	
		if (!$zf2Path) {
		        if (defined('ZF2_PATH')) {
		            	$zf2Path = ZF2_PATH;
		            } 
			    elseif (is_dir($vendorPath . '/ZF2/library')) {
		            	$zf2Path = $vendorPath . '/ZF2/library';
		            } 
			    elseif (is_dir($vendorPath . '/zendframework/zendframework/library')) {
		                $zf2Path = $vendorPath . '/zendframework/zendframework/library';
		            }
	        }

        	if (!$zf2Path) {
	        	throw new RuntimeException('Unable to load ZF2. Run `php composer.phar install` or' . ' define a ZF2_PATH environment variable.');
        	}

        	if (file_exists($vendorPath . '/autoload.php')) {
	        	include $vendorPath . '/autoload.php';
	        }

        	include $zf2Path . '/Zend/Loader/AutoloaderFactory.php';

		AutoloaderFactory::factory(array(
			            'Zend\Loader\StandardAutoloader' => array(
					    'autoregister_zf' => true,          
					    'namespaces' => array(
					    	__NAMESPACE__ => __DIR__ . '/' . __NAMESPACE__,
						),
					    ),
				    ));		    
	}

	protected static function findParentPath($path)
	{
	        $dir = __DIR__;
	        $previousDir = '.';
	        while (!is_dir($dir . '/' . $path)) {
        		$dir = dirname($dir);
	 	       	if ($previousDir === $dir) 
				return false;
			$previousDir = $dir;
        	}
        	return $dir . '/' . $path;
	}	

}

Bootstrap::init();
Bootstrap::chroot();
