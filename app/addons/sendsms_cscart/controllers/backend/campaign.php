<?php

use Tygh\Registry;
use Tygh\Tools\DateTimeHelper;
use Tygh\Settings;
require_once (Registry::get('config.dir.addons') ."sendsms_cscart/API/sendsms.php");

defined('BOOTSTRAP') or die('Access denied');

ini_set('auto_detect_line_endings', true);

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
                    if($_SESSION['auth']['user_type'] === 'A')
                        fn_set_notification("W", "send SMS", "Reverse the order of fields \"From\" and \"To\".", 'K');
                    return array(CONTROLLER_STATUS_OK, "campaign.manage");
                } 
            }else
            {
                if($_SESSION['auth']['user_type'] === 'A')
                    fn_set_notification("W", "send SMS", "The fields \"From\" and \"To\" are invalid or empty.", 'K');
                return array(CONTROLLER_STATUS_OK, "campaign.manage");
            }
        }

        $orders = fn_get_orders(array())[0];
        $p_ids = preg_split('@,@', $_REQUEST['p_ids'], NULL, PREG_SPLIT_NO_EMPTY);

        foreach($orders as $order)
        {
            $order_info = fn_get_order_info($order['order_id']);

            if($order_info['subtotal'] >= $_REQUEST['price'] || empty($_REQUEST['price']))
            {
                if(in_array($order_info['b_country_descr'], $_REQUEST['countries']) || empty($_REQUEST['countries']))
                {
                    if( intval($order_info['timestamp']) > $time_1 && $_REQUEST["time"] != "period" ||
                        intval($order_info['timestamp']) > $time_1 && intval($order_info['timestamp']) < $time_2 && $_REQUEST["time"] == "period")
                    {
                        $ok = false;
                        if(!empty($p_ids))
                        {
                            foreach($order_info['products'] as $product)
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
            }
        }        
        if(isset($_REQUEST['check']))
        {
            if (defined('AJAX_REQUEST')) {
                if($_SESSION['auth']['user_type'] === 'A')
                    fn_set_notification('N', "send SMS", "A total of " . count($phones) . " phone numbers were found, resulting in approximately " . count($phones) * $messages_to_send . " messages will be send.", 'K');
            }
            
        }else
        {
            $message = $_REQUEST['message'];
            $label = Registry::get('addons.sendsms_cscart.message-expeditor');
            $api = new SendsmsApi();
            if(Registry::get('addons.sendsms_cscart.login-name') !== "")
                $api -> setUsername(Registry::get('addons.sendsms_cscart.login-name'));
                else
                {
                    $error_data = array
                    (
                        'status' => 'fail',
                        'type' => 'failed log in attempt - campaign',
                        'send_to' => $phone,
                        'date' => date('Y-m-d H:i:s', time()),
                        'message' => $message,
                        'info' => 'Your log in name is empty',
                    );
                    if($_SESSION['auth']['user_type'] === 'A')
                        fn_set_notification('W', "send SMS", "The message was not sent, your log in name is empty", 'K'); 
                    db_query('INSERT INTO ?:sendsms_errors ?e', $error_data);
                    return array(CONTROLLER_STATUS_OK, "campaign.manage");;
                }
            if(Registry::get('addons.sendsms_cscart.login-pass') !== "")
                $api -> setPassword(Registry::get('addons.sendsms_cscart.login-pass'));
                else
                {
                    $error_data = array
                    (
                        'status' => 'fail',
                        'type' => 'failed log in attempt - campaign',
                        'send_to' => $phone,
                        'date' => date('Y-m-d H:i:s', time()),
                        'message' => $message,
                        'info' => 'Your log in password is empty',
                    );
                    if($_SESSION['auth']['user_type'] === 'A')
                        fn_set_notification('W', "send SMS", "The message was not sent, your password is empty", 'K');  
                    db_query('INSERT INTO ?:sendsms_errors ?e', $error_data);
                    return array(CONTROLLER_STATUS_OK, "campaign.manage");;
                }
            
            foreach($phones as $phone)
            {
                $result = $api -> message_send_gdpr($phone,$message, $label ? $label : "0", 19, null, null, null, -1, null, true);

                if($api->ok($result)) 
                {
                    $error_data = array
                    (
                        'status' => 'success',
                        'type' => 'send message - campaign',
                        'send_to' => $phone,
                        'date' => date('Y-m-d H:i:s', time()),
                        'message' => $message,
                        'info' => "Message sent! ID was {$result['details']}\n",
                    );
                    if($_SESSION['auth']['user_type'] === 'A')
                        fn_set_notification('N', "send SMS", "Message sent!", 'K'); 
                    db_query('INSERT INTO ?:sendsms_errors ?e', $error_data);
                } else 
                {
                    $error_data = array
                    (
                        'status' => 'fail',
                        'type' => 'attempt to send failed - campaign',
                        'send_to' => $phone,
                        'date' => date('Y-m-d H:i:s', time()),
                        'message' => $message,
                        'info' => $api->getError() ,
                    );
                    if($_SESSION['auth']['user_type'] === 'A')
                        fn_set_notification('W', "send SMS", $api->getError(), 'K');  
                    db_query('INSERT INTO ?:sendsms_errors ?e', $error_data);
                }
            }
        }
    }
    return array(CONTROLLER_STATUS_OK, "campaign.manage");
}
if($mode == 'manage')
{
    $countries = fn_populate_cointries();
    Tygh::$app['view']->assign('countries', $countries);
}