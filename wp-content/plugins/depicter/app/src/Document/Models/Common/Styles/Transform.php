<?php
namespace Depicter\Document\Models\Common\Styles;

use Depicter\Document\CSS\Breakpoints;

class Transform extends States
{
	/**
	 * style name
	 */
	const NAME = 'transform';

	public function set( $css ) {
		$devices = Breakpoints::names();
		foreach ( $devices as $device ) {
			$transform_styles = '';

			if ( !empty( $this->{$device} ) ) {
				if ( isset( $this->{$device}->rotate ) ) {
					$transform_styles .= "rotate(" . $this->{$device}->rotate . "deg) ";
				}
				if ( isset( $this->{$device}->scaleX ) ) {
					$transform_styles .= "scaleX(" . $this->{$device}->scaleX . ") ";
				}
				if ( isset( $this->{$device}->scaleY ) ) {
					$transform_styles .= "scaleY(" . $this->{$device}->scaleY . ") ";
				}
				if ( isset( $this->{$device}->scale ) ) {
					$transform_styles .= "scale(" . $this->{$device}->scale . ") ";
				}

				if( $transform_styles = trim( $transform_styles ) ){
					$css[ $device ][ self::NAME ] = $transform_styles;
				}
			}
		}

		return $css;
	}
}
