<?php

error_reporting(E_ALL & ~E_NOTICE);

include('files/functions.php');
	
?>

<!DOCTYPE html>
<html lang="de">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<meta name="viewport" content="width=device-width; initial-scale=1.0">

	<title>Split</title>
	
	<link type="text/css" rel="stylesheet" href="files/main.css" />
	<link rel="stylesheet" href="files/css/font-awesome.min.css">
	<link rel="stylesheet" href="files/tipso.min.css">
	<link rel="stylesheet" href="files/animate.css">
	
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
	<script src="files/tipso.min.js"></script>
	<script src="files/actions.js"></script>
	
	<!-- Replace favicon.ico & apple-touch-icon.png in the root of your domain and delete these references -->
	<link rel="shortcut icon" href="icons/favicon.ico">
	<link rel="apple-touch-icon" href="icons/apple-touch-icon.png">
	<meta name="theme-color" content="#ff7e00">

</head>

<body>

	<div class="wrapper">
		<header>
			<div class="left">
				<a href="<?= $basurl ?>"><h1><img src="icons/s.png" style="margin: -12px 2px -12px 5px; height: 50px;" />plit</h1></a>
			</div>
			<div class="right">
				<span class="showuser">
					<i class="fa fa-users" data-tipso="Benutzer verwalten" aria-hidden="true"></i>
				</span>
				<form method="get" id="form1">
					<span class="newformaction"><i class="fa fa-user-plus" data-tipso="Benutzer anlegen" aria-hidden="true"></i></span>
					<input type="text" name="newuser" class="newuser" placeholder="Neuer Benutzer" />
					<input type="hidden" name="userform" value="AddUser" />
					<!--<input type="submit" name="userform" />-->
				</form>
				<form method="get" id="form2">
					<span class="formaction"><i class="fa fa-user-times" data-tipso="Benutzer löschen" aria-hidden="true"></i></span>
					<select class="deluser" name="deluser">
					 <option disabled selected value> -- Löschen -- </option>
					<?php foreach ($userdata as $id => $name) { ?>
						<option value="<?= $id ?>"><?= $name ?></option>
					<?php } ?>
					</select>
					<input type="hidden" name="userform" value="Delete" />
					<!--<input type="submit" name="userform" value="Delete" />-->
				</form>
			</div>
		</header>

		<div class="content">
			<!--<pre>
				
<?php
print_r($returned);
?>

			</pre>-->
			
			
			<div class="currency">
				<div class="block">
				<?php foreach ($userdata as $id => $name) {
					if(isset($exp[$id][0])){	$myexp = $exp[$id][0]; }
					else 	{					$myexp = 0; }
					
					if(isset($exp[$id][1])){	$myrev = $exp[$id][1]; }
					else 	{					$myrev = 0; }
					
					$total = $myexp - $myrev;
					if ($total < 0) { $class = "red";}
					elseif ($total > 0) { $class = "green";}
					else{ $class = " ";}
					
				?>
					<div class="user-list">
						<div class="name"><?= $name ?></div>
						<div class="exp"><?= $myexp ?></div>
						<div class="ref"><?= $myrev ?></div>
						<div class="total <?= $class ?>"><?= $total ?></div>
					</div>
				<?php } ?>
				</div>
			</div>
			
			<div class="transactions">
			
			<form method="get" id="form3">

				<div class="list">
			<?php 
				foreach ($cdata as $id => $data) {
				
				// check if User exists
				if(isset($userdata[$data["from-id"]])){
					$from = $userdata[$data["from-id"]];
				}
				else{
					$from = "<i>gelöscht (id:".$data["from-id"].")</i>";
				}
				if(isset($userdata[$data["to-id"]])){
					$to = $userdata[$data["to-id"]];
				}
				else{
					$from = "<i>gelöscht (id:".$data["to-id"].")</i>";
				}
				
				// correct text
				if(isset($data["text"])){
					$text = str_replace(';;', ',', $data["text"]);
					$textnice = str_replace(';;', ',', $data["text"]);
					$textnice = str_replace("'", '"', $data["text"]);
				}
				else{
					$text = " ";
					$textnice = " ";
				}


				// evaluate price
				$prices = explode(".",$data["price"]);
				if(count($prices) < 2){
					$price = '<span class="p1">'.$data["price"].'</span><span class="p2">00</span>';
				}
				else{
					$price1 = "";
					if(strlen($prices[1]) < 2){
						$price1 = "0";
					}
					$price = '<span class="p1">'.$prices[0].'</span><span class="p2">'.$prices[1].$price1.'</span>';
				}
			
			?>
			
				<div class="entry" data-id="<?= $id; ?>">
					<div class="c-from"><?= $from; ?></div>
					<div class="c-to"><?= $to; ?></div>
					<div class="price"><?= $price; ?></div>
					<div class="text"><?= $text; ?></div>
					<div class="action">
						<span data-tipso="Bearbeiten" data-id="<?= $id; ?>" data-from="<?= $data["from-id"]; ?>" data-to="<?= $data["to-id"]; ?>" data-price='<?= $data["price"]; ?>' data-text='<?= $textnice; ?>' class="edit"><i class="fa fa-pencil" aria-hidden="true"></i></span>
						<span data-tipso="Löschen" data-id="<?= $id; ?>" class="delete"><i class="fa fa-trash" aria-hidden="true"></i></span>
					</div>
				</div>
			
			<?php } ?>
			
			
			
			
			<!-- New Entry -->
					<div class="entry newentry">
						<div class="c-from">
							<select name="fromuser">
   								 <option disabled selected value> -- Von -- </option>
								<?php foreach ($userdata as $id => $name) { ?>
									<option value="<?= $id ?>"><?= $name ?></option>
								<?php } ?>
								</select>
						</div>
						<div class="c-to">
							<select name="touser">
   								 <option disabled selected value> -- Für -- </option>
							<?php foreach ($userdata as $id => $name) { ?>
								<option value="<?= $id ?>"><?= $name ?></option>
							<?php } ?>
							</select>
						</div>
						<div class="price">
							<input name="price" type="number" step="0.01" placeholder="0.00" autocomplete="off" />
						</div>
						<div class="text">
							<input type="text" name="text" />
						</div>
						<div class="action">
							<input type="submit" name="entryform" value="+" />
						</div>
					</div>
					
				</div>
				
				</form>
				
				
			</div>
			
			<div class="transactions">
				<div class="button showbalance"><i class="fa fa-calculator" aria-hidden="true"></i> Ausgleich</div>
			</div>
			
			
		</div>

	</div>
	
	<div class="totop"><i class="fa fa-arrow-circle-o-up" aria-hidden="true"></i></div>








