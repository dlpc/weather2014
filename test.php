<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>天气查询</title>
<?php
//echo '<strong>Hello, SAE!</strong>';
$city = isset($_GET['city']) ? $_GET['city'] : "北京" ;

$json_weather = file_get_contents("http://api.map.baidu.com/telematics/v3/weather?location=".$city."&output=json&ak=qPFnHQ18Y3mbqGmrTolRqhKd");

$weather = json_decode($json_weather,true);
if ($weather['status'] !== "success") {
	exit("数据错误，请检查重试");
}

$today = $weather['results'][0];
$today_des = $today['index'];
$yb = $today['weather_data'];

?>
<h2><?php print_r($today['currentCity']."今日天气");?></h2>
<p><?php print_r("PM2.5：".$today['pm25']);?></p>

<h2>指数</h2>
<?php
	foreach ( $today_des as $item ) {
?>
	<p>
		<?php echo $item['title']."<br/>".$item['zs']."<br/>".$item['des']; ?>

	</p>
<?php 
	}
?>
<h2>预报</h2>
<?php
	foreach ( $yb as $item ) {
?>
	<p>
		<img src="<?php echo $item['dayPictureUrl']; ?>"><?php echo $item['date'].$item['weather'].$item['wind'].$item['temperature']; ?>

	</p>
<?php 
	}
?>
