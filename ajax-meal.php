<?php
	session_start();
	include("config.php");
	$id = $_POST['id'];
	$status = $_POST['status'];
	if($status == 'update') {
		$qry = "select * from CanteenMeals where id='$id'";
		$result = sqlsrv_query($conn, $qry);
		$res = sqlsrv_fetch_object($result);
		echo $res->OriginalName."##".$res->FromTime->format("H:i")."##".$res->ToTime->format("H:i")."##".$res->IsMandatory;
	} else if($status == 'delete') {
		$qry = "delete from CanteenMeals where id='$id'";
		$result = sqlsrv_query($conn, $qry);
		echo '1';
	} else if($status == 'updateMess') {
        extract($_POST);
		$qry = "update EmployeeMessInfo set $meal='".$changeMeal."' where EmployeeId='$id'";
		$result = sqlsrv_query($conn, $qry);
		echo '1';
	}
?>