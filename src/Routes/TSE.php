<?php

namespace Tualo\Office\PointOfSale\Routes;

use Tualo\Office\Basic\TualoApplication;
use Tualo\Office\Basic\Route;
use Tualo\Office\Basic\IRoute;


class TSE implements IRoute
{
    public static function register()
    {
        Route::add('/pos/tse/information', function ($matches) {
            TualoApplication::contenttype('application/json');
            try {
                $session = TualoApplication::get('session');
                $terminalid = $session->getHeader('terminalid');
                if (!$terminalid) {
                    $terminalid = '0d09a80a-8f66-4e18-944c-0b5ba31aae4a';
                    //                    throw new \Exception('Terminal ID is required');
                }
                if (!preg_match('/^[\w\-\_]+$/', $terminalid)  || strlen($terminalid) < 1 || strlen($terminalid) > 64) {
                    throw new \Exception('Terminal ID is required');
                }
                if (class_exists('\Tualo\Office\FiskalyAPI\API') == false) {
                    throw new \Exception('FiskalyAPI not installed');
                }
                TualoApplication::result('data', \Tualo\Office\FiskalyAPI\API::getTSSInformation($terminalid));
                TualoApplication::result('success', true);
            } catch (\Exception $e) {
                TualoApplication::result('msg', $e->getMessage());
            }
        }, ['get'], true);
    }
}
