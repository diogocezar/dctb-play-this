<?php

set_time_limit(0);

require_once("./Config/config.php");
require_once("./Api/TwitterOAuth/TwitterOAuth.php");
require_once("./Api/TwitterOAuth/Exception/TwitterException.php");

use TwitterOAuth\TwitterOAuth;

class TocaEssa{
	
	private $consumer_key;
	
	private $consumer_secret;
	
	private $oauth_token;
	
	private $oauth_token_secret;

	private $in_acess_token;
	
	private $list_of_twits;

	private $list_of_instas;
	
	private $connection;
	
	private $last_id;
	
	private $data_base;

	private $dir_musics;

	private $reply_twitter;

	private $data_tw_last_id;

	private $data_in_last_id;

	private $data_unidentified;

	private $log_tw_requests;

	private $log_in_requests;

	private $log_erros;

	private $data_row;

	private $in_hashtag;

	private $patern_date;
	
	public function __construct(){

		$this->setDirMusics(DIR_MUSICS);
		$db = $this->LoadDataBase();

		$this->setConsumerKey(TW_CONSUMER_KEY);
		$this->setConsumerSecret(TW_CONSUMER_SECRET);
		$this->setOauthToken(TW_ACCESS_TOKEN);
		$this->setOauthTokenSecret(TW_ACCESS_TOKEN_SECRET);
		$this->setInAcessToken(IN_ACCESS_TOKEN);

		$this->setDataTwLastId(DATA_TW_LAST_ID);
		$this->setDataInLastId(DATA_IN_LAST_ID);
		$this->setLogTwRequests(LOG_TW_REQUESTS);
		$this->setLogInRequests(LOG_IN_REQUESTS);
		$this->setLogErrors(LOG_ERRORS);
		$this->setDataRow(DATA_ROW);
		$this->setInHashtag(IN_HASHTAG);
		$this->setDataUnidentified(DATA_UNIDENTIFIED);
		$this->setPaternDate(PATERN_DATE);

		$this->setReplyTwitter(REPLY_TWITTER);

		$this->setListOfTwits("");
		$this->setListOfInstas("");
		$this->setConnection("");

		$this->setDataBase($db);

		$this->goTocaEssa();
	}

	public function LoadDataBase(){
		$dir = $this->getDirMusics();
		$files = array();
		if ($handle = opendir($dir)) {
			$i = 0;
		    while (false !== ($file = readdir($handle))){
		    	if($file != "." && $file != ".."){
			    	$files[$i] = $file;
			    	$i++;
		    	}
		    }
		    closedir($handle);
		}
		return $files;
	}
	
	public function goTocaEssa(){
		$this->startConnection();
	}
	
	public function startConnection(){

		$config = array(
		    'consumer_key'       => $this->getConsumerKey(),
		    'consumer_secret'    => $this->getConsumerSecret(),
		    'oauth_token'        => $this->getOauthToken(),
		    'oauth_token_secret' => $this->getOauthTokenSecret(),
		    'output_format'      => 'object'
		);
		$this->connection = new TwitterOAuth($config);

	}
	
	public function appendError($error){
		$this->saveFile($this->getLogErrors(), "a+", date($this->getPaternDate()) . ";" . $error);
	}
	
	public function sendTwit($twit){
		if(!$this->connection){
			$this->appendError("SEND_TWIT: Error trying to establish conection with twitter");
		}
		else{
			$this->connection->post('statuses/update', array('status' => $twit));
		}
	}
	
	public function recoverLastId($type){
		$file = "";
		switch($type){
			case 'TW' : $file = $this->getDataTwLastId(); break;
			case 'IN' : $file = $this->getDataInLastId(); break;
		}
		return file_get_contents($file);
	}
	
	public function saveLastId($id, $type){
		$file = "";
		switch($type){
			case 'TW' : $file = $this->getDataTwLastId(); break;
			case 'IN' : $file = $this->getDataInLastId(); break;
		}
		file_put_contents($file, $id);
	}
	
	public function getTwits($id){
		if(!$this->connection){
			$this->appendError("GET_TWITS: Error trying to establish conection with twitter");
		}
		else{
			try{
				if(!empty($id)){
					$result = $this->connection->get('statuses/mentions_timeline', array('since_id' => $id));
				}
				else{
					$result = $this->connection->get('statuses/mentions_timeline', array());
				}
				$this->setListOfTwits($result);
			}
			catch(Exception $e){
				$this->appendError("TWITTER_ERROR_EXCEPTION: " . $e->getMessage());
			}
		}
	}

	public function getInstas($id){
		$hashtag = $this->getInHashtag();
		$in_url = "https://api.instagram.com/v1/tags/" . $hashtag . "/media/recent?access_token=" . $this->getInAcessToken();
		if(!empty($id)){
			$in_url .= "&min_tag_id=".$id;
		}
		$result = $this->__curl($in_url);
		$this->setListOfInstas(json_decode($result, true));
	}

