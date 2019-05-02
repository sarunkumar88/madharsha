<?php
   $serverName = "DESKTOP-E6T8H05\SQLEXPRESS"; //serverName\instanceName
   // Since UID and PWD are not specified in the $connectionInfo array,
   // The connection will be attempted using Windows Authentication.
   //$connectionInfo = array( "Database"=>"dbName", "UID"=>"username", "PWD"=>"password");
   $connectionInfo = array( "Database"=>"etimetracklite1");
   $conn = sqlsrv_connect( $serverName, $connectionInfo);	
?>