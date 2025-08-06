<?php

namespace Tualo\Office\PointOfSale\Routes;

use Tualo\Office\Basic\TualoApplication;
use Tualo\Office\Basic\Route;
use Tualo\Office\Basic\IRoute;
use Ramsey\Uuid\Uuid;


class SumUp implements IRoute
{
    public static function register()
    {

        Route::add('/pos/sumup/affiliateKey/(?P<terminalid>[\w\-\_]+)', function ($matches) {
            TualoApplication::contenttype('application/json');
            $session = TualoApplication::get('session');
            $db = $session->getDB();
            try {

                $matches['terminalid'] = $matches['terminalid'] ?? '';
                // not yet implemented

                $affiliateKey = $db->singleValue(
                    'select val from sumup_environment where id={id}',
                    ['id' => 'affiliateKey'],
                    'val'
                );
                if ($affiliateKey === false) {
                    TualoApplication::result('success', false);
                } else {
                    TualoApplication::result('affiliateKey', $affiliateKey);
                    TualoApplication::result('success', true);
                }
            } catch (\Exception $e) {
                http_response_code(500);
                TualoApplication::result('msg', $e->getMessage());
            }
        }, ['get'], true, [
            'errorOnUnexpected' => false,
            'errorOnInvalid' => false,
            'fields' => [
                '_dc' => [
                    'required' => false,
                    'type' => 'int',
                ]
            ]
        ]);
    }
}
