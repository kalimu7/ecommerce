<?php
namespace Depicter\Document\Models\Common\Styles;

use Depicter\Document\CSS\Breakpoints;

class Base
{

	/**
	 * @var array
	 */
	public $variations = [];

	/**
	 * @var array
	 */
	protected $breakpoint_dictionary = [];

	public function __construct()
	{
		$this->breakpoint_dictionary = Breakpoints::all();
	}

	public function set( $name, $value )
	{
		return $this->variations[ $name ] = $value;
	}
}
