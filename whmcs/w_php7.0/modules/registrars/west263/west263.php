<?php
require_once("west263_function.php");
function west263_getConfigArray() {
	$configarray = array(
	 "Username" => array( "Type" => "text", "Size" => "20", "Description" => "Input Your API UserName", ),
	 "Email" => array( "Type" => "text", "Size" => "30", "Description" => "Input Your API Email", ),
	 "Password" => array( "Type" => "password", "Size" => "20", "Description" => "Input Your API Password", ),
	 "Key" => array( "Type" => "text", "Size" => "20", "Description" => "Input Key", ),
	 "Dns1" => array( "Type" => "text", "Size" => "30", "Description" => "Domain DNS Server 1", ),
	 "Dns2" => array( "Type" => "text", "Size" => "30", "Description" => "Domain DNS Server 2", ),
	);
	return $configarray;
}

/*
 //初始化API信息
 $username = $params["Username"];
 $email = $params["Email"];
 $password = $params["Password"];
*/


//解析xml函数
function postdata2xml($data) {
	$data = str_replace("gb2312","utf-8",$data);
	$data = mb_convert_encoding($data,"utf-8","gb2312"); 
	$data = explode("\n",$data);
	$i = count($data);
	$xmldata = "";
	for($a = 1; $a < $i ; $a++)
		{
		$xmldata .=$data[$a]."\n";
		}
	$resxml = simplexml_load_string($xmldata);
	return $resxml;
}

/*
$arg,$username,$email,$password 分别表示：
用户post的数据，您的id，您的email，您的密码
*/
function PostData($arg,$userid,$email,$password) 
{
 //构造要post的字符串 
 $params ="";
 foreach ($arg as $key=>$value) { 
    $params.= "&".$key."="; $params.= urlencode(iconv('UTF-8', 'GB2312',$value)); 
 } 
 $params = substr($params,1);
 $params.="&userid=".$userid;
 $sVTime =date("YmdHi",time());
 $params.="&vtime=".$sVTime;
 $params.="&userstr=".md5($userid.$password.$email.$sVTime);
 
      $length = strlen($params);
    
    //以下参数请按西部数码最终 API 发布地址进行修改
    $apiHost = "api.west263.com";
    $apiPort = 80;
    $apiUrl = "/api/west263.asp";
    
      $fp = fsockopen($apiHost,$apiPort,$errno,$errstr,10) or exit($errstr."--->".$errno); 
    
      //构造post请求的头 
      $header = "POST ". $apiUrl . " HTTP/1.1\r\n";
    $header .= "Host:" . $apiHost . "\r\n";
      $header .= "Content-Type: application/x-www-form-urlencoded\r\n"; 
      $header .= "Content-Length: ".$length."\r\n"; 
      $header .= "Connection: Close\r\n\r\n";
      //添加post的字符串 
      $header .= $params."\r\n"; 
      //发送post的数据 
      fputs($fp,$header); 
      $inheader = 1; 
 $ret="";
      while (!feof($fp)) {
            $line = fgets($fp,1024); //去除请求包的头只显示页面的返回数据 
            if ($inheader && ($line == "\n" || $line == "\r\n")) {
                   $inheader = 0; 
        } 
            if ($inheader == 0) { 
                  $ret.=$line; 
        } 
    } 
 fclose($fp); 
 return $ret;
}

function west263_GetNameservers($params) {
	$username = $params["Username"];
	$email = $params["Email"];
	$password = $params["Password"];
	$domain = $params["sld"] . '.' . $params["tld"];
	# Put your code to get the nameservers here and return the values below
    $argv = array(
    'category'=>'domain', 
    'action'=>'getdomaininfo',
    'domain'=>$domain);
    $resxml = postdata2xml(PostData($argv,$username,$email,$password));
    $rcode = $resxml->returncode;
	$values["ns1"] = $resxml->dns1;
	$values["ns2"] = $resxml->dns2;
    $values["ns3"] = $resxml->dns3;
    $values["ns4"] = $resxml->dns4;
	# If error, return the error message in the value below
	if($rcode != "200")
	$values["error"] = "code:".$resxml->returncode.";".$resxml->failreason;
	return $values;
}

function west263_SaveNameservers($params) {
	$username = $params["Username"];
	$password = $params["Password"];
	$email = $params["Email"];
	$domain = $params["sld"] . '.' . $params["tld"];
    $nameserver1 = $params["ns1"];
	$nameserver2 = $params["ns2"];
    $nameserver3 = $params["ns3"];
	$nameserver4 = $params["ns4"];
	# Put your code to save the nameservers here
	$argv = array(
    'category'=>'domain', 
    'action'=>'chgdns',
    'domain'=>$domain,
    'dnshost01'=>$nameserver1,
    'dnshost02'=>$nameserver2);
    $resxml = postdata2xml(PostData($argv,$username,$email,$password));
    $rcode = $resxml->returncode;
	print_r($argv);
	# If error, return the error message in the value below
	if($rcode != "200")
	$values["error"] = "code:".$resxml->returncode.";".$resxml->failreason;
	return $values;
}

