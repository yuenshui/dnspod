<?php
include("dnspod.php");
$login_email = '**********';
$login_password = '**********';

$dns = new dnspod($login_email, $login_password);
echo date("Y-m-d H:i:s") . " localhost IP：" . $dns->localIP, "\r\n";
$dns->ignoreLocalIP = true;
$dns->addDomain("inkever.net", 'office');
$dns->addDomain("inkever.net", 'office2');
$dns->addDomain("inkever.net", 'office3');

$dns->addDomain("yuenshui.com", 'test1');
$dns->addDomain("yuenshui.com", 'test2');


$dns->setIP();
