<?php
	session_start();
	include("config.php");
	$id = $_POST['id'];
	$status = $_POST['status'];
	if($status=='update') {
		$qry = "select * from CanteenMeals where id='$id'";
		$result = sqlsrv_query($conn, $qry);
		$res = sqlsrv_fetch_object($result);
		echo $res->meal."##".$res->fromTime->format("H:i")."##".$res->toTime->format("H:i");
	}  else if($status=='delete') {
		echo $qry = "delete from CanteenMeals where id='$id'";
		$result = sqlsrv_query($conn, $qry);
		$_SESSION['msg'] = "Deleted Successfully";
		echo '1';
	}
?>