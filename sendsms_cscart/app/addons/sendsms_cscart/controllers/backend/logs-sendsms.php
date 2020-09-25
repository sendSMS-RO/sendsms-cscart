<?php

use Tygh;

defined('BOOTSTRAP') or die('Access denied');

ini_set('auto_detect_line_endings', true);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if ($mode == 'view') {
        //rnd code
    }

    return array(CONTROLLER_STATUS_OK, "logs.view");
}
if($mode == "view")
{
    Tygh::$app['view']->assign('errors', false);
}

