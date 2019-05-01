<?php 
    include "header.php";
    include("config.php");
    
	if(isset($_POST) && !empty($_POST)) {
        extract($_POST);
        if(!empty($mealId) || $mealId != '') {
            $query = "update CanteenMeals set meal = '".$meal."', fromTime = '".$fromTime."', toTime = '".$toTime."' where ID = '".$mealId."'";
            $_SESSION['updated'] = true;
            $_SESSION['status'] = 2;
        } else {
            $query = "insert into CanteenMeals(meal, fromTime, toTime) 
            values('".$meal."','".$fromTime."','".$toTime."')";
            $_SESSION['added'] = true;
            $_SESSION['status'] = 2;
        }        
        sqlsrv_query($conn, $query);
        echo "<script>location.href='view-timings.php';</script>";
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
                    <button type="button" data-color="teal" class="btn bg-teal waves-effect" style="float:right;" data-toggle="modal" data-target="#defaultModal">Add Meal</button>
                </h2>
                
            </div>
            <div class="body">
                <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover js-basic-example dataTable">
                        <thead>
                            <tr>
                                <th>Meal</th>
                                <th>From</th>
                                <th>To</th>
                                <th>&nbsp;</th>
                                <th>&nbsp;</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                                while($res = sqlsrv_fetch_array($mealResult, SQLSRV_FETCH_ASSOC)) { ?>
                                    <tr>
                                        <td><?php echo $res['meal']; ?></td>
                                        <td><?php echo $res['fromTime']->format('H:i'); ?></td>
                                        <td><?php echo $res['toTime']->format('H:i'); ?></td>
                                        <td><a href="javascript:void(0);" class="edit-meal" data-meal-id="<?php echo $res['ID']; ?>">Edit</a></td>
                                        <td><a href="javascript:void(0);">Delete</a></td>
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
                        <input type="text" id="meal" name="meal" class="form-control" required placeholder="Enter meal type">
                    </div>
                </div>
                <label for="fromTime">From Time</label>
                <div class="form-group">
                    <div class="form-line">
                        <input type="text" id="fromTime" name="fromTime" class="timepicker form-control" required placeholder="Please choose a time...">
                    </div>
                </div>
                <label for="toTime">To Time</label>
                <div class="form-group">
                    <div class="form-line">
                        <input type="text" id="toTime" name="toTime" name="toTime" class="timepicker form-control" required placeholder="Please choose a time...">
                    </div>
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
				$('#defaultModal').modal('show');
			}
		});
	});
	
	$(".deleteModal").click(function() {
		var con = confirm("Are sure want to delete?");
		if(con==true) {
			var id = $(this).attr("data-deleteid");
			$.ajax({
				url:"ajaxTimeslot.php",
				data:{id:id, status:'delete'},
				type:'post',
				success:function() {
					location.href=location.href;
				}
			});
		}		
	});
</script>

<?php
    $updated = $_SESSION['updated'];
    $added = $_SESSION['added'];
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