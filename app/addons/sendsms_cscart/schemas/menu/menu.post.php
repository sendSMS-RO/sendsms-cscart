<?php
defined('BOOTSTRAP') or die('Access denied');
$schema['top']['administration']['items']['sendsms_cscart.title'] = array(
    'title' => 'send SMS',
    'attrs' => array(
        'class'=>'is-addon'
    ),
    'href' => 'addons.update&addon=sendsms_cscart',
    'position' => 0,
);

$schema['top']['administration']['items']['sendsms_cscart.title']['subitems']['send_sms_campaign'] = array(
    'title' => 'SMS Campaign',
    'attrs' => array(
        'class'=>'is-addon'
    ),
    'href' => 'campaign.manage',
    'position' => 0,
);

$schema['top']['administration']['items']['sendsms_cscart.title']['subitems']['send_sms_logs'] = array(
    'title' => 'SMS Logs',
    'attrs' => array(
        'class'=>'is-addon'
    ),
    'href' => 'logs-sendsms.view',
    'position' => 0,
);

return $schema;