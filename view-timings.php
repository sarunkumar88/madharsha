<?php 
    $page = "timings";
    include "header.php";
    include("config.php");    
    
	if(isset($_POST) && !empty($_POST)) {
        extract($_POST);
        $mandatory = isset($IsMandatory) && $IsMandatory == 'on'? 'Y': null;
        $mealColumn = str_replace(' ', '', $Meal);
        if(!empty($mealId) || $mealId != '') {
            $query = "if not exists (select column_name from INFORMATION_SCHEMA.COLUMNS where table_name = 'canteenmeals' and column_name = '".$mealColumn."' ) begin update CanteenMeals set Meal = '".$mealColumn."', OriginalName='".$Meal."', FromTime = '".$FromTime."', ToTime = '".$ToTime."', IsMandatory='".$mandatory."' where ID = '".$mealId."' end";
            $_SESSION['updated'] = true;
            $_SESSION['status'] = 1;
        } else {
            $query = "if not exists (select column_name from INFORMATION_SCHEMA.COLUMNS where table_name = 'canteenmeals' and column_name = '".$mealColumn."' ) begin insert into CanteenMeals(Meal, OriginalName, FromTime, ToTime, IsMandatory) 
            values('".$mealColumn."', '".$Meal."','".$FromTime."','".$ToTime."', '".$mandatory."') end";
            $_SESSION['added'] = true;
            $_SESSION['status'] = 1;
        }      
        sqlsrv_query($conn, $query);
    }
    
    $mealQuery = "select * from CanteenMeals";
    $mealResult = sqlsrv_query($conn, $mealQuery, array(), array( "Scrollable" => 'static' ));
?>
<div class="row clearfix">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="card">
            <div class="header">
                <h2>
                    View meal details
                    <button type="button" data-color="teal" class="btn bg-teal waves-effect addModal" style="float:right;" data-toggle="modal" data-target="#defaultModal">Add Meal</button>
                </h2>
                
            </div>
            <div class="body">
                <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover js-basic-example dataTable">
                        <thead>
                            <tr>
                                <th>Meal</th>
                                <th>From Time</th>
                                <th>To Time</th>
                                <th>&nbsp;</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                                while($res = sqlsrv_fetch_array($mealResult, SQLSRV_FETCH_ASSOC)) { ?>
                                    <tr>
                                        <td>
                                            <a href="javascript:void(0);" class="edit-meal" data-meal-id="<?php echo $res['ID']; ?>">
                                                <?php echo $res['OriginalName']; ?>
                                            </a>
                                        </td>
                                        <td><?php echo $res['FromTime']->format('H:i'); ?></td>
                                        <td><?php echo $res['ToTime']->format('H:i'); ?></td>
                                        <td><a href="javascript:void(0);" onclick="showAjaxLoaderMessage(<?php echo $res['ID']; ?>);"><i class="material-icons">delete</i></a></td>
                                    </tr>
                            <?php }	?>                                 
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Popup  -->
<div class="modal fade" id="defaultModal" tabindex="-1" role="dialog" style="display: none;">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="defaultModalLabel">Modal title</h4>
            </div>
            <div class="modal-body">
            <form id="form_validation" method="POST" novalidate="novalidate">
                <label for="meal">Meal</label>
                <div class="form-group">
                    <div class="form-line">
                        <input type="text" id="meal" name="Meal" class="form-control" required placeholder="Enter meal type">
                    </div>
                </div>
                <label for="fromTime">From Time</label>
                <div class="form-group">
                    <div class="form-line">
                        <input type="text" id="fromTime" name="FromTime" class="timepicker form-control" required placeholder="Please choose a time...">
                    </div>
                </div>
                <label for="toTime">To Time</label>
                <div class="form-group">
                    <div class="form-line">
                        <input type="text" id="toTime" name="ToTime" name="toTime" class="timepicker form-control" required placeholder="Please choose a time...">
                    </div>
                </div>                
                <div class="form-group">
                    <input type="checkbox" id="isMandatory" name="IsMandatory" />    
                    <label for="isMandatory">Is Mandatory</label>                
                </div>
                <input type="hidden" name="mealId" id="mealId" />
                <button type="submit" class="btn btn-primary m-t-15 waves-effect">Submit</button>
            </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-link waves-effect" data-dismiss="modal">CLOSE</button>
            </div>
        </div>
    </div>
</div>

<?php include "footer.php"; ?>

<script>
	$(".edit-meal").click(function(){
		var id = $(this).attr("data-meal-id");
		$.ajax({
			url:"ajax-meal.php",
			data:{id:id, status:'update'},
			type:'post',
			success:function(data) {
				var res = data.split("##");
				$("#meal").val(res[0]);
				$("#fromTime").val(res[1]);
				$("#toTime").val(res[2]);
                $("#mealId").val(id);                
                $("#isMandatory").prop('checked', res[3] == 'Y'? true: false);
				$('#defaultModal').modal('show');
			}
		});
    });

    $(".addModal").click(function() {
        $("#meal").val("");
        $("#fromTime").val("");
        $("#toTime").val("");
        $("#mealId").val("");                
        $("#isMandatory").prop('checked', false); 
    })
    
    function showAjaxLoaderMessage(id) {
        swal({
            title: "Are you sure?",
            text: "You will not be able to recover this data again!",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Yes, delete it!",
            cancelButtonText: "No, cancel!",
            closeOnConfirm: false,
            showLoaderOnConfirm: true,
        }, function () {
			$.ajax({
				url:"ajax-meal.php",
				data:{id:id, status:'delete'},
				type:'post',
				success:function() {
                    swal("Data deleted successfully!");
                    setTimeout(function () {
                        location.href = location.href;
                    }, 2000);					
				}
			});
        });
    }
</script>

<?php
    $updated = isset($_SESSION['updated'])? $_SESSION['updated']: false;
    $added = isset($_SESSION['added'])? $_SESSION['added']: false;
    $msg = "";
    if(isset($_SESSION['status']) && $_SESSION['status'] > 0) {        
        if($added) {
            $msg = "Meal details added successfully";
        }
        if($updated) {
            $msg = "Meal details updated successfully";
        }
        ?>
        <script>
            showNotification("bg-green", "<?php echo $msg; ?>", "top", "center", "animated bounceIn", "animated bounceOut");
        </script>
        <?php                    
            $_SESSION['status'] = $_SESSION['status'] - 1;
            $_SESSION['updated'] = false;
            $_SESSION['added'] = false;
    }
?>
