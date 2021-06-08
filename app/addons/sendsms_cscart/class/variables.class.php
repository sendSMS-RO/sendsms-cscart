<?php

namespace Sendsms;

use Exception;

class SendsmsVariables
{
    public static function updateValue($key, $assigned)
    {
        try {
            db_query('INSERT INTO ?:sendsms_variables ?e', array(
                'key' => $key,
                'assigned' => $assigned
            ));
        } catch (Exception $e) { //dublicate or database not found
            try {
                db_query('UPDATE ?:sendsms_variables SET ?u WHERE `key`=?s', array('assigned' => $assigned), $key);
            } catch (Exception $e) {
                error_log($e->getMessage());
            }
        }
    }

    public static function getValue($key, $default = false)
    {
        try {
            $result = db_get_row('SELECT assigned FROM ?:sendsms_variables WHERE `key`=?s', $key);
            return $result['assigned'];
        } catch (Exception $e) {
            fn_print_r($e->getMessage());
            return $default;
        }
    }
}
