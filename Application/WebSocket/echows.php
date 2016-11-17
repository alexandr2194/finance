<?php
namespace Finance\Application\WebSocket;

include dirname(dirname(dirname(__FILE__))) . '/vendor/autoload.php';
$f = new WebSocket();
$f->start();







