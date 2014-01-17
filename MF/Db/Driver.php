<?php
namespace MF\Db;

class Driver {
	protected $errorStatus 		= 0;

	/**************************************************
	 * STRING HANDLING
	 **************************************************/

	public function quote ($value) {
		return ($value);
	}

	/**************************************************
	 * ERRORS HANDLING
	 **************************************************/

	/**
	 * Return true if there is an error
	 *
	 * @return Boolean
	 */
	public function isError() {
		return ($this->errorStatus != 0);
	}

	/**
	 * Return the SQL error code
	 *
	 * @return Int
	 *
	 */
	public function errorCode() {
		return ($this->errorStatus);
	}

	/**
	 * Return the last error message
	 *
	 * @return String
	 */
	public function errorMessage() {
		return ('');
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
		return (true);
	}

	/**
	 * Renvoie une ligne du curseur sous la forme d'un tableau indexé
	 *
	 * @param 	String	$queryName	nom de la requête
	 *
	 * @return	integer	un tableau de la ligne en cours du curseur, FALSE en cas d'erreur ou fin de curseur
	 *
	 * @access	public
	 */
	public function fetchRow($queryName = 'query') {
		return (false);
	}

	/**
	 * Renvoie une ligne du curseur sous la forme d'un tableau associatif
	 *
	 * @param 	String	$queryName	nom de la requête
	 *
	 * @return	mixed	un tableau associatif de la ligne en cours du curseur, FALSE en cas d'erreur ou fin de curseur
	 *
	 * @access	public
	 */
	public function fetchArray($queryName = 'query') {
		return (false);
	}

	/**
	 * Renvoie une ligne du curseur sous la forme d'un object
	 *
	 * @param 	String	$queryName	nom de la requête
	 *
	 * @return	mixed	un tableau associatif de la ligne en cours du curseur, FALSE en cas d'erreur ou fin de curseur
	 *
	 * @access	public
	 */
	public function fetchObject($queryName = 'query') {
		return (false);
	}

	/**
	 * Renvoie le nombre de lignes retournées par la requête SELECT
	 *		ou le nombre de lignes affectées par la requête
	 *
	 * @param 	String	$queryName	nom de la requête
	 *
	 * @return	integer le nombre de lignes, FALSE en cas d'erreur
	 *
	 * @access	public
	 */
	public function numRows($queryName = 'query') {
		return (false);
	}

	/**
	 * Renvoie une ligne du curseur sous la forme d'un tableau associatif
	 *
	 * @param 	String	$queryName	nom de la requête
	 *
	 * @return	mixed	id généré pour une colonne AUTO_INCREMENT lors du dernier INSERT ou FALSE en cas d'erreur
	 *
	 * @access	public
	 */
	public function insertId($queryName = 'query') {
		return (false);
	}

	/**
	 * Libère les ressources associées à la requête
	 *
	 * @param 	String	$queryName	nom de la requête
	 *
	 * @access	public
	 */
	public function freeQuery($queryName = 'query') {
	}
	
	/**
	 * Create and return an unique ID, compared with existing values in a given table
	 *
	 * @param $table		String
	 * @param $field		String
	 * @param $length		Int
	 * 
	 * @return String
	 */
	public function getSingleID ($table, $field, $length = 20) {
		do {
			$id = substr(strtoupper(md5(uniqid(''))), 0, $length);
			
			$this->query('__select_getSingleID__', "SELECT `".$field."` FROM `".$table."` WHERE (`".$field."` = '".$id."')");
		}
		while ($this->numRows('__select_getSingleID__') >= 1);
		
		$this->freeQuery('__select_getSingleID__');
		
		return ($id);
	}
}
