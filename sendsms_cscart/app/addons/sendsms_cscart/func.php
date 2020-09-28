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
        if(Registry::get('addons.sendsms_cscart.login-name') !== "")
            $api -> setUsername(Registry::get('addons.sendsms_cscart.login-name'));
            else
            {
                //error handling 
                return;
            }
        $api->debug("Error here");
        if(Registry::get('addons.sendsms_cscart.login-pass') !== "")
            $api -> setPassword(Registry::get('addons.sendsms_cscart.login-pass'));
            else
            {
                //error handling 
                return;
            }
        
        if(Registry::get('addons.sendsms_cscart.' . $statuses[$status_to] . '-validation') == "Y")
        {
            $message = Registry::get('addons.sendsms_cscart.' . $statuses[$status_to] . '-message');
            $phone = $order_statuses["phone"];
            $label = Registry::get('addons.sendsms_cscart.message-expeditor');
            if($message !== "")
            {
                if($phone !== "")
                {
                    foreach($wordsToReplace as $key => $value)
                    {
                        $message = str_replace($key, $value, $message);
                    }
                    $phone = preg_replace("/[^0-9]/", "", $phone);

                    //this is how the msg will be send    
                    $result = $api -> message_send_gdpr($phone,$message, $label, 19, null, null, null, -1, null, true);

                    if($api->ok($result)) {
                        fn_print_r("Message sent! ID was {$result['details']}\n");    
                    } else {
                        /* There was an error */
                        fn_print_r($api->getError());
                    }
                }else
                {
                    fn_print_r("No phone number.");
                }
            }else
            {
                fn_print_r("Empty message.");
            }
        }
    }
    // if (file_exists('order_info.txt'))
    //     unlink('order_info.txt');

    // file_put_contents('order_info.txt', "Status to: " . var_export($status_to, true) . "\n", FILE_APPEND);
    // file_put_contents('order_info.txt', "Status from: " . var_export($status_from, true) . "\n", FILE_APPEND);
    // file_put_contents('order_info.txt', var_export($order_info, true) . "\n", FILE_APPEND);
}