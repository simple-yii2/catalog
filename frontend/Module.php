<?php

namespace cms\catalog\frontend;

use cms\components\FrontendModule;

/**
 * Catalog frontend module
 */
class Module extends FrontendModule
{

	/**
	 * @var array
	 */
	public $productListConfig = [
		'layout' => "{list}",
		'listOptions' => ['class' => 'col-sm-12'],
		'listItemOptions' => ['class' => 'col-sm-3'],
	];

	/**
	 * @var array
	 */
	public $productListWithFilterConfig = [];

}
