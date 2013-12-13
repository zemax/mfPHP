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
class MF_SQL {
	private static $instance;

	/**
	 * Return the SQL connexion instance, create it if needed
	 *
	 * @return MF_Db_Driver
	 */
	public static function getConnection ($params = array()) {
		if (isset(MF_SQL::$instance)) {
			return (MF_SQL::$instance);
		}
		else {
			if (!MF_SQL::create($params)) {
				return (false);
			}
			else {
				return (MF_SQL::$instance);
			}
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
		$driver   = empty($params['driver'])?'mysql':$params['driver'];
		$hostname = empty($params['host'])?(defined('DB_HOSTNAME')?DB_HOSTNAME:'localhost'):$params['host'];
		$database = empty($params['dbname'])?(defined('DB_BASENAME')?DB_BASENAME:''):$params['dbname'];
		$username = empty($params['user'])?(defined('DB_USERNAME')?DB_USERNAME:'root'):$params['user'];
		$password = empty($params['password'])?(defined('DB_PASSWORD')?DB_PASSWORD:''):$params['password'];
		$utf8     = empty($params['utf8'])?true:$params['utf8'];

		switch ($driver) {
			case 'sqlite':
				MF_SQL::$instance = new MF_Db_SQLite($database);
				break;
				
			case 'mysql':
			default:
				MF_SQL::$instance = new MF_Db_MySQL($database, $username, $password, $hostname, $utf8);
		}

		return (!MF_SQL::$instance->isError());
	}

	/**
	 * Return the SQL connexion instance, create it if needed
	 *
	 * @return MF_Db_Driver
	 */
	public static function getInstance ($params = array()) {
		return (MF_SQL::getConnection($params));
	}
}
