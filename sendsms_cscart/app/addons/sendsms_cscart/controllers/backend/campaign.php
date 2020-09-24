<?php

use Tygh\Registry;
use Tygh\Database;
use Tygh\Database\Connection;
use Tygh\Tools\DateTimeHelper;
use Tygh\Settings;

defined('BOOTSTRAP') or die('Access denied');

ini_set('auto_detect_line_endings', true);


if ($_SERVER['REQUEST_METHOD'] == 'POST') 
{
    if ($mode == 'manage') {
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

        if ($_REQUEST['time'] == "all" || $_REQUEST['time'] == "3-mths" || $_REQUEST['time'] == "6-mths") 
        {

            //timestamp calc
            $time = time() + DateTimeHelper::getTimeZoneOffset(Settings::instance()->getValue('timezone', 'Appearance'));
            if($_REQUEST['time'] == "3-mths")
            {
                $time = strtotime(date('Y-m-d', $time) . ' -3 months');
            }else if($_REQUEST['time'] == "6-mths")
            {
                $time = strtotime(date('Y-m-d', $time) . ' -6 months');
            }else if($_REQUEST['time'] == "all")
            {
                $time = 0;
            }

            //getting the order made after the timestamp
            $orders = fn_get_orders(array())[0];
            $phones = [];
            $p_ids = explode(",", $_REQUEST['p_ids']);
            foreach($orders as $order)
            {
                if(intval(fn_get_order_info($order['order_id'])['timestamp']) > $time)
                {
                    $ok = false;
                    foreach(fn_get_order_info($order['order_id'])['products'] as $product)
                    {
                        if(in_array(($product['product_id']), $p_ids))
                        {   
                            $ok = true;
                            break;
                        }
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
            fn_print_r($phones);
        }
        
        if(isset($_REQUEST['check']))
        {
            if (defined('AJAX_REQUEST')) {
                fn_set_notification('I', "send SMS", $messages_to_send, 'K');
            }
            
        }else
        {
        }
    }
    return array(CONTROLLER_STATUS_OK, "campaign.manage");
}
if($mode == 'manage')
{
    // $products = db_query('  SELECT cscart_product_descriptions.product 
    //                         FROM cscart_products, cscart_product_descriptions
    //                         WHERE cscart_product_descriptions.product_id = cscart_products.product_id')->fetch_all();
    //fn_print_r($products);
}