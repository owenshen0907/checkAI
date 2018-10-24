<?php 
header("Content-type: text/html; charset=utf-8"); 
date_default_timezone_set('PRC');


function characet($data){
  if( !empty($data) ){
    $fileType = mb_detect_encoding($data , array('UTF-8','GBK','LATIN1','BIG5')) ;
    if( $fileType != 'UTF-8'){
      $data = mb_convert_encoding($data ,'utf-8' , $fileType);
    }
  }
  return $data;
}
function flashLog(){
	$file_name = 'C:/xampp/htdocs/checkAI/log/flashLog_'.date('Y-m-d').'.log';
	file_put_contents($file_name, "Programmer has benn falshed at  ".date('Y-m-d G:i:s')."\r\n",FILE_APPEND);
}
function msgSend($megtxt){
	$phones="13554651090,13681638564,18930140617";
	$msg = characet($megtxt);
	$file_name = 'C:/xampp/htdocs/checkAI/log/balance_sms_'.date('Y-m-d').'.log';
	file_put_contents($file_name, "Has sent to ".$phones."\r\n".$msg,FILE_APPEND);


	$url = "http://sms.10690221.com:9011/hy/";
	$post_string = "uid=810851&auth=00b3710cfa5b01b374356442cab68115&mobile=$phones&msg=".$msg."&expid=0&encode=utf-8";
		$this_header = array("content-type: application/x-www-form-urlencoded;charset=UTF-8");
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $this_header);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post_string);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$data = curl_exec($ch);
		echo $data;
}
 ?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<script src="http://ajax.aspnetcdn.com/ajax/jQuery/jquery-1.8.0.js">
</script>
<style type="text/css">
input[type='text']{width:35px;height:35px;padding:5px 10px;margin:5px;border:1px solid #ffe5e5;}
</style>


</head>
<body>
当前时间：<p id="time1" style="color: red;"></p><br>
距离下次自动刷新时间：
     <input type="text" id="minute_show">分<input type="text" id="second_show">秒
<?php
/*
$servername = "116.62.188.156:3797";
$username = "root";
$password = "@2008WhatW&UJM";
$dbname = "ai01";
*/
$servername = "91family.net:3307";
$username = "root";
$password = "2wsx#EDC";
$dbname = "ai01";
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
    die("MySQL connect die " . $conn->connect_error);
	$file_name = 'C:/xampp/htdocs/checkAiRun/log/flashLog_'.date('Y-m-d').'.log';
	file_put_contents($file_name, "MySQL connect die  at  ".date('Y-m-d G:i:s')."\r\n",FILE_APPEND);
} 
 
$sql = "SELECT * FROM `riskmonitor_viewsmg` ";

$result = $conn->query($sql);
?>
 <table border="1">
  <tr>
    <th>account</th>
    <th>cost</th>
	<th>balance</th>
	<th>equity</th>
	<th>risk</th>
	<th>name</th>
	<th>dateti</th>
	<th>annRB</th>
  </tr>
<?php

$nowTime = date('Y.m.d H:i:s', time());
//echo $nowTime;

if ($result->num_rows > 0) {
	
    while($row = $result->fetch_assoc()) {
		echo "<tr>";
        echo "<td>".$row["account"]."</td>";
		echo "<td>".$row["cost"]."</td>";
		echo "<td>".$row["balance"]."</td>";
		echo "<td>".$row["equity"]."</td>";
		echo "<td>".$row["risk"]."</td>";
		$name = characet($row["name"]);
		echo "<td>".$name."</td>";		
		echo "<td>".$row["dateti"]."</td>";
		echo "<td>".$row["annRB"]."</td>";
		echo "</tr>";
    }
} 

