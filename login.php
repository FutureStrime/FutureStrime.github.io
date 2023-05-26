<?php
require_once 'D:/server-homework/php/vendor/autoload.php';
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

function base64url_encode($data){
  return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
}

header('Content-Type:application/json;charset=utf-8');

if(!$_POST["userName"] && !$_POST["password"] && !$_POST["validation"]){
  $ret = array('status' => 'failed','code' => 4,'message' => '非法操作','token' => null);
  echo json_encode($ret,JSON_UNESCAPED_UNICODE);
  exit;
}else{
  $valid = false;
  for($t = time() - 3;$t <= time() + 3;$t++){
    if($_POST['validation'] == md5($t)){
      $valid = true;
      break;
    }
  }
  if(!$valid){
    $ret = array('status' => 'failed','code' => 3,'message' => '非法操作','token' => null);
    echo json_encode($ret,JSON_UNESCAPED_UNICODE);
    exit;
  }
}

$connection = new mysqli("localhost","root","zhangyan2002");
$ret = null;
$userName = $_POST['userName'];
$password = $_POST['password'];
if($connection -> connect_error){
  $ret = array('status' => 'failed','code' => 1,'message' => '无法连接数据库','token' => null);
  echo json_encode($ret,JSON_UNESCAPED_UNICODE);
  exit;
}else{
  mysqli_query($connection,"SET NAMES UTF8MB4");
  mysqli_select_db($connection,"users");
  $check = mysqli_query($connection,"SELECT * FROM users_table WHERE userName = '".$userName."' AND password = '".$password."' LIMIT 1");
  $result = mysqli_fetch_assoc($check);
  if(!$result){
    $ret = array('status' => 'failed','code' => 2,'message' => '用户名或密码错误','token' => null);
    echo json_encode($ret,JSON_UNESCAPED_UNICODE);
    exit;
  }else{
    $userID = $result['userID'];
    $secretKey = '51ec!a-Kd5/94;d`85]1e2815\832';
    $payload = [
      'user' => $userName,
      'userID' => $userID,
      'iat' => time(),
    ];
    $token = JWT::encode($payload,$secretKey,'HS256');
    $ret = array('status' => 'success','code' => 0,'message' => '登录成功','token' => $token);
    echo json_encode($ret,JSON_UNESCAPED_UNICODE);
    exit;
  }
}

?>