<?php
/**
 * MySQL Object
 *
 * @version  2.0.3 2008-03-18
 * - bug fixed on insertId
 *
 * @version 	2.0.2 2008-02-20
 * - ADDED type 'SHOW'
 * - rewrite condition with switch
 *
 * @version	2.0.1 2007-12-19
 * - SET NAMES 'UTF8' is now in MySQL and test the MySQL server version
 *
 * @version	2.0.0 2007-06-13
 *
 * @version	1.0.1 2006-10-24
 * - bugfix : delete numeric keys in fetch_array
 *
 * @author	Maxime Cousinou
 */
class MF_Db_MySQL extends MF_Db_Driver {
	private $connexionID 		= false;
	private $database 			= '';
	private $username 			= '';
	private $password 			= '';
	private $hostname 			= '';

	private $cursors			= array();

	/**
	 * Constructor
	 *
	 * @param $database		String		nom de la base de données à sélectionner
	 * @param $username		String		nom de l'utilisateur pour la connexion à la base
	 * @param $password		String		mot de passe de l'utilisateur
	 * @param $hostname		String		adresse ou nom du serveur base de données
	 * @param $utf8			Boolean		Open an UTF8 connexion
	 *
	 * @return MySQL
	 */
	public function __construct ($database, $username = 'root', $password = '', $hostname = 'localhost', $utf8 = true) {
		// renseigne les variables membres
		$this->database = $database;
		$this->username = $username;
		$this->password = $password;
		$this->hostname = $hostname;

		// ouvre la base
		$this->connect();

		if ($utf8) {
			$mysql_version = explode('.', mysql_get_server_info());

			switch (true) {
				case ($mysql_version[0] > 4):
				case (($mysql_version[0] == 4) && ($mysql_version[1] >= 1)):
					$this->query("SET NAMES 'UTF8'", '__character_encoding__');
					$this->freeQuery('__character_encoding__');
					break;

				default:
			}
		}
	}

	/**
	 * Connect to database server and select the database
	 */
	private function connect() {
		$this->connexionID = mysql_connect($this->hostname, $this->username, $this->password);

		if (!$this->connexionID)	{
			$this->errorStatus = mysql_errno();
			return;
		}

		if (!mysql_select_db($this->database,	$this->connexionID))	{
			$this->errorStatus = mysql_errno();
			return;
		}
	}

	/**************************************************
	 * STRING HANDLING
	 **************************************************/

	/**
	 * Quote a string
	 *
	 * @param $value		String
	 *
	 * @return String
	 */
	public function quote ($value) {
		return (mysql_real_escape_string($value));
	}

	/**************************************************
	 * ERRORS HANDLING
	 **************************************************/

	/**
	 * Return the last error message
	 *
	 * @return String
	 */
	public function errorMessage() {
		return (mysql_error());
	}

	/**************************************************
	 * METHODS
	 **************************************************/

	/**
	 * Execute a SQL query
	 *
	 * @param $queryName		String	nom de la requête
	 * @param $query			String	requête SQL à exécuter
	 *
	 * @return Boolean
	 */
	public function query($query, $queryName = 'query') {
		// teste si le nom de la requête n'existe pas déjà dans le tableau $cursors
		if (isset($this->cursors[$queryName])) {
			// requête déjà existante, on libère les ressources
			$this->freeQuery($queryName);
		}

		// teste si requête SELECT ou autre
		switch(true) {
			case (preg_match('/^select /i', $query)) :
				$this->cursors[$queryName]['type'] = 'SELECT';
				break;

			case (preg_match('/^insert /i', $query)) :
				$this->cursors[$queryName]['type'] = 'INSERT';
				break;

			case (preg_match('/^update /i', $query)) :
				$this->cursors[$queryName]['type'] = 'UPDATE';
				break;
					
			case (preg_match('/^delete /i', $query)) :
				$this->cursors[$queryName]['type'] = 'DELETE';
				break;

			case (preg_match('/^show /i', $query)) :
				$this->cursors[$queryName]['type'] = 'SHOW';
				break;

			default:
				$this->cursors[$queryName]['type'] = 'UNKNOWN';
		}

		$this->cursors[$queryName]['query'] = $query;

		// teste si la base est ouverte
		if ($this->connexionID) {
			$this->cursors[$queryName]['id'] = mysql_query($query, $this->connexionID);
			$this->errorStatus = mysql_errno();

			// teste si requête select ou non
			if (!$this->isError()) {
				switch ($this->cursors[$queryName]['type']) {
					case 'UNKNOWN':
					case 'SELECT':
						break;
							
					default:
						// récupère le nombre de lignes affectées par la dernière commande SQL
						$this->cursors[$queryName]['numrows'] = mysql_affected_rows($this->connexionID);
						$this->errorStatus = mysql_errno();
				}
			}

			return (!$this->isError());
		}
		else {
			$this->cursors[$queryName]['id'] = false;
			return (false);
		}
	}

