<?php 
define('ROOTPATH', str_replace("/tasks", "", __DIR__));

$tempPath = ROOTPATH;
$content = date('H:i:s');
$fp = fopen("$tempPath/myText.txt","wb");
fwrite($fp,$content);
fclose($fp);

//  /usr/bin/php CronTest.Task.php * * * * * /var/www/html/desarrollo2/alquiler/flexio/vendor/bin/crunz schedule:run /var/www/html/desarrollo2/alquiler/flexio/tasks