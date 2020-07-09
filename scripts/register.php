<?php
    if(!isset($_POST['fullname']) || !isset($_POST['reg_username']) || !isset($_POST['reg_psw']) || !isset($_POST['reg_cnfpsw']) || !isset($_POST['reg_email'])) {
        header("Location:http://localhost/buddyBonds_backup/");
        exit();
    }
    $config = parse_ini_file("varcsc.ini");
    $db_server = $config['db_server'];
    $db_user = $config['db_user'];
    $db_pass = $config['db_pass'];
    $db_name = $config['db_name'];
    try {
        $conn = new PDO("mysql:host=$db_server;dbname=$db_name",$db_user,$db_pass);
        //set PDO error mode to exception
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch(PDOException $e) {
        echo 'Connection failed:'.$e->getMessage();
    }
    function test_input($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }
    $username = test_input($_POST['reg_username']);
    //define errors
    $nameErr = $reg_unErr = $reg_pswErr = $reg_cnfpswErr = $emailErr = "";
	//check if username taken
	if(!$result = $conn->prepare("SELECT username FROM users WHERE username=?")) die('Query failed:('.$conn->errno.')'.$conn->error);
	if(!$result->bindParam(1, $username, PDO::PARAM_STR)) die('Binding parameters failed:('.$result->errno.')'.$result->error);
	if(!$result->execute()) die('Execute failed:('.$result->errno.')'.$result->error);
	//save form data in vars
	$fullname = test_input($_POST['fullname']);
	$password = test_input($_POST['reg_psw']);
	$reg_cnfpsw = test_input($_POST['reg_cnfpsw']);
	$email = test_input($_POST['reg_email']);
	//process form data
	//fn
	if(empty($fullname)) $nameErr = "Name cannot be empty !";
	else $nameErr = "";
	//un
	if(empty($username)) $reg_unErr = "Username cannot be empty !";
	else if(preg_match("/[^a-z0-9A-Z_]/",$username)) $reg_unErr = "Username cannot contain invalid characters !";
	else if($result->rowCount() > 0) $reg_unErr = 'Username <b>'.$username.'</b> is already taken !';
	else $reg_unErr = "";
	$result = null;
	//psw
	if(empty($password)) {
		$reg_pswErr = "Password cannot be empty !";
	} else {
		$reg_pswErr = "";
		$password = hash('sha256',$password);
		$password = base64_encode($password);
	}
	//cnfpsw
	if(empty($reg_cnfpsw)) {
		$reg_cnfpswErr = "Confirmed password cannot be empty !";
	} else {
		$reg_cnfpswErr = "";
		$reg_cnfpsw = hash('sha256', $reg_cnfpsw);
	    $reg_cnfpsw = base64_encode($reg_cnfpsw);
	}
	//email
	if(empty($email)) {
		$emailErr = "Email cannot be empty !";
	} else {
		$emailErr = "";
		if(!filter_var($email, FILTER_VALIDATE_EMAIL)) $emailErr = "Invalid email format !";
	}
	//check for matching of passwords
	if($password != $reg_cnfpsw) $reg_cnfpswErr = "Confirmed password does not match the original password !";
	//imp func.-->to generate public and private keys for the user
	$public_key = md5(microtime().rand());
	//total user query
	if(!$tuqr = $conn->query("SELECT * FROM users ORDER BY id DESC LIMIT 1")) die("Query failed!");
	$tuqrw = $tuqr->fetch(PDO::FETCH_ASSOC);
	$n = $tuqrw['id'] + 1;
    $private_key = $public_key.".".$n;		
    //
    //show the results
    //
	if((!$nameErr) && (!$reg_unErr) && (!$reg_pswErr) && (!$reg_cnfpswErr) && (!$emailErr)) {
		$result = $conn->prepare("INSERT INTO users(fullname,username,password,email,private_key,public_key,join_date) VALUES(?,?,?,?,?,?,NOW())");
		$result->bindParam(1, $fullname, PDO::PARAM_STR);
		$result->bindParam(2, $username, PDO::PARAM_STR);
		$result->bindParam(3, $password, PDO::PARAM_STR);
		$result->bindParam(4, $email, PDO::PARAM_STR);
		$result->bindParam(5, $private_key, PDO::PARAM_STR);
		$result->bindParam(6, $public_key, PDO::PARAM_STR);
		//public_key
		$bb_pk = "79d0849ec511c34b1d428bcbdea9d13b";
		$bb_id = 1;
		$bdy_key = $bb_pk.".".$public_key;
		$active = 1;
		//make buddybonds as buddy
		$mbbab = $conn->prepare("INSERT INTO buddies(bud_id1,bud_id2,buddy_key,buddy_time,active) VALUES(?,?,?,NOW(),?)");
		$mbbab->bindParam(1, $bb_id, PDO::PARAM_INT);
		$mbbab->bindParam(2, $n, PDO::PARAM_INT);
		$mbbab->bindParam(3, $bdy_key, PDO::PARAM_INT);
		$mbbab->bindParam(4, $active, PDO::PARAM_INT);
		//increase buddybonds buddies
		$sbbb=$conn->prepare("SELECT nobuddies FROM users WHERE id=?");
		$sbbb->bindParam(1, $active, PDO::PARAM_INT);
		$sbbb->execute();
		$sbbbr = $sbbb->fetch(PDO::FETCH_ASSOC);
		$nobbb = $sbbbr['nobuddies'] + 1;
		$ibbb = $conn->prepare("UPDATE users SET nobuddies= ? WHERE id=?");
		$ibbb->bindParam(1, $nobbb, PDO::PARAM_INT);
		$ibbb->bindParam(2, $active, PDO::PARAM_INT);
		//
		if(($result->execute()) && ($mbbab->execute()) && ($ibbb->execute())) {
			$result = null;
			echo '<div class="alert alert-info text-center"><span class="text-success"><b>Signed Up successfully as '.$username.'</b></span></div>';
		} else {
			echo '<div class="alert alert-info text-center"><span class="text-danger">We could not sign you up! Please try again. </span></div>';
			die('Query failed');
		} 
	} else { 
        echo '<div class="alert alert-info text-center"><span class="text-danger">';
        echo $nameErr;
        if(strlen($reg_unErr) >= 1) 
            if(strlen($nameErr) >= 1) echo '<br>';
            echo $reg_unErr;
        if(strlen($reg_pswErr) >= 1)
            if(strlen($nameErr) >= 1 || strlen($reg_unErr) >= 1) echo '<br>';
            echo $reg_pswErr;
        if(strlen($reg_cnfpswErr) >= 1) 
            if(strlen($nameErr) >= 1 || strlen($reg_unErr) >= 1 || strlen($reg_pswErr) >= 1) echo '<br>';
            echo $reg_cnfpswErr;
        if(strlen($emailErr) >= 1) 
            if(strlen($nameErr) >= 1 || strlen($reg_unErr) >= 1 || 
            strlen($reg_pswErr) >= 1 ||strlen($reg_cnfpswErr) >= 1) echo '<br>';
            echo $emailErr;
        echo '</span></div>';
	} 
	$conn=null;
?>