<?php
/**
 * Open SWF file and get informations from it
 * 
 * @author Maxime
 *
 */
class MF_Swf_Parser {
	/**
	 * Return Header Array from the SWF
	 * 
	 * @param $filename		String
	 * 
	 * @return Array
	 */
	public static function getHeader ($filename) {
		$file = fopen ($filename, "rb");
		$header = unpack("a3Signature/C1Version/V1FileLength", fread ($file, 8));
		
		if ($header["Signature"] == "CWS") {
			$file_header = gzuncompress ( fread ($file, filesize($filename)) );
			fclose($file);
		}
		else if ($header["Signature"] == "FWS") {
			$file_header = fread ($file, 14);
			fclose($file);
		}
		else {
			fclose($file);
			return (false);
		}
				
		$rect = unpack("C1size", substr($file_header, 0, 1) );
		$nbits = $rect["size"] >> 3;
		
		$size = ceil(($nbits * 4 + 5) / 8) - 1;
		
		$rect = unpack("C".$size."b", substr($file_header, 1, $size) );
		
		$header["FrameWidth"] 	= SWFTools::concat_bits ($rect, $nbits - 3, $nbits) / 20;
		$header["FrameHeight"] 	= SWFTools::concat_bits ($rect, 3 * $nbits - 3, $nbits) / 20;
		
		$end = unpack("C1fpsreal/C1fps/vFrameCount", substr($file_header, $size+1, 4));
		$header["FrameRate"] = $end["fps"] + ($end["fpsreal"] / 256);
		$header["FrameCount"] = $end["FrameCount"];
			
		return ($header);
	}
	
	/**
	 * Concat bits
	 * 
	 * @param $bytes_array
	 * @param $start_bit
	 * @param $length
	 * @return unknown_type
	 */
	private static function concat_bits ($bytes_array, $start_bit, $length) {
		$current_byte = floor ($start_bit / 8) + 1;
		$end_byte = ceil (($start_bit+$length) / 8);
		
		$bits_to_read = 8 * $current_byte - $start_bit;
		$bits_left = $length - $bits_to_read;
		
		$return = ($bytes_array["b".$current_byte++] & (pow(2, $bits_to_read)-1)) << $bits_left;
		
		while ($current_byte < $end_byte) {
			$bits_left -= 8;
			$return += $bytes_array["b".$current_byte++] << $bits_left;
		}
		$return += ($bytes_array["b".$current_byte] & ((pow(2, $bits_left)-1) << (8 - $bits_left))) >> (8 - $bits_left);
		
		return ($return);
	}


	/**
	 * Return the old school SWF Tag
	 * 
	 * @param $filename				String
	 * @param $width				String
	 * @param $height				String
	 * @param $version				String
	 * @param $background_color		String
	 * 
	 * @return String
	 */
	public static function getHTMLTag ($filename, $width="", $height="", $version="", $background_color="#FFFFFF") {
		if (($version == "") || ($width == "") || ($height == "")) {
			$header = SWFTools::getHeader($filename);
			
			if ($header) {
				if ($version == "") {
					$version = $header["Version"].',0,0,0';
				}
				if ($width == "") {
					$width = $header["FrameWidth"];
				}
				if ($height == "") {
					$height = $header["FrameHeight"];
				}
			}		
		}
		
		$html  = '<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000"'.NL;
	 	$html .= '	codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version='.$version.'"'.NL;
	 	$html .= '	width="'.$width.'" height="'.$height.'">'.NL;
		$html .= '	<param name="movie" value="'.$filename.'">'.NL;
		$html .= '	<param name="quality" value="high">'.NL;
		$html .= '	<param name="bgcolor" value="'.$background_color.'">'.NL;
		$html .= '	<embed src="'.$filename.'"';
		$html .= '		quality="high"';
		$html .= '		bgcolor="'.$background_color.'"';
		$html .= '		width="'.$width.'" height="'.$height.'"';
		$html .= '		type="application/x-shockwave-flash"';
		$html .= '		pluginspage="http://www.macromedia.com/shockwave/download/index.cgi?p1_prod_version=shockwaveflash"></embed>'.NL;
		$html .= '</object>'.NL;
		
		return ($html);
	}
}
