<?php

namespace Tualo\Office\PointOfSale\Routes;

use Tualo\Office\Basic\TualoApplication;
use Tualo\Office\Basic\Route;
use Tualo\Office\Basic\IRoute;


class Register implements IRoute
{
    public static function register()
    {
        Route::add('/pos/register', function ($matches) {
            $session = TualoApplication::get('session');
            $db = $session->getDB();
            try {

                $sql = 'insert into kassenterminals (
                        id,
                        name,
                        kasse,
                        lager,
                        beleg
                    ) 
                    values (
                        {id},
                        {name},
                        {kasse},
                        {lager},
                        {beleg}
                    );
                    ';
                $data = $db->direct($sql, [
                    'id'    =>  $_REQUEST['id'],
                    'name'  =>  $_REQUEST['id'],
                    'kasse' => $db->singleValue('select min(id) id from hauptkassenbuecher', [], 'id'),
                    'lager' => $db->singleValue('select min(id) id from lager', [], 'id'),
                    'beleg' => $db->singleValue('select min(id) id from blg_config', [], 'id')
                ]);
                TualoApplication::result('success', true);
            } catch (\Exception $e) {
                TualoApplication::result('msg', $e->getMessage());
            }
            TualoApplication::contenttype('application/json');
        }, array('post', 'get'), true);
    }
}
