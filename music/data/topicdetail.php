<?php
    $code = $_GET['code'];
    $url = "http://tingapi.ting.baidu.com/v1/restserver/ting?method=baidu.ting.diy.getSongFromOfficalList&format=json&code=$code&ver=2&from=mixapp";
    echo file_get_contents($url);
?>