<?php
/**
 * Created by PhpStorm.
 * User: York
 * Date: 24.10.2015
 * Time: 21:43
 */

namespace Core;

use PDO;
use PDOException;

/** ДБ 2015
 * Class DB
 * @package Core
 */
class DB extends SingleTone
{
    protected $connection;

    public static function set()
    {
        $conf = Config::get('db.mysql');
        try {
            $DBH = new PDO("mysql:host=".$conf['host'].";dbname=".$conf['database'], $conf['user'], $conf['password']);
            $DBH->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
            $DBH->exec('SET NAMES utf8');
            static::getInstance()->connection = $DBH;

        }
        catch(PDOException $e) {
            echo $e->getMessage();
        }
    }

    public static function query($q, $params = false)
    {
        $db = static::getInstance()->connection;
        if (!$params)
            $sth = $db->query($q);
        else {
            $sth = $db->prepare($q);
            $sth->execute($params);
        }
        if (!$sth) {
        }

        return $sth;
    }

    public static function make($q, $params = false)
    {
        $db =  static::getInstance()->connection;
        try {
            $sth = $db->prepare($q);
            if ($params) {
                if ($sth->execute($params))
                    return $db->lastInsertId() === 0 ? true : $db->lastInsertId() ;
            } else {
                if ($sth->execute())
                    return $db->lastInsertId();
            }

        } catch (PDOException $e) {
            var_dump($e->getMessage());
        }

        return false;
    }
    public static function insert($table, $arr)
    {
        return self::make("INSERT INTO {$table} " . self::insertFieldsByArr($arr), $arr);
    }

    public static function update($table, $arr)
    {
        $q = "UPDATE {$table} SET " . self::updateFieldsByArr($arr) . " WHERE id = :id";
        return self::make($q, $arr);
    }

    public static function byPk($table, $id)
    {
        return self::getOne("SELECT * FROM {$table} WHERE id = :id", array('id' => $id));
    }

    public static function select($q, $params = false)
    {

        $sth = self::query($q, $params);
        $ret = $sth->fetchAll();

        $new_ret = [];
        foreach ($ret as $item)
            $new_ret[] = self::ClearIntKey($item);

        return $new_ret;

    }

    public static function count($q, $params = false)
    {
        $data = self::getOne($q, $params);

        if (!$data) return false;
        return $data['COUNT(*)'];
    }

    public static function getCount($table, $where = false, $params = false)
    {
        $q = "SELECT COUNT(*) FROM {$table}";
        if ($where)
            $q .= " WHERE {$where}";

        $data = self::getOne($q, $params);

        if (!$data) return false;
        return $data['COUNT(*)'];
    }


    public static function whereFieldsByArr($arr)
    {
        $ret = array();
        foreach ($arr as $key => $val) {
            if ($key == 'id') continue;
            $ret[] = "{$key} = :{$key}";
        }

        return ' ' . implode(' AND ', $ret) . ' ';
    }

    public static function updateFieldsByArr($arr)
    {
        $ret = array();
        foreach ($arr as $key => $val) {
            if ($key == 'id') continue;
            $ret[] = "`{$key}` = :{$key}";
        }

        return ' ' . implode(', ', $ret) . ' ';
    }

    public static function insertFieldsByArr($arr)
    {
        $ret = array();
        foreach ($arr as $key => $val) {
            $ret['insert'][] = "`{$key}`";
            $ret['val'][] = ":{$key}";
        }

        return "(" . implode(',', $ret['insert']) . ") VALUES (" . implode(',', $ret['val']) . ")";
    }

    public static function getOne($q, $params)
    {
        $sth = self::query($q, $params);
        return self::clearIntKey($sth->fetch());

    }

    public static function delete($table, $id)
    {
        self::make("DELETE FROM {$table} WHERE id = :id", array('id' => $id));
    }

    public static function clearIntKey($arr)
    {
        $ret = array();

        if (!$arr) return false;
        foreach ($arr as $key => $val) {
            if (is_numeric($key)) continue;
            $ret[$key] = $val;
        }

        return $ret;
    }

    public static function updateQuery($table, $value)
    {
        $q = "UPDATE {$table} SET ";
        $items = [];
        foreach ($value as $key => $val) {
            if ($key === 'id') continue;
            $items[] = "`{$key}` = '{$val}'";
        }

        $q .= implode(',', $items) . " WHERE `id` = '{$value['id']}'";
        return $q;
    }
}
