<?php
use Tygh\Registry;
use Tygh\Settings;
use Tygh\Tools\DateTimeHelper;
require_once (Registry::get('config.dir.addons') ."sendsms_cscart/API/sendsms.php");

if (!defined('BOOTSTRAP')) { die('Access denied'); }

function fn_sendsms_cscart_change_order_status_post($order_id, $status_to, $status_from, $order_info, $force_notification, $order_statuses, $place_order)
{
    if(($status_to != $status_from && $status_from != 'N') || ($status_from == 'N' && empty($order_info)))
    {
        $phone = $order_statuses["phone"];
        $phone = preg_replace("/[^0-9]/", "", $phone);

        $statuses = array
        (
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
        $wordsToReplace = array
        (
            "{order_id}" => $order_id,
            "{total}" => $order_statuses['total']. '$',
            "{date}" => "",
            "{firstname}" => $order_statuses['firstname']
        );

        $timeZone = floor(DateTimeHelper::getTimeZoneOffset(Settings::instance()->getValue('timezone', 'Appearance')) / 3600);
        if ($timeZone == 0)
        {
            $wordsToReplace["{date}"] = date('Y-m-d H:i:s', $order_statuses['timestamp']). " (UTC)";
        }else 
        {
            if($timeZone > 0)
                $timeZone = "+" . $timeZone; 
            $wordsToReplace["{date}"] = date('Y-m-d H:i:s', $order_statuses['timestamp']). " (" . $timeZone . " UTC)";
        }

        $api = new SendsmsApi();
        
        if(Registry::get('addons.sendsms_cscart.' . $statuses[$status_to] . '-validation') == "Y")
        {
            $message = Registry::get('addons.sendsms_cscart.' . $statuses[$status_to] . '-message');
            $label = Registry::get('addons.sendsms_cscart.message-expeditor');
            if($message !== "")
            {
                if($phone !== "")
                {
                    foreach($wordsToReplace as $key => $value)
                    {
                        $message = str_replace($key, $value, $message);
                    }
                    
                    if(Registry::get('addons.sendsms_cscart.login-name') !== "")
                        $api -> setUsername(Registry::get('addons.sendsms_cscart.login-name'));
                        else
                        {   
                            $error_data = array
                            (
                                'status' => 'fail',
                                'type' => 'failed log in attempt - order status',
                                'send_to' => $phone,
                                'date' => date('Y-m-d H:i:s', time()),
                                'message' => $message,
                                'info' => 'Your log in name is empty',
                            );
                            fn_set_notification('W', "send SMS", "The message was not send, your log in name is empty", 'K'); 
                            db_query('INSERT INTO ?:sendsms_errors ?e', $error_data);
                            return;
                        }
                    if(Registry::get('addons.sendsms_cscart.login-pass') !== "")
                        $api -> setPassword(Registry::get('addons.sendsms_cscart.login-pass'));
                        else
                        {
                            $error_data = array
                            (
                                'status' => 'fail',
                                'type' => 'failed log in attempt - order status',
                                'send_to' => $phone,
                                'date' => date('Y-m-d H:i:s', time()),
                                'message' => $message,
                                'info' => 'Your log in password is empty',
                            );
                            fn_set_notification('W', "send SMS", "The message was not send, your password is empty", 'K');  
                            db_query('INSERT INTO ?:sendsms_errors ?e', $error_data);
                            return;
                        }

                    //this is how the msg will be send    
                    $result = $api -> message_send_gdpr($phone,$message, $label ? $label : "0", 19, null, null, null, -1, null, true);

                    if($api->ok($result)) {
                        $error_data = array
                        (
                            'status' => 'success',
                            'type' => 'send message - order status',
                            'send_to' => $phone,
                            'date' => date('Y-m-d H:i:s', time()),
                            'message' => $message,
                            'info' => "Message sent!",
                        );
                        fn_set_notification('N', "send SMS", "Message sent!", 'K'); 
                        db_query('INSERT INTO ?:sendsms_errors ?e', $error_data);
                    } else {
                        $error_data = array
                        (
                            'status' => 'fail',
                            'type' => 'attempt to send failed - order status',
                            'send_to' => $phone,
                            'date' => date('Y-m-d H:i:s', time()),
                            'message' => $message,
                            'info' => $api->getError() ,
                        );
                        fn_set_notification('W', "send SMS", $api->getError(), 'K');  
                        db_query('INSERT INTO ?:sendsms_errors ?e', $error_data);
                    }
                }
            }else
            {
                $error_data = array
                (
                    'status' => 'fail',
                    'type' => 'attempt to send failed - order status',
                    'send_to' => $phone,
                    'date' => date('Y-m-d H:i:s', time()),
                    'info' => 'The message box (' . $statuses[$status_to] . ') is empty' ,
                );
                fn_set_notification('W', "send SMS", "The message was not send, the message box in empty", 'K');  
                db_query('INSERT INTO ?:sendsms_errors ?e', $error_data);
            }
        }
    }
}

function fn_populate_errors($conditions = null)
{
    $search = Array
    (
        'page' => $_REQUEST['page'] ? $_REQUEST['page'] : 1,
        'items_per_page' => $_REQUEST['items_per_page'] ? $_REQUEST['items_per_page'] : Registry::get('settings.Appearance.admin_elements_per_page'),
        'total_items' => db_query('SELECT COUNT(*) from ?:sendsms_errors') -> fetch_all()[0][0],
    );
    $startIndex = ($search['page']  - 1) * $search['items_per_page'];
    $errors = db_query
    (
        '   SELECT * 
            FROM ?:sendsms_errors
            ORDER BY id ASC 
            LIMIT ?i, ?i', 
        $startIndex, 
        $search['items_per_page']
    ) -> fetch_all();

    return array($errors, $search);
}