<?php
    $tag = $_GET['tag'];
    $url = "http://tingapi.ting.baidu.com/v1/restserver/ting?method=baidu.ting.tag.songlist&format=json&tagname=$tag&limit=20&from=mixapp";
    echo file_get_contents($url);
?>