	/**
	 * Renvoie une ligne du curseur sous la forme d'un tableau indexé
	 *
	 * @param 	String	nom de la requête
	 *
	 * @return	integer	un tableau de la ligne en cours du curseur, false en cas d'erreur ou fin de curseur
	 *
	 * @access	public
	 */
	public function fetchRow($queryName = 'query') {
		$ret = false;
		// teste si le nom de la requête existe dans le tableau $cursors
		if (isset($this->cursors[$queryName])) {
			// requête existante
			if ($this->cursors[$queryName]['id']) {
				switch ($this->cursors[$queryName]['type']) {
					case 'SELECT':
					case 'SHOW':
						$ret = mysql_fetch_row($this->cursors[$queryName]['id']);
						break;
							
					default:
				}
			}
		}

		return ($ret);
	}

	/**
	 * Renvoie une ligne du curseur sous la forme d'un tableau associatif
	 *
	 * @param 	String	nom de la requête
	 *
	 * @return	mixed	un tableau associatif de la ligne en cours du curseur, false en cas d'erreur ou fin de curseur
	 *
	 * @access	public
	 */
	public function fetchArray($queryName = 'query') {
		$ret = false;
		// teste si le nom de la requête existe dans le tableau $cursors
		if (isset($this->cursors[$queryName])) {
			// requête existante
			if ($this->cursors[$queryName]['id']) {
				switch ($this->cursors[$queryName]['type']) {
					case 'SELECT':
					case 'SHOW':
						if ($ret = mysql_fetch_array($this->cursors[$queryName]['id'])) {
							foreach($ret as $key => $value) {
								if (is_numeric($key)) {
									unset ($ret[$key]);
								}
							}
						}
						break;
							
					default:
				}
			}
		}

		return ($ret);
	}

	/**
	 * Renvoie une ligne du curseur sous la forme d'un object
	 *
	 * @param 	String	nom de la requête
	 *
	 * @return	mixed	un tableau associatif de la ligne en cours du curseur, false en cas d'erreur ou fin de curseur
	 *
	 * @access	public
	 */
	public function fetchObject($queryName = 'query') {
		$ret = false;
		// teste si le nom de la requête existe dans le tableau $cursors
		if (isset($this->cursors[$queryName])) {
			// requête existante
			if ($this->cursors[$queryName]['id']) {
				switch ($this->cursors[$queryName]['type']) {
					case 'SELECT':
					case 'SHOW':
						$ret = mysql_fetch_object($this->cursors[$queryName]['id']);
						break;
							
					default:
				}
			}
		}

		return ($ret);
	}

	/**
	 * Renvoie le nombre de lignes retournées par la requête SELECT
	 *		ou le nombre de lignes affectées par la requête
	 *
	 * @param 	String	nom de la requête
	 *
	 * @return	integer le nombre de lignes, false en cas d'erreur
	 *
	 * @access	public
	 */
	public function numRows($queryName = 'query') {

		$ret = false;
		// teste si le nom de la requête n'existe pas déjà dans le tableau $cursors
		if (isset($this->cursors[$queryName])) {
			// requête déjà existante
			if ($this->cursors[$queryName]['id']) {
				switch ($this->cursors[$queryName]['type']) {
					case 'SELECT' :
						$ret = mysql_num_rows($this->cursors[$queryName]['id']);
						break;
							
					default:
						$ret = $this->cursors[$queryName]['numrows'];
				}
			}
		}

		return ($ret);
	}

	/**
	 * Renvoie une ligne du curseur sous la forme d'un tableau associatif
	 *
	 * @param 	String	nom de la requête
	 *
	 * @return	mixed	id généré pour une colonne AUTO_INCREMENT lors du dernier INSERT ou false en cas d'erreur
	 *
	 * @access	public
	 */
	public function insertId($queryName = 'query') {
		$ret = false;
		// teste si le nom de la requête existe dans le tableau $cursors
		if (isset($this->cursors[$queryName])) {
			// requête existante
			if ($this->cursors[$queryName]['id']) {
				switch ($this->cursors[$queryName]['type']) {
					case 'INSERT' :
						$ret = mysql_insert_id($this->connexionID);
						break;
							
					default:
				}
			}
		}

		return ($ret);
	}

	/**
	 * Libère les ressources associées à la requête
	 *
	 * @param 	String	$queryName	nom de la requête
	 *
	 * @access	public
	 */
	public function freeQuery($queryName = 'query') {
		// teste si le nom de la requête n'existe pas déjà dans le tableau $cursors
		if (isset($this->cursors[$queryName])) {
			// requête déjà existante, on libère les ressources
			if (($this->cursors[$queryName]['id']) && ('SELECT' == $this->cursors[$queryName]['type'])) {
				mysql_free_result($this->cursors[$queryName]['id']);
			}
			unset($this->cursors[$queryName]);
		}
	}

	/**
	 * Destructeur de la classe - Ferme la base de données
	 *
	 * @access	public
	 */
	public function __destruct() {
		// libère les ressources prises par les queries dans $cursors
		while (list($key) = each($this->cursors)) {
			$this->freeQuery($key);
		}
		
		unset($this->cursors);
		// ferme la connexion à la base de données
		mysql_close($this->connexionID);
	}
}
