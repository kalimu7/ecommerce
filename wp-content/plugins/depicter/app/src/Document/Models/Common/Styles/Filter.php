<?php
namespace Depicter\Document\Models\Common\Styles;


use Depicter\Document\CSS\Breakpoints;

class Filter extends States
{
	/**
	 * style name
	 */
	const NAME = 'filter';

	/**
	 * @var int
	 */
	public $brightness = 100;

	/**
	 * @var int
	 */
	public $contrast = 100;

	/**
	 * @var int
	 */
	public $saturation = 100;

	/**
	 * @var int
	 */
	public $hue = 0;

	/**
	 * @var int
	 */
	public $blur = 0;

	public function set( $css ) {
		$devices = Breakpoints::names();
		foreach ( $devices as $device ) {
			if ( isset( $this->{$device}->enable ) ) {

				// If it is disabled in a breakpoint other than default, generate a reset style for breakpoint
				if( $device !== 'default' && ! empty( $this->default->enable )  && ! $this->{$device}->enable ) {
					$css[$device][ self::NAME ] = 'none';

				} elseif( $this->{$device}->enable ) {
					$this->brightness = $this->{$device}->brightness ?? $this->brightness;
					$this->contrast = $this->{$device}->contrast ?? $this->contrast;
					$this->saturation = $this->{$device}->saturation ?? $this->saturation;
					$this->hue = $this->{$device}->hue ?? $this->hue;
					$this->blur = $this->{$device}->blur ?? $this->blur;

					$css[$device][self::NAME] = "brightness(" . $this->brightness . "%) contrast(" . $this->contrast . '%) saturate(' . $this->saturation . '%) blur(' . $this->blur . 'px) hue-rotate(' . $this->hue . 'deg)';
				}
			}
		}

		return $css;
	}
}
