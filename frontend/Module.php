<?php

namespace cms\catalog\frontend;

use cms\components\FrontendModule;

/**
 * Catalog frontend module
 */
class Module extends FrontendModule
{

	/**
	 * @inheritdoc
	 */
	public static function moduleName()
	{
		return 'catalog';
	}

}
