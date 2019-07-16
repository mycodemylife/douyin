<?php
require "./lib/Curl.php";
class DouyinService
{
    /**
     * Api地址
     */
    private $apis = [];

    /**
     * awemeId
     */
    private $awemeId;

    /**
     * 设备列表
     */
    private $devices = [
        'iid=43619087057&device_id=57318346369',
        'iid=74655440239&device_id=57318346369',
    ];

    /**
     * curl http client
     */
    private $http;

    public function __construct(){
        $this->http = new Curl();
    }
    /**
     * 对外暴露接口
     */
    public function get($url)
    {
        //第一步解析分享链接中的URl
        $url = $this->parseUrl($url);
        //第二步获取AwemeId
        $this->getAwemeId($url);
        //第三步获取视频数据
        return $this->getVideoData();
    }
    /**
     * 解释URL地址
     */
    private function parseUrl($shareOrUrl)
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
        $this->awemeId = $arr[1];
        return $this->awemeId;
    }

    /**
     *  生成API地址
     */
    private function buildApi()
    {
        $apis = [];
        $url = "https://aweme.snssdk.com/aweme/v1/aweme/detail/?origin_type=link&retry_type=no_retry&%s&ac=wifi&channel=update&aid=1128&app_name=aweme&version_code=251&version_name=2.5.1&device_platform=android&ssmix=a&device_type=MI+8&device_brand=xiaomi&language=zh&os_api=22&os_version=5.1.1&uuid=865166029463703&openudid=ec6d541a2f7350cd&manifest_version_code=251&resolution=1080*1920&dpi=480&update_version_code=2512&ts=%d&as=a1e500706c54fd8c8d&cp=004ad55fc8d60ac4e1&aweme_id=%s";
        if(is_array($this->devices) && count($this->devices)){
            foreach($this->devices as $device){
                $apis[] = sprintf($url,$device,time(),$this->awemeId);
            }
        }
        return $apis;
    }
    
    /**
     * 请求接口
     */
    private function getVideoData()
    {

        $this->http->setHeader('Accept-Encoding','utf-8');
        $apis = $this->buildApi();
        $data = [];
        foreach($apis as $api){
            $json = $this->http->get($api);
            $data = json_decode($json,true);
            if(!empty($data['aweme_detail'])){
                break;
            }
        }
        if(empty($data['aweme_detail'])){
            throw new \Exception('接口数据获取失败');
        }
        return $this->formatData($data);
    
    }

    /**
     * 格式化数据
     */
    private function formatData(array $data)
    {
        if(empty($data['aweme_detail'])){
            throw new \Exception('不是有效的接口数据');
        }
        $user = $data['aweme_detail'];
        return [
            'nickname' => $user['author']['nickname'],
            'shortId'  => $user['author']['short_id'],
            'userId'   => $user['author_user_id'],
            'avatar'   => $user['author']['avatar_medium']['url_list'],
            'video'    => [
                'origin_cover'  => $user['video']['origin_cover']['url_list'],
                'play_addr'     => $user['video']['play_addr']['url_list'],
                'long_video'    => $user['long_video']
            ],
            'music'   => $user['music']['play_url']['url_list']
        ];
    }
}