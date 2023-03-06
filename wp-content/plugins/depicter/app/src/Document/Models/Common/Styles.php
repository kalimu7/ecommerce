<?php
namespace Depicter\Document\Models\Common;


use Depicter\Document\CSS\Breakpoints;

class Styles
{
	/**
	 * @var Styles\Transform
	 */
	public $transform;

	/**
	 * @var Styles\Opacity
	 */
	public $opacity;

	/**
	 * @var Styles\BoxShadow
	 */
	public $boxShadow;

	/**
	 * @var Styles\BackgroundBlur
	 */
	public $backgroundBlur;

	/**
	 * @var Styles\BackgroundColor
	 */
	public $backgroundColor;

	/**
	 * @var Styles\Border
	 */
	public $border;

	/**
	 * @var Styles\Corner
	 */
	public $corner;

	/**
	 * @var Styles\Filter
	 */
	public $filter;

	/**
	 * @var Styles\TextShadow
	 */
	public $textShadow;

	/**
	 * @var Styles\Margin
	 */
	public $margin;

	/**
	 * @var Styles\Padding
	 */
	public $padding;

	/**
	 * @var Styles\BlendingMode
	 */
	public $blendingMode;

	/**
	 * @var Styles\Typography
	 */
	public $typography;

	/**
	 * @var Styles\Transition
	 */
	public $transition;

	/**
	 * @var Styles\Svg
	 */
	public $svg;

	/**
	 * @var Styles\Hover
	 */
	public $hover;


	/**
	 * Retrieves list of fonts used in typography options
	 *
	 * @return array
	 */
	public function getFontsList()
	{
		if( !empty( $this->typography ) ){
			return $this->typography->getFontsList();
		}

		return [];
	}

	/**
	 * Retrieves styles for all available modules
	 *
	 * @param array $states The states to generate style for
	 *
	 * @return array
	 */
	public function getGeneralCss( $states = 'all' ) {
		$cssModules = [
			'filter',
			'textShadow',
			'opacity',
			'padding',
			'typography',
			'boxShadow',
			'transform',
			'backgroundBlur',
			'backgroundColor',
			'border',
			'corner',
			'margin'
		];

		return $this->generateCssForModules( $cssModules, $states );
	}

	/**
	 * Get styles for SVG
	 *
	 * @return array
	 */
	public function getSvgCss() {
		return $this->generateCssForModules( [ 'svg' ] );
	}

	/**
	 * Get transition CSS
	 *
	 * @return array
	 */
	public function getTransitionCss() {
		$css = $this->getInitialCssVariable();
		/**
		 * While transition is defined in 'hover' property, we need to consider an exception to generate
		 * transition style for normal state not :hover
		 **/
		$css = !empty( $this->hover->transition ) ? $this->hover->transition->set( $css ) : $css;

		return $css;
	}

	/**
	 * Collects and retrieves responsive styles from css modules
	 *
	 * @param array|string $cssModules
	 * @param string       $states     The states to generate style for
	 *
	 * @return array
	 */
	protected function generateCssForModules( $cssModules = [], $states = 'all' ){
		$knownStates = ['normal', 'hover'];

		// translate $states if string or null passed
		if( is_string( $states ) ){
			if( $states === 'all' ){
				$states = $knownStates;
			} elseif ( in_array( $states, $knownStates ) ){
				$states = (array) $states;
			}
		}
		if( is_null( $states ) ){
			$states = $knownStates;
		}

		$css = $this->getInitialCssVariable();

		// Collect styles from modules
		foreach ( $cssModules as $cssModule ) {
			if( in_array( 'normal', $states ) ){
				$css = ! empty( $this->{$cssModule} ) ? $this->{$cssModule}->set( $css ) : $css;
			}
			if( in_array( 'hover', $states ) ){
				$css['hover'] = !empty( $this->hover->{$cssModule} ) ? $this->hover->{$cssModule}->set( $css['hover'] ) : $css['hover'];
			}
		}

		// check if hover style is disabled for one breakpoint then remove all the css modules for that breakpoint
		if ( in_array( 'hover', $states ) ) {
			$devices = Breakpoints::names();
			foreach ( $devices as $device ) {
				if ( empty( $this->hover->enable->{$device} ) ) {
					$css['hover'][ $device ] = [];
				}
			}
		}

		return $css;
	}

	/**
	 * Retrieves a structured list for normal and hover states and breakpoints
	 *
	 * @return array
	 */
	protected function getInitialCssVariable(){
		$css = [];
		$devices = Breakpoints::names();
		foreach ( $devices as $device ) {
			$css[ $device ] = [];
		}
		$css['hover'] = $css;

		return $css;
	}

	/**
	 * Get blending mode styles
	 * 
	 * @return array
	 */
	public function getBlendingModeStyle(): array{
		return $this->generateCssForModules(['blendingMode']);
	}

}