	public function saveFile($fileName, $metod, $str){
		$filename = $fileName;
		$file     = fopen($filename, $metod);
		fwrite($file, $str . "\n"); 
		fclose($file);
	}

	public function normalizeStr($str){
		if(ereg(".mp3", $str)){
			$str = str_replace(".mp3", "", $str);
		}
		$str = preg_replace('/\s+/', ' ', $str);
		$str = trim($str);
		$str = str_replace(" ", "@", $str);
		$str = strtoupper($str);
		return $str;
	}

	public function musicPosition($str){
		$musics = $this->getDataBase();
		$hash = $this->__get_hashtags($str, 0);
		print_r($hash);
		if(!empty($hash)){
			foreach ($hash as $value) {
				if(eregi('play', $value)){
					$number = str_replace("play", "", $value);
					if(is_numeric($number)){
						$toPlay = (int)$number - 1000;
						return (string)$toPlay;
					}
				}
			}
		}
		foreach ($musics as $key => $music) {
			if($this->normalizeStr($str) == $this->normalizeStr($music)){
				return (string)$key;
			}
		}
		return false;
	}
	
	public function scanTwitter(){
		$id = $this->recoverLastId('TW');
		$this->getTwits($id);

		$data  = $this->getListOfTwits();
		$flag  = true;
		$name  = "";
		$music = "";

		if(!empty($data)){
			foreach($data as $node){
				if($flag){
					$id   = $node->id_str;
					$flag = false;
				}
				$name        = $node->user->screen_name;
				$music       = str_replace("@tocaessa ", "", $node->text);
				$created_at  = $node->created_at;
				$profile_img = str_replace("_normal", "", $node->user->profile_image_url);
				$created_at  = $this->__convert_date($created_at, 'TW');
				$music_pos   = $this->musicPosition($music);
				if($music_pos != false){
					$db = $this->getDataBase();
					$music = str_replace(".mp3", "", $db[$music_pos]);
					$this->pushList($name, $music, $created_at, $profile_img, 'NULL', 'TW');
				}
				else{
					$this->saveFile($this->getDataUnidentified(), "a+", date($this->getPaternDate()) . ";" . $created_at . ";" . $music . ";" . $name . ";" . 'TW');
				}
			}
			if(!empty($id)){
				$this->saveLastId($id, 'TW');
			}
		}
	}

	public function scanInstagram(){
		$id = $this->recoverLastId('IN');
		$this->getInstas($id);

		$data = $this->getListOfInstas();

		if(!empty($data)){
			if(array_key_exists("next_min_id", $data['pagination'])){
				$id = $data['pagination']['next_min_id'];
				$this->saveLastId($id, 'IN');
			}
			foreach($data['data'] as $item){
				$name        = $item['user']['username'];
				$profile_img = $item['user']['profile_picture'];
				$img_in      = $item['images']['low_resolution']['url'];
				$caption     = $item['caption']['text'];
				$created_at  = $item['created_time'];
				$created_at  = $this->__convert_date($created_at, 'IN');
				$music       = str_replace("#tocaessa ", "", $caption);
				$music_pos   = $this->musicPosition($music);
				if($music_pos != false){
					$db = $this->getDataBase();
					$music = str_replace(".mp3", "", $db[$music_pos]);
					$this->pushList($name, $music, $created_at, $profile_img, $img_in, 'IN');
				}
				else{
					$this->saveFile($this->getDataUnidentified(), "a+", date($this->getPaternDate()) . ";" . $created_at . ";" . $music . ";" . $name . ";" . 'IN');
				}
	    	}
    	}
	}


	public function saveLogRequests($name, $music, $created_at, $type){
		$file = "";
		switch($type){
			case 'IN' : $file = $this->getLogInRequests(); break;
			case 'TW' : $file = $this->getLogTwRequests(); break;
		}
		$this->saveFile($file, "a+", $created_at . ";" . "@" . $name . ";" . $music);
	}

	public function pushList($name, $music, $created_at, $profile_img, $img_in, $type){
		$this->saveLogRequests($name, $music, $created_at, $type);
		$this->saveFile($this->getDataRow(), "a+", $name . ";" . $music . ";" . $profile_img . ';' . $img_in . ';' . $type);
	}

	public function pullList(){
		$file   = file($this->getDataRow());
		$output = $file[count($file)-1];
		unset($file[count($file)-1]);
		file_put_contents($this->getDataRow(), $file);
		if($this->getReplyTwitter()){
			$twitToSend = "Olá @". $name . ", vou tocar agora: " . $music;
			$this->sendTwit($twitToSend);
		}
		$return      = array();
		$return      = explode(";", $output);
		$name        = $return[0];
		$music       = trim($return[1]);
		$profile_img = $return[2];
		$img_in      = $return[3];
		$type        = $return[4];
		$return[0]   = $this->musicPosition($music);
		$return[1]   = $name;
		$return[2]   = $profile_img; 
		if(!empty($return[1]))
			return $return[0].'#'.$return[1].'#'.$return[2].'#'.$return[3].'#'.$return[4];
		else
			return "";
	}

