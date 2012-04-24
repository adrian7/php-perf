<?php 
/**
 * PHP-Perf is a small* single-file PHP script aimed to help you test the PHP performance on your server(s) 
 * @package PHP-Perf_1.0
 * @author Adrian Silimon (http://adrian.silimon.eu)
 * @version 1.0 
 */

session_start();

//---- script settings ---//
define('MSG_TYPE_ERROR', 'error');
define('MSG_TYPE_INFO', 'info');
define('MSG_TYPE_WARN', 'warn');

define('TABLE_PREFIX', 'tst_');

$Settings = array();

//csv file 
$csvhead= "TestID";
$csv 	= "";

//---- script settings ---//

//---- functions ----//

function assign_rand_value($num){
	
	// accepts 1 - 36
	
	switch($num){
	  	
	    case "1": $rand_value = "a"; break;
	    case "2":  $rand_value = "b"; break;
	    case "3": $rand_value = "c"; break; 
	    case "4":  $rand_value = "d";
	    break;
	    case "5":
	     $rand_value = "e";
	    break;
	    case "6":
	     $rand_value = "f";
	    break;
	    case "7":
	     $rand_value = "g";
	    break;
	    case "8":
	     $rand_value = "h";
	    break;
	    case "9":
	     $rand_value = "i";
	    break;
	    case "10":
	     $rand_value = "j";
	    break;
	    case "11":
	     $rand_value = "k";
	    break;
	    case "12":
	     $rand_value = "l";
	    break;
	    case "13":
	     $rand_value = "m";
	    break;
	    case "14":
	     $rand_value = "n";
	    break;
	    case "15":
	     $rand_value = "o";
	    break;
	    case "16":
	     $rand_value = "p";
	    break;
	    case "17":
	     $rand_value = "q";
	    break;
	    case "18":
	     $rand_value = "r";
	    break;
	    case "19":
	     $rand_value = "s";
	    break;
	    case "20":
	     $rand_value = "t";
	    break;
	    case "21":
	     $rand_value = "u";
	    break;
	    case "22":
	     $rand_value = "v";
	    break;
	    case "23":
	     $rand_value = "w";
	    break;
	    case "24":
	     $rand_value = "x";
	    break;
	    case "25":
	     $rand_value = "y";
	    break;
	    case "26":
	     $rand_value = "z";
	    break;
	    case "27":
	     $rand_value = "0";
	    break;
	    case "28":
	     $rand_value = "1";
	    break;
	    case "29":
	     $rand_value = "2";
	    break;
	    case "30":
	     $rand_value = "3";
	    break;
	    case "31":
	     $rand_value = "4";
	    break;
	    case "32":
	     $rand_value = "5";
	    break;
	    case "33":
	     $rand_value = "6";
	    break;
	    case "34":
	     $rand_value = "7";
	    break;
	    case "35":
	     $rand_value = "8";
	    break;
	    case "36":
	     $rand_value = "9";
	    break;
	  }
	  
	return $rand_value;
}

function get_rand_string($length=7){
	
 	if($length > 0) { 
  		$rand_id="";
		
   		for($i=1; $i<=$length; $i++){
   			
   			mt_srand((double)microtime() * 1000000);
			
   			$num = mt_rand(1,36);
			
   			$rand_id .= assign_rand_value($num);
   		}
  	}

  return $rand_id;
}

function get_csv_filename(){
	
	$uname = php_uname();
	$uname = @explode(' ', $uname);
					
	$csv_filename = ( $uname[1] . '-tests.csv');
	
	return $csv_filename;
}

function get_server_name(){
	
	$uname = php_uname();
	$uname = @explode(' ', $uname);
	
	if($uname[1]) 
		echo ($uname[1] . '@' . $_SERVER['SERVER_NAME']);
	else
		echo $_SERVER['SERVER_NAME'];
}

function get_server_meminfo($spec="MemTotal", $detailview=false){

	$fh = @fopen('/proc/meminfo', 'r');
	
	$mem = 0;
	
	if($fh) while ($line = @fgets($fh) ) {
		$pieces = array();
		
		if($detailview) echo ($line . '<br>');
		
		if (@preg_match('/^'.$spec.':\s+(\d+)\skB$/', $line, $pieces)) {
			$mem = $pieces[1]; break;
		}
	}
	
	if(empty($fh)) return "not available";
	
	@fclose($fh);
	
	return $mem;
}

