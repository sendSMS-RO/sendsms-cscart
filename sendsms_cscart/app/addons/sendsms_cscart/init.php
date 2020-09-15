<?php
if ( !defined('AREA') ) { die('Access denied'); }

fn_register_hooks(
	'change_order_status_post',
	'change_order_status_child_order_post'
);

?>