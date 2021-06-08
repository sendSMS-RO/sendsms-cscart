<?php

include "class/variables.class.php";

use Tygh\Registry;
use Tygh\Settings;
use Tygh\Tools\DateTimeHelper;
use Sendsms\SendsmsVariables;
require_once(Registry::get('config.dir.addons') . "sendsms_cscart/API/sendsms.php");

if (!defined('BOOTSTRAP')) {
    die('Access denied');
}

function fn_sendsms_cscart_change_order_status_post($order_id, $status_to, $status_from, $order_info, $force_notification, $order_statuses, $place_order)
{
    if (($status_to != $status_from && $status_from != 'N') || ($status_from == 'N' && empty($order_info))) {
        $phone = $order_statuses["phone"];
        $phone = fn_validate_phone_sendsms_cscart($phone);

        $statuses = array(
            "P" => "paid",
            "C" => "complete",
            "O" => "open",
            "F" => "failed",
            "D" => "declined",
            "B" => "backordered",
            "I" => "canceled",
            "Y" => "awaiting_call",
            "N" => "incomplete"
        );
        $wordsToReplace = array(
            "{order_id}" => $order_id,
            "{total}" => number_format($order_statuses['total'], (int)db_get_array('SELECT decimals FROM ?:currencies WHERE currency_id=1')[0]['decimals'], ',', '') . " $",
            "{date}" => "",
            "{firstname}" => $order_statuses['firstname']
        );

        $timeZone = floor(DateTimeHelper::getTimeZoneOffset(Settings::instance()->getValue('timezone', 'Appearance')) / 3600);
        if ($timeZone == 0) {
            $wordsToReplace["{date}"] = date('Y-m-d H:i:s', $order_statuses['timestamp']) . " (UTC)";
        } else {
            if ($timeZone > 0)
                $timeZone = "+" . $timeZone;
            $wordsToReplace["{date}"] = date('Y-m-d H:i:s', $order_statuses['timestamp']) . " (" . $timeZone . " UTC)";
        }

        $api = new SendsmsApi();

        if (Registry::get('addons.sendsms_cscart.' . $statuses[$status_to] . '-validation') == "Y") {
            $message = Registry::get('addons.sendsms_cscart.' . $statuses[$status_to] . '-message');
            $label = Registry::get('addons.sendsms_cscart.message-expeditor');
            if ($message !== "") {
                if ($phone !== "") {
                    foreach ($wordsToReplace as $key => $value) {
                        $message = str_replace($key, $value, $message);
                    }

                    if (Registry::get('addons.sendsms_cscart.login-name') !== "")
                        $api->setUsername(Registry::get('addons.sendsms_cscart.login-name'));
                    else {
                        $error_data = array(
                            'status' => 'fail',
                            'type' => 'failed log in attempt - order status',
                            'send_to' => $phone,
                            'date' => date('Y-m-d H:i:s', time()),
                            'message' => $message,
                            'info' => 'Your log in name is empty',
                        );
                        if ($_SESSION['auth']['user_type'] === 'A')
                            fn_set_notification('W', "send SMS", "The message was not sent, your log in name is empty", 'K');
                        db_query('INSERT INTO ?:sendsms_errors ?e', $error_data);
                        return;
                    }
                    if (Registry::get('addons.sendsms_cscart.login-pass') !== "")
                        $api->setPassword(Registry::get('addons.sendsms_cscart.login-pass'));
                    else {
                        $error_data = array(
                            'status' => 'fail',
                            'type' => 'failed log in attempt - order status',
                            'send_to' => $phone,
                            'date' => date('Y-m-d H:i:s', time()),
                            'message' => $message,
                            'info' => 'Your log in password is empty',
                        );
                        if ($_SESSION['auth']['user_type'] === 'A')
                            fn_set_notification('W', "send SMS", "The message was not sent, your password is empty", 'K');
                        db_query('INSERT INTO ?:sendsms_errors ?e', $error_data);
                        return;
                    }

                    //this is how the msg will be send  
                    $gdpr = Registry::get('addons.sendsms_cscart.' . $statuses[$status_to] . '-gdpr') == 'Y' ? true : false;
                    $short = Registry::get('addons.sendsms_cscart.' . $statuses[$status_to] . '-short') == 'Y' ? true : false;

                    if ($gdpr)
                        $result = $api->message_send_gdpr($phone, $message, $label ? $label : "0", 19, null, null, null, -1, null, $short);
                    else
                        $result = $api->message_send($phone, $message, $label ? $label : "0", 19, null, null, null, -1, null, $short);

                    if ($api->ok($result)) {
                        $error_data = array(
                            'status' => 'success',
                            'type' => 'send message - order status',
                            'send_to' => $phone,
                            'date' => date('Y-m-d H:i:s', time()),
                            'message' => $message,
                            'info' => "Message sent! ID was {$result['details']}\n",
                        );
                        if ($_SESSION['auth']['user_type'] === 'A')
                            fn_set_notification('N', "send SMS", "Message sent!", 'K');
                        db_query('INSERT INTO ?:sendsms_errors ?e', $error_data);
                    } else {
                        $error_data = array(
                            'status' => 'fail',
                            'type' => 'attempt to send failed - order status',
                            'send_to' => $phone,
                            'date' => date('Y-m-d H:i:s', time()),
                            'message' => $message,
                            'info' => $api->getError(),
                        );
                        if ($_SESSION['auth']['user_type'] === 'A')
                            fn_set_notification('W', "send SMS", $api->getError(), 'K');
                        db_query('INSERT INTO ?:sendsms_errors ?e', $error_data);
                    }
                }
            } else {
                $error_data = array(
                    'status' => 'fail',
                    'type' => 'attempt to send failed - order status',
                    'send_to' => $phone,
                    'date' => date('Y-m-d H:i:s', time()),
                    'info' => 'The message box (' . $statuses[$status_to] . ') is empty',
                );
                if ($_SESSION['auth']['user_type'] === 'A')
                    fn_set_notification('W', "send SMS", "The message was not sent, the message box in empty", 'K');
                db_query('INSERT INTO ?:sendsms_errors ?e', $error_data);
            }
        }
    }
}