function get_server_cpuinfo($detailview=false){

	$fh = @fopen('/proc/cpuinfo', 'r');
	
	$cpucores = 0;
	$cpumodel = "";
	$cpucache = "";
	
	if($fh) while ($line = @fgets($fh) ) {
	
		$pieces = array();
		
		if($detailview) echo ($line . '<br>');
		
		if ( strpos($line, 'processor') === false ); else $cpucores++;
		if ( strpos($line, 'model name') === false ); else if(empty($cpumodel)){ 
			$cpumodel = str_replace('model name', '', $line);
			$cpumodel = str_replace(':', '', $cpumodel);
			$cpumodel = str_replace('\t', ' ', $cpumodel);
			$cpumodel = str_replace('\n', ' ', $cpumodel);
		}
		
		if ( strpos($line, 'cpu cores') === false ); else if($cpucores < 2){
			$cpucores = str_replace('cpu cores', '', $line);
			$cpucores = str_replace(':', '', $cpucores);
			
			$cpucores = intval($cpucores);
		}
		
		if ( strpos($line, 'cache size') === false ); else if( empty($cpucache) ){
			$cpucache = str_replace('cache size', '', $line);
			$cpucache = str_replace(':', '', $cpucache);
			$cpucache = str_replace('\t', ' ', $cpucache);
		}
	}

	if(empty($fh)) return "not available";
	
	@fclose($fh);
	
	return ($cpumodel . ', '. $cpucores . ' cores, '. $cpucache . ' cache');
}

function get_server_hddinfo($detailview=false){
	$cmdout = array();
	//@exec('system', $cmdout); //TODO
	
	$hddmodel= "";
	$hddsize = disk_total_space();
}

function get_server_osinfo($detailview=false){
	//TODO
}

function display_msg($msg, $type=""){

	if(empty($type)) $type = MSG_TYPE_INFO;

	echo ('<p class="' . $type . '">' . $msg . '</p>');
}

function test_setup(){

	global $Settings, $csv, $csvhead;

	if( count($_POST) ) $Settings = $_POST;
	else 				$Settings = $_GET;
	
	if(empty($_SESSION['test_id'])) $_SESSION['test_id'] = 1;
	else 							$_SESSION['test_id']++;
	
	if(empty($Settings['test_id'])) $Settings['test_id'] = intval($_SESSION['test_id']);
	
	$csv.="Test {$Settings['test_id']}";
}

