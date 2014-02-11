<!DOCTYPE html>
<html>
<head>
	<title>SQL Dump + DropBox Backup </title>
</head>
<body>
<?php
set_time_limit(0);
/*Database Configuration
==========================================*/
$dbhost = ""; // Databade host
$dbuser = ""; // Databade username
$dbpass = ""; // Databade password
$dbname = ""; // Databade name
/*=======================================*/
/*Dropbox Configuration
=========================================*/
$dropbox_folder_path = ""; //file saving path in dropbox
$dropbox_user =""; //dropbox email
$dropbox_pass = ""; //dropbox password
/*======================================*/
function tableDump($link,$table_name)
{
	$query = $link -> prepare("SHOW CREATE TABLE $table_name");
	$query -> execute();
	$create_table = $query ->fetchAll();
	$ct = $create_table[0][1].";\n\n\n";
	$query = $link -> prepare("SELECT * FROM $table_name");
	$query -> execute();
	$column = $query  -> columnCount();
	$table = $ct; $x = "";
	foreach ($query as $key) 
	{
		for ($i=0; $i < $column; $i++) 
		{
			$x.= "\"$key[$i]\"";
			if($i<$column-1){$x.= ",";}
		}
		$table.="INSERT INTO {$table_name} VALUES({$x});\n";
		$x ="";

	}
	return $table."\n\n\n";	
}
try {$link = new PDO("mysql:host={$dbhost};dbname={$dbname};charset=utf8",$dbuser,$dbpass);} 
catch(PDOException $e){echo '<span style="color: red">ERROR: ' . $e->getMessage()."</span>\n"; exit();}
$msc=microtime(true);
$result = $link -> prepare('SHOW TABLES');
$result -> execute();
$num_table = $result -> rowCount();
if($num_table==0){ echo '<br><span style="color: red">Zero Table exist!</span>'."\n"; exit();}
$dump = "";
foreach ($result as $row){$dump.=tableDump($link,$row[0]);}
$msc = number_format(trim(microtime(true)-$msc), 4, '.', '');
if(!empty($dump)) {echo '<span style="color: green">SQL Dumped in '.$msc.' second</span><br>'."\n";}
else{echo '<span style="color: red">Dumping Failed!</span><br>'."\n";}
$filename = "{$dbname}_".date('Y_m_d_H_i_s').".sql";
$fo = fopen("$filename", 'w');
if(fwrite($fo, $dump))
{
	echo '<span style="color: green">'."Saved as $filename </span><br>"."\n"; 
	fclose($fo);
	//uploading to dropbox
	require 'DropboxUploader.php';
	try 
	{
	    $msc=microtime(true);
	    $uploader = new DropboxUploader($dropbox_user, $dropbox_pass);
		$uploader->setCaCertificateFile(dirname(__FILE__)."\certificate.cer");
	    $uploader->upload(dirname(__FILE__)."\\$filename", $dropbox_folder_path, $filename);
	    $msc = number_format(trim(microtime(true)-$msc), 4, '.', '');
	    echo '<span style="color: green">File successfully uploaded to your Dropbox in '.$msc.' second!</span><br>'."\n";
	}
	catch (Exception $e) 
	{
	    $label = ($e->getCode() & $uploader::FLAG_DROPBOX_GENERIC) ? 'DropboxUploader' : 'Exception';
	    $error = sprintf("[%s] #%d %s", $label, $e->getCode(), $e->getMessage());
	    echo '<span style="color: red">Error: ' . htmlspecialchars($error) . '</span> <br>'."\n";
	}
	if(unlink($filename)){echo '<span style="color: green">File successfully removed from computer/server!</span>'."\n";}
	else{echo '<span style="color: red">File removing failed!</span>'."\n";}
}
else {echo "<span style=\"color: green\">SQL Saving failed</span>"."\n"; fclose($fo);}
?>
</body>
</html>
