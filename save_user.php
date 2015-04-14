<?php

$dbhost  = 'localhost';   
$dbname  = 'enginee8_facebook';    
$dbuser  = 'enginee8_fb';   
$dbpass  = 'inci15';

$link = mysql_connect($dbhost, $dbuser, $dbpass) or die(mysql_error());
mysql_select_db($dbname) or die(mysql_error());



function query($query)
{
     $result = mysql_query($query) or die(mysql_error());
	 return $result;
}

function get_unique_id( $fullname )
{
	if( strlen($fullname) < 3 )
	{
		$post_fix = "reg";
	}

 

	$post_fix = strtolower( substr( $fullname,0,3 ) );
	$unique_code_original = "inci15".$post_fix;
	$unique_code_copy = $unique_code_original;
	
	$iteration = 0;
	
	
	while (1) 
	{
		if( mysql_num_rows( query("SELECT * FROM `user_details` WHERE `user_unique_code` ='".$unique_code_copy."'") ) == 0 )
		{
			break;
		}
		else
		{
			$iteration++;
			$unique_code_copy = $unique_code_original.$iteration;
		}	
	}
	return $unique_code_copy;
}


if( isset($_POST["facebook_id"]) )
{
	$facebook_id = $_POST["facebook_id"];
	$query = query("SELECT * FROM user_details WHERE facebook_id = $facebook_id");
	$user_unique_code = "";
	if ( mysql_num_rows($query) == 0 )
	{
		$email_id = $_POST["email_id"];
		$fullname = $_POST["fullname"];
		$college_name = $_POST["college_name"];
		$phone_no = $_POST["phone_no"];
		$user_unique_code = get_unique_id( $fullname );


		$insert_statement = "INSERT INTO user_details". 
							"(facebook_id, email_id, phone_no, fullname, college_name, user_unique_code )"
							."VALUES ( '$facebook_id', '$email_id', '$phone_no', '$fullname', '$college_name', '$user_unique_code')";

		if( query($insert_statement) )
		{
			$message = "Success".$user_unique_code;
			//", please remember it. The user id is used during registration";
		}
		else
		{
			$message = "Error";
		}
	
	}
	
	else
	{
		while( $r = mysql_fetch_array($query) )
    	{
        	$unique_id = $r["user_unique_code"];
    	}	
		$message = "Exists";
		$user_unique_code = $unique_id;
	}
}

else
{
	$message = "Error";
}

//echo $message;

header("location: http://www.incident.co.in/?message=".$message."&ID=".$user_unique_code);