function test_mysql(){
	
	global $Settings, $csv, $csvhead;
	
	$starttime = microtime(true);
	
	$conn = @mysql_connect($Settings['mysql_host'], $Settings['mysql_user'], $Settings['mysql_pass']);
	$db	  = @mysql_select_db($Settings['mysql_dbname'], $conn);
	$rows = intval($Settings['mysql_rows']);
	
	if($conn and $db){
	
		$table 		= TABLE_PREFIX.'table';
		$tabledata	= array();
		$searchval  = array();
		$summary	= "";
	
		//--- create a table ---//
		$sql = "CREATE TABLE `{$table}`(
			 id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
			 data VARCHAR(255),
			 cur_timestamp TIMESTAMP
		);";
		
		if(@mysql_query($sql, $conn)) $summary.="&bull; Created table $table.<br>";
		//--- create a table ---//
		
		//--- insert a few rows ---//
		for($i=0; $i<$rows; $i++){
			
			$string = get_rand_string(25);
			
			$sql = "INSERT INTO `{$table}`(`data`) VALUES('{$string}')";
			$inserted = @mysql_query($sql, $conn);
			
			if(count($searchval) < 5) $searchval[] = mysql_real_escape_string($string);
			
			//echo "$i rows inserted<br>";//TODO debug
		}
		
		if($inserted) 
			$summary.="&bull; $rows rows inserted in $table.<br>";
		else{ //insert failed
			display_msg("MySQL INSERT operation failed at row $i. The user you provided might not have this permission or the database server went away.", MSG_TYPE_WARN); return;
		}
		
		//--- insert a few rows ---//
		
		//--- select a few rows ---//
		$row_id = rand(1, $rows);
		$sql = "SELECT * FROM `{$table}` WHERE `id`=" . $row_id;
		$res = @mysql_query($sql, $conn);
		
		$summary.="&bull; Selected row $row_id.<br>";
		
		while($line = mysql_fetch_array($res)){
			$tabledata[] = $line;
		}
		
		$sql = "SELECT * FROM `{$table}` WHERE `data` IN('" . $searchval[1] . "', '". $searchval[2] . "', '". $searchval[3] . "')";
		$res = @mysql_query($sql, $conn);
		
		$summary.="&bull; Select rows with data in: '" . $searchval[1] . "', '". $searchval[2] . "', '". $searchval[3] . "'.<br>";
		
		while($line = mysql_fetch_array($res)){
			$tabledata[] = $line;
		}
		
		
		$summary.="Selected data:<br>";
		$summary.= "<textarea cols='50' rows='10' style='width:100%;'>" . print_r($tabledata, TRUE) . "</textarea><br>";
		
		//--- select a few rows ---//
		
		//--- update some rows ---//
		$row_id = rand(1, $rows);
		$sql = "UPDATE `{$table}` SET `data`='test1 test1 test1' WHERE `id`=" . $row_id;
		@mysql_query($sql, $conn);
		
		$row_id = rand(1, $rows);
		$sql = "UPDATE `{$table}` SET `data`='test2 test2 test2' WHERE `id`=" . $row_id;
		@mysql_query($sql, $conn);
		
		$row_id = rand(1, $rows);
		$sql = "UPDATE `{$table}` SET `data`='test3 test3 test3' WHERE `id`=" . $row_id;
		@mysql_query($sql, $conn);
		
		$summary.= "&bull; 3 random rows updated. <br>";
		//--- update some rows ---//
		
		
		//--- delete a row ---//
		$sql = "DELETE FROM `{$table}` WHERE `id`=" . rand(1, $rows) ;
		@mysql_query($sql, $conn);
		
		$summary.= "&bull; 1 random row deleted. <br>";
		//--- delete a row ---//
		
		//--- delete the table ---//
		$sql = "DROP TABLE `{$table}`";
		@mysql_query($sql, $conn);
		
		$summary.= "&bull; $table table dropped. <br>";
		//--- delete the table ---//
		
		$endtime = microtime(true);
		
		$runtime = ($endtime - $starttime);
		$runtime = number_format($runtime, 4);
		
		//--- finish
		if( isset($Settings['export-csv']) ){
			$csvhead.= ",PHP-MySQL";
			$csv	.=",{$runtime}";
		}
		
		display_msg("Operations:<br> " . $summary . "<br> done in " . $runtime . ' seconds.' );
		//--- finish
		
	}
	else { //oups could not connect!
		display_msg("Could not connect to mysql database {$Settings['mysql_dbname']} on {$Settings['mysql_host']}!", MSG_TYPE_ERROR);
	}
	
}

function test_file(){
		
	global $Settings, $csv, $csvhead;
	
	$starttime = microtime(true);	
		
	$onebyteword = "01010101";
	$file_bytes	 = intval($Settings['file_size']) * 1024;
	
	for($i=0; $i<$file_bytes; $i++){
		
		$fh	= @fopen('file.txt', 'a');
		
		if(!$fh){ display_msg("File file.txt could not be opened. Please check script permissions, or try create yourself the file.txt.", MSG_TYPE_ERROR); return; }
		
		@fwrite($fh, $onebyteword . "\n");
		
		@fclose($fh);
	}
	
	$fh	= @fopen('file.txt', 'r');
	$fr = fread($fh, 808);
	
	$endtime = microtime(true);
	
	$runtime = ($endtime - $starttime);
	$runtime = number_format($runtime, 4);
		
	//--- finish
	if( isset($Settings['export-csv']) ){
		$csvhead.= ",File write/read";
		$csv	.=",{$runtime}";
	}
	
	display_msg($file_bytes . ' characters written in <a href="file.txt" target="_blank">file.txt</a> in '. $runtime . ' seconds.' );
	//--- finish
	
}

function test_gd(){
	
	global $Settings, $csv, $csvhead;
	
	$starttime = microtime(true);
	
	$width = intval($Settings['image_w']);
	$height= intval($Settings['image_h']);
	
	if(!function_exists('imagecreate')) {
		display_msg("GD extension does not seems to be enabled.", MSG_TYPE_WARN); return;
	}
	
	$image = @imagecreate($width, $height);
	
	$background_color = @imagecolorallocate($image, 0, 0, 0);
	$text_color 	  = @imagecolorallocate($image, 0, 237, 28);
	
	@imagestring($image, 4, floor($width/2), floor($height/2),  "Test Image", $text_color);
	@imagepng($image, 'image.png');
	@imagedestroy($image);
	
	$endtime = microtime(true);
	
	$runtime = ($endtime - $starttime);
	$runtime = number_format($runtime, 4);
	
	if(!file_exists('image.png')) {
		display_msg("GD Image test failed, could not save the image file, please check script permissions.", MSG_TYPE_ERROR);
		return;
	}
		
	//--- finish
	if( isset($Settings['export-csv']) ){
		$csvhead.= ",GD Image";
		$csv	.=",{$runtime}";
	}
	
	display_msg( $width . 'x'. $height .' <a href="image.png" target="_blank">image.png</a> image generated in ' . $runtime . ' seconds.' );
	//--- finish
	
}


