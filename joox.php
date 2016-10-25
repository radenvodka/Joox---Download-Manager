<?php
error_reporting(0);
/*
Name         : Joox - Download Manager
Description  : prioritizing file mp4a
Author       : Eka Syahwan
-----------------------
// I am not responsible if anything happens to be what you do, at your own risk - happy coding.
-----------------------
*/
class Joox
{
	function ngecurl($url , $post=null , $header=null){
		mkdir("Download");
        $ch = curl_init($url);
        if($post != null) {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (iPhone; U; CPU iPhone OS 8_3_3 like Mac OS X; en-SG) AppleWebKit/537.25 (KHTML, like Gecko) Version/7.0 Mobile/8C3 Safari/6533.18.1");
        curl_setopt($ch, CURLOPT_COOKIEJAR, getcwd()."cookies.txt");
        curl_setopt($ch, CURLOPT_COOKIEFILE, getcwd()."cookies.txt");
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
        if($header != null) {
            curl_setopt($ch, CURLOPT_HTTPHEADER,$header);
        }
        curl_setopt($ch, CURLOPT_COOKIESESSION, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT ,0); 
        curl_setopt($ch, CURLOPT_TIMEOUT, 20);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        return curl_exec($ch);
        curl_close($ch);
	}	
	function bersih($data){return preg_replace('/\s+/', '', $data);}
	function decode($data){
		foreach ($data as $is) {
			$data[] = base64_decode($is);
		}
		return $data;
	}
	function parsing($data){
		$regex = array(
			'judul' 	=> 	'/"info1":"(.*?)"/',
			'penyanyi' 	=> 	'/"name":"(.*?)"/',
			'album' 	=> 	'/"info3":"(.*?)"/',
			'songid' 	=>	'/"songid":"(.*?)"/',
		);
		foreach ($regex as $label => $regexnya) {
			preg_match_all($regexnya, $data, $matches);
			if($label != "songid"){
				foreach ($matches[1] as $key => $datanya) {
					$result_[] = base64_decode($datanya);
				}
				$result[] = array($label => $result_);
				unset($result_);
			}else{
				$result[] = array($label => $matches[1]);
				unset($result_);
			}
		}
		return $result;
	}
	function readline($pesan){
        echo "[Download Mp4a/mp3] ".$pesan;
        $answer =  rtrim( fgets( STDIN ));
        return $answer;
    }
	function secondsToTime($seconds) {
	  $hours = floor($seconds / (60 * 60));
	  $divisor_for_minutes = $seconds % (60 * 60);
	  $minutes = floor($divisor_for_minutes / 60);
	  $divisor_for_seconds = $divisor_for_minutes % 60;
	  $seconds = ceil($divisor_for_seconds);
	  $obj = array(
	      "h" => (int) $hours,
	      "m" => (int) $minutes,
	      "s" => (int) $seconds,
	   );
	  return $obj;
	}
	function times(){
		$time = microtime();
		$time = explode(' ', $time);
		return $time[1] + $time[0];
	}

	function curl_get_file_size( $url ) {
	    $result = -1;
	    $curl = curl_init( $url );
	    curl_setopt($curl, CURLOPT_USERAGENT, "Mozilla/5.0 (iPhone; U; CPU iPhone OS 8_3_3 like Mac OS X; en-SG) AppleWebKit/537.25 (KHTML, like Gecko) Version/7.0 Mobile/8C3 Safari/6533.18.1");
	    curl_setopt( $curl, CURLOPT_NOBODY, true );
	    curl_setopt( $curl, CURLOPT_HEADER, true );
		curl_setopt($ch, CURLOPT_COOKIEJAR, getcwd()."cookies.txt");
       	curl_setopt($ch, CURLOPT_COOKIEFILE, getcwd()."cookies.txt");
		curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $curl, CURLOPT_FOLLOWLOCATION, true );
		$data = curl_exec( $curl );
		curl_close( $curl );
		if( $data ) {
		$content_length = "unknown";
		$status = "unknown";
		if( preg_match( "/^HTTP\/1\.[01] (\d\d\d)/", $data, $matches ) ) {
		  $status = (int)$matches[1];
		}
		if( preg_match( "/Content-Length: (\d+)/", $data, $matches ) ) {
		  $content_length = (int)$matches[1];
		}
		if( $status == 200 || ($status > 300 && $status <= 308) ) {
		  $result = $content_length;
		}
		}
		return $result;
	}

