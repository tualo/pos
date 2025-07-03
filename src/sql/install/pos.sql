delimiter;

create table if not exists dsfinvk_steuern(
     id integer not null default 1 primary key
);

alter table bfkonten add if not exists dsfinvk_steuern integer default 1;
alter table bfkonten add if not exists dsfinvk_steuern_1 integer default 1;
alter table bfkonten add if not exists dsfinvk_steuern_2 integer default 1;
alter table bfkonten add if not exists dsfinvk_steuern_3 integer default 1;
alter table bfkonten add if not exists dsfinvk_steuern_4 integer default 1;
alter table bfkonten add if not exists dsfinvk_steuern_5 integer default 1;
alter table bfkonten add if not exists dsfinvk_steuern_6 integer default 1;


create
or replace view view_point_of_sale_taxes as
select
    id,
    name,
    JSON_MERGE('{}', group_concat(def separator ',')) js
from
    (
        select
            bfkonten.id,
            bfkonten.name,
            JSON_OBJECT(
                steuergruppen.steuergruppe,
                JSON_OBJECT(
                    'steuergruppe',
                    steuergruppen.steuergruppe,
                    'steuer',
                    bfkonten.steuer,
                    'konto',
                    bfkonten.konto,
                    'dsfinvk_steuern',
                    bfkonten.dsfinvk_steuern
                )
            ) def
        from
            bfkonten
            join steuergruppen on steuergruppen.feld = ''
        union all
        select
            bfkonten.id,
            bfkonten.name,
            JSON_OBJECT(
                steuergruppen.steuergruppe,
                JSON_OBJECT(
                    'steuergruppe',
                    steuergruppen.steuergruppe,
                    'steuer',
                    bfkonten.steuer_1,
                    'konto',
                    bfkonten.konto_1,
                    'dsfinvk_steuern',
                    bfkonten.dsfinvk_steuern_1
                )
            ) def
        from
            bfkonten
            join steuergruppen on steuergruppen.feld = '_1'
        union all
        select
            bfkonten.id,
            bfkonten.name,
            JSON_OBJECT(
                steuergruppen.steuergruppe,
                JSON_OBJECT(
                    'steuergruppe',
                    steuergruppen.steuergruppe,
                    'steuer',
                    bfkonten.steuer_2,
                    'konto',
                    bfkonten.konto_2,
                    'dsfinvk_steuern',
                    bfkonten.dsfinvk_steuern_2
                )
            ) def
        from
            bfkonten
            join steuergruppen on steuergruppen.feld = '_2'
        union all
        select
            bfkonten.id,
            bfkonten.name,
            JSON_OBJECT(
                steuergruppen.steuergruppe,
                JSON_OBJECT(
                    'steuergruppe',
                    steuergruppen.steuergruppe,
                    'steuer',
                    bfkonten.steuer_3,
                    'konto',
                    bfkonten.konto_3,
                    'dsfinvk_steuern',
                    bfkonten.dsfinvk_steuern_3
                )
            ) def
        from
            bfkonten
            join steuergruppen on steuergruppen.feld = '_3'
        union all
        select
            bfkonten.id,
            bfkonten.name,
            JSON_OBJECT(
                steuergruppen.steuergruppe,
                JSON_OBJECT(
                    'steuergruppe',
                    steuergruppen.steuergruppe,
                    'steuer',
                    bfkonten.steuer_4,
                    'konto',
                    bfkonten.konto_4,
                    'dsfinvk_steuern',
                    bfkonten.dsfinvk_steuern_4
                )
            ) def
        from
            bfkonten
            join steuergruppen on steuergruppen.feld = '_4'
        union all
        select
            bfkonten.id,
            bfkonten.name,
            JSON_OBJECT(
                steuergruppen.steuergruppe,
                JSON_OBJECT(
                    'steuergruppe',
                    steuergruppen.steuergruppe,
                    'steuer',
                    bfkonten.steuer_5,
                    'konto',
                    bfkonten.konto_5,
                    'dsfinvk_steuern',
                    bfkonten.dsfinvk_steuern_5
                )
            ) def
        from
            bfkonten
            join steuergruppen on steuergruppen.feld = '_5'
        union all
        select
            bfkonten.id,
            bfkonten.name,
            JSON_OBJECT(
                steuergruppen.steuergruppe,
                JSON_OBJECT(
                    'steuergruppe',
                    steuergruppen.steuergruppe,
                    'steuer',
                    bfkonten.steuer_6,
                    'konto',
                    bfkonten.konto_6,
                    'dsfinvk_steuern',
                    bfkonten.dsfinvk_steuern_6
                )
            ) def
        from
            bfkonten
            join steuergruppen on steuergruppen.feld = '_6'
    ) x
group by
    id;

