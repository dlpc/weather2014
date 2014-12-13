<?php
/**
 * 微信公众平台 PHP SDK 示例文件
 *
 * @author NetPuter <netputer@gmail.com>
 */

require('wechat.php');

/**
* 微信公众平台演示类
*/
class MyWechat extends Wechat {

    /**
     * 用户关注时触发，回复「欢迎关注」
     *
     * @return void
     */
    protected function onSubscribe() {
      $this->responseText('欢迎关注，发送城市名称获取两天内天气预报');
    }

    /**
     * 用户已关注时,扫描带参数二维码时触发，回复二维码的EventKey (测试帐号似乎不能触发)
     *
     * @return void
     */
    protected function onScan() {
      $this->responseText('二维码的EventKey：' . $this->getRequest('EventKey'));
    }

    /**
     * 用户取消关注时触发
     *
     * @return void
     */
    protected function onUnsubscribe() {
      // 「悄悄的我走了，正如我悄悄的来；我挥一挥衣袖，不带走一片云彩。」
    }

    /**
     * 上报地理位置时触发,回复收到的地理位置
     *
     * @return void
     */
    protected function onEventLocation() {
      $this->responseText('收到了位置推送：' . $this->getRequest('Latitude') . ',' . $this->getRequest('Longitude'));
    }


    /**
     * 收到图片消息时触发，回复由收到的图片组成的图文消息
     *
     * @return void
     */
    protected function onImage() {
      $items = array(
        new NewsResponseItem('标题一', '描述一', $this->getRequest('picurl'), $this->getRequest('picurl')),
        new NewsResponseItem('标题二', '描述二', $this->getRequest('picurl'), $this->getRequest('picurl')),
      );

      $this->responseNews($items);
    }

    /**
     * 收到地理位置消息时触发，回复收到的地理位置
     *
     * @return void
     */
    protected function onLocation() {
        //$num = 1 / 0;
        $location_x = $this->getRequest('location_x');
        $location_y = $this->getRequest('location_y');

        $json_city = file_get_contents("http://api.map.baidu.com/geocoder/v2/?ak=qPFnHQ18Y3mbqGmrTolRqhKd&location=".$location_x.",".$location_y."&output=json&pois=0");

        $city = json_decode($json_city,true);

        if( $city['status'] == 0 ){

            $weather = $this -> weather($city['result']['addressComponent']['city']);

            $this -> responseText($weather);
            //print_r($city['result']['addressComponent']['city']);
        }else{
            $this -> responseText("请输入正确的城市名称，如赤峰");
        }

        //$this->responseText('收到了位置消息：' . $this->getRequest('location_x') . ',' . $this->getRequest('location_y'));
        
    }

    /**
     * 收到链接消息时触发，回复收到的链接地址
     *
     * @return void
     */
    protected function onLink() {
      $this->responseText('收到了链接：' . $this->getRequest('url'));
    }

    /**
     * 收到语音消息时触发，回复语音识别结果(需要开通语音识别功能)
     *
     * @return void
     */
    protected function onVoice() {
        
        $voice_text = $this->getRequest('Recognition');
        $weather = $this -> weather($voice_text);

        $this->responseText($weather);
    }

    /**
     * 收到自定义菜单消息时触发，回复菜单的EventKey
     *
     * @return void
     */
    protected function onClick() {
      $this->responseText('你点击了菜单：' . $this->getRequest('EventKey'));
    }

    /**
     * 收到未知类型消息时触发，回复收到的消息类型
     *
     * @return void
     */
    protected function onUnknown() {
      $this->responseText('收到了未知类型消息：' . $this->getRequest('msgtype'));
    }
      
      

    /**
     * 收到文本消息时触发，回复收到的文本消息内容
     *
     * @return void
     */
    protected function onText() {
        //$this->responseText('收到了文字消息：' . $this->getRequest('content'));
        
        $text = $this->getRequest('content');
        
        $weather = $this -> weather($text);
        //$this->responseText(mb_detect_encoding($text, array('ASCII','GB2312','GBK','UTF-8')));
        $this->responseText($weather);
    }
      

    /**
     * 根据城市名返回天气
     *
     * @return void
     */
    protected function weather( $city ) {
        
        $json_weather = file_get_contents("http://api.map.baidu.com/telematics/v3/weather?location=".$city."&output=json&ak=qPFnHQ18Y3mbqGmrTolRqhKd");
        //echo "http://api.map.baidu.com/telematics/v3/weather?location=".$city."&output=json&ak=qPFnHQ18Y3mbqGmrTolRqhKd";
        
        //return $json_weather;
        
        $weather = json_decode($json_weather,true);
        if ($weather['status'] !== "success") {
            return "请输入正确的城市名称，如赤峰";
            die();
        }
        
        $today = $weather['results'][0];
        $today_des = $today['index'];
        $yb = $today['weather_data'];

        $string  = "查询城市：".$city;
        $string .= "\n当前时间：".date("H:i");
        $string .= "\n【今天】".$yb[0]['date']."，".$yb[0]['weather']."，".$yb[0]['wind']."，".$yb[0]['temperature'];
        //$string = "";
        /*foreach ( $today_des as $item ) {
            
            $string.= "\n".$item['title']."\n".$item['zs']."\n";

        }
        
        foreach ( $yb as $item ) {


            $string.= $item['date'].$item['weather'].$item['wind'].$item['temperature']."\n";


        }*/
        $string .="\n【明天】".$yb[1]['date']."，".$yb[1]['weather']."，".$yb[1]['wind']."，".$yb[1]['temperature'];
        

        return $string;
        
    } 
}

$wechat = new MyWechat('NBTh9LxIPfT2jVZUbvGItTOV', TRUE);
$wechat->run();