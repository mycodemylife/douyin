# douyin-clear-php


使用方法：  
==
    环境：curl、php（本人v7.3）
    php -S 0.0.0.0:9501   //开启一个http服务并监听9501端口
	在浏览器 http://localhost:9501/url=http://视频地址/

  复制play_addr列表里的另一链接，
  然后打列浏览器，按F12(模拟成手机，不然会提示视频不存在)，或直接把连接发到微信里
  ![效果图](img/1.png)
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
"nickname": "我姓叶", //作者
"shortId": "16652732",
"userId": 60718374109, //用户id
//头像列表
"avatar": [
"https://p9-dy.byteimg.com/aweme/720x720/2b2010001212956251bf9.jpeg",
"https://p1-dy.byteimg.com/aweme/720x720/2b2010001212956251bf9.jpeg",
"https://p3-dy.byteimg.com/aweme/720x720/2b2010001212956251bf9.jpeg"
],
//视频信息
"video": {
  //封面列表
"origin_cover": [
"http://p3-dy.byteimg.com/large/2a79a0004667806257351.jpeg",
"http://p9-dy.byteimg.com/large/2a79a0004667806257351.jpeg",
"http://p1-dy.byteimg.com/large/2a79a0004667806257351.jpeg"
],
//无水印播放列表
"play_addr": [
"http://v1-dy.ixigua.com/e81f31d446898319f343c0b90af355ce/5d2d4745/video/m/220cee9d70f5ff64e7e8e4dd4cc8964bf691162dad7a0000661f4e721803/?rc=amkzb3JlaGs8bjMzaGkzM0ApQHRAbzY1Njw0NTUzNDgzMzk6PDNAKXUpQGczdSlAZjN2KUBmcHcxZnNoaGRmOzRAZmlfaTQ1LW5pXy0tXi0wc3MtbyNvI0I2Ly0vLS4tLTAxLi4tLi9pOmIucCM6YS1xIzpgLW8jYmZoXitqdDojLy5e",
"http://v3-dy.ixigua.com/d36c00bb3a46052a13b5dab708a03057/5d2d4745/video/m/220cee9d70f5ff64e7e8e4dd4cc8964bf691162dad7a0000661f4e721803/",
"https://aweme.snssdk.com/aweme/v1/play/?video_id=v0300fae0000bkl9qh0e8b7ollb0lfig&line=0&ratio=540p&media_type=4&vr_type=0&improve_bitrate=0&is_play_url=1",
"https://api.amemv.com/aweme/v1/play/?video_id=v0300fae0000bkl9qh0e8b7ollb0lfig&line=1&ratio=540p&media_type=4&vr_type=0&improve_bitrate=0&is_play_url=1"
],
//长视频
"long_video": null
},
//背景音乐播放列表
"music": [
"http://p9-dy.byteimg.com/obj/ies-music/1638966034256951.mp3"
]
}
      
````
请求失败：
````json
{
    "errcode": -1,
    "errmsg": "错误提示"
}
````

````
   如有间歇性无法使用请反馈给我。经测试，有时候APP也会出现无法解析的情况，这种情况可以换低版本的API试试。    
   鉴于当前版本需手动获取链接，之后会提供更加稳定的方式（动态生成链接）。    

**喜欢的话，给个star呗**

