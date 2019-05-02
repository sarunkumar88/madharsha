<?php
    $page = "absence";
    include "header.php";
    include("config.php");
        
	// if(isset($_POST) && !empty($_POST)) {
    //     extract($_POST);
    //     if(!empty($mealId) || $mealId != '') {
    //         $query = "update CanteenMeals set meal = '".$meal."', fromTime = '".$fromTime."', toTime = '".$toTime."' where ID = '".$mealId."'";
    //         $_SESSION['updated'] = true;
    //         $_SESSION['status'] = 2;
    //     } else {
    //         $query = "insert into CanteenMeals(meal, fromTime, toTime) 
    //         values('".$meal."','".$fromTime."','".$toTime."')";
    //         $_SESSION['added'] = true;
    //         $_SESSION['status'] = 2;
    //     }        
    //     sqlsrv_query($conn, $query);
    //     echo "<script>location.href='view-timings.php';</script>";
    // }
    
    $employeeQuery = "exec GetEmployeeMessDetails";
    $employeeResult = sqlsrv_query($conn, $employeeQuery, array(), array( "Scrollable" => 'static' ));
?>
<div class="row clearfix">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="card">
            <div class="header">
                <h2>
                    Employee activities
                </h2>                
            </div>
            <div class="body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover dataTable js-exportable">
                        <thead>
                            <tr>
                                <th>S. No</th>
                                <th>Date</th>
                                <th>Employee Code</th>
                                <th>Employee Name</th>
                                <th>Floor</th>
                                <th>Shift</th>
                                <th>Interval</th>
                                <th>Time</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                                $i = 0;
                                while($res = sqlsrv_fetch_array($employeeResult, SQLSRV_FETCH_ASSOC)) { ?>
                                    <tr>
                                        <td><?php echo ++$i; ?>
                                        <td><?php echo $res['LogDate']->format('d-m-Y'); ?>
                                        <td><?php echo $res['EmployeeCode']; ?></td>
                                        <td><?php echo $res['EmployeeName']; ?></td>
                                        <td><?php echo $res['CompanySName']; ?></td>
                                        <td><?php echo $res['DepartmentSName']; ?></td>
                                        <td>&nbsp;</td>
                                        <td><?php echo $res['LogDate']->format('H:i'); ?>
                                        <td>&nbsp;</td>
                                    </tr>
                            <?php }	?>                              
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include "footer.php"; ?>