function fn_populate_errors()
{
    $extra_query = "";
    $phone = isset($_GET['phone']) ? $_GET['phone'] : "";
    $date = isset($_GET['date']) ? $_GET['date'] : "";

    $extra_query = ' WHERE send_to LIKE "%' . $phone . '%" AND date LIKE "%' . $date . '%"';

    $search = array(
        'page' => isset($_REQUEST['page']) ? $_REQUEST['page'] : 1,
        'items_per_page' => isset($_REQUEST['items_per_page']) ? $_REQUEST['items_per_page'] : Registry::get('settings.Appearance.admin_elements_per_page'),
        'total_items' => db_query('SELECT COUNT(*) from ?:sendsms_errors'  . $extra_query)->fetch_all()[0][0],
    );
    $startIndex = ($search['page']  - 1) * $search['items_per_page'];
    $errors = db_query(
        '   SELECT * 
            FROM ?:sendsms_errors
            '  . $extra_query . '
            ORDER BY id DESC 
            LIMIT ?i, ?i',
        $startIndex,
        $search['items_per_page']
    )->fetch_all();

    return array($errors, $search);
}

function fn_populate_countries()
{
    $countries = db_get_array(
        'SELECT country FROM ?:countries LEFT JOIN ?:country_descriptions USING(code)'
    );
    return $countries;
}

function fn_validate_phone_sendsms_cscart($phone_number)
{
    if (empty($phone_number)) return '';
    include 'cc.php';
    $phone_number = fn_clear_phone_sendsms_cscart($phone_number);
    //Strip out leading zeros:
    //this will check the country code and apply it if needed
    $cc = Registry::get('addons.sendsms_cscart.login_cc');
    if ($cc === null || empty($cc)) {
        $cc = "INT";
    }
    if ($cc === "INT") {
        return $phone_number;
    }
    $phone_number = ltrim($phone_number, '0');
    $country_code = $country_codes[$cc];

    if (!preg_match('/^' . $country_code . '/', $phone_number)) {
        $phone_number = $country_code . $phone_number;
    }

    return $phone_number;
}

function fn_clear_phone_sendsms_cscart($phone_number)
{
    $phone_number = str_replace(['+', '-'], '', filter_var($phone_number, FILTER_SANITIZE_NUMBER_INT));
    //Strip spaces and non-numeric characters:
    $phone_number = preg_replace("/[^0-9]/", "", $phone_number);
    return $phone_number;
}

function fn_save_price_sendsms_cscart($phone)
{
    $api = new SendsmsApi();
    if (Registry::get('addons.sendsms_cscart.login-name') !== "" && Registry::get('addons.sendsms_cscart.login-pass') !== "") {
        $api->setUsername(Registry::get('addons.sendsms_cscart.login-name'));
        $api->setPassword(Registry::get('addons.sendsms_cscart.login-pass'));
        if (SendsmsVariables::getValue("default-price-time") < date('Y-m-d H:i:s')) {
            $result = $api->route_check_price($phone);
            if ($result['details']['status'] === 64) {
                SendsmsVariables::updateValue("default-price-time", date('Y-m-d H:i:s', strtotime('+1 day')));
                SendsmsVariables::updateValue("default-price", $result['details']['cost']);
                return;
            }
        }
    }
}
