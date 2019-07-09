# douyin-clear-php
抖音去水印PHP版接口  
原项目地址：https://github.com/zbfzn/douyin-clear-php
对该项目进行了封装优化

修复5.29无法解析的问题  
源码已上传  
19-06-04:接口变更（https://aweme.snssdk.com/aweme/v1/aweme/detail/）  
19-06-05：新增长视频，userId、抖音id  
19-06-13：提供几个可用API，在apis.txt里，源代码的api不能使用时换一个即可  
19-06-25：3种接口失效自动切换（注：官方APP在某些时候也会出现不能解析出视频链接的情况，此情况下若你的APP能识别并解析分享链接，而接口解析不出来的话请反馈给我，接口抓取请参照https://github.com/zbfzn/douyin-clear-php/issues/5 ）  

使用方法：  
==
    环境：curl、php（本人v7.3）
    php -S 0.0.0.0:9501   //开启一个http服务并监听9501端口
	在浏览器 http://localhost:9501/url=http://视频地址/
 ********
 文档： 
 ==
  请求方式：GET  
  --
  请求参数：  
  --
  url：http://v.douyin.com/jJub3C/ 或 http://v.douyin.com/jJub3C/ 复制此链接，打开【抖音短视频】，直接观看视频！
都行。（地址前面不能带\#号，服务器会忽略\#后面的内容,必须以 / 结尾）  

  Response：JSON  
  --
请求成功：
````json
{
"nickname": "捧猫小可爱",
"shortId": "2012684890",
"userId": 111005943456,
"awemeId": "6710895529905245453",
"headImage": "https://p1-dy.byteimg.com/aweme/720x720/1736f0002e95b88989504.jpeg",
"image": "http://p3-dy.byteimg.com/large/29ebc00093564bd46b8e0.jpeg",
"dynamic_cover": "https://p9-dy.byteimg.com/obj/29d5c00029d671ef7ccaa",
"video_urls": [
"https://aweme.snssdk.com/aweme/v1/play/?video_id=v0300f980000bkhc8247u4t60lh2rmgg&line=0&ratio=540p&media_type=4&vr_type=0&improve_bitrate=0&is_play_url=1",
"https://api.amemv.com/aweme/v1/play/?video_id=v0300f980000bkhc8247u4t60lh2rmgg&line=1&ratio=540p&media_type=4&vr_type=0&improve_bitrate=0&is_play_url=1"
],
"long_video": [],
"music_urls": [
"http://p3-dy.byteimg.com/obj/ies-music/1638458755001399.mp3"
]
}
      
````
请求失败：
````json
{
    "status": false,
    "message": "地址无效"
}
````
````json
{
   "status": false,
   "message": "抖音接口调用失败"
}
````

    参数：
    status:请求状态码true/false  
    message:提示文本，返回结果错误时会返回地址信息  
    nickname:抖音昵称  
    awemeId：视频资源Id
    info:视频信息 
    image:封面图片地址(静态)
    headImage:用户头像地址  
    urls:无水印地址  
    music_urls:音乐原声地址 
    dynamic_cover:动态封面图（19-06-05加）  
    long_video:长视频（完整视频信息（19-06-05加）  
    userId:作者userId（19-06-05加）  
    shortId：作者抖音Id（19-06-05加）  
    
    
   如有间歇性无法使用请反馈给我。经测试，有时候APP也会出现无法解析的情况，这种情况可以换低版本的API试试。    
   鉴于当前版本需手动获取链接，之后会提供更加稳定的方式（动态生成链接）。    

**喜欢的话，给个star呗**

