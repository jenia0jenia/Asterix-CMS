<?php

/************************************************************/
/*                              */
/*  Ядро системы управления Asterix  CMS           */
/*    Интерфейс работы с СУБД MySQL           */
/*                              */
/*  Версия ядра 2.0.b5                    */
/*  Версия скрипта 1.00                    */
/*                              */
/*  Copyright (c) 2009  Мишин Олег             */
/*  Разработчик: Мишин Олег                 */
/*  Email: dekmabot@gmail.com               */
/*  WWW: http://mishinoleg.ru               */
/*  Создан: 10 февраля 2009  года              */
/*  Модифицирован: 25 сентября 2009 года         */
/*                              */
/************************************************************/

class mysql
{
    public $title = 'ACMS Класс работы с базой данных MySQL';
    public $version = '1.0';

    public $type = 'mysql';
    public $active;

    public $query_counter = 0;
    public $query_log = array();
    public $name;

    public function __construct($model)
    {
        $this->model = $model;
        $this->connection = false;
        if (property_exists('model', 'active_database')) {
            model::$active_database = false;
        }
    }

    public function activate()
    {
        mysql_select_db($this->name, $this->connection);
        if (property_exists('model', 'active_database')) {
            model::$active_database = $this->name;
        }
    }

    public function Connect($host, $user, $password, $name)
    {
        $this->name = $name;
        $this->connection = @mysql_connect($host, $user, $password) or $this->error('connection');
        $this->activate();
    }

    public function PConnect($host, $user, $password, $name)
    {
        $this->name = $name;
        $this->connection = @mysql_pconnect($host, $user, $password) or $this->error('connection');
        $this->activate();
    }

    public function GetAll($sql)
    {
        //$this->activate();

        $items = array();
        if ($result = mysql_query($sql, $this->connection)) {
            while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
                $items[] = $row;
            }
            mysql_free_result($result);
        } else {
            $this->error($sql);
            return false;
        }

        return $items;
    }

    public function GetRow($sql)
    {
        //$this->activate();

        $row = array();
        if ($result = mysql_query($sql, $this->connection)) {
            $row = mysql_fetch_array($result, MYSQL_ASSOC);
            mysql_free_result($result);
        } else {
            $this->error($sql);
            return false;
        }

        return $row;
    }

    public function Insert($sql)
    {
        //$this->activate();

        $result = mysql_query($sql, $this->connection);
        if (!$result) {
            $this->error($sql);
            return false;
        }

        return mysql_insert_id();
    }

    public function Execute($sql)
    {
        //$this->activate();

        $result = mysql_query($sql, $this->connection);
        if (!$result) {
            $this->error($sql);
            return false;
        }

        return $result;
    }

    public function error($sql)
    {
        if (property_exists('model', 'settings')) {
            if (in_array('sql', (array)model::$settings['errors'])) {
                pr('Обнаружена ошибка в запросе<br />' . $sql . '<br />' . mysql_errno() . ": " . mysql_error());
            }
        }
    }

}

/*
Разработка: Мишин Олег.
Email: mishinoleg@mail.ru
Web: http://www.mishinoleg.ru/
*/

?>