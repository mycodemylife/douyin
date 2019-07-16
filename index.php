<?php

require './lib/DouyinService.php';
header('content-type:application/json');

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
    echo json_encode($data);

}catch(Throwable $e){
    echo '{"errcdoe":-1,"errmsg":'.$e->getMessage().'}';
}