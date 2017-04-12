<?php
	// Variables
	$userdata;	// [id] => name 
	$allids;	// all ids
	$allnames;	// all names
	$maxid;		// largest user ID
	$cdata;		// [id] => id-from, id-to, prize, text 
	$exp = array();		// [id] => exp, rev
	$maxc;		// largest Currency ID
	$balance;	// positive & negative ranked to highest value
	$balanceinfo;	// list of expenses to get even
					
	$strrepl = array(',', ' ', ';', '.'); // invalid characters
	
	$usertxt = "users.txt";
	$currencytxt = "booking.txt";
	$backupdir = "badir";
	$maxbafiles = 30;
	$txtfiles = [$usertxt, $currencytxt]; // files in array zum loopen
	
	$basurl = explode("?", "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]");
	$basurl = $basurl[0];
	
	$returned;	// print variable
	
	
	
		
	// create User Data
	$userfile = file($usertxt);
	for ($i=0; $i < count($userfile) ; $i++) {
		$data = explode(",", $userfile[$i]);
		$userdata[$data[0]] = strtolower(preg_replace( "/\r|\n/", "", $data[1] ));
		$allids[] = preg_replace( "/\r|\n/", "", $data[0] );
		$allnames[] = strtolower(preg_replace( "/\r|\n/", "", $data[1] ));
	}
	$maxid = max($allids);
	
	
	// create Currency Data
	$cfile = file($currencytxt);
	for ($i=0; $i < count($cfile) ; $i++) {
		$data = explode(",", $cfile[$i]);
		$cdata[$data[0]] = array(
			"from-id"	=> preg_replace( "/\r|\n/", "", $data[1] ),
			"to-id"		=> preg_replace( "/\r|\n/", "", $data[2] ),
			"price"		=> preg_replace( "/\r|\n/", "", $data[3] ),
			"text"		=> preg_replace( "/\r|\n/", "", $data[4] ),
		);
		$maxc = $data[0];
	}
	
	
	// backup
	$bafiles = scandir($backupdir);
	$filecnt = count($bafiles)-1;
		
	$date = getdate();
	$year = $date['year']-2000;
	$month = $date['mon'];
	if($month < 10){
		$month = "0".$month;
	}
	$today = $year.$month.$date['mday'];
	$todayfiles = [$today."_users.txt", $today."_booking.txt"];
	
	$count = 0;
	foreach ($todayfiles as $file) {
		if(!in_array($file, $bafiles)){
			$newfile = fopen($backupdir."/".$file, "w") or die("Unable to open file!");
			$contents = file_get_contents($txtfiles[$count]);
			fwrite($newfile, $contents);
			fclose($newfile);
		}
		else{
			//$returned =$bafiles;
		}
		$count ++;
	}

	while($maxbafiles+1 < $filecnt){
		unlink ( $backupdir."/".$bafiles[2] );
		$bafiles = scandir($backupdir);
		$filecnt = count($bafiles)-1;
	}
	
	
	
	
	
	// calculate User Expenses
	foreach ($cdata as $data) {
		
		// add plus
		if(array_key_exists($data['from-id'], $exp)){
			$exp[$data['from-id']][0] = $exp[$data['from-id']][0] + $data['price'] ;
		}
		else{
			$exp[$data['from-id']][0] = $data['price'] ;
		}
		
		// add minus
		if(array_key_exists($data['to-id'], $exp)){
			$exp[$data['to-id']][1] = $exp[$data['to-id']][1] + $data['price'] ;
		}
		else{
			$exp[$data['to-id']][1] = $data['price'] ;
		}
	}
	
	// Calculate how to get even
		//generate balance array
	foreach ($exp as $id => $values) {
		$cur = $exp[$id][0]-$exp[$id][1];
		if($cur > 0){
			$balance["+"][$id] =  abs($cur);
		}
		if($cur < 0){
			$balance["-"][$id] =  abs($cur);
		}
	}
	if(isset($balance["+"]) && is_array($balance["+"])){
		arsort($balance["+"]);
	}
	if(isset($balance["-"]) && is_array($balance["-"])){
		arsort($balance["-"]);
	}
	
		//run calculate whom to pay
	foreach ($balance["+"] as $id => $values) {
		while ($balance["+"][$id] != 0) {
			$lowestkey = array_keys($balance["-"]);
			$lowest = $balance["-"][$lowestkey[0]];
			
			if($lowest <= $balance["+"][$id] && $lowest !== 0){
				$balanceinfo[] = array($lowestkey[0], $id, $lowest);
				$balance["+"][$id] = $balance["+"][$id] - $lowest;
				$balance["-"][$lowestkey[0]] = 0;
			}
			elseif($lowest !== 0){
				$balanceinfo[] = array($lowestkey[0], $id, $balance["+"][$id]);
				$balance["-"][$lowestkey[0]] = $balance["-"][$lowestkey[0]] - $balance["+"][$id];
				$balance["+"][$id] = 0;
			}
			else{
				$balanceinfo[] = array('none', $id, $balance["+"][$id]);
				$balance["+"][$id] = 0;
			}
			
			arsort($balance["+"]);
			arsort($balance["-"]);
		}
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	// create or Delete User
	if(isset($_GET['userform']) && isset($_GET['newuser'])){
		// Check if Username exist
		if(array_search(strtolower($_GET['newuser']), $allnames) === false){
			$writeme = $maxid+1;
			$writeme = "\n".$writeme.",".strtolower(str_replace($strrepl, "-", $_GET['newuser']));
			
			file_put_contents ( $usertxt , $writeme, FILE_APPEND | LOCK_EX);
			
			header('Location: '.$basurl.'?success=User+angelegt');
		}
		else{
			header('Location: '.$basurl.'?error=Username+bereits+vorhanden');
		}
	}
	elseif (isset($_GET['userform']) && isset($_GET['deluser'])) {
		$contents = file_get_contents($usertxt);
		$remove = $_GET['deluser'].",".$userdata[$_GET['deluser']];
		
		if(strpos($contents, $remove) !== false){
			if(strpos($contents, $remove) === 0){$remove = $remove."\n";}
			else{$remove = "\n".$remove;}
			$contents = str_replace($remove, '', $contents);
			file_put_contents($usertxt, $contents);
			header('Location: '.$basurl.'?success=User+gelöscht');
		}
		else{
			header('Location: '.$basurl.'?error=Eintrag+nicht+gefunden');
		}
		
		//$returned = $contents;
		
		
		
	}
	
	
	// New List
	if(isset($_GET['newlist']) && isset($_GET['newlistform'])){
		// Check if Username exist
		if(isset($balanceinfo) && count($balanceinfo) > 0){
			$text = "";
			foreach ($balanceinfo as $key => $value) {
				if($key != 0){ $text = $text."\n"; }
				$text = $text.$key.','.$value[1].','.$value[0].','.$value[2].",Ausgleich";
			}
			
			// create backup first
			$newfile = fopen($backupdir."/".$todayfiles[1], "w") or die("Unable to open file!");
			$contents = file_get_contents($currencytxt);
			fwrite($newfile, $contents);
			fclose($newfile);
			
			
			file_put_contents($currencytxt, $text);
			header('Location: '.$basurl.'?success=Neue+Liste+angelegt');
		}
		else{
			header('Location: '.$basurl.'?error=Liste+konnte+nicht+angelegt+werden');
		}
	}
	
	
	
	// create, delete & update Entry
	if(isset($_GET['fromuser']) && isset($_GET['touser']) && isset($_GET['price'])  && $_GET['price'] != "" && isset($_GET['entryform'])){
		// Check if User ID's are Valid
		if(array_key_exists($_GET['fromuser'], $userdata) && array_key_exists($_GET['touser'], $userdata)){
			
			$writeme = $maxc+1;
			$writeme = "\n".$writeme.",".$_GET['fromuser'].",".$_GET['touser'].",".$_GET['price'].",";
			
			if(isset($_GET['text'])){
				$writeme = $writeme.str_replace(",", ";;", $_GET['text']);
			}
			else{
				$writeme = $writeme." "; // Add space if no text
			}
			
			file_put_contents ( $currencytxt , $writeme, FILE_APPEND | LOCK_EX);
			
			//header('Location: '.$basurl.'?success=Eintrag+erstellt');
			header('Location: '.$basurl);
			
		}
		else{
			header('Location: '.$basurl.'?error=User+nicht+gefunden');
		}
	}
	elseif (isset($_GET['entryform']) && isset($_GET['delentry']) && $_GET['delentry'] != "") {
		$key = $_GET['delentry'];
		if(array_key_exists($key, $cdata)){
			$text =$key.",".$cdata[$key]["from-id"].",".$cdata[$key]["to-id"].",".$cdata[$key]["price"].",".$cdata[$key]["text"];
			
			
			$contents = file_get_contents($currencytxt);
			$remove = $text;
			
			if(strpos($contents, $remove) !== false){
				if(strpos($contents, $remove) === 0){$remove = $remove."\n";}
				else{$remove = "\n".$remove;}
				$contents = str_replace($remove, '', $contents);
				file_put_contents($currencytxt, $contents);
				
				//header('Location: '.$basurl.'?success=Eintrag+gelöscht');
				header('Location: '.$basurl);
			}
			else{
				header('Location: '.$basurl.'?error=Eintrag+nicht+gefunden');
			}
		}
		else{
			$text ="nope";
		}
		
		
		
	}
	elseif (isset($_GET['entryform']) && isset($_GET['update']) && $_GET['update'] != "" && isset($_GET['u-fromuser']) && isset($_GET['u-touser']) && isset($_GET['u-price'])  && $_GET['u-price'] != "") {
		$key = $_GET['update'];
		if(array_key_exists($key, $cdata)){
			$text =$key.",".$cdata[$key]["from-id"].",".$cdata[$key]["to-id"].",".$cdata[$key]["price"].",".$cdata[$key]["text"];
			$updatetext =$key.",".$_GET["u-fromuser"].",".$_GET['u-touser'].",".$_GET["u-price"].",";
			
			if(isset($_GET['u-text'])){
				$updatetext = $updatetext.str_replace(",", ";;", $_GET['u-text']);
			}
			else{
				$updatetext = $updatetext." "; // Add space if no text
			}
			
			//$returned = $text."\n".$updatetext;
			
			$contents = file_get_contents($currencytxt);
			$remove = $text;
			$update = $updatetext;
			
			if(strpos($contents, $remove) !== false){
				$contents = str_replace($remove, $update, $contents);
				file_put_contents($currencytxt, $contents);
				
				//header('Location: '.$basurl.'?success=Eintrag+gelöscht');
				header('Location: '.$basurl);
			}
			else{
				header('Location: '.$basurl.'?error=Eintrag+nicht+gefunden');
			}
		}
		else{
			$text ="nope";
		}
		
		
		
	}
	elseif (isset($_GET['entryform'])) {
		header('Location: '.$basurl.'?error=Eine+oder+mehrere+Felder+sind+leer');
	}

	
	
	
	
	
	
	
	
	?>
	