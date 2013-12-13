<?php
class MF_Db_SQLite extends MF_Db_Driver {
	private $connexion 		= false;

	private $cursors			= array();

	public function __construct ($database) {
		$this->connexion = new SQLiteDatabase($database);
	}

	/**************************************************
	 * STRING HANDLING
	 **************************************************/

	public function quote ($value) {
		return (sqlite_escape_string($value));
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
		return (sqlite_error_string($this->$errorStatus));
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
		if ($this->connexion) {
			$this->cursors[$queryName]['id'] = $this->connexion->query($query);
			$this->errorStatus = $this->connexion->lastError();

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
	 * @return	integer	un tableau de la ligne en cours du curseur, FALSE en cas d'erreur ou fin de curseur
	 *
	 * @access	public
	 */
	public function fetchRow($queryName = 'query') {
		$sqlResult = $this->cursors[$queryName]['id'];
		
		return ($sqlResult->fetch(SQLITE_NUM));
	}

	/**
	 * Renvoie une ligne du curseur sous la forme d'un tableau associatif
	 *
	 * @param 	String	nom de la requête
	 *
	 * @return	mixed	un tableau associatif de la ligne en cours du curseur, FALSE en cas d'erreur ou fin de curseur
	 *
	 * @access	public
	 */
	public function fetchArray($queryName = 'query') {
		$sqlResult = $this->cursors[$queryName]['id'];
		
		return ($sqlResult->fetch(SQLITE_ASSOC));
	}

	/**
	 * Renvoie une ligne du curseur sous la forme d'un object
	 *
	 * @param 	String	nom de la requête
	 *
	 * @return	mixed	un tableau associatif de la ligne en cours du curseur, FALSE en cas d'erreur ou fin de curseur
	 *
	 * @access	public
	 */
	public function fetchObject($queryName = 'query') {
		$sqlResult = $this->cursors[$queryName]['id'];
		
		return ($sqlResult->fetchObject('stdClass'));
	}

	/**
	 * Renvoie le nombre de lignes retournées par la requête SELECT
	 *		ou le nombre de lignes affectées par la requête
	 *
	 * @param 	String	nom de la requête
	 *
	 * @return	integer le nombre de lignes, FALSE en cas d'erreur
	 *
	 * @access	public
	 */
	public function numRows($queryName = 'query') {
		$sqlResult = $this->cursors[$queryName]['id'];
		
		return ($sqlResult->numRows());
	}

	/**
	 * Renvoie une ligne du curseur sous la forme d'un tableau associatif
	 *
	 * @param 	String	nom de la requête
	 *
	 * @return	mixed	id généré pour une colonne AUTO_INCREMENT lors du dernier INSERT ou FALSE en cas d'erreur
	 *
	 * @access	public
	 */
	public function insertId($queryName = 'query') {
		return ($this->connexion->lastInsertRowid());
	}

	/**
	 * Libère les ressources associées à la requête
	 *
	 * @param 	String	$queryName	nom de la requête
	 *
	 * @access	public
	 */
	public function freeQuery($queryName = 'query') {
		unset($this->cursors[$queryName]);
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
	}
}
