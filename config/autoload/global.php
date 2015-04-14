<?php

/**
 *
 * bareos-webui - Bareos Web-Frontend
 *
 * @link      https://github.com/bareos/bareos-webui for the canonical source repository
 * @copyright Copyright (c) 2013-2015 Bareos GmbH & Co. KG (http://www.bareos.org/)
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

/**
 * Global Configuration Override
 *
 * You can use this file for overriding configuration values from modules, etc.
 * You would place values in here that are agnostic to the environment and not
 * sensitive to security.
 *
 * @NOTE: In practice, this file will typically be INCLUDED in your source
 * control, so do not include passwords or other sensitive information in this
 * file.
 */

$file = "/etc/bareos-webui/directors.ini";
$config = null;

if(!file_exists($file)) {
	echo "Error: Missing configuration file ".$file.".";
		exit();
}
else {
	$config = parse_ini_file($file, true, INI_SCANNER_NORMAL);
}

if (!function_exists('read_db_config')) {
    function read_db_config($config, $file)
    {

        $arr = array();

        foreach($config as $instance) {

            if(array_key_exists('enabled', $instance) && isset($instance['enabled']) && strtolower($instance['enabled']) == "yes") {

                if(array_key_exists('dbaddress', $instance) && isset($instance['dbaddress'])) {
                    $arr['adapters'][key($config)] = array();
                }
                else {
                    if(array_key_exists('diraddress', $instance) && isset($instance['diraddress'])) {
                        $arr['adapters'][key($config)] = array();
                        $instance['dbaddress'] = $instance['diraddress'];
                    }
                    else {
                        echo "Error: Missing parameters 'dbaddress' and 'diraddress' in ".$file.", section ".key($config).".";
                        exit();
                    }
                }

                if(array_key_exists('dbdriver', $instance) && isset($instance['dbdriver'])) {
                    if(strtolower($instance['dbdriver']) == "postgresql") {
                        $arr['adapters'][key($config)]['driver'] = "Pdo_Pgsql";
                    }
                    elseif(strtolower($instance['dbdriver']) == "mysql") {
                        $arr['adapters'][key($config)]['driver'] = "Pdo_Mysql";
                    }
                    else {
                        echo "Error: Mispelled value for parameter 'dbdriver' in ".$file.", section ".key($config).".";
                        exit();
                    }
                }
                else {
                    $arr['adapters'][key($config)]['driver'] = "Pdo_Pgsql";
                }

                if(array_key_exists('dbname', $instance) && isset($instance['dbname'])) {
                    $arr['adapters'][key($config)]['dbname'] = $instance['dbname'];
                }
                else {
                    $arr['adapters'][key($config)]['dbname'] = "bareos";
                }

                if(array_key_exists('dbaddress', $instance) && isset($instance['dbaddress'])) {
                    $arr['adapters'][key($config)]['host'] = $instance['dbaddress'];
                }
                else {
                    $arr['adapters'][key($config)]['host'] = "127.0.0.1";
                }

                if(array_key_exists('dbport', $instance) && isset($instance['dbport'])) {
                    $arr['adapters'][key($config)]['port'] = $instance['dbport'];
                }
                else {
                    if($arr['adapters'][$instance['dbaddress']]['driver'] == "Pdo_Pgsql") {
                        $arr['adapters'][key($config)]['port'] = 5432;
                    }
                    else {
                        $arr['adapters'][key($config)]['port'] = 3306;
                    }
                }

                if(array_key_exists('dbuser', $instance) && isset($instance['dbuser'])) {
                    $arr['adapters'][key($config)]['username'] = $instance['dbuser'];
                }
                else {
                    $arr['adapters'][key($config)]['username'] = "bareos";
                }

                if(array_key_exists('dbpassword', $instance) && isset($instance['dbpassword'])) {
                    $arr['adapters'][key($config)]['password'] = $instance['dbpassword'];
                }
                else {
                    $arr['adapters'][key($config)]['password'] = "";
                }

            }

            next($config);

        }

        return $arr;

    }
}

