<?php
namespace Depicter\Document\Models\Common;

use Averta\Core\Utility\Data;
use Averta\WordPress\Utility\JSON;
use Depicter\Document\CSS\Breakpoints;

class KenBurns extends States {

	/**
	 * Get all kenburn attributes
	 *
	 * @return array
	 */
	public function getKenBurnsAttrs(): array{
		$attrs = [];
		// Collect animation attributes
		foreach ( Breakpoints::names() as $breakpoint  ){
			$breakpoint_prefix = $breakpoint ? $breakpoint . '-' : $breakpoint;
			$breakpoint_prefix = $breakpoint == 'default' ? '' : $breakpoint_prefix;

			if( isset( $this->{$breakpoint}->enable ) ){
				$attrs[ 'data-'.  $breakpoint_prefix .'ken-burns' ] = !empty($this->{$breakpoint}->enable) ? $this->getKenBurnsOption( $this->{$breakpoint} ) : 'false';
			} else if ( isset( $this->default->enable ) ) {
				$attrs[ 'data-'.  $breakpoint_prefix .'ken-burns' ] = "false";
			}

		}
		return $attrs;
	}

	/**
	 * Get kenburns option
	 *
	 * @param object $kenBurnsOptions
	 * @return string
	 */
	public function getKenBurnsOption( $kenBurnsOptions ): string{
		$options = [];

		isset( $kenBurnsOptions->params->scale ) && $options['scale'] = $kenBurnsOptions->params->scale;
		isset( $kenBurnsOptions->params->duration ) && $options['duration'] = $kenBurnsOptions->params->duration;
		isset( $kenBurnsOptions->params->focalPoint ) && $options['focalPoint']['x'] = $kenBurnsOptions->params->focalPoint->x;
		isset( $kenBurnsOptions->params->focalPoint ) && $options['focalPoint']['y'] = $kenBurnsOptions->params->focalPoint->y;
		isset( $kenBurnsOptions->params->easing ) && $options['easing'] = $kenBurnsOptions->params->easing;

		return JSON::encode( $options );
	}
}
