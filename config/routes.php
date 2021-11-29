<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */
use Hyperf\HttpServer\Router\Router;

Router::addRoute(['GET', 'POST', 'HEAD'], '/', 'App\Controller\IndexController@index');
Router::addRoute(['GET', 'POST'], '/merchandise/create', 'App\Controller\MerchandiseController@create');
Router::addRoute(['GET', 'POST'], '/merchandise/get', 'App\Controller\MerchandiseController@get');
Router::addRoute(['GET', 'POST'], '/merchandise/update', 'App\Controller\MerchandiseController@update');
Router::addRoute(['GET', 'POST'], '/merchandise/merchandiseAssociatedMerchandiseItemsList', 'App\Controller\MerchandiseController@merchandiseAssociatedMerchandiseItemsList');
