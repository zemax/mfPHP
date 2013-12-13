<?php
class MF_Localisation_Infos {
	var $ip 			= '';
	var $countryCode 	= '';
	var $countryName 	= '';
	var $regionCode 	= '';
	var $regionName 	= '';
	var $city 			= '';
	var $zipPostalCode 	= '';
	var $latitude 		= 0;
	var $longitude 		= 0;
	var $timezone 		= 0;
	var $GMTOffset 		= 0;
	var $DSTOffset 		= 0;
	
	const WEBSERVICE_QUERY			= 'http://ipinfodb.com/ip_query.php?ip=';
	const WEBSERVICE_BACKUP_QUERY	= 'http://backup.ipinfodb.com/ip_query.php?ip=';
	
	const WEBSERVICE_COUNTRY		= 'http://ipinfodb.com/ip_query_country.php?ip=';
	const WEBSERVICE_BACKUP_COUNTRY	= 'http://backup.ipinfodb.com/ip_query_country.php?ip=';
	
	/**
	 * Return Geolocalisation Complete from an IP
	 *
	 * @param String $ip
	 * @return MF_Localisation_Infos Geolocalisation [return false on failure]
	 */
	public static function getInfosFromWebservice ($ip = '') {
		return (self::getWebservice($ip, self::WEBSERVICE_QUERY, self::WEBSERVICE_BACKUP_QUERY));
	}
	
	/**
	 * Return Geolocalisation Country from an IP
	 *
	 * @param String $ip
	 * @return MF_Localisation_Infos Geolocalisation [return false on failure]
	 */
	public static function getCountryFromWebservice ($ip = ''){
		return (self::getWebservice($ip, self::WEBSERVICE_COUNTRY, self::WEBSERVICE_BACKUP_COUNTRY));
	}
	
	/**
	 * Return Geolocalisation from an IP
	 *
	 * @param String $ip
	 * @param String $url
	 * @param String $backup_url
	 * @return MF_Localisation_Infos Geolocalisation [return false on failure]
	 */
	private static function getWebservice ($ip = '', $url = '', $backup_url = '') {
		if (empty($ip)) {
			$ip = $_SERVER['REMOTE_ADDR'];
		}
		
		$infosXML = @file_get_contents($url.$ip);

		// Failed to open connection
		if (!$infosXML && !empty($backup_url)) {
			// Use backup server if cannot make a connection
			$infosXML = @file_get_contents($backup_url.$ip);
			
			// Failed to open connection
			if (!$infosXML) {
				return (false); 
			}
		}
		
		try {
			$infosXMLObject = @new SimpleXMLElement($infosXML);
			
			if (!empty($infosXMLObject)) {
				$infos = new MF_Localisation_Infos();
				
				if (!empty($infosXMLObject->Ip)) 			$infos->ip = (string) $infosXMLObject->Ip;
				if (!empty($infosXMLObject->CountryCode)) 	$infos->countryCode = (string) $infosXMLObject->CountryCode;
				if (!empty($infosXMLObject->CountryName)) 	$infos->countryName = (string) $infosXMLObject->CountryName;
				
				if (!empty($infosXMLObject->RegionCode)) 	$infos->regionCode = (string) $infosXMLObject->RegionCode;
				if (!empty($infosXMLObject->RegionName)) 	$infos->regionName = (string) $infosXMLObject->RegionName;
				if (!empty($infosXMLObject->City)) 			$infos->city = (string) $infosXMLObject->City;
				if (!empty($infosXMLObject->ZipPostalCode)) $infos->zipPostalCode = (string) $infosXMLObject->ZipPostalCode;
				
				if (!empty($infosXMLObject->Latitude)) 		$infos->latitude = (float) $infosXMLObject->Latitude;
				if (!empty($infosXMLObject->Longitude)) 	$infos->longitude = (float) $infosXMLObject->Longitude;
				
				if (!empty($infosXMLObject->Timezone)) 		$infos->timezone = (float) $infosXMLObject->Timezone;
				if (!empty($infosXMLObject->Gmtoffset)) 	$infos->GMTOffset = (float) $infosXMLObject->Gmtoffset;
				if (!empty($infosXMLObject->Dstoffset)) 	$infos->DSTOffset = (float) $infosXMLObject->Dstoffset;
				
				return ($infos);
			}
		}
		catch (Exception $e) { 
			return (false);
		}

		// Failed
		return (false);
	}
}