?>
</table>
<?php
$sql = "SELECT dateti FROM `riskmonitor_viewsmg` ";
$result = $conn->query($sql);
$flag1 = 0;
if ($result->num_rows > 0) {
	
    while($row = $result->fetch_assoc()) {	
		$targetTime = strtotime(explode(" ",$row["dateti"])[1]);
		echo time($nowTime)." - ".$targetTime." = ".(time($nowTime)-$targetTime)."<br>";
		$subtract = time($nowTime)-$targetTime;
		//刷新频率分钟
		$frequency = 10;
		if($subtract > $frequency*60){
			$flag1 = $flag1+1; 
		}
    }
} 
$finistime = date('Y-m-d H:i:s',time($nowTime)+300);
echo $finistime;
$time=time();
$week=date("N",$time);
echo "星期".$week." ";
$hour=date("H");
echo $hour."点<br>";
if($week==1&&$hour>4&&$flag1>0){
			$sqlI = "insert into z_alarm(alarmTime,alarmTxt) values('{$finistime}','{$flag1}');";
		$result = $conn->query($sqlI);
		if($result){
			echo "添加成功!<br>";
			}else{
				echo "添加失败！<br>";
			}
echo "现在是周一五点之后"."<br>";
}
if($week<6&&$week>1&&$flag1>0){
			$sqlI = "insert into z_alarm(alarmTime,alarmTxt) values('{$finistime}','{$flag1}');";
		$result = $conn->query($sqlI);
		if($result){
			echo "添加成功!<br>";
			}else{
				echo "添加失败！<br>";
			}
echo "现在是周二到 周五"."<br>";
}
if($week==6&&$hour<=4&&$flag1>0){
			$sqlI = "insert into z_alarm(alarmTime,alarmTxt) values('{$finistime}','{$flag1}');";
		$result = $conn->query($sqlI);
		if($result){
			echo "添加成功!<br>";
			}else{
				echo "添加失败！<br>";
			}
echo "现在是周六的5点前"."<br>";
}
		$smgCount =0;
		$sqlC = "SELECT * FROM smgcount1h;";
		$result = $conn->query($sqlC);
		$row = $result->fetch_assoc();
		$smgCount = $row["a"];
		echo "一小时内已经发了";
		echo $smgCount."次短信了<br>";
if($week==1&&$hour>4&&$flag1>0){
	$txt= "警告！！！当前有".$flag1."个账号数据超过".$frequency."分钟未更新，请注意查看监控服务器！";
	if ($smgCount<2){
		msgSend($txt);
	}
	if ($smgCount==2){
		$txt= "警告！当前1小时内有两次报警，".$flag1."个账号数据超过".$frequency."分钟未更新，注意查看监控服务器！本小时内将不再报警请注意！";
		msgSend($txt);
	}
}
if($week<6&&$week>1&&$flag1>0){
	$txt= "警告！！！当前有".$flag1."个账号数据超过".$frequency."分钟未更新，请注意查看监控服务器！";
	if ($smgCount<2){
		msgSend($txt);
	}
	if ($smgCount==2){
		$txt= "警告！当前1小时内有两次报警，".$flag1."个账号数据超过".$frequency."分钟未更新，注意查看监控服务器！本小时内将不再报警请注意！";
		msgSend($txt);
	}
}
if($week==6&&$hour<=4&&$flag1>0){
	$txt= "警告！！！当前有".$flag1."个账号数据超过".$frequency."分钟未更新，请注意查看监控服务器！";
	if ($smgCount<2){
		msgSend($txt);
	}
	if ($smgCount==2){
		$txt= "警告！当前1小时内有两次报警，".$flag1."个账号数据超过".$frequency."分钟未更新，注意查看监控服务器！本小时内将不再报警请注意！";
		msgSend($txt);
	}
}
flashLog();

$conn->close();
?>



</body>

<script type="text/javascript">
 $(function(){ 
    show_time();
}); 

function show_time(){ 
    var time_start = new Date().getTime(); //设定当前时间

    var time_end =  new Date('<?php echo $finistime ?>').getTime(); //设定目标时间
    // 计算时间差 
    var time_distance = time_end - time_start; 
    /*判断活动是否结束*/
    if(time_distance<0){
		location.reload();
        int_day=0;
        int_hour=0;
        int_minute=0;
        int_second=0;
    }else{
          // 天
    var int_day = Math.floor(time_distance/86400000) 
    time_distance -= int_day * 86400000; 
    // 时
    var int_hour = Math.floor(time_distance/3600000) 
    time_distance -= int_hour * 3600000; 
    // 分
    var int_minute = Math.floor(time_distance/60000) 
    time_distance -= int_minute * 60000; 
    // 秒 
    var int_second = Math.floor(time_distance/1000)
    // 时分秒为单数时、前面加零 
    if(int_day < 10){ 
        int_day = "0" + int_day; 
    } 
    if(int_hour < 10){ 
        int_hour = "0" + int_hour; 
    } 
    if(int_minute < 10){ 
        int_minute = "0" + int_minute; 
    } 
    if(int_second < 10){
        int_second = "0" + int_second; 
    } 
    }

    // 显示时间 
    $("#day_show").val(int_day); 
    $("#hour_show").val(int_hour); 
    $("#minute_show").val(int_minute); 
    $("#second_show").val(int_second); 
    // 设置定时器
    setTimeout("show_time()",1000); 
}

    function mytime(){
        var a = new Date();
        var b = a.toLocaleTimeString();
        var c = a.toLocaleDateString();
        document.getElementById("time1").innerHTML = c+"&nbsp"+b;
        }
    setInterval(function() {mytime()},1000);

</script>
</html>
