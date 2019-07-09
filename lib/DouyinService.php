<?php

require "./lib/Curl.php";

class DouyinService
{
    private $api = "https://aweme.snssdk.com/aweme/v1/aweme/detail/?retry_type=no_retry&iid=74655440239&device_id=57318346369&ac=wifi&channel=wandoujia&aid=1128&app_name=aweme&version_code=140&version_name=1.4.0&device_platform=android&ssmix=a&device_type=MI+8&device_brand=xiaomi&os_api=22&os_version=5.1.1&uuid=865166029463703&openudid=ec6d541a2f7350cd&manifest_version_code=140&resolution=1080*1920&dpi=1080&update_version_code=1400&as=a125372f1c487cb50f&cp=728dcc5bc7f4f558e1&aweme_id=";
    private $apis = [
            "https://aweme.snssdk.com/aweme/v1/aweme/detail/?origin_type=link&retry_type=no_retry&iid=74655440239&device_id=57318346369&ac=wifi&channel=wandoujia&aid=1128&app_name=aweme&version_code=140&version_name=1.4.0&device_platform=android&ssmix=a&device_type=MI+8&device_brand=xiaomi&os_api=22&os_version=5.1.1&uuid=865166029463703&openudid=ec6d541a2f7350cd&manifest_version_code=140&resolution=1080*1920&dpi=1080&update_version_code=1400&as=a13520b0e9c40d9cbd&cp=064fdf579fdd07cae1&aweme_id=",
            "https://aweme.snssdk.com/aweme/v1/aweme/detail/?origin_type=link&retry_type=no_retry&iid=74655440239&device_id=57318346369&ac=wifi&channel=wandoujia&aid=1128&app_name=aweme&version_code=140&version_name=1.4.0&device_platform=android&ssmix=a&device_type=MI+8&device_brand=xiaomi&os_api=22&os_version=5.1.1&uuid=865166029463703&openudid=ec6d541a2f7350cd&manifest_version_code=140&resolution=1080*1920&dpi=1080&update_version_code=1400&as=a13510902a54ed1cad&cp=0a40dc5ba5db09cee1&aweme_id=",
            "https://aweme.snssdk.com/aweme/v1/aweme/detail/?origin_type=link&retry_type=no_retry&iid=43619087057&device_id=57318346369&ac=wifi&channel=update&aid=1128&app_name=aweme&version_code=251&version_name=2.5.1&device_platform=android&ssmix=a&device_type=MI+8&device_brand=xiaomi&language=zh&os_api=22&os_version=5.1.1&uuid=865166029463703&openudid=ec6d541a2f7350cd&manifest_version_code=251&resolution=1080*1920&dpi=480&update_version_code=2512&as=a1e500706c54fd8c8d&cp=004ad55fc8d60ac4e1&aweme_id=",
            "https://aweme.snssdk.com/aweme/v1/aweme/detail/?origin_type=link&retry_type=no_retry&iid=43619087057&device_id=57318346369&ac=wifi&channel=update&aid=1128&app_name=aweme&version_code=251&version_name=2.5.1&device_platform=android&ssmix=a&device_type=MI+8&device_brand=xiaomi&language=zh&os_api=22&os_version=5.1.1&uuid=865166029463703&openudid=ec6d541a2f7350cd&manifest_version_code=251&resolution=1080*1920&dpi=480&update_version_code=2512&as=a10500409d74bdec1d&cp=0a4ed456dedf0acee1&aweme_id=",
            "https://aweme.snssdk.com/aweme/v1/aweme/detail/?origin_type=link&retry_type=no_retry&iid=75364831157&device_id=68299559251&ac=wifi&channel=wandoujia&aid=1128&app_name=aweme&version_code=650&version_name=6.5.0&device_platform=android&ssmix=a&device_type=xiaomi+8&device_brand=xiaomi&language=zh&os_api=22&os_version=5.1.1&openudid=2e5c5ff4ce710faf&manifest_version_code=660&resolution=1080*1920&dpi=480&update_version_code=6602&mcc_mnc=46000&js_sdk_version=1.16.2.7&as=a1257080aec45ddcad&cp=0b4cd25fe4d00ccfe1&aweme_id=",
            "https://aweme.snssdk.com/aweme/v1/aweme/detail/?origin_type=link&retry_type=no_retry&iid=75364831157&device_id=68299559251&ac=wifi&channel=wandoujia&aid=1128&app_name=aweme&version_code=650&version_name=6.5.0&device_platform=android&ssmix=a&device_type=xiaomi+8&device_brand=xiaomi&language=zh&os_api=22&os_version=5.1.1&openudid=2e5c5ff4ce710faf&manifest_version_code=660&resolution=1080*1920&dpi=480&update_version_code=6602&mcc_mnc=46000&js_sdk_version=1.16.2.7&as=a125a0b01f946d2cdd&cp=0744d553ffd60cc3e1&aweme_id=",
    ];

