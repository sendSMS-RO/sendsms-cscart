<?php
function fn_sendsms_cscart_change_order_status_post($status_to, $status_from, $order_info, $force_notification, $order_statuses, $place_order)
{
    if (file_exists('order_info.txt'))
        unlink('order_info.txt');
        
    file_put_contents('order_info.txt', 'Status to: ' . var_export($status_to, true) . "\n", FILE_APPEND);
    file_put_contents('order_info.txt', 'Status from: ' . var_export($status_from, true) . "\n", FILE_APPEND);
    file_put_contents('order_info.txt', 'Order info: ' . var_export($order_info, true) . "\n", FILE_APPEND);
    file_put_contents('order_info.txt', 'Force notification: ' . var_export($force_notification, true) . "\n", FILE_APPEND);
    file_put_contents('order_info.txt', 'Order statuses: ' . var_export($order_statuses, true) . "\n", FILE_APPEND);
    file_put_contents('order_info.txt', 'Place order: ' . var_export($place_order, true) . "\n", FILE_APPEND);
}