<?php

use Tygh;

defined('BOOTSTRAP') or die('Access denied');

ini_set('auto_detect_line_endings', true);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if ($mode == 'view') {
        list($errors, $search) = fn_populate_errors();
        Tygh::$app['view']->assign('errors', $errors);
        Tygh::$app['view']->assign('search', $search);
    }

    return array(CONTROLLER_STATUS_OK, "logs.view");
}
if($mode == "view")
{
    list($errors, $search) = fn_populate_errors();
    Tygh::$app['view']->assign('errors', $errors);
    Tygh::$app['view']->assign('search', $search);
}