<!-- 
----
----
----	Overlay Panel
----
----
-->
	<div class="overlay">
		<div class="panel">
			<div class="header">
				<h3>Title</h3>
				<div class="close-panel"><i class="fa fa-times" aria-hidden="true"></i></div>
			</div>
			<div class="panel-content">
				<div class="balance con">
					<div class="list">
						<?php 
							foreach ($balanceinfo as $id => $data) {
							
							// check if User exists
							if(isset($userdata[$data[0]])){
								$from = $userdata[$data[0]];
							}
							else{
								$from = "<i>gelöscht (id:".$data[0].")</i>";
							}
							if(isset($userdata[$data[1]])){
								$to = $userdata[$data[1]];
							}
							else{
								$from = "<i>gelöscht (id:".$data[1].")</i>";
							}
							
			
							// evaluate price
							$prices = explode(".",$data[2]);
							if(count($prices) < 2){
								$price = '<span class="p1">'.$data[2].'</span><span class="p2">00</span>';
							}
							else{
								$price1 = "";
								if(strlen($prices[1]) < 2){
									$price1 = "0";
								}
								$price = '<span class="p1">'.$prices[0].'</span><span class="p2">'.$prices[1].$price1.'</span>';
							}
						
						?>
						<div class="entry" data-id="<?= $id; ?>">
							<div class="c-from"><?= $from; ?></div>
							<div class="c-to"><?= $to; ?></div>
							<div class="price"><?= $price; ?></div>
							<!--<div class="action">
								<span data-tipso="Buchen" data-id="<?= $id; ?>" class="balancait"><i class="fa fa-check-square-o" aria-hidden="true"></i>
</span>
							</div>-->
						</div>
						
						<?php } ?>
					</div>
					<br><br>
					<div class="button newlist"><i class="fa fa-list-ul" aria-hidden="true"></i>
						Neue Liste anlegen
					</div>

				</div>
				
				<div class="editentry con">
					<form method="get" id="form5">
						<input type="hidden" name="entryform" value="Update" />
						<input type="hidden" name="update" value />
						<div class="col2">
							<select name="u-fromuser">
								 <option disabled selected value> -- Von -- </option>
								<?php foreach ($userdata as $id => $name) { ?>
									<option value="<?= $id ?>"><?= $name ?></option>
								<?php } ?>
							</select>
							<span class="arrow">&nbsp;</span>
							<select name="u-touser">
	   							 <option disabled selected value> -- Für -- </option>
							<?php foreach ($userdata as $id => $name) { ?>
								<option value="<?= $id ?>"><?= $name ?></option>
							<?php } ?>
							</select>
						</div>
						<div class="price">
							<input name="u-price" type="number" step="0.01" placeholder="0.00" autocomplete="off" />
						</div>
						<div class="text">
							<input type="text" name="u-text" />
						</div>
						
						<div class="button saveentry"><i class="fa fa-check-square-o" aria-hidden="true"></i> Speichern</div>
					</form>
				</div>
			</div>
		</div>
	</div>


<form method="get" id="form4">
	<input type="hidden" name="delentry"  class="delentry" value="" />
	<input type="hidden" name="entryform" value="Delete" />
	<!--<input type="submit" name="userform" value="Delete" />-->
</form>

<form method="get" id="form6">
	<input type="hidden" name="newlist"  class="newlist" value="1" />
	<input type="hidden" name="newlistform" value="List" />
	<!--<input type="submit" name="userform" value="Delete" />-->
</form>
		
		
<?php
	
	if(isset($_GET['error'])){
		echo '<div class="alert error">'.$_GET['error'].'</div>';
	}
	if(isset($_GET['success'])){
		echo '<div class="alert success">'.$_GET['success'].'</div>';
	}
	
?>
		
</body>
</html>