create
or replace view view_point_of_sale_articles as
select
    artikelgruppen.gruppe article,
    artikelgruppen.kurztext shorttext,
    artikelgruppen.artikelnummer articlenumber,
    warengruppen.warengruppe waregroup,
    warengruppen.farbe waregroup_color,
    warengruppen.wgsort waregroup_sort,
    artikelgruppen.mdeartikel = 1 mobile_article,
    artikelgruppen.plugin article_plugin,
    bfkonten.steuer taxrate,
    JSON_MERGE(
        '[]',
        concat(
            '[',
            group_concat(
                JSON_OBJECT(
                    'min_amount',
                    staffeln.von,
                    'max_amount',
                    staffeln.bis,
                    'net',
                    staffeln.preis,
                    'gross',
                    staffeln.bruttopreis,
                    'pricetype',
                    staffeln.preiskategorie,
                    'time_start',
                    staffeln.zeitraum_von,
                    'time_stop',
                    staffeln.zeitraum_bis,
                    'date_start',
                    staffeln.zeitraum_von,
                    'date_stop',
                    staffeln.zeitraum_bis
                ) separator ','
            ),
            ']'
        )
    ) price_scales,
    if (kombiartikel.resultartikel is null, false, true) use_combination_articles,
    JSON_MERGE(
        '[]',
        concat(
            '[',
            group_concat(
                JSON_OBJECT(
                    'result_article',
                    ifnull (kombiartikel.resultartikel, artikelgruppen.gruppe),
                    'amount_factor',
                    ifnull (kombiartikel.resultfaktor, 1),
                    'price_factor',
                    ifnull (kombiartikel.resultpfaktor, 1),
                    'use_result_price',
                    ifnull (kombiartikel.originalpreis, 1) = 0
                ) separator ','
            ),
            ']'
        )
    ) article_combinations
from
    artikelgruppen
    join staffeln on artikelgruppen.gruppe = staffeln.gruppe
    join bfkonten_zuordnung on bfkonten_zuordnung.gruppe = artikelgruppen.gruppe
    join warengruppen on warengruppen.warengruppe = artikelgruppen.warengruppe
    join bfkonten on bfkonten.id = bfkonten_zuordnung.konto_id
    and bfkonten.gueltig <= current_date()
    left join kombiartikel on kombiartikel.triggerartikel = artikelgruppen.gruppe
group by
    artikelgruppen.gruppe;

/*
CREATE TABLE IF NOT EXISTS
    `blg_abschluss` (
        `id` bigint (20) NOT NULL,
        `terminalid` varchar(36) NOT NULL,
        `datum_zeit` datetime DEFAULT NULL,
        `login` varchar(100) NOT NULL,
        `start_saldo` decimal(15, 6) DEFAULT 0.000000,
        `stopp_saldo` decimal(15, 6) DEFAULT 0.000000,
        `start_bar_saldo` decimal(15, 6) DEFAULT 0.000000,
        `stopp_bar_saldo` decimal(15, 6) DEFAULT 0.000000,
        PRIMARY KEY (`id`),
        KEY `idx_kblg_abschlussterminalid` (`terminalid`),
        CONSTRAINT `fk_blg_abschluss_terminalid` FOREIGN KEY (`terminalid`) REFERENCES `kassenterminals` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
    );

CREATE TABLE IF NOT EXISTS
`blg_abschluss_br` (
    `belegnummer` bigint (20) NOT NULL,
    `abschluss_id` bigint (20) NOT NULL,
    PRIMARY KEY (`belegnummer`, `abschluss_id`),
    KEY `idx_blg_abschluss_br_belegnummer` (`belegnummer`),
    KEY `idx_blg_abschluss_br_abschluss_id` (`abschluss_id`),
    CONSTRAINT `fk_blg_abschluss_br_abschluss_id` FOREIGN KEY (`abschluss_id`) REFERENCES `blg_abschluss` (`id`) ON UPDATE CASCADE,
    CONSTRAINT `fk_blg_abschluss_br_belegnummer` FOREIGN KEY (`belegnummer`) REFERENCES `blg_hdr_br` (`id`) ON UPDATE CASCADE
);

CREATE OR REPLACE VIEW `view_zbeleg` AS
select
    `lager`.id,
    `lager`.`name` AS `name`,
    '1. Anfangsbestand' AS `typ`,
    `blg_pay_br`.`art` AS `Zahlart`,
    sum(`blg_pay_br`.`betrag`) AS `Wert`
from
    (
        (
            `blg_hdr_br`
            join blg_pay_br on (`blg_pay_br`.`belegnummer` = `blg_hdr_br`.`id`)
            join blg_abschluss_br on blg_abschluss_br.belegnummer = `blg_hdr_br`.`id`
        )
        join `lager` on (`blg_hdr_br`.`von_lager` = `lager`.`id`)
    )
group by
    `lager`.id,
    `lager`.`name`,
    `blg_pay_br`.`art`
union all
select
    `lager`.id,
    `lager`.`name` AS `name`,
    '3. Kassensaldo' AS `typ`,
    `blg_pay_br`.`art` AS `Zahlart`,
    sum(`blg_pay_br`.`betrag`) AS `Wert`
from
    (
        (
            `blg_hdr_br`
            join blg_pay_br on (`blg_pay_br`.`belegnummer` = `blg_hdr_br`.`id`)
            left join blg_abschluss_br on blg_abschluss_br.belegnummer = `blg_hdr_br`.`id`
        )
        join `lager` on (`blg_hdr_br`.`von_lager` = `lager`.`id`)
    )
where
    `blg_hdr_br`.`datum` <= curdate ()
group by
    `lager`.id,
    `lager`.`name`,
    `blg_pay_br`.`art`
union all
select
    `lager`.id,
    `lager`.`name` AS `name`,
    '2. Veränderung' AS `typ`,
    `blg_pay_br`.`art` AS `Zahlart`,
    sum(`blg_pay_br`.`betrag`) AS `Wert`
from
    (
        (
            `blg_hdr_br`
            join blg_pay_br on (`blg_pay_br`.`belegnummer` = `blg_hdr_br`.`id`)
            left join blg_abschluss_br on blg_abschluss_br.belegnummer = `blg_hdr_br`.`id`
        )
        join `lager` on (`blg_hdr_br`.`von_lager` = `lager`.`id`)
    )
where
    `blg_abschluss_br`.`abschluss_id` is null
group by
    `lager`.id,
    `lager`.`name`,
    `blg_pay_br`.`art`;
    */