    private $http;

    public function __construct(){
        $this->http = new Curl();
    }

    /**
     * 对外暴露接口
     */
    public function get($url)
    {
        $url = $this->getLinkFromDouyinShareText($url);
        $awemeId=$this->getAwemeId($url);
        $cookie = '0.2-20190329-5199e-ZWZpjyj1GklaNSYVf4Jm';
        return $this->getVideoData($this->api,$cookie,$awemeId);
    }

    /**
     * 解释URL地址
     */
    private function getLinkFromDouyinShareText($shareOrUrl)
    {
        preg_match('@[a-zA-z]+://[^\s]*/@',$shareOrUrl,$urlArr);
        if(empty($urlArr[0])){
            throw new \Exception('不是有效的网址');
        }
        return $urlArr[0];
    }

    /**
     * 获取getAwemeId
     */
    private function getAwemeId($link)
    {
        $html_text= $this->http->get($link);
        preg_match('/itemId: "(.*)",/',$html_text,$arr);
        if(empty($arr[1])){
            throw new \Exception('itemId 获取错误');
        }
        return $arr[1];
    }
    
    /**
     * 请求接口
     */
    private function getVideoData($api,$cookie,$awemeId)
    {
        $header=array("Accept-Encoding: utf-8",
                    "Cookie: ".$cookie,
                    "User-Agent: okhttp/3.10.0.1"
                    );
    
        $time=time();
        $_rticket=$time.'139';
        $this->http->addHeaders($header);
        $data = json_decode($this->http->get($api.$awemeId."&ts=$time"."&_rticket=$_rticket"),true);
    
        if(empty($data['aweme_detail']['author']['short_id'])){
            foreach($this->apis as $url){
                $data = json_decode($this->http->get($url.$awemeId."&ts=$time"."&_rticket=$_rticket"),true);
                if(is_array($data) && count($data)) break;
                //echo $url.PHP_EOL;
            }
        }

        $detail=!empty($data['aweme_detail'])?$data['aweme_detail']:'';
        $user_name=!empty($detail['author']['nickname'])?$detail['author']['nickname']:'';//作者昵称
        $shortId=!empty($detail['author']['short_id'])?$detail['author']['short_id']:'';//作者抖音号
        $user_headImg=!empty($detail['author']['avatar_medium']['url_list'][0])?$detail['author']['avatar_medium']['url_list'][0]:'';//作者头像
        $image=!empty($detail['video']['origin_cover']['url_list'][0])?$detail['video']['origin_cover']['url_list'][0]:'';//封面图片
        $urls=!empty($detail['video']['play_addr']['url_list'])?$detail['video']['play_addr']['url_list']:'';//无水印地址
        $music_urls=!empty($detail['music']['play_url']['url_list'])?$detail['music']['play_url']['url_list']:'';//音乐地址
        $userId=!empty($detail['author_user_id'])?$detail['author_user_id']:'';//用户userId
        $dynamic_cover=!empty($detail['video']['dynamic_cover']['url_list'][0])?$detail['video']['dynamic_cover']['url_list'][0]:'';//封面动态图地址
        $longVideo=!empty($detail['long_video'][0]['video']['bit_rate'])?$detail['long_video'][0]['video']['bit_rate']:[];//长视频
        
        
        $douyin=[
            'nickname'=>$user_name,
            'shortId'=>$shortId,
            'userId'=>$userId,
            'awemeId'=>$awemeId,
            'headImage'=>$user_headImg,
            'image'=>$image,
            'dynamic_cover'=>$dynamic_cover,
            'video_urls'=>$urls,
            'long_video'=>$longVideo,
            'music_urls'=>$music_urls,
        ];
        if($urls==null&&$user_name==null){
            $error=[
                'status'=>false,
                'message'=>'抖音接口调用失败'
            ];
            return json_encode($error);
        }
        return json_encode($douyin);
    
    }
}

