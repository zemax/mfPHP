<?php
/**
* SQL Object
*
* @version	2.1.1 2009-01-15
* - static keyword
*
* @version	2.1.0 2008-09-03
* - use static $instance
* - remove explicit references
*
* @version	2.0.4 2007-12-19
* - UTF8 is now in subclass
*
* @version  2.0.3 2007-11-06
* - create automatically a new connection if no connection exists
*
* @version  2.0.2 2007-10-16
* - added getInstance() method in replacement of instance(), which is now deprecated
*
* @version	2.0.1 2007-10-05
* - compatible with PHP4
*
* @author	Maxime Cousinou
*/

namespace MF;

class SQL {
	private static $instance;

	/**
	 * Return the SQL connexion instance, create it if needed
	 *
	 * @return \Doctrine\DBAL\Connection
	 */
	public static function getConnection ($params = array()) {
		if (isset(SQL::$instance)) {
			return (SQL::$instance);
		}
		else {
			return (SQL::create($params));
		}
	}

	/**
	 * Create a SQL connexion
	 *
	 * @param $database		String
	 * @param $username		String
	 * @param $password		String
	 * @param $hostname		String
	 *
	 * @return Boolean
	 */
	public static function create ($params) {
		$params['driver']   = empty($params['driver'])?'pdo_mysql':$params['driver'];
		$params['host'] 	= empty($params['host'])?(defined('DB_HOSTNAME')?DB_HOSTNAME:'localhost'):$params['host'];
		$params['dbname'] 	= empty($params['dbname'])?(defined('DB_BASENAME')?DB_BASENAME:''):$params['dbname'];
		$params['user'] 	= empty($params['user'])?(defined('DB_USERNAME')?DB_USERNAME:'root'):$params['user'];
		$params['password'] = empty($params['password'])?(defined('DB_PASSWORD')?DB_PASSWORD:''):$params['password'];
		$params['utf8']     = empty($params['utf8'])?true:$params['utf8'];

		if ($params['utf8']) {
			$params['charset'] = 'utf8';
			if ($params['driver'] == 'pdo_mysql') {
				$params['driverOptions'] = array(
					1002 => 'SET NAMES utf8'
				);
			}
		}

		$dbConfig = new \Doctrine\DBAL\Configuration();

		SQL::$instance = \Doctrine\DBAL\DriverManager::getConnection($params, $dbConfig);

		return (SQL::$instance);
	}

	/**
	 * Return the SQL connexion instance, create it if needed
	 *
	 * @return \Doctrine\DBAL\Connection
	 */
	public static function getInstance ($params = array()) {
		return (SQL::getConnection($params));
	}
}
