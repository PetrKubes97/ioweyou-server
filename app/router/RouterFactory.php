<?php

namespace App;

use Nette;
use Nette\Application\Routers\RouteList;
use Nette\Application\Routers\Route;


class RouterFactory
{
	use Nette\StaticClass;

	/**
	 * @return Nette\Application\IRouter
	 */
	public static function createRouter()
	{
		$router = new RouteList;

		// API
		$router[] = new Route('api/debts/update', 'Api:Debts:update');
		$router[] = new Route('api/debts[/<which>]', 'Api:Debts:default');

		$router[] = new Route('<module>/<presenter>/<action>[/<id>]', 'Web:Homepage:default');
		$router[] = new Route('<presenter>/<action>[/<id>]', 'Web:Homepage:default');
		return $router;
	}

}