function test_email(){
	
	global $Settings, $csv, $csvhead;
	
	$starttime = microtime(true);
	
	$subject = ( "Test email from " . get_server_name() );
	$message = "This is a test email, you may ignore and delete it.\n However if you received it, that means your mail server at " . get_server_name() . " is working!";
	
	$headers = 'From: test@example.com' . "\r\n" .
    		   'Reply-To: '. $Settings['mail_to'] . "\r\n" .
    		   'X-Mailer: PHP/' . phpversion();
	
	$mail_sent = @mail($Settings['mail_to'], $subject, $message, $headers);
	
	if(!$mail_sent){
		display_msg("Email could not be sent to {$Settings['mail_to']}. Please check your mail server settings, and if PHP mail function may work properly.", MSG_TYPE_ERROR);
		return;
	}
	
	$endtime = microtime(true);
	
	$runtime = ($endtime - $starttime);
	$runtime = number_format($runtime, 4);
	
	
	//--- finish
	if( isset($Settings['export-csv']) ){
		$csvhead.= ",Email Test";
		$csv	.=",{$runtime}";
	}
	
	display_msg('One email sent to ' . $Settings['mail_to'] . ' in ' . $runtime . ' seconds.' );
	//--- finish
	
}

function test_rand(){
	
	global $Settings, $csv, $csvhead;
	
	$starttime = microtime(true);
	
	$rsize = intval($Settings['rand_size']);
	$rands = array();
	
	for($i=0; $i<$rsize; $i++){
		$rands[] = rand(100000, 70000000);
	}
	
	$endtime = microtime(true);
	
	$runtime = ($endtime - $starttime);
	$runtime = number_format($runtime, 4);
		
	//--- finish
	if( isset($Settings['export-csv']) ){
		$csvhead.= ",Random Generation";
		$csv	.=",{$runtime}";
	}
		
	display_msg($rsize . " random numbers generated in " . $runtime . ' seconds.' );
	//--- finish
	
}

function test_while(){
			
	global $Settings, $csv, $csvhead;
	
	$starttime = microtime(true);
	
	$loops_size = intval($Settings['loop_size']);
	$loops_step = intval($Settings['loop_steps']);
	
	$str = "";
	$k   = 0;
	
	for($i=0; $i<$loops_size; $i++){
		
		$k=0;
		
		 while ($k < $loops_step) {
		 	
			$x = rand(100, 1999);
			
			$k++;
			
		 }
		
	}
	
	$endtime = microtime(true);
	
	$runtime = ($endtime - $starttime);
	$runtime = number_format($runtime, 4);
		
	//--- finish
	if( isset($Settings['export-csv']) ){
		$csvhead.= ",While Loops";
		$csv	.=",{$runtime}";
	}
		
	display_msg(($loops_size * $loops_step) . " loops executed in: " . $runtime . ' seconds.' );
	//--- finish
	
}
//---- functions ----//

?>
<html>
<head>
<title>PHP Perf Suite</title>
<style>
body{
	margin:0px;
	padding:0px;
}

.Main, .powerby{
	width:960px;
	height:auto;
	margin:10px auto;
	border:1px solid #CCC;
	border-radius:4px;
	font: normal 120%/180% tahoma, arial, verdana, san-serif;
}

.Main .inner{
	margin:10px;
	width:97.5%;
	height:auto;
	min-height:300px;
}

br.isclear{
	clear:both;
}

h2{
	border-bottom:1px #DDD solid;
}

.info, .warn, .error{
	border: 1px solid;
    margin: 10px 0px;
	color:#121212;
    padding:10px;
    background-repeat: repeat;
	border-radius: 4px;
}

.info{
    background-color: #BDE5F8;
}

