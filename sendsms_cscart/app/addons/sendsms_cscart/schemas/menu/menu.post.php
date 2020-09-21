<?php
defined('BOOTSTRAP') or die('Access denied');
$schema['top']['administration']['items']['send_sms'] = array(
    'title' => 'send SMS',
    'attrs' => array(
        'class'=>'is-addon'
    ),
    'href' => 'addons.update&addon=sendsms_cscart',
    'position' => 0,
);

$schema['top']['administration']['items']['send_sms']['subitems']['advanced_import.advanced_products_import'] = array(
    'title' => 'SMS campaign',
    'attrs' => array(
        'class'=>'is-addon'
    ),
    'href' => 'campaign.manage',
    'position' => 0,
);

return $schema;