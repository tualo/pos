<?php

namespace Tualo\Office\PointOfSale\Routes;

use Tualo\Office\Basic\TualoApplication;
use Tualo\Office\Basic\Route;
use Tualo\Office\Basic\IRoute;
use Ramsey\Uuid\Uuid;


class Register extends \Tualo\Office\Basic\RouteWrapper
{
    public static function register()
    {
        /*
        self::$clientID = self::db()
                ->singleValue(
                    'select tss_client_id from kassenterminals_client_id where  kassenterminal={kassenterminal}',
                    [
                        'kassenterminal' => $terminal_id
                    ],
                    'tss_client_id'
                );
        */
        Route::add('/pos/isRegistered/(?P<terminalid>[\w\-\_]+)', function ($matches) {
            TualoApplication::contenttype('application/json');
            $session = TualoApplication::get('session');
            $db = $session->getDB();
            try {
                $terminal_id = $matches['terminalid'];
                $isRegistered = $db->singleValue(
                    'select count(*) as cnt from kassenterminals where id={id}',
                    ['id' => $terminal_id],
                    'cnt'
                );
                TualoApplication::result('isRegistered', ($isRegistered > 0));
                TualoApplication::result('success', true);
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

        Route::add('/pos/register/(?P<terminalid>[\w\-\_]+)', function ($matches) {
            try {
                TualoApplication::contenttype('application/json');
                $db = TualoApplication::get('session')->getDB();
                $terminal_id = $matches['terminalid'];
                $clientID = (Uuid::uuid4())->toString();
                $db->direct(
                    'insert ignore into kassenterminals (id, name, kasse,lager,beleg) values ({kassenterminal}, {name}, {kasse},{lager},{beleg} )  ',
                    [
                        'kassenterminal' => $terminal_id,
                        'name' => 'Kassenterminal ' . $terminal_id,
                        'kasse' => $db->singleValue('select min(id) x from hauptkassenbuecher', [], 'x') ?: 1,
                        'lager' => $db->singleValue('select min(id) x from lager', [], 'x') ?: 1,
                        'beleg' => $db->singleValue('select min(id) x from blg_config', [], 'x') ?: 1
                    ]
                );

                $db->direct(
                    'insert ignore into kassenterminals_client_id (kassenterminal,tss_client_id) values ({kassenterminal},{tss_client_id}) ',
                    [
                        'kassenterminal' => $terminal_id,
                        'tss_client_id' => $clientID
                    ]
                );

                $clientID = $db->singleValue(
                    'select tss_client_id from kassenterminals_client_id where kassenterminal={id}',
                    ['id' => $terminal_id],
                    'cnt'
                );
                TualoApplication::result('clientID', $clientID);
                TualoApplication::result('success', true);
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
