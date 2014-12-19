<?php
require_once('mysql.conf.php');

$conf['authtype'] = 'authmysql'; 
$conf['passcrypt'] =  'wp-hash'; //Worpdress-auth

$conf['defaultgroup'] = 'Registered' //default group
$conf['superuser'] = '@Webmaster';  //Which Worpdress group is admin
$conf['manager'] = '@Webmaster';
$conf['disableactions'] = 'register,resendpwd';