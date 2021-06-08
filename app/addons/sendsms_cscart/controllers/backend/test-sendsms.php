<?php

use Tygh\Registry;
require_once (Registry::get('config.dir.addons') ."sendsms_cscart/API/sendsms.php");

defined('BOOTSTRAP') or die('Access denied');

ini_set('auto_detect_line_endings', true);

if ($_SERVER['REQUEST_METHOD'] == 'POST') 
{
    if ($mode == 'send') 
    {
        $message = $_POST['message'];
        $phone = $_POST['phone'];
        $phone = fn_validate_phone_sendsms_cscart($phone);
        $short = filter_var(isset($_POST['short_sendsms_1']) ? $_POST['short_sendsms_1'] : "false", FILTER_VALIDATE_BOOLEAN);
        $gdpr = filter_var(isset($_POST['gdpr_sendsms_1']) ? $_POST['gdpr_sendsms_1'] : "false", FILTER_VALIDATE_BOOLEAN);
        if($phone != "")
        {
            $label = Registry::get('addons.sendsms_cscart.message-expeditor');
            $api = new SendsmsApi();
            if(Registry::get('addons.sendsms_cscart.login-name') !== "")
                $api -> setUsername(Registry::get('addons.sendsms_cscart.login-name'));
                else
                {
                    $error_data = array
                    (
                        'status' => 'fail',
                        'type' => 'failed log in attempt - in test message',
                        'send_to' => $phone,
                        'date' => date('Y-m-d H:i:s', time()),
                        'message' => $message,
                        'info' => 'Your log in name is empty',
                    );
                    if($_SESSION['auth']['user_type'] === 'A')
                        fn_set_notification('W', "send SMS", "The message was not sent, your log in name is empty", 'K'); 
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
                        'type' => 'failed log in attempt - in test message',
                        'send_to' => $phone,
                        'date' => date('Y-m-d H:i:s', time()),
                        'message' => $message,
                        'info' => 'Your log in password is empty',
                    );
                    if($_SESSION['auth']['user_type'] === 'A')
                        fn_set_notification('W', "send SMS", "The message was not sent, your password is empty", 'K');  
                    db_query('INSERT INTO ?:sendsms_errors ?e', $error_data);
                    return;
                }
            
                if($gdpr)
                    $result = $api -> message_send_gdpr($phone,$message, $label ? $label : "0", 19, null, null, null, -1, null, $short);
                else
                    $result = $api -> message_send($phone,$message, $label ? $label : "0", 19, null, null, null, -1, null, $short);
            if($api->ok($result)) 
            {
                $error_data = array
                (
                    'status' => 'success',
                    'type' => 'send message - in test message',
                    'send_to' => $phone,
                    'date' => date('Y-m-d H:i:s', time()),
                    'message' => $message,
                    'info' => "Message sent! ID was {$result['details']}\n",
                );
                if($_SESSION['auth']['user_type'] === 'A')
                    fn_set_notification('N', "send SMS", "Message sent!", 'K'); 
                db_query('INSERT INTO ?:sendsms_errors ?e', $error_data);
                return;
            } else 
            {
                $error_data = array
                (
                    'status' => 'fail',
                    'type' => 'attempt to send failed - in test message',
                    'send_to' => $phone,
                    'date' => date('Y-m-d H:i:s', time()),
                    'message' => $message,
                    'info' => $api->getError() ,
                );
                if($_SESSION['auth']['user_type'] === 'A')
                    fn_set_notification('W', "send SMS", $api->getError(), 'K');  
                db_query('INSERT INTO ?:sendsms_errors ?e', $error_data);
            }             
        }else
        {
            if($_SESSION['auth']['user_type'] === 'A')
                fn_set_notification("W", "send SMS", "The message box is empty.", 'K');
            return;
        }
    }
}