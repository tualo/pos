<?php

namespace Tualo\Office\PointOfSale\Routes;

use Tualo\Office\Basic\TualoApplication;
use Tualo\Office\Basic\Route;
use Tualo\Office\Basic\IRoute;


class Setup implements IRoute
{
    public static function register()
    {
        Route::add(
            '/pos/setup',
            function ($matches) {
                TualoApplication::contenttype('application/json');
                try {
                    $session = TualoApplication::get('session');
                    $db = $session->getDB();
                    try {
                        $data = $db->direct('select * from view_point_of_sale_articles ', []);
                        foreach ($data as &$row) {
                            $row['article_combinations'] = json_decode($row['article_combinations'], true);
                            $row['price_scales'] = json_decode($row['price_scales'], true);
                        }
                        TualoApplication::result('articles', $data);
                        TualoApplication::result('terminals', $db->direct('select * from kassenterminals  '));
                        TualoApplication::result('warehouses', $db->direct('select * from lager  '));
                        TualoApplication::result('cashregisters', $db->direct('select * from hauptkassenbuecher  '));
                        TualoApplication::result('reporttypes', $db->direct('select * from blg_config  '));


                        TualoApplication::result('productlists', $db->direct('select * from cash_register_productlist  '));
                        TualoApplication::result('productlistsArticles', $db->direct('select * from cash_register_productlist_articles  '));

                        TualoApplication::result('success', true);
                    } catch (\Exception $e) {
                        TualoApplication::result('msg', $e->getMessage());
                    }
                } catch (\Exception $e) {
                    TualoApplication::result('msg', $e->getMessage());
                }
            },
            ['get', 'post'],
            true,
            [
                'errorOnUnexpected' => false,
                'errorOnInvalid' => false,
                'fields' => [
                    '_dc' => [
                        'required' => false,
                        'type' => 'int',
                    ]
                ]
            ]
        );
    }
}
