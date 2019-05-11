<?php 
    $page = "mess-absence";
    include "header.php";
    include("config.php");
    $fromdate = date('Ymd');
    $todate = date('Ymd');
        
	if(isset($_GET) && !empty($_GET)) {
		extract($_GET);
		$fromdate = $fromdate;
		$todate = $todate;
    }

    $messQuery = "select Meal from CanteenMeals";
    $messResult = sqlsrv_query($conn, $messQuery, array(), array("Scrollable" => 'static'));

    $params = array(
        array(&$myparams['fromdate'], SQLSRV_PARAM_IN),
        array(&$myparams['todate'], SQLSRV_PARAM_IN)
    );

    //$employeeQuery = "exec GetEmployeeMessAbsenceDetails @fromdate = $fromdate, @todate = $todate";
    $employeeQuery = "exec GetEmployeeMessDetails @fromdate = $fromdate, @todate = $todate";
    $employeeResult = sqlsrv_prepare($conn, $employeeQuery, $params);

    $params = array(
        array(&$myparams['fromdate'], SQLSRV_PARAM_IN),
        array(&$myparams['todate'], SQLSRV_PARAM_IN)
    );

    if (!sqlsrv_execute($employeeResult)) {
        //echo "error!";
        //die(print_r(sqlsrv_errors(), true));  
    }
?>
<div class="row clearfix">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="card">
            <div class="header">
                <h2>
                    Canteen Absence details
                </h2>                
            </div>
            <div class="body" style="padding-bottom: 0px;">
                <div class="row clearfix">
                <form method="get" action="mess-absence-details.php">
                    <div class="col-xs-3">
                        <h2 class="card-inside-title">From Date</h2>
                        <div class="form-group">
                            <div class="form-line">
                                <input type="text" class="form-control datepicker" name="fromdate" value="<?php echo $fromdate; ?>" placeholder="Please choose a date...">
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-3">
                        <h2 class="card-inside-title">To Date</h2>
                        <div class="form-group">
                            <div class="form-line">
                                <input type="text" class="form-control datepicker" name="todate" value="<?php echo $todate; ?>" placeholder="Please choose a date...">
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-6">
                        <button type="submit" class="btn btn-primary m-t-15 waves-effect">Submit</button>
                    </div>
                    </form>
                </div>
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
                            <th>Location</th>
                            <th>Mess</th>
                            <th>Skipped</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                            $data = [];
                            while($rs = sqlsrv_fetch_array($messResult, SQLSRV_FETCH_ASSOC)) {
                                $data[] = $rs; 
                            }
                        ?>
                        <?php 
                            $j = 0;                            
                            while($res = sqlsrv_fetch_array($employeeResult, SQLSRV_FETCH_ASSOC)) { 
                                $skipped = "";
                                for($i = 0; $i < count($data); $i++) { 
                                    $m = $data[$i]['Meal'];                                    
                                    if($res[$m] == 'Y' && empty($res["login_".$m])) {
                                        $skipped .= $m.", ";
                                    }
                                }
                                if($skipped != "" && $res['punchtime']) {
                                ?>
                                <tr>
                                    <td><?php echo ++$j; ?>
                                    <td><?php echo $res['PunchIn'] != null? $res['PunchIn']->format('d-m-Y'): ''; ?></td>
                                    <td><?php echo $res['EmployeeCode']; ?></td>
                                    <td><?php echo $res['EmployeeName']; ?></td>
                                    <td><?php echo $res['CompanySName']; ?></td>
                                    <td><?php echo $res['DepartmentSName']; ?>
                                    <td><?php echo substr($skipped, 0, -2); ?></td>
                                </tr>
                        <?php }	
                        }?>                              
                    </tbody>
                </table>
            </div>
            </div>
        </div>
    </div>
</div>

<?php include "footer.php"; ?>