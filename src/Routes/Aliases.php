<?php

namespace Tualo\Office\PointOfSale\Routes;

use Tualo\Office\Basic\TualoApplication;
use Tualo\Office\Basic\Route;
use Tualo\Office\Basic\IRoute;


class Aliases implements IRoute
{
    public static function register()
    {
        Route::alias('/pos/ping', '/dashboard/ping');
        Route::alias('/pos/profile/read', '/profile/read');
        Route::alias('/pos/ds/(?P<tablename>\w+)/read', '/ds/(?P<tablename>\w+)/read');
        Route::alias('/pos/registerclient', '/registerclient');
        Route::alias('/pos/tse/information/(?P<terminalid>[\w\-\_]+)', '/fiskaly/information/(?P<terminalid>[\w\-\_]+)');
        Route::alias('/pos/tse/createclient/(?P<terminalid>[\w\-\_]+)', '/fiskaly/createClient/(?P<terminalid>[\w\-\_]+)');
    }
}
