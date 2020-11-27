<?php

defined('BOOTSTRAP') or die('Access denied');

ini_set('auto_detect_line_endings', true);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $phone = isset($_POST['phone']) ? $_POST['phone'] : " ";
    $date = isset($_POST['date']) ? $_POST['date'] : " ";
    //fn_print_r($_SESSION);
    if ($mode == 'view') {
        return array(CONTROLLER_STATUS_OK, "logs-sendsms.view&phone=$phone&date=$date");

    }

}
if($mode == "view")
{
    list($errors, $search) = fn_populate_errors();
    Tygh::$app['view']->assign('errors', $errors);
    Tygh::$app['view']->assign('search', $search);
}

