<?php
namespace MF\Localisation;

class Object {
	var $portail;
	var $locales;
	
	/**
	 * Constructor
	 * 
	 * @return Object
	 */
	protected function __construct () {
		$this->locales = array();
	}
	
	/**
	 * Retourne le portail
	 *
	 * @return
	 */
	public function setPortail ($portail) {
		$this->portail = $portail;
	}
	
	/**
	 * Retourne le portail
	 *
	 * @return
	 */
	public function getPortail () {
		return ($this->portail);
	}
	
	/**
	 * Retourne un texte localisé
	 *
	 * @param	id
	 */
	public function getText ($id, $values = array()) {
		$locale = $this->getLocale($this->getPortail());
		
		if (!empty($locale)) {
			$s = $locale->getText($id);
		}
		else {
			$s = $id;
		}
		
		return ($s);
	}

/************************************************
 * METHODES PRIVEES
 ************************************************/

	/**
	 * Retourne la localisation adaptée
	 *
	 * @param $portail
	 * @return LocaleObject
	 */
	private function getLocale ($portail) {
		if (!empty($this->locales[$this->getPortail()])) {
			return ($this->locales[$this->getPortail()]);
		}
		else {
			return false;
		}
	}
}
