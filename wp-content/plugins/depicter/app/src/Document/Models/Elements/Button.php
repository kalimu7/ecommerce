<?php
namespace Depicter\Document\Models\Elements;

use Depicter\Document\Models;
use Depicter\Html\Html;

class Button extends Models\Element
{

	public function render() {

		$args = $this->getDefaultAttributes();
		$div = Html::div( $args, $this->options->content );

		if ( false !== $a = $this->getLinkTag() ) {
			return $a->nest( $div );
		}
		return $div;
	}
}
