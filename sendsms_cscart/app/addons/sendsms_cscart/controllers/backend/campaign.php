<?php

use Tygh\Registry;
use Tygh\Database;
use Tygh\Database\Connection;

defined('BOOTSTRAP') or die('Access denied');

ini_set('auto_detect_line_endings', true);


if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if ($mode == 'manage') {
        
    }
    return array(CONTROLLER_STATUS_OK, "campaign.manage");
}
// if($mode == 'manage')
// {
//     // Registry::get('view')->assign('end_date', $end_date);
//     $products = Connection::getArray('SELECT * FROM cscart_products');
//     fn_print_r($products);
// }