function west263_GetDNS($params) {
	$username = $params["Username"];
	$password = $params["Password"];
	$email = $params["Email"];
	$domain = $params["sld"] . '.' . $params["tld"];
	$argv = array(
    'category'=>'domain', 
    'action'=>'getdomaininfo',
    'domain'=>$domain);
    $resxml = postdata2xml(PostData($argv,$username,$email,$password));
    $pwd = $resxml->domainpwd;
	$west263url = "http://api.west263.com/dns/dnslogin.asp";
	echo "<meta http-equiv=\"content-type\" content=\"text/html; charset=utf-8\"/>\n";
	echo "<form name=\"dnsform1\" method=\"post\" action=\"".$west263url."\" ID=\"dnsform1\" />\n";
	echo "<input name=\"domain\" type=\"hidden\" value=\"".$domain."\" />\n";
	echo "<input name=\"inipass\" type=\"hidden\" value=\"".$pwd."\">\n";
	echo "</form>";
	echo "<script type=\"text/javascript\">document.dnsform1.submit();</script>";
    # Put your code here to get the current DNS settings - the result should be an array of hostname, record type, and address
 
	return $hostrecords;

}

function west263_RegisterDomain($params) {
	$username = $params["Username"];
	$password = $params["Password"];
	$email = $params["Email"];
	$domain = $params["sld"] . '.' . $params["tld"];
	$regperiod = $params["regperiod"];
	$nameserver1 = $params["Dns1"];
	$nameserver2 = $params["Dns2"];
	$tld = $params["tld"];
	# Registrant Details
	$RegistrantFirstName = $params["firstname"];
	$RegistrantLastName = $params["lastname"];//姓
	$RegistrantAddress1 = $params["address1"];
	$RegistrantAddress2 = $params["address2"];
	$RegistrantCity = $params["city"];
	$RegistrantStateProvince = $params["state"];
	$RegistrantPostalCode = $params["postcode"];
	$RegistrantCountry = $params["country"];
	$RegistrantEmailAddress = $params["email"];
	$RegistrantPhone = $params["phonenumber"];
	$RegistrantCity = $params["city"];
	$RegistrantStateProvince = $params["state"];
	$RegistrantPostalCode = $params["postcode"];
	$RegistrantPhone = $params["phonenumber"];
	# West263 Function
	$pwd = GeneratePWD();
	$PinyinFirstName = Cn2Pinyin(trim($RegistrantFirstName));
	$PinyinLastName = Cn2Pinyin(trim($RegistrantLastName));//姓
	$PinyinName = $PinyinLastName. ' ' .$PinyinFirstName;
	$PinyinAddress = Cn2Pinyin(trim($RegistrantAddress1));
	$PinyinCity = Cn2Pinyin(trim($RegistrantCity));
	$PinyinState = Cn2Pinyin(trim($RegistrantStateProvince));
	# Put your code to register domain here
	$argv = array(
    'category'=>'domain', 
    'action'=>'activate',
    'domain'=>$domain,
    'domainpwd'=>$pwd,
    'Vyear'=>$regperiod,
    'dns1'=>$nameserver1,
    'dns2'=>$nameserver2,
    'productid'=>$tld,
    'firstname'=>$PinyinFirstName,
    'lastname'=> $PinyinLastName != "" ? $PinyinLastName : "Null",
    'organization_en'=>$PinyinName != "" ? $PinyinName : "Null",
	'address_en'=>$PinyinAddress != "" ? $PinyinAddress : "Null",
    'city'=>$PinyinCity != "" ? $PinyinCity : "Null",
    'state'=>$PinyinState != "" ? $PinyinState : "Null",
    'postcode'=>$RegistrantPostalCode,
    'country'=>'CN',
    'phone'=>$RegistrantPhone ,
    'fax'=>$RegistrantPhone ,
    'email'=>$RegistrantEmailAddress,
    'admin_same_as'=>'2',
    'tech_same_as'=>'2',
    'bill_same_as'=>'2',
	'address_zh'=>$RegistrantAddress1,
	'name'=>$RegistrantLastName.$RegistrantFirstName,
	'organization_zh'=>$RegistrantLastName.$RegistrantFirstName,
	'ccity'=>$RegistrantCity ,
	'cstate'=>$RegistrantStateProvince,
	'manager'=>$RegistrantLastName.$RegistrantFirstName,	
	);
	//Post To API
    $resxml = postdata2xml(PostData($argv,$username,$email,$password));
    $rcode = $resxml->returncode;
	# If error, return the error message in the value below
	if($rcode != "200")
	$values["error"] = "code:".$resxml->returncode.";".$resxml->failreason;
	return $values;
}

function west263_RenewDomain($params) {
	$username = $params["Username"];
	$password = $params["Password"];
	$email = $params["Email"];
	$domain = $params["sld"] . '.' . $params["tld"];
	$regperiod = $params["regperiod"];
	echo $regperiod ;
	$argv = array(
    'category'=>'domain', 
    'action'=>'getdomaininfo',
    'domain'=>$domain);
    $resxml = postdata2xml(PostData($argv,$username,$email,$password));
    $deaddate = $resxml->deaddate;
    $deaddate = date('Y-m-d',strtotime("$deaddate +$regperiod year"));
	# Put your code to renew domain here
	$argv = array(
    'category'=>'domain', 
    'action'=>'renew',
    'domain'=>$domain,
    'deaddate'=>$deaddate,
    'vyear'=>$regperiod);
    $resxml = postdata2xml(PostData($argv,$username,$email,$password));
    $rcode = $resxml->returncode;
	# If error, return the error message in the value below
	if($rcode != "200")
	$values["error"] = "code:".$resxml->returncode.";".$resxml->failreason;
	return $values;
}
?>