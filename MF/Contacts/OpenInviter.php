<?php
namespace MF\Contacts;

use MF\String,
	MF\ReturnObject;

class OpenInviter {
	public function getAddressBook($mailbox, $login, $password){
		/****************************************************************************************************
		* VERIFICATION
		****************************************************************************************************/
		
		if (!function_exists ('curl_version')){
			return (new ReturnObject(0, 'curl_error'));
		}
				
		/****************************************************************************************************
		* VERIFICATION
		****************************************************************************************************/
		
		if (empty($mailbox)) {
			return (new ReturnObject(0, 'mailbox_invalid'));
		}
		
		if (empty($login)) {
			return (new ReturnObject(0, 'login_invalid'));
		}
		
		if (empty($password)) {
			return (new ReturnObject(0, 'password_invalid'));
		}
		
		/****************************************************************************************************
		* CREATION DU SERVICE
		****************************************************************************************************/
		
		if (!class_exists('openinviter_base', false)) 	require("plugins/_base.php");
		if (!class_exists($mailbox, false)) 			require("plugins/{$mailbox}.plg.php");
		
		$service = new $mailbox();
		$service->settings = array(
			'cookie_path'	=> sys_get_temp_dir(), 
			'transport'		=> "curl", 
			'local_debug'	=> false, 
			'remote_debug'	=> false
		);
		$service->base_version = '1.9.0';
		$service->base_path = '';
    	
		if (file_exists("conf/{$mailbox}.conf")) {
			include("conf/{$mailbox}.conf");
			
			$service->messageDelay = !empty($messageDelay)?$messageDelay:1;
			$service->maxMessages 	= !empty($maxMessages)?$maxMessages:10;
		}
		
		/****************************************************************************************************
		* LOGIN
		****************************************************************************************************/
		
		if (!$service->login($login, $password)) {
			return (new ReturnObject(0, 'login_invalid'));
		}
		
		/****************************************************************************************************
		* RECUPERATION
		****************************************************************************************************/
		
		$contacts = $this->cleanContacts($service->getMyContacts());
		
		$service->logout();
		
		// On vérifie que le carnet n'est pas vide
		if (empty($contacts)){
			return (new ReturnObject(0, 'get_addressbook_empty'));
		}
		else {
			return (new ReturnObject(1, 'get_addressbook_success', $contacts));
		}
	}
	
	/**
	 * Nettoie la liste de contacts renvoyée
	 * @param $contacts
	 * @return Array
	 */
	private function cleanContacts($contacts){
		$contactsClean = array();
		
		foreach ($contacts as $email => $name) {
			$email = String::cleanEmail($email);
			
			if (String::isValidEmail($email) && !String::isJetableEmail($email)) {
				$contactsClean[]	= array(
					'name' 	=> utf8_decode($name), 
					'email'	=> $email
				);
			}
		}
		// Retour du tableau nettoyé
		return ($contactsClean);
	}
}