.warn{
    background-color: #FEEFB3;
}
.error {
    background-color: #FFBABA;
}

label{
	cursor:pointer;
	padding:4px;
}

ul.list{
	list-style:none;
	margin:10px 0px;
	padding:0px;
	clear:both;
}

ul.list li{
	float:left;
	margin-right:30px;
	margin-bottom:10px;
	padding:5px;
}

ul.list li:hover, ul.list li.selected{
	border:1px solid #00DE2C;
	background: #63FF83;
	border-radius:3px;
	color: #0A0A0A;
}

ul.list li.selected{
	font-weight:bold;
}

.print{
	position: absolute;
	margin-top:10px;
	margin-left:887px;
}

.submit button, .submit input[type="submit"]{
	font-size: 130%;
	cursor: pointer;
	color: #0A0A0A;
}

#tests_cfg label{
	width:250px;
	display:inline-block;
}

#tests_cfg input.less{
	width:90px;
}

#tests_cfg input, #tests_cfg texarea, #tests_cfg select{
	padding:2px;
	font-size:110%;
	width:350px;
}

input#submit, button, input[type="submit"], input[type="button"]{
	display: inline-block;
	zoom: 1; /* zoom and *display = ie7 hack for display:inline-block */
	*display: inline;
	vertical-align: baseline;
	margin: 0 2px;
	outline: none;
	cursor: pointer;
	text-align: center;
	text-decoration: none;
	padding: .3em 1.3em;
	-webkit-border-radius: .5em; 
	-moz-border-radius: .5em;
	border-radius: .5em;
	
	color: #606060;
	border: solid 1px #b7b7b7;
	background: #fff;
	background: -webkit-gradient(linear, left top, left bottom, from(#fff), to(#ededed));
	background: -moz-linear-gradient(top,  #fff,  #ededed);
	filter:  progid:DXImageTransform.Microsoft.gradient(startColorstr='#ffffff', endColorstr='#ededed');
}

input#submit:hover, button:hover, input[type="submit"]:hover, input[type="button"]:hover{
	background: #ededed;
	background: -webkit-gradient(linear, left top, left bottom, from(#fff), to(#dcdcdc));
	background: -moz-linear-gradient(top,  #fff,  #dcdcdc);
	filter:  progid:DXImageTransform.Microsoft.gradient(startColorstr='#ffffff', endColorstr='#dcdcdc');
}

input#submit:active, button:active, input[type="submit"]:active, input[type="button"]:active{
	color: #999;
	background: -webkit-gradient(linear, left top, left bottom, from(#ededed), to(#fff));
	background: -moz-linear-gradient(top,  #ededed,  #fff);
	filter:  progid:DXImageTransform.Microsoft.gradient(startColorstr='#ededed', endColorstr='#ffffff');
}

.powerby{
	border: none;
	padding: 10px;
	font-size: 12px;
}

#block-ui-msg{
	font: normal 120%/180% tahoma, arial, verdana, san-serif;
}
</style>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js" type="text/javascript"></script>
<script src="http://code.highcharts.com/highcharts.js" type="text/javascript"></script>
<script src="http://yes.googlecode.com/svn/trunk/jquery/blockui/1.2.3/jquery.blockUI.js" type="text/javascript"></script>
<script>
$(document).ready(function(){
	$('ul.list input').change(function(){
		var liParent = $(this).parents().get(0);
		
		if($(this).is(':checked')) 
			$(liParent).addClass('selected');
		else
			$(liParent).removeClass('selected');
	});
});

function show_block_ui(){
	 $.blockUI({ 
		message: $('#block-ui-msg'),
		css: { borderRadius: '6px', borderColor: '#CCC', boxShadow:'3px 3px 8px #000' }
	});
}
</script>
</head>
<body>
	<div class="Main">
	
		<div class="print">
			<a href="#print" onclick="window.print(); return false;" title="Print this page">
				<img style="border:none;" src="http://cdn.printfriendly.com/button-print-gry20.png" alt="Print Friendly and PDF"/>
			</a>
		</div>
	
		<div class="inner">
			<?php if(empty($_POST) or ( isset($_POST['action']) and $_POST['action'] == "execute" ) ): ?>
			<h2>Machine <em><?php get_server_name(); ?></em></h2>
			
			<em>OS:</em> <?php echo php_uname(); ?> <br>
 			<em>Software:</em> <?php echo $_SERVER['SERVER_SOFTWARE']; ?> <br>
			<em>RAM:</em> <?php $m = get_server_meminfo(); if($m != "not available"): echo ceil($m/1024); ?> MB <sup>(real value <?php echo $m; ?> kb)</sup><?php else: echo $m; endif; ?> <br>
			<em>CPU:</em> <?php echo get_server_cpuinfo(); ?> <br>
			<em>Location:</em> <?php echo date_default_timezone_get(); ?> <sup>(approx., based on timezone)</sup> <br>
			<?php endif; ?>
			
			<?php if(empty($_POST)):?>
			
			<h2>What to test?</h2>
			<form name="tests" id="tests" target="_self" method="post">
				<ul type="disc" class="list">
					<li> <input type="checkbox" name="test_mysql" id="test_mysql" value="mysql"> <label for="test_mysql">PHP-MySQL</label> </li>
					<li> <input type="checkbox" name="test_file" id="test_file" value="file"> <label for="test_file">File create/read</label> </li>
					<li> <input type="checkbox" name="test_gd" id="test_gd" value="gd"> <label for="test_gd">GD image</label> </li>
					<li> <input type="checkbox" name="test_mail" id="test_mail" value="mail"> <label for="test_mail">Email</label> </li>
					<li> <input type="checkbox" name="test_rand" id="test_rand" value="rand"> <label for="test_rand">Random gen</label> </li>
					<br class="isclear">
					<li> <input type="checkbox" name="test_while" id="test_while" value="while"> <label for="test_while">While loops</label> </li>

				</ul>
			
				<br class="isclear">
				
				<p align="center" class="submit">
					<input type="hidden" name="action" value="submit">
					<button type="submit"> Start </button>
				</p>
				
			</form>
			<?php endif; ?>
			
			<?php if( isset($_POST['action']) and $_POST['action'] == "submit"):/*tests configuration*/ ?>
				<?php 
				
					//--- wipe out previous saved csv file, as is not relevant if test configuration will change ---//
					$csv_filename = get_csv_filename();
					
					if(file_exists($csv_filename)) @unlink($csv_filename);
					
					//--- wipe out previous saved csv file, as is not relevant if test configuration will change ---//
				
				?>
				
				<h2>Configure your tests</h2>
				
				<form name="tests_cfg" id="tests_cfg" target="_self" method="post">
				
					<?php if(isset($_POST['test_mysql'])): ?>
					<h3>MySQL Test</h3>
					<p> 
						<label for="mysql_host">Host:</label> 
						<input type="text" name="mysql_host" id="mysql_host" size="50" maxlength="255" value="localhost">
					</p>
					<p> 
						<label for="mysql_dbname">Database:</label> 
						<input type="text" name="mysql_dbname" id="mysql_dbname" size="50" maxlength="255" value="">
					</p>
					<p> 
						<label for="mysql_user">User:</label> 
						<input type="text" name="mysql_user" id="mysql_user" size="50" maxlength="255" value="">
					</p>
					<p> 
						<label for="mysql_pass">Password:</label> 
						<input type="text" name="mysql_pass" id="mysql_pass" size="50" maxlength="255" value="">
					</p>
					<p> 
						<label for="mysql_rows">How many rows:</label> 
						<input type="text" name="mysql_rows" id="mysql_rows" size="50" maxlength="255" value="99"> 
					</p>
					<input type="hidden" name="test_mysql" value="1">
					<?php endif; ?>
					
					<?php if(isset($_POST['test_file'])): ?> 
					<h3>File Test</h3>
					<p> 
						<label for="file_size">File size:</label> 
						<input type="text" name="file_size" id="file_size" size="50" class="less" maxlength="255" value="1"> KB
						<input type="hidden" name="test_file" value="1">
					</p>
					<?php endif; ?>
					
					<?php if(isset($_POST['test_gd'])): ?> 
					<h3>GD Image Test</h3>
					<p> 
						<label for="image_w">Image size:</label> 
						<input type="text" name="image_w" id="image_w" size="50" class="less" maxlength="255" value="200"> x 
						<input type="text" name="image_h" id="image_h" size="50" class="less" maxlength="255" value="200">
						<input type="hidden" name="test_gd" value="1">
					</p>
					<?php endif; ?>
					
					<?php if(isset($_POST['test_mail'])): ?> 
					<h3>Email Test</h3>
					<p> 
						<label for="mail_to">Send email to:</label> 
						<input type="text" name="mail_to" id="mail_to" size="50" maxlength="255" value="">
						<input type="hidden" name="test_mail" value="1">
					</p>
					<?php endif; ?>
					
					<?php if(isset($_POST['test_rand'])): ?> 
					<h3>Random Generator Test</h3>
					<p> 
						<label for="rand_size">Random numbers to generate:</label> 
						<input type="text" name="rand_size" id="rand_size" class="less" size="50" maxlength="255" value="99999">
						<input type="hidden" name="test_rand" value="1">
					</p>
					<?php endif; ?>
					
					<?php if(isset($_POST['test_while'])): ?> 
					<h3>While Loops Test</h3>
					<p> 
						<label for="loop_size">How many loops:</label> 
						<input type="text" name="loop_size" id="loop_size" class="less" size="50" maxlength="255" value="99">
					</p>
					<p>
						<label for="loop_steps">How many steps/loop:</label> 
						<input type="text" name="loop_steps" id="loop_steps" class="less" size="50" maxlength="255" value="999">
					</p>
					<input type="hidden" name="test_while" value="1">
					<?php endif; ?>
					
					<br class="isclear">
					
					<p align="center" class="submit">
						<input type="hidden" name="action" value="execute">
						<button type="submit" onclick="show_block_ui();"> Execute </button>
						&nbsp;&nbsp;&nbsp;&nbsp;
						<button type="submit" onclick="show_block_ui();" name="export-csv" value="1">Execute And Save to CSV </button>
					</p>
				
				</form>
				
			<?php endif; ?>
			
			<?php if(isset($_POST['action']) and $_POST['action'] == "execute"): /*execute tests*/ ?>
				
				<h2>Results</h2>
				
				<?php test_setup(); ?>
				
				<?php if(isset($_POST['test_mysql'])): ?>
					<h3>MySQL Test</h3> <?php test_mysql(); ?>
				<?php endif; ?>
				
				<?php if(isset($_POST['test_file'])): ?>
					<h3>File Test</h3> <?php test_file(); ?>
				<?php endif; ?>
				
				<?php if(isset($_POST['test_mail'])): ?>
					<h3>Email Test</h3> <?php test_email(); ?>
				<?php endif; ?>
				
				<?php if(isset($_POST['test_gd'])): ?>
					<h3>GD Image Test</h3> <?php test_gd(); ?>
				<?php endif; ?>
				
				<?php if(isset($_POST['test_rand'])): ?>
					<h3>Random Generator Test</h3> <?php test_rand(); ?>
				<?php endif; ?>
				
				<?php if(isset($_POST['test_while'])): ?>
					<h3>While Loops Test</h3> <?php test_while(); ?>
				<?php endif; ?>
				
				<?php 
				
				//---- csv save ----//
				if( isset($_POST['export-csv']) ):
					
					$csvhead.="\n";
					$csv	.="\n";
					
					$csv_filename = get_csv_filename();
					
					if( !file_exists($csv_filename) ) @file_put_contents($csv_filename, $csvhead);
					
					@file_put_contents($csv_filename, $csv, FILE_APPEND);
					
				//---- csv prepare ----//
				?>
				
					<?php if( file_exists($csv_filename) ):?>
					<h3 align="center"> <a href="<?php echo $csv_filename; ?>">Download your csv file</a> </h3>
					<?php else: display_msg("CSV file could not be saved!", MSG_TYPE_ERROR); endif; ?>
				
				<?php endif; ?>
				
				<br class="isclear">
				
				<form name="re-run" id="re-run" method="post" target="_self">
					
					<?php if(count($_POST)) foreach($_POST as $key=>$val):?>
					<input type="hidden" name="<?php echo stripslashes($key); ?>" value="<?php echo stripslashes($val); ?>">
					<?php endforeach;?>
					
					<p class="submit" align="center">
						
						<button type="submit" onclick="show_block_ui();"> Re-run &#x21BB; </button>
						
					</p>
				</form>
				
				<form name="chart" id="chart" method="post" target="_self">
					
					<p class="submit" align="center">
						<input type="hidden" name="action" value="generate-chart">
						<button type="submit"> Show Chart </button>
						
					</p>
				</form>
				
			<?php endif; ?>
			
			<?php if(isset($_POST['action']) and $_POST['action'] == "generate-chart"): /*show the chart from csv-saved file*/ ?>
				
				<h2>Results Chart</h2>
				
				<?php 
				
					$csv_filename = get_csv_filename();
					$fh			  = @fopen($csv_filename, 'r');
					$csv_head	  = array();
					$csv_lines	  = array();
					
					//---- prepare the csv data ----//
					if($fh){
						
						$csv_head = fgetcsv($fh, 1000, ",");
						
						while( ($line = fgetcsv($fh, 1000, ",") ) !== FALSE ){
							$csv_lines[] = $line;
						}
						
					}
					else display_msg("Csv file could not be read", MSG_TYPE_ERROR);
					//---- prepare the csv data ----//
					
				if(count($csv_head)): 
					
					//----- prepare the detailed chart data ----//
					$x_axis = array();
					
					foreach($csv_lines as $line){
						$x_axis[] = $line[0];
					}
					
					$x_axis = @implode("', '", $x_axis);
					$x_axis = "['$x_axis']";
					
					//---- build the x-axis values ---//
					$i=$k 			= 0;
					$series 		= array();
					$new_csv_head 	= array();
					$averages		= array();
					
					foreach($csv_head as $key=>$val) $new_csv_head[] = $val;

					$csv_head = $new_csv_head; 
					
					for($i=1; $i<count($csv_head); $i++){ 
					
						
						$vals = array();						
						
						foreach($csv_lines as $line){
						
							//--- prepare series values 
							
							$vals[] = $line[$i];
							
							//--- prepare series values 
						
						}
						
						$average = array_sum($vals);
						$average = number_format($average/count($csv_lines), 4);
						
						$vals = @implode(", ", $vals);
						$vals = "[$vals]";
						
						$averages[] = array('name'=>$csv_head[$i], 'data'=>$average);
						 
						$series[] 	= array('name'=>$csv_head[$i], 'data'=>$vals); 
						
						$k++;
					}
					//----- prepare the detailed chart data ----//	
					
					
					//----- prepare the averages chart data -----//
					$averages_x_axis = array();
					$averages_series = array();
					
					foreach($averages as $avg){
						$averages_x_axis[] = $avg['name'];
						$averages_series[] = $avg['data'];
					}
					
					$averages_x_axis = @implode("', '", $averages_x_axis);
					$averages_x_axis = "['$averages_x_axis']";						
				
					$averages_series = @implode(", ", $averages_series);
					$averages_series = "[{data: [$averages_series]}]";	
					//----- prepare the averages chart data -----//
				?>
			
				<div id="perf-chart" style="width: 100%; background: none;"></div>
				
				<br class="isclear">
				
				<div id="perf-chart2" style="width: 100%; background: none;"></div>
				
				<script>
					$(document).ready(function() {
						
					 //--- display chart 1 (detailed chart)	
				      chart1 = new Highcharts.Chart({
				         chart: {
				            renderTo: 'perf-chart',
				            type: 'bar',
				            height: 600
				         },
				         title: {
				            text: 'Detailed chart'
				         },
				         xAxis: {
				            categories: <?php echo $x_axis; ?>
				         },
				         yAxis: {
				            title: {
				               text: 'seconds'
				            }
				         },
				         
				         series: [<?php foreach($series as $s):?>
				          {name: "<?php echo addslashes($s['name']); ?>", data: <?php echo $s['data']; ?>},
				        <?php endforeach; ?>]
				      });
				      //--- display chart 1 (detailed chart)	
				      
				      //--- display chart 2 (averages chart)	
				      chart1 = new Highcharts.Chart({
				         chart: {
				            renderTo: 'perf-chart2',
				            type: 'column',
				            height: 600
				         },
				         legend: {
				         	enabled: false
				         },
				         title: {
				            text: 'Averages'
				         },
				         xAxis: {
				            categories: <?php echo $averages_x_axis; ?>
				         },
				         yAxis: {},
				         
				         series: <?php echo $averages_series; ?>
				      });
				      //--- display chart 2 (averages chart)
				      
				   });
				</script>
				
				<?php endif; ?>
			<?php endif; ?>
			
		</div>
		
		<br class="isclear">
		
	</div>
	
	<div id="block-ui-msg" style="display:none;">
		<h1 style="text-align:center;">
			<img src="http://s17.postimage.org/wkjalnlzf/php_perf_preloader.gif" align="middle"> 
			<br><br>... running tests ...
		</h1>
	</div>
	
	<div class="powerby">
		<p align="right">Script by <a href="http://adrian.silimon.eu/" target="_blank">Adrian7</a></p>
	</div>
</body>
</html>