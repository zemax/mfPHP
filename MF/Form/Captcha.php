<?php
namespace MF\Form;

use MF\Response;

class Captcha {
	private $chars = '23456789ABCDEFGHJKMNPQRSTUVXYZ';
	private $fonts = array();
	private $colors = array();
	private $background = array( 255, 255, 255 );
	private $sessionId = 'captcha';

	private $image;
	private $width;
	private $height;

	public function __construct ( $width = 100, $height = 50 ) {
		$this->width = $width;
		$this->height = $height;

		if ( !isset( $_SESSION ) ) {
			session_start();
		}
	}

	public function setChars ( $chars ) {
		if ( !empty( $chars ) ) {
			$this->chars = (string) $chars;
		}
	}

	public function addFont ( $font ) {
		if ( !empty( $font ) ) {
			if ( is_array( $font ) ) {
				foreach ( $font as $f ) {
					array_push( $this->fonts, $f );
				}
			}
			else {
				array_push( $this->fonts, $font );
			}
		}
	}

	public function setBackground ( $color ) {
		$this->background = $color;
	}

	public function addColor ( $color ) {
		array_push( $this->colors, $color );
	}

	public function generateValue ( $length = 4 ) {
		$l = strlen( $this->chars );

		$value = '';
		for ( $i = 0; $i < $length; $i++ ) {
			$value .= $this->chars[ mt_rand( 0, $l - 1 ) ];
		}

		$_SESSION[ $this->sessionId ] = $value;
	}

	public function getValue () {
		if ( empty( $_SESSION[ $this->sessionId ] ) ) {
			$this->generateValue();
		}

		return ( $_SESSION[ $this->sessionId ] );
	}

	public function compareValue ( $value, $caseSensitive = false ) {
		if ( $caseSensitive ) {
			return ( strcmp( $value, $this->getValue() ) == 0 );
		}
		else {
			return ( strcasecmp( $value, $this->getValue() ) == 0 );
		}
	}

	private function getColor ( $image ) {
		if ( empty( $this->colors ) ) {
			$color = imagecolorallocate( $image, rand( 0, 255 ), rand( 0, 255 ), rand( 0, 255 ) );
		}
		else {
			$colorIndex = $this->colors[ mt_rand( 0, count( $this->colors ) - 1 ) ];
			$color = imagecolorallocate( $image, $colorIndex[ 0 ], $colorIndex[ 1 ], $colorIndex[ 2 ] );
		}

		return ( $color );
	}

	private function getFont () {
		$font = $this->fonts[ mt_rand( 0, count( $this->fonts ) - 1 ) ];

		return ( $font );
	}

	protected function getRGB ( $col ) {
		return array(
			(int) ( $col >> 16 ) & 0xff,
			(int) ( $col >> 8 ) & 0xff,
			(int) ( $col ) & 0xff,
		);
	}

	protected function interpolateColor ( $x, $y, $nw, $ne, $sw, $se ) {
		list( $r0, $g0, $b0 ) = $this->getRGB( $nw );
		list( $r1, $g1, $b1 ) = $this->getRGB( $ne );
		list( $r2, $g2, $b2 ) = $this->getRGB( $sw );
		list( $r3, $g3, $b3 ) = $this->getRGB( $se );

		$cx = 1.0 - $x;
		$cy = 1.0 - $y;

		$m0 = $cx * $r0 + $x * $r1;
		$m1 = $cx * $r2 + $x * $r3;
		$r = (int) ( $cy * $m0 + $y * $m1 );

		$m0 = $cx * $g0 + $x * $g1;
		$m1 = $cx * $g2 + $x * $g3;
		$g = (int) ( $cy * $m0 + $y * $m1 );

		$m0 = $cx * $b0 + $x * $b1;
		$m1 = $cx * $b2 + $x * $b3;
		$b = (int) ( $cy * $m0 + $y * $m1 );

		return ( $r << 16 ) | ( $g << 8 ) | $b;
	}

	protected function getColorAt ( $image, $x, $y ) {
		$p = imagecolorat(
			$image,
			max( 0, min( $this->width - 1, round( $x ) ) ),
			max( 0, min( $this->height - 1, round( $y ) ) )
		);

		return $p;
	}

	/**
	 * Distorts the image
	 */
	public function distortImage ( $interpolate = true ) {
		$imageD = imagecreatetruecolor( $this->width, $this->height );

		$X = mt_rand( 0, $this->width );
		$Y = mt_rand( 0, $this->height );
		$phase = mt_rand( 0, 100 );
		$scale = 2 + mt_rand( 0, 10000 ) / 30000;

		for ( $x = 0; $x < $this->width; $x++ ) {
			for ( $y = 0; $y < $this->height; $y++ ) {
				$Vx = $x - $X;
				$Vy = $y - $Y;
				$Vn = sqrt( $Vx * $Vx + $Vy * $Vy );

				if ( $Vn != 0 ) {
					$Vn2 = $Vn + 10 * sin( $Vn / 30 );
					$nX = $X + ( $Vx * $Vn2 / $Vn );
					$nY = $Y + ( $Vy * $Vn2 / $Vn );
				}
				else {
					$nX = $X;
					$nY = $Y;
				}
				$nY = $nY + $scale * sin( $phase + $nX * 0.2 );

				if ( $interpolate ) {
					$p = $this->interpolateColor(
						$nX - floor( $nX ),
						$nY - floor( $nY ),
						$this->getColorAt( $this->image, floor( $nX ), floor( $nY ) ),
						$this->getColorAt( $this->image, ceil( $nX ), floor( $nY ) ),
						$this->getColorAt( $this->image, floor( $nX ), ceil( $nY ) ),
						$this->getColorAt( $this->image, ceil( $nX ), ceil( $nY ) )
					);
				}
				else {
					$p = $this->getColorAt( $this->image, round( $nX ), round( $nY ) );
				}

				imagesetpixel( $imageD, $x, $y, $p );
			}
		}

		$this->image = $imageD;
	}

	public function createImage ( $angleMax = 25, $padding = 8, $spacingRatio = 1.3 ) {
		$this->image = imagecreatetruecolor( $this->width, $this->height );
		$bg = imagecolorallocate(
			$this->image,
			$this->background[ 0 ],
			$this->background[ 1 ],
			$this->background[ 2 ]
		);
		imagefilledrectangle( $this->image, 0, 0, $this->width - 1, $this->height - 1, $bg );

		if ( empty( $this->fonts ) ) {
			$this->addFont( $path = realpath( dirname( __FILE__ ) ) . '/doris.ttf' );
		}

		$value = $this->getValue();
		$valueLength = strlen( $value );
		$valueWidth = $spacingRatio * ( $valueLength - 1 ) + 1;

		$hSize = ( $this->width - 2 * $padding ) / $valueWidth;
		$vSize = ( $this->height - 2 * $padding );

		$size = min( $vSize, $hSize );
		$spacing = $spacingRatio * $size;
		$x = 0.5 * ( $this->width - ( $size * $valueWidth ) );
		$y = 0.5 * ( $this->height - $size ) + $size;

		// Génération des lettres
		for ( $i = 0; $i < $valueLength; $i++ ) {
			// font
			$font = $this->getFont();

			// color
			$color = $this->getColor( $this->image );

			// angle
			$angle = mt_rand( -$angleMax, $angleMax );

			imagettftext( $this->image, $size, $angle, $x, $y, $color, $font, $value[ $i ] );
			$x += $spacing;
		}
	}

	public function sendImage () {
		Response::setContentType( 'image/png' );
		Response::setNoCache();
		imagepng( $this->image );
	}
}
