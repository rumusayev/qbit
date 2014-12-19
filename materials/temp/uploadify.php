<?php
$session_id = $_GET['session_id'];

/*
Uploadify
Copyright (c) 2012 Reactive Apps, Ronnie Garcia
Released under the MIT License <http://www.opensource.org/licenses/mit-license.php> 
*/

// Define a destination
//$verifyToken = md5('unique_salt' . $_POST['timestamp']);
if (!empty($_FILES)) 
{	//&& $_POST['token'] == $verifyToken) {
	if (!is_dir(dirname(__FILE__).'/files/'.$session_id))
	{
		mkdir(dirname(__FILE__).'/files/'.$session_id, 0755);
	}
	$targetFolder = dirname(__FILE__).'/files/'.$session_id; // Relative to the root
	$tempFile = $_FILES['Filedata']['tmp_name'];
	$targetPath = $targetFolder;
	$targetFile = rtrim($targetPath,'/') . '/' . $_FILES['Filedata']['name'];
	
	// Validate the file type
	$fileTypes = array('jpg','jpeg','gif','png', 'bmp'); // File extensions
	$fileParts = pathinfo($_FILES['Filedata']['name']);
	
	if (in_array(strtolower($fileParts['extension']),$fileTypes)) 
	{
		move_uploaded_file($tempFile, $targetFile);
		echo '1';
	} else {
		echo 'Invalid file type.';
	}
}

?>