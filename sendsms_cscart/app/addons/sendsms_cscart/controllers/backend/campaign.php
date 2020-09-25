<?php

use Tygh\Registry;
use Tygh\Database;
use Tygh\Database\Connection;
use Tygh\Tools\DateTimeHelper;
use Tygh\Settings;
require_once (Registry::get('config.dir.addons') ."sendsms_cscart/API/sendsms.php");

defined('BOOTSTRAP') or die('Access denied');

ini_set('auto_detect_line_endings', true);

require_once (Registry::get('config.dir.addons') . 'ebay/config.php');


if ($_SERVER['REQUEST_METHOD'] == 'POST') 
{
    if ($mode == 'manage') {
        $phones = [];
        $messages_to_send = 0;
        $message = $_REQUEST['message'];
        $lenght = strlen($message);
        $messages = $lenght / 160 + 1;
        if($lenght > 0)
        {
            if($lenght % 160 == 0)
            {
                $messages--;
            }
            $messages_to_send = floor($messages);
        }

        //timestamp calc
        $time_1 = time() + DateTimeHelper::getTimeZoneOffset(Settings::instance()->getValue('timezone', 'Appearance'));
        $time_2 = 0;
        if($_REQUEST['time'] == "3-mths")
        {
            $time_1 = strtotime(date('Y-m-d', $time_1) . ' -3 months');
        }else if($_REQUEST['time'] == "6-mths")
        {
            $time_1 = strtotime(date('Y-m-d', $time_1) . ' -6 months');
        }else if($_REQUEST['time'] == "all")
        {
            $time_1 = 0;
        }else if($_REQUEST["time"] == "period")
        {   
            $time_1 = strtotime($_REQUEST["from"]);
            $time_2 = strtotime($_REQUEST["to"] . ' +1 day');
            if($time_1 && $time_2)
            {
                $time_1 += DateTimeHelper::getTimeZoneOffset(Settings::instance()->getValue('timezone', 'Appearance'));
                $time_2 += DateTimeHelper::getTimeZoneOffset(Settings::instance()->getValue('timezone', 'Appearance'));
                if($time_1 > $time_2)
                {
                    fn_set_notification("W", "send SMS", "Reverse the order of fields \"From\" and \"To\".", 'K');
                    return array(CONTROLLER_STATUS_OK, "campaign.manage");
                } 
            }else
            {
                fn_set_notification("W", "send SMS", "The fields \"From\" and \"To\" are invalid or empty.", 'K');
                return array(CONTROLLER_STATUS_OK, "campaign.manage");
            }
        }

        //getting the order made after the timestamp
        $orders = fn_get_orders(array())[0];
        $p_ids = preg_split('@,@', $_REQUEST['p_ids'], NULL, PREG_SPLIT_NO_EMPTY);

        foreach($orders as $order)
        {
            if( intval(fn_get_order_info($order['order_id'])['timestamp']) > $time_1 && $_REQUEST["time"] != "period" ||
                intval(fn_get_order_info($order['order_id'])['timestamp']) > $time_1 && intval(fn_get_order_info($order['order_id'])['timestamp']) < $time_2 && $_REQUEST["time"] == "period")
            {
                $ok = false;
                if(!empty($p_ids))
                {
                    foreach(fn_get_order_info($order['order_id'])['products'] as $product)
                    {
                        if(in_array(($product['product_id']), $p_ids))
                        {   
                            $ok = true;
                            break;
                        }
                    }
                }else
                {
                    $ok = true;
                }
                if($ok)
                {
                    $phone = intval(preg_replace("/[^0-9]/", "", $order['phone']));
                    if($phone != "")
                    {
                        if(!in_array($phone, $phones, true))
                        {
                            array_push($phones, $phone);
                        }
                    }
                }
            }
        }        
        if(isset($_REQUEST['check']))
        {
            if (defined('AJAX_REQUEST')) {
                fn_set_notification('I', "send SMS", "A total of " . count($phones) . " phone numbers were found, resulting in approximately" . count($phones) * $messages_to_send . " messages to be send.", 'K');
            }
            
        }else
        {
            $label = Registry::get('addons.sendsms_cscart.message-expeditor');
            $api = new SendsmsApi();
            if(Registry::get('addons.sendsms_cscart.login-name') !== "")
                $api -> setUsername(Registry::get('addons.sendsms_cscart.login-name'));
                else
                {
                    //error handling 
                    return array(CONTROLLER_STATUS_OK, "campaign.manage");;
                }
            $api->debug("Error here");
            if(Registry::get('addons.sendsms_cscart.login-pass') !== "")
                $api -> setPassword(Registry::get('addons.sendsms_cscart.login-pass'));
                else
                {
                    //error handling 
                    return array(CONTROLLER_STATUS_OK, "campaign.manage");;
                }
            $message = $_REQUEST['message'];
            
            foreach($phones as $phone)
            {
                $result = $api -> message_send_gdpr($phone,$message, $label, 19, null, null, null, -1, null, true);

                if($api->ok($result)) {
                    fn_print_r("Message sent! ID was {$result['details']}\n");    
                } else {
                    /* There was an error */
                    fn_print_r($api->getError());
                }
            }
        }
    }
    return array(CONTROLLER_STATUS_OK, "campaign.manage");
}
if($mode == 'manage')
{

}