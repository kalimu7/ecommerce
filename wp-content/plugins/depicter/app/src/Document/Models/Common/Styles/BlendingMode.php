<?php
namespace Depicter\Document\Models\Common\Styles;


use Depicter\Document\CSS\Breakpoints;

class BlendingMode extends States
{
	/**
	 * style name
	 */
	const NAME = 'mix-blend-mode';

	public function set( $css ) {
		$devices = Breakpoints::names();
		foreach ( $devices as $device ) {
			if ( isset( $this->{$device}->enable ) ) {

				// If it is disabled in a breakpoint other than default, generate a reset style for breakpoint
				if( $device !== 'default' && ! empty( $this->default->enable )  && ! $this->{$device}->enable ) {
					$css[$device][ self::NAME ] = 'normal';

				} elseif( ! empty( $this->{$device}->type ) && $this->{$device}->enable ) {
					$css[ $device ][ self::NAME ] = $this->{$device}->type;
				}
			}
		}

		return $css;
	}
}
