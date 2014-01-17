<?php
/**
 * Links
 * Build Social networks links
 * 
 * @author Maxime
 */

namespace MF\Links;

class Links {
	protected static function link ($content, $url, $w = 500, $h = 300) {
		return '<a '
					.'target="_blank" '
					.'href="'.$url.'" '
					.'rel="nofollow" '
					.'onclick="javascript:window.open(this.href, \'sharer\', \''
									.'menubar=no,'
									.'toolbar=no,'
									.'resizable=yes,'
									.'scrollbars=yes,'
									.'width='.$w.','
									.'height='.$h.','
									.'left=\' + (0.5 * (screen.width - '.$w.')) + \','
									.'top=\'  + (0.5 * (screen.height - '.$h.'))'
								.'); return false;"'
				.'>'.$content.'</a>';
	}

	/**
	 * Facebook
	 *
	 * @param $content
	 * @param $url
	 * @param $title
	 * @return string
	 */
	public static function facebook ($content, $url, $title) {
		$url   = urlencode($url);
		$title = urlencode($title);
		return Links::link($content, 'https://www.facebook.com/sharer.php?u='.$url.'&t='.$title);
	}

	/**
	 * Twitter
	 *
	 * @param $content
	 * @param $url
	 * @param $title
	 * @return string
	 */
	public static function twitter ($content, $url, $title) {
		$url   = urlencode($url);
		$title = urlencode($title);
		return Links::link($content, 'https://twitter.com/share?url='.$url.'&text='.$title);
	}

	/**
	 * Google
	 *
	 * @param $content
	 * @param $url
	 * @param $title
	 * @return string
	 */
	public static function google ($content, $url, $title) {
		$url   = urlencode($url);
		$title = urlencode($title);
		return Links::link($content, 'https://plus.google.com/share?url='.$url.'&hl=fr');
	}

	/**
	 * Linkedin
	 *
	 * @param $content
	 * @param $url
	 * @param $title
	 * @return string
	 */
	public static function linkedin ($content, $url, $title) {
		$url   = urlencode($url);
		$title = urlencode($title);
		return Links::link($content, 'https://www.linkedin.com/shareArticle?mini=true&url='.$url.'&title='.$title);
	}
}