if (!function_exists('read_dir_config')) {
    function read_dir_config($config, $file)
    {

        $arr = array();

        foreach($config as $instance) {

            if(array_key_exists('enabled', $instance) && isset($instance['enabled']) && strtolower($instance['enabled']) == "yes") {

                if(array_key_exists('diraddress', $instance) && isset($instance['diraddress'])) {
                    $arr[key($config)] = array();
                    $arr[key($config)]['host'] = $instance['diraddress'];
                }
                else {
                    echo "Error: Missing parameter 'diraddress' in ".$file.", section ".key($config).".";
                    exit();
                }

                if(array_key_exists('dirport', $instance) && isset($instance['dirport'])) {
                    $arr[key($config)]['port'] = $instance['dirport'];
                }
                else {
                    $arr[key($config)]['port'] = 9101;
                }

                if(array_key_exists('tls_verify_peer', $instance) && isset($instance['tls_verify_peer'])) {
                    $arr[key($config)]['tls_verify_peer'] = $instance['tls_verify_peer'];
                }
                else {
                    $arr[key($config)]['tls_verify_peer'] = false;
                }

                if(array_key_exists('server_can_do_tls', $instance) && isset($instance['server_can_do_tls'])) {
                    $arr[key($config)]['server_can_do_tls'] = $instance['server_can_do_tls'];
                }
                else {
                }

                if(array_key_exists('server_requires_tls', $instance) && isset($instance['server_requires_tls'])) {
                    $arr[key($config)]['server_requires_tls'] = $instance['server_requires_tls'];
                }
                else {
                    $arr[key($config)]['server_requires_tls'] = false;
                }

                if(array_key_exists('client_can_do_tls', $instance) && isset($instance['client_can_do_tls'])) {
                    $arr[key($config)]['client_can_do_tls'] = $instance['client_can_do_tls'];
                }
                else {
                    $arr[key($config)]['client_can_do_tls'] = false;
                }

                if(array_key_exists('client_requires_tls', $instance) && isset($instance['client_requires_tls'])) {
                    $arr[key($config)]['client_requires_tls'] = $instance['client_requires_tls'];
                }
                else {
                    $arr[key($config)]['client_requires_tls'] = false;
                }

                if(array_key_exists('ca_file', $instance) && isset($instance['ca_file'])) {
                    $arr[key($config)]['ca_file'] = $instance['ca_file'];
                }
                else {
                    $arr[key($config)]['ca_file'] = "";
                }

                if(array_key_exists('cert_file', $instance) && isset($instance['cert_file'])) {
                    $arr[key($config)]['cert_file'] = $instance['cert_file'];
                }
                else {
                    $arr[key($config)]['cert_file'] = "";
                }

                if(array_key_exists('cert_file_passphrase', $instance) && isset($instance['cert_file_passphrase'])) {
                    $arr[key($config)]['cert_file_passphrase'] = $instance['cert_file_passphrase'];
                }
                else {
                    $arr[key($config)]['cert_file_passphrase'] = "";
                }

                if(array_key_exists('allowed_cns', $instance) && isset($instance['allowed_cns'])) {
                    $arr[key($config)]['allowed_cns'] = $instance['allowed_cns'];
                }
                else {
                    $arr[key($config)]['allowed_cns'] = "";
                }

            }

            next($config);

        }

        return $arr;

    }
}

return array(
	'db' => read_db_config($config, $file),
	'directors' => read_dir_config($config, $file),
	'service_manager' => array(
		'factories' => array(
			'Zend\Db\Adapter\Adapter' => 'Zend\Db\Adapter\AdapterServiceFactory',
		),
		'abstract_factories' => array(
			// to allow other adapters to be called by $sm->get('adaptername')
			'Zend\Db\Adapter\AdapterAbstractServiceFactory',
		),
	),
	'session' => array(
		'config' => array(
			'class' => 'Zend\Session\Config\SessionConfig',
			'options' => array(
				'name' => 'Bareos-WebUI',
			),
		),
		'storage' => 'Zend\Session\Storage\SessionArrayStorage',
		'validators' => array(
			'Zend\Session\Validator\RemoteAddr',
			'Zend\Session\Validator\HttpUserAgent',
		),
	),
);