	public function getMusics(){
		$str = "[";
		$db = $this->getDataBase();
		foreach($db as $data){
			$str .= "{\"file\" : \"". $this->getDirMusics().$data."\" , \"title\" : \"".str_replace(".mp3", "", $data)."\"},";
		}
		$str = rtrim($str, ",");
		$str .= "]";
		return $str;
	}

	public function listMusics(){
		$str  = "";
		$str .= "<table style=\"width:100%\">" . "\n";
		$str .= "<tr>" . "\n";
			$str .= "<th>Hashtag</th>" . "\n";
			$str .= "<th>Música</th>" . "\n";
			$str .= "<th>Twitter</th>" . "\n";
			$str .= "<th>Instagram</th>" . "\n";
		$str .= "</tr>" . "\n";
		$db = $this->getDataBase();
		foreach($db as $key =>$data){
			$str .= "<tr>" . "\n";
				$str .= "<td width=\"20%\">"."#play". ($key + 1000)."</td>" . "\n";
				$str .= "<td width=\"35%\">".str_replace(".mp3", "", $data)."</td>" . "\n";
				$str .= "<td width=\"20%\">"."<a class=\"click-list\" href=\"http://twitter.com/home?status=@tocaessa " . str_replace(".mp3", "", $data) . "\" target=\"_blank\">Nome</a>" . "<a class=\"click-list\" href=\"http://twitter.com/intent/tweet?text=@tocaessa" . "&hashtags=play". ($key + 1000) . "\" target=\"_blank\">Hashtag</a>"."</td>" . "\n";
				$str .= "<td width=\"20%\">"."<a class=\"click-list\" onClick=\"List.generateInstagram('".str_replace(".mp3", "", $data)."','');\">Nome</a>" . "<a class=\"click-list\" onClick=\"List.generateInstagram('','"."#play". ($key + 1000)."');\">Hashtag</a>" ."</td>" . "\n";
			$str .= "</tr>" . "\n";
		}
		$str .= "</table>";
		return $str;
	}
	
	public function __call ($metodo, $parametros){
		if (substr($metodo, 0, 3) == 'set') {
			$var = substr(strtolower(preg_replace('/([a-z])([A-Z])/', "$1_$2", $metodo)), 4);
			$this->$var = $parametros[0];
		}
		elseif (substr($metodo, 0, 3) == 'get') {
			$var = substr(strtolower(preg_replace('/([a-z])([A-Z])/', "$1_$2", $metodo)), 4);
			return $this->$var;
		}
	}

	public function __curl($url){
		$curl_handle=curl_init();
		curl_setopt($curl_handle, CURLOPT_URL, $url);
		curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, 10);
		curl_setopt($curl_handle, CURLOPT_TIMEOUT, 60);
		curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl_handle, CURLOPT_USERAGENT, 'TocaEssa');
		curl_setopt($curl_handle, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl_handle, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($curl_handle, CURLOPT_SSLVERSION,3);
		curl_setopt($curl_handle, CURLOPT_HTTPHEADER, array("User-Agent: Mozilla/5.0 (Windows NT 6.3; WOW64; rv:31.0) Gecko/20100101 Firefox/31.0"));
		$query = curl_exec($curl_handle);
		$http_status = curl_getinfo($curl_handle, CURLINFO_HTTP_CODE);
		curl_close($curl_handle);
		$i = 0;
		while($http_status != 200){
			$i++;
			$query = curl_exec($curl_handle);
			$http_status = curl_getinfo($curl_handle, CURLINFO_HTTP_CODE);
			if($i == 10){
				$this->appendError("CURL_ERROR: Error after 10 connection attempts");
				$http_status == 200;
				$query = false;
			}
			curl_close($curl_handle);
			sleep(10);
		}
		return $query;
	}

	public function __convert_date($date, $type){
		$return_date = "";
		switch($type){
			case 'IN' : 
				$return_date = Date($this->getPaternDate(), $date);
			break;
			case 'TW' : 
				$return_date = Date($this->getPaternDate(), strtotime($date));
			break;
		}
		return $return_date;
	}

	public function __get_hashtags($string, $str = 1) {
		$keyword  = "";
		$keywords = "";
		preg_match_all('/#(\w+)/',$string,$matches);
		$i = 0;
		if($str){
			foreach ($matches[1] as $match) {
				$count = count($matches[1]);
				$keywords .= "$match";
				$i++;
				if ($count > $i) $keywords .= ", ";
			}
		} 
		else{
			foreach ($matches[1] as $match) {
			$keyword[] = $match;
			}
			$keywords = $keyword;
		}
		return $keywords;
	}
}
?>