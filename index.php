<?php

require './DouyinService.php';

/**
 * 接口会抛出异常必须捕获
 */
try{
    $douyin = new DouyinService();

    if(!empty($_GET['url'])){
        $url = $_GET['url'];
    }else{
        throw new \Exception('url不能为空');
    }
    $data = $douyin->get($url);
    echo $data;
    
}catch(Throwable $e){
    echo $e->getMessage();
}