    function download($judul,$url){
    	if(file_exists("Download/".$judul)){
    		echo "[Download Mp4a/mp3] Download Size : sudah ada di list\r\n";
    	}else{
    		$sizene = $this->formatSizeUnits( $this->curl_get_file_size( $url ) );
			$start  = $this->times();
	    	echo "[Download Mp4a/mp3] Download Size : ". $sizene;
	    	$fp = fopen ("Download/".$judul, 'w+');
			$ch = curl_init();
			curl_setopt( $ch, CURLOPT_URL, $url );
			curl_setopt($ch, CURLOPT_COOKIEJAR, getcwd()."cookies.txt");
       		curl_setopt($ch, CURLOPT_COOKIEFILE, getcwd()."cookies.txt");
			curl_setopt( $ch, CURLOPT_RETURNTRANSFER, false );
			curl_setopt( $ch, CURLOPT_BINARYTRANSFER, true );
			curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (iPhone; U; CPU iPhone OS 8_3_3 like Mac OS X; en-SG) AppleWebKit/537.25 (KHTML, like Gecko) Version/7.0 Mobile/8C3 Safari/6533.18.1");
			curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );
	  		curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT, 10 );
	  		curl_setopt( $ch, CURLOPT_FILE, $fp );
	  		curl_exec( $ch );
			curl_close( $ch );
			fclose( $fp );
			$end = $this->times();
			$time = $this->secondsToTime(round(($end - $start), 1));
			/* // sistem redownload
			if( filesize("Download/".$judul) < $sizene){
				echo "\r\n[Download Mp4a/mp3] Redownload : ".$judul."\r\n";
				unlink("Download/".$judul);
				$this->download($judul,$url);
			}else{
				if(filesize("Download/".$judul) > 0){
					echo " | (".$this->formatSizeUnits( filesize("Download/".$judul) ).") Selesai pada ".$time[m]." menit ".$time[s]." detik\r\n";
				}else{
					echo " | (".$this->formatSizeUnits( filesize("Download/".$judul) ).") (Gagal)\r\n";
				}
			}*/
			/*// sistem redownload
			if( $this->labelSize(filesize("Download/".$judul)) === "MB"){
				if(filesize("Download/".$judul) > 0){
					echo " | (".$this->formatSizeUnits( filesize("Download/".$judul) ).") Selesai pada ".$time[m]." menit ".$time[s]." detik\r\n";
				}else{
					echo " | (".$this->formatSizeUnits( filesize("Download/".$judul) ).") (Gagal)\r\n";
				}
			}else{
				echo "\r\n[Download Mp4a/mp3] Redownload : ".$judul." (Corrupt file)\r\n";
				unlink("Download/".$judul);
				$this->download($judul,$url);
			}*/
			if(filesize("Download/".$judul) > 0){
				echo " | (".$this->formatSizeUnits( filesize("Download/".$judul) ).") Selesai pada ".$time[m]." menit ".$time[s]." detik\r\n";
			}else{
				echo " | (".$this->formatSizeUnits( filesize("Download/".$judul) ).") (Gagal)\r\n";
			}
    	}
    }
    function getHeaders($url){
	  $ch = curl_init($url);
	  curl_setopt( $ch, CURLOPT_NOBODY, true );
	  curl_setopt( $ch, CURLOPT_RETURNTRANSFER, false );
	  curl_setopt( $ch, CURLOPT_HEADER, false );
	  curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, true );
	  curl_setopt( $ch, CURLOPT_MAXREDIRS, 3 );
	  curl_exec( $ch );
	  $headers = curl_getinfo( $ch );
	  curl_close( $ch );
	  return $headers;
	}
	function formatSizeUnits($bytes)
    {
        if ($bytes >= 1073741824)
        {
            $bytes = number_format($bytes / 1073741824, 2) . ' GB';
        }
        elseif ($bytes >= 1048576)
        {
            $bytes = number_format($bytes / 1048576, 2) . ' MB';
        }
        elseif ($bytes >= 1024)
        {
            $bytes = number_format($bytes / 1024, 2) . ' kB';
        }
        elseif ($bytes > 1)
        {
            $bytes = $bytes . ' bytes';
        }
        elseif ($bytes == 1)
        {
            $bytes = $bytes . ' byte';
        }
        else
        {
            $bytes = '0 bytes';
        }
        return $bytes;
	}
	function labelSize($bytes)
    {
        if ($bytes >= 1073741824)
        {
            $bytes = 'GB';
        }
        elseif ($bytes >= 1048576)
        {
            $bytes = 'MB';
        }
        elseif ($bytes >= 1024)
        {
            $bytes ='kB';
        }
        elseif ($bytes > 1)
        {
            $bytes = 'bytes';
        }
        elseif ($bytes == 1)
        {
            $bytes = 'byte';
        }
        else
        {
            $bytes = 'bytes';
        }
        return $bytes;
	}
	function getList($katakunci,$page,$sin){
		$pilURL = "http://api.joox.com/web-fcgi-bin//web_search?callback=jQuery110003061714756163918_1477336272879&lang=id&country=id&type=1&user_type=1&search_input=".urlencode($katakunci)."&sin=".$sin."pn=".$page;
		return $this->bersih( $this->ngecurl($pilURL) );
	}
	function unique_multidim_array($array, $key) { 
	    $temp_array = array(); 
	    $i = 0; 
	    $key_array = array(); 
	    
	    foreach($array as $val) { 
	        if (!in_array($val[$key], $key_array)) { 
	            $key_array[$i] = $val[$key]; 
	            $temp_array[$i] = $val; 
	        } 
	        $i++; 
	    } 
	    return $temp_array; 
	} 
	function pencarian($katakunci)
	{
	
	$data = $this->getList($katakunci,0);
	preg_match_all('/"sum":(.*?),/', $data, $sunMatch);
	preg_match_all('/"ein":(.*?),/', $data, $einMatch);
	if($sunMatch[1][0] <= 30){
		$is = 1;
	}else{
		$is = 30;
	}
	if(ceil($sunMatch[1][0]/30) == 1){
		$il = 2;
	}else{
		$il = ceil($sunMatch[1][0]/30);
	}
	for ($page=1; $page < $il; $page++) { 
		for ($sin=0; $sin <$sunMatch[1][0]; $sin+=$is) { 
			$data = $this->getList($katakunci,$page,$sin);
			preg_match_all('/"info1":"(.*?)"/', $data , $match1);
			preg_match_all('/"name":"(.*?)"/', $data , $match2);
			preg_match_all('/"info3":"(.*?)"/', $data , $match3);
			preg_match_all('/"songid":"(.*?)"/', $data , $match4);
			foreach ($match1[1] as $key => $judul) {
				$arrayName[] = array(
					'songid' 	=> $match4[1][$key],
					'judul' 	=> base64_decode($judul),
					'artis' 	=> base64_decode($match2[1][$key]),
					'album' 	=> base64_decode($match3[1][$key])
				);
			}
		}
	}
	$listLagu = $this->unique_multidim_array($arrayName,'songid');
	echo ".-[ID]-|------[ PENYANYI ]--------.--------------[ JUDUL LAGU ]--------------.\r\n";
	foreach ($listLagu as $keyID => $string) {
		$all[] = $keyID;
		if(strlen($keyID) == 1){
			echo "|  ".$keyID."   | ".$string[artis];
			for ($i=strlen($string[artis]); $i <25; $i++) { 
				echo " ";
			}
			echo "| ".substr($string[judul], 0, 40);
			for ($i=strlen($string[judul]); $i <40; $i++) { 
				echo " ";
			}
			echo " |	\r\n";
		}else{
			echo "|  ".$keyID."  | ".$string[artis];
			for ($i=strlen($string[artis]); $i <25; $i++) { 
				echo " ";
			}
			echo "| ".substr($string[judul], 0, 40);
			for ($i=strlen($string[judul]); $i <40; $i++) { 
				echo " ";
			}
			echo " |	\r\n";
		}
	}
	echo "\----------------------------------------------------------------------------/\r\n";
	$IDnya = $this->readline("Pilih ID Lagu (0 - ".(count($listLagu)-1).")  : ");
	if( $IDnya  === ""){
		$this->start();
	}
	if($IDnya === "all"){
		$IDnya = $all; 
	}else{
		$IDnya = explode(",", $IDnya);
	}
	if(!isset($IDnya)){
		$this->start();
	}
	foreach ($IDnya as $keys => $idnum) {
		echo "\r\n[Download Mp4a/mp3] Judul Lagu : ".$listLagu[$idnum][artis]." - ".$listLagu[$idnum][judul]." (".($keys+1)." / ".count($IDnya).")\r\n";
		$data = $this->bersih($this->ngecurl("http://api.joox.com/web-fcgi-bin/web_get_songinfo?songid=".$listLagu[$idnum][songid]."&lang=id&country=id&from_type=-1&channel_id=-1"));
		$mp4a = '/"m4aUrl":"(.*?)"/';
		$mp3  = '/"mp3Url":"(.*?)"/';
		preg_match_all($mp4a, $data, $matchesmp4a);
		preg_match_all($mp3, $data, $matchesmp3);
		if($matchesmp4a[1][0] != null){
			$this->download( $listLagu[$idnum][artis]." - ".$listLagu[$idnum][judul].".mp4a" ,$matchesmp4a[1][0]);
		}else if($matchesmp3[1][0] != null){
			$this->download( $listLagu[$idnum][artis]." - ".$listLagu[$idnum][judul].".mp3" ,$matchesmp3[1][0]);
		}else{
			echo "\r\n[Download Mp4a/mp3] Link ".$listLagu[$idnum][artis]." - ".$listLagu[$idnum][judul]." Tidak ada\r\n";
		}
	}
	
	}
	function start(){
		$this->pencarian($this->readline("Masukan kata pencarian : "));
	}
}
$Joox = new Joox;
$Joox->start();
?>
