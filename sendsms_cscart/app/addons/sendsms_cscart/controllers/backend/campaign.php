<?php
defined('BOOTSTRAP') or die('Access denied');

ini_set('auto_detect_line_endings', true);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if ($mode == 'add') {
        // here comes the code which should be executed when submitting product addition form
    }
    return array(CONTROLLER_STATUS_OK, "campaign.manage");
}