<?php 
    $page = "home";
    include "header.php";
    include("config.php");

    $messQuery = "select Meal from CanteenMeals";
    $messResult = sqlsrv_query($conn, $messQuery, array(), array("Scrollable" => 'static'));

    $employeeQuery = "exec [GetEmployeeDetails]";
    // $employeeResult = sqlsrv_query($conn, $employeeQuery, array(), array( "Scrollable" => 'static' ));
    $employeeResult = sqlsrv_prepare($conn, $employeeQuery);
    if (!sqlsrv_execute($employeeResult)) {
        echo "error!";
        die;
    }
?>
<div class="row clearfix">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="card">
            <div class="header">
                <h2>
                    Employee Mess Settings
                </h2>                
            </div>
            <div class="body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover js-basic-example dataTable">
                        <thead>
                            <tr>
                                <th>S. No</th>
                                <th>Employee Code</th>
                                <th>Employee Name</th>
                                <th>Floor</th>
                                <th>Shift</th>
                                <?php 
                                    $data = [];
                                    while($rs = sqlsrv_fetch_array($messResult, SQLSRV_FETCH_ASSOC)) {
                                        $data[] = $rs; ?>
                                        <th><?php echo $rs['Meal']; ?></th>
                                        <?php
                                    }
                                ?>
                                <!-- <th>&nbsp;</th> -->
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                                $j = 0;
                                while($res = sqlsrv_fetch_array($employeeResult)) { ?>
                                    <tr>
                                        <td><?php echo ++$j; ?></td>
                                        <td><?php echo $res['EmployeeCode']; ?></td>
                                        <td><?php echo $res['EmployeeName']; ?></td>
                                        <td><?php echo $res['CompanySName']; ?></td>
                                        <td><?php echo $res['DepartmentSName']; ?></td>
                                        <?php 
                                            for($i = 0; $i < count($data); $i++) { 
                                                    $m = $data[$i]['Meal'];
                                                ?>
                                                <td>
                                                    <div class="switch">
                                                        <input type="checkbox" onClick="updateMessInfo(<?php echo $res['EmployeeId']; ?>, '<?php echo $m; ?>');" <?php echo $res[$m] == 'Y'? 'checked': ''; ?> id="chk-<?php echo $m; ?>-<?php echo $res['EmployeeId']; ?>">
                                                        <label for="chk-<?php echo $m; ?>-<?php echo $res['EmployeeId']; ?>"></label>
                                                    </div>
                                                </td>
                                                <?php
                                            }
                                        ?>
                                        <!-- <td>
                                            <a href="javascript:void(0);" onclick="updateMessInfo(<?php echo $res['EmployeeId']; ?>);"><i class="material-icons">mode_edit</i></a>
                                        </td> -->
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

<script>    
    function updateMessInfo(id, meal) {   
        var changeMeal = $("#chk-"+meal+"-"+id).is(":checked")? 'Y': 'N';
        // var lunch = $("#chk-l-"+id).is(":checked")? 'Y': 'N';
        // var dinner = $("#chk-d-"+id).is(":checked")? 'Y': 'N';     
        $.ajax({
            url:"ajax-meal.php",
            data:{id:id, changeMeal: changeMeal, meal: meal, status:'updateMess'},
            type:'post',
            success:function() {
                swal("Success", "Details updated successfully!", "success");					
            }
        });
    }
</script>