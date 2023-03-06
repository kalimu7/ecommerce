<?php
namespace Depicter\Document\Models\Common\Styles;

use Depicter\Document\CSS\Breakpoints;

class Transition extends States
{
	/**
	 * @var string
	 */
	public $timingFunction;

	/**
	 * @var int
	 */
	public $duration;

	/**
	 * style name
	 */
	const NAME = 'transition';

	public function set( $css ) {
		$devices = Breakpoints::names();
		foreach ( $devices as $device ) {
			if ( !empty( $this->{$device} ) ) {
				if ( !empty( $this->{$device}->enable ) ) {
					$duration = isset( $this->{$device}->duration ) ? $this->{$device}->duration : 0;
					$css[ $device ][ self::NAME ] = "all " . $this->{$device}->timingFunction . ' ' . $duration . 's';
				}
			}
		}

		return $css;
	}
}
