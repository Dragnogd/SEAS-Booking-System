  <!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="https://github.com/Dragnogd/SEAS-Booking-System" class="brand-link">
      <!-- <img src="<?php echo base_url() . "public/images/AdminLTELogo.png";?>" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8"> -->
      <span class="brand-text font-weight-light text-center">SEAS Booking System</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">

      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
          <li class="nav-item">
            <a id="create" href="#" class="nav-link createLoan">
              <i class="nav-icon fas fa-plus"></i>
              <p>
                New Loan
              </p>
            </a>
          </li>
		  <li class="nav-item">
            <a id="modify" href="#" class="nav-link createLoan">
			<i class="nav-icon fas fa-undo-alt"></i>
              <p>
                Modify Loan
              </p>
            </a>
          </li>
        </ul>
      </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <div class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-lg-12">
            <div class="card">
              <div id="loanTable" class="card-body table-responsive p-0">
				<?php
					echo $this->ManageLoans_model->getListOfLoans();
				?>
              </div>
            </div>
          </div>
          <!-- /.col-md-6 -->
        </div>
        <!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

<script type="text/javascript">
	var selectLoanDateFilled = false;
	var loanStartDateFilled = false;
	var loanEndDateFilled = false;
	var startPeriodFilled = false;
	var loanEndDateFilled = false;
	var loanType = null;

	var equipmentCheck = false;
	var queueEquipmentCheck = false;

  	function init(){
		$(".completeBooking").on("click", function(){
			var uniqueID = $(this).closest('tr').attr('id');
			var user = "";
			var endDate = "";
			var counter = 1

			$(this).closest('tr').find('td').each(function(){
				if(counter == 2){
					user = $(this).text();
				} else if(counter == 4){
					endDate = $(this).text();
				}
				counter += 1;
			});
			bootbox.confirm({
				title: "Complete Booking",
				message: "Do you want to complete a booking from " + user + " with end date " + endDate + ". This action cannot be undone?",
				buttons: {
					cancel: {
						label: '<i class="fa fa-times"></i> Cancel'
					},
					confirm: {
						label: '<i class="fa fa-check"></i> Confirm'
					}
				},
				callback: function (result) {
					if(result){
						//User has confirmed this booking has been completed
						//Send ajax request to the server to save to database and then update the table on the website accordingly
						jQuery.ajax({
							type: "POST",
							url: "<?php echo base_url(); ?>" + "index.php/manageLoans/completeLoan",
							data: {loanID: uniqueID},
							success: function(message) {
								toastr.success('Booking completed for ' + user);

								//If message returned is "success" then insert new asset into table and add to other dropdowns
								if(message == "Success"){
									$("#" + uniqueID).remove();
								}
							}
						});
					}
				}
			});
		});

		$(".cancelReservation").on("click", function(){
			var uniqueID = $(this).closest('tr').attr('id');
			var user = "";
			var endDate = "";
			var counter = 1

			$(this).closest('tr').find('td').each(function(){
				if(counter == 2){
					user = $(this).text();
				} else if(counter == 4){
					endDate = $(this).text();
				}
				counter += 1;
			});
			bootbox.confirm({
				title: "Complete Booking",
				message: "Do you want to cancel the reservation from " + user + ". This action cannot be undone?",
				buttons: {
					cancel: {
						label: '<i class="fa fa-times"></i> No'
					},
					confirm: {
						label: '<i class="fa fa-check"></i> Yes'
					}
				},
				callback: function (result) {
					if(result){
						//User has confirmed this booking has been completed
						//Send ajax request to the server to save to database and then update the table on the website accordingly
						jQuery.ajax({
							type: "POST",
							url: "<?php echo base_url(); ?>" + "index.php/manageLoans/cancelReservation",
							data: {loanID: uniqueID},
							success: function(message) {
								toastr.success('Reservation cancelled for ' + user);

								//If message returned is "success" then insert new asset into table and add to other dropdowns
								if(message == "Success"){
									$("#" + uniqueID).remove();
								}
							}
						});
					}
				}
			});
		});

		$(".bookReservation").on("click", function(){
			var uniqueID = $(this).closest('tr').attr('id');
			var user = "";
			var endDate = "";
			var counter = 1

			$(this).closest('tr').find('td').each(function(){
				if(counter == 2){
					user = $(this).text();
				} else if(counter == 4){
					endDate = $(this).text();
				}
				counter += 1;
			});
			bootbox.confirm({
				title: "Complete Booking",
				message: "Do you want to book out a reservation for " + user + ". This action cannot be undone?",
				buttons: {
					cancel: {
						label: '<i class="fa fa-times"></i> Cancel'
					},
					confirm: {
						label: '<i class="fa fa-check"></i> Confirm'
					}
				},
				callback: function (result) {
					if(result){
						//User has confirmed this booking has been completed
						//Send ajax request to the server to save to database and then update the table on the website accordingly
						jQuery.ajax({
							type: "POST",
							url: "<?php echo base_url(); ?>" + "index.php/manageLoans/bookReservation",
							data: {loanID: uniqueID},
							success: function(message) {
								toastr.success('Booking created for ' + user);

								//If message returned is "success" then insert new asset into table and add to other dropdowns
								if(message == "Success"){
									jQuery.ajax({
										type: "POST",
										url: "<?php echo base_url(); ?>" + "index.php/manageLoans/getListOfLoans",
										success: function(message){
											$("#loansTable").html(message);
											init();
										}
									});
								}
							}
						});
					}
				}
			});
		});

		//Modify Booking
		$('#modifyBooking').on('click', function (e) {
			var modal = bootbox.dialog({
				message: $(".modifyBooking").html(),
				size: "large",
				title: "Modify Booking",
				buttons: [
				{
					label: "Save",
					className: "btn btn-primary pull-right",
					callback: function(result) {
						//Get the data that was input into each field
						var errorFound = false;

						//Booking Type
						var bookingType = $('.modal-body input[name=bookingType]:checked').attr("id");
						//Booking Period
						var bookingPeriod = $('.modal-body input[name=loanType]:checked').attr("id");
						//User
						var selectedUser = $(".modal-body #userSelected").children(":selected").attr("id");
						//Equipment
						var assets = [];
						$('.modal-body #equipmentTable td:last-child a').each(function() {
							if(jQuery.inArray($(this).attr("id"), assets) == -1) {
								assets.push($(this).attr("id"));
								console.log($(this).attr("id"));
							}
						});
						//Additional Details
						var additionalDetails = $(".modal-body #additionalDetails").val();

						//Loan Start Date
						var loanStartDate;
						var loanEndDate;
						var loanStartPeriod;
						var loanEndPeriod;
						if(bookingPeriod == "loanTypeMulti"){
							loanStartPeriod = 1;
							loanEndPeriod = 11;
							loanStartDate = $('#loanStartDate', '.bootbox').val();
							loanEndDate = $('#loanEndDate', '.bootbox').val();

							//Loan start date not filled in
							if(loanStartDate.length == 0){
								errorFound = true;
								$(".modal-body #loanStartDateLabel").css("color", "red");
							} else{
								$(".modal-body #loanStartDateLabel").css("color", "black");
							}

							//Loan end date not filled in
							if(loanEndDate.length == 0){
								errorFound = true;
								$(".modal-body #loanEndDateLabel").css("color", "red");
							} else{
								$(".modal-body #loanEndDateLabel").css("color", "black");
							}
						} else if(bookingPeriod == "loanTypeSingle"){
							loanStartPeriod = $(".modal-body #selectStartPeriod").children(":selected").attr("id");
							loanEndPeriod = $(".modal-body #selectEndPeriod").children(":selected").attr("id");
							loanStartDate = $('#loanDate', '.bootbox').val();
							loanEndDate = $('#loanDate', '.bootbox').val();

							//Loan Date not filled in
							if(loanStartDate.length == 0){
								errorFound = true;
								$(".modal-body #loanDateLabel").css("color", "red");
							} else{
								$(".modal-body #loanDateLabel").css("color", "black");
							}

							//Loan Start Period not filled in
							if($(".modal-body #selectStartPeriod").children(":selected").attr("id") == undefined){
								errorFound = true;
								$(".modal-body #selectStartPeriodLabel").css("color", "red");
							} else{
								$(".modal-body #selectStartPeriodLabel").css("color", "black");
							}

							//Loan End Period not filled in
							if($(".modal-body #selectEndPeriod").children(":selected").attr("id") == undefined){
								errorFound = true;
								$(".modal-body #selectEndPeriodLabel").css("color", "red");
							} else{
								$(".modal-body #selectEndPeriodLabel").css("color", "black");
							}
						}

						//Booking Type not filled in
						if(bookingType == undefined){
							errorFound = true;
							$(".modal-body #bookingTypeLabel").css("color", "red");
						} else {
							$(".modal-body #bookingTypeLabel").css("color", "black");
						}

						//Booking Period not filled in
						if(bookingPeriod == undefined){
							errorFound = true;
							$(".modal-body #bookingPeriodLabel").css("color", "red");
						} else {
							$(".modal-body #bookingPeriodLabel").css("color", "black");
						}

						//User not selected
						if($(".modal-body #userSelected").children(":selected").attr("id") == undefined){
							errorFound = true;
							$(".modal-body #userSelectedLabel").css("color", "red");
						} else{
							$(".modal-body #userSelectedLabel").css("color", "black");
						};

						//No Equipment selected
						console.log($('#equipmentTable tr').length);
						if($('#equipmentTable tr').length == 2){
							errorFound = true;
							$(".modal-body #equipmentTableLabel").css("color", "red");
						} else {
							$(".modal-body #equipmentTableLabel").css("color", "black");
						}

						if(errorFound == false){
							//Send ajax request to the server to save to database and then update the table on the website accordingly
							jQuery.ajax({
								type: "POST",
								url: "<?php echo base_url(); ?>" + "index.php/manageloans/insertBooking",
								data: {assets: assets, loanStartDate: loanStartDate, loanEndDate: loanEndDate, additionalDetails: additionalDetails, loanStartPeriod: loanStartPeriod, loanEndPeriod: loanEndPeriod, bookingType: bookingType, bookingPeriod: bookingPeriod, selectedUser: selectedUser},
								dataType: 'json',
								success: function(objJSON) {
									var severity = "";
									$.each(objJSON, function(index, element) {
										console.log(element)

										if(element == "Success"){
											severity = "success"
										} else if(element == "Warning"){
											severity = "warning"
										} else if(element == "Danger"){
											severity = "danger";
										} else {
											//Output the message
											if(severity == "success"){
												$.notify({
													// options
													icon: 'glyphicon glyphicon-warning-sign',
													message: 'Booking Was Successfull. Redirecting back to homepage in 5 seconds',
												},{
													// settings
													element: 'body',
													type: "success",
													allow_dismiss: true,
													newest_on_top: true,
													placement: {
														align: "center"
													},
													showProgressbar: true,
												});

												//Wait 5 seconds than redirect back to homepage
												window.setTimeout(function() {
													window.location.href = "<?php echo site_url('manageloans'); ?>";
												}, 5000);
											}else if(severity == "warning" || severity == "danger"){
												$.notify({
													// options
													icon: 'glyphicon glyphicon-warning-sign',
													message: 'The following assets could not be booked. ' + element,
												},{
													// settings
													element: 'body',
													type: "warning",
													allow_dismiss: true,
													newest_on_top: true,
													placement: {
														align: "center"
													},
													showProgressbar: true,
												});
											}

										}
									});
								}
							});
						}
					}
				},
				{
					label: "Cancel",
					className: "btn btn-danger pull-right",
				}
				],
				show: false,
				onEscape: function() {
				modal.modal("hide");
				}
			});

			modal.modal("show");
			$(".modal-body #singleDayBooking").hide();
			$(".modal-body #multiDayBooking").hide();

			modal.on("shown.bs.modal", function() {
				var currentDate = new Date();
				$('.datetimepicker6').datetimepicker({
					format: "YYYY-MM-DD",
					minDate: currentDate
				});
				$('.datetimepicker7').datetimepicker({
					format: "YYYY-MM-DD",
					minDate: currentDate
				});
				$('.datetimepicker8').datetimepicker({
					useCurrent: false,
					format: "YYYY-MM-DD",
					minDate: currentDate
				});
				$(".datetimepicker7").on("change.datetimepicker", function (e) {
					$('.datetimepicker8').datetimepicker('minDate', e.date);
				});
				$(".datetimepicker8").on("change.datetimepicker", function (e) {
					$('.datetimepicker7').datetimepicker('maxDate', e.date);
				});
				$('.datetimepicker9').datetimepicker({
					useCurrent: false,
					format: "HH:mm",
				});
				$('.datetimepicker10').datetimepicker({
					useCurrent: false,
					format: "HH:mm",
				});
				$(".datetimepicker9").on("change.datetimepicker", function (e) {
					$('.datetimepicker10').datetimepicker('minDate', e.date);
				});
				$(".datetimepicker10").on("change.datetimepicker", function (e) {
					$('.datetimepicker9').datetimepicker('maxDate', e.date);
				});
			});
		});

		$(document).on('input', '#selectBookingID', function(e) {
			var loanID = $(".modal-body #selectBookingID").children(":selected").attr("id");

			//Send of ajax request to populate the booking fields
			jQuery.ajax({
				type: "POST",
				url: "<?php echo base_url(); ?>" + "index.php/manageloans/getLoanInfo",
				data: {loanID: loanID},
				success: function(loanID) {
					var obj = JSON.parse(loanID);

					//Booking Loan
					if(obj.LoanType == "bookingLoan"){
						$(".modal-body #bookingLoan").prop('checked', true)
					} else if(obj.LoanType == "bookingSetup"){
						$(".modal-body #bookingSetup").prop('checked', true)
					}

					//Booking Period
					if(obj.LoanStartDate == obj.LoanEndDate){
						$(".modal-body #loanTypeSingle").prop('checked', true)
						$(".modal-body #singleDayBooking").show();
						$(".modal-body #multiDayBooking").hide();

						//Date
						$(".modal-body #loanDate").val(obj.LoanStartDate)

						//Start Period
						$(".modal-body #selectStartPeriod option[id='" + obj.LoanStartPeriod +"']").prop('selected', true);

						//End Period
						$(".modal-body #selectEndPeriod option[id='" + obj.LoanEndPeriod +"']").prop('selected', true);
					} else{
						$(".modal-body #loanTypeMulti").prop('checked', true)
						$(".modal-body #singleDayBooking").hide();
						$(".modal-body #multiDayBooking").show();

						//Start Date
						$(".modal-body #loanStartDate").val(obj.LoanStartDate)

						//End Date
						$(".modal-body #loanEndDate").val(obj.LoanEndDate)
					}

					//User
					$(".modal-body #userSelected option[id='" + obj.UserID +"']").prop('selected', true);

					//Additional Details
					$(".modal-body #additionalDetails").val(obj.AdditionalNotes)

					//Get Equipment
					jQuery.ajax({
						type: "POST",
						url: "<?php echo base_url(); ?>" + "index.php/manageloans/getEquipmentFromLoanID",
						data: {loanID: loanID},
						success: function(equipment) {

						}
					});

					//$("#assetsTable > tbody").append("<tr id='" + obj.AssetID + "'><td>" + assetName + "</td><td>" + assetDescription + "</td><td>" + assetTag + "</td><td>" + assetLocation + "</td></tr>");

					//Add to the delete asset dropdown
					//$("<option id='Delete-" + obj.AssetID + "'>" + assetName + " (" + assetTag + ")</option>").appendTo("#assetToDelete");
					//$("<option id='Modify-" + obj.AssetID + "'>" + assetName + " (" + assetTag + ")</option>").appendTo("#assetToModify");
				}
			});
		});
	}

	$(document).ready(function () {
		//Create Booking
		$('.createLoan').on('click', function (e) {
			//Check if we are creating or modifying a loan
			console.log($(this).attr("id"));
			var title = "";
			var bookingOrModify = "booking";
			if($(this).attr("id") == "create"){
				title = "Create New Loan";

				$("#selectBooking").hide();
				$("#reasonForModificationDiv").hide();
			}else if($(this).attr("id") == "modify"){
				title = "Modify Existing Loan";
				bookingOrModify = "modify";

				$("#selectBooking").show();
				$("#reasonForModificationDiv").show();
			}

			var modal = bootbox.dialog({
				message: $(".createBooking").html(),
				size: "large",
				title: title,
				buttons: [
				{
					label: "Save",
					className: "btn btn-primary pull-right",
					callback: function(result) {
						//Get the data that was input into each field
						var errorFound = false;

						//Booking Type
						// var bookingType = $('.modal-body input[name=bookingType]:checked').attr("id");
						var bookingType = "bookingLoan";
						
						//Booking Period
						var bookingPeriod = $('.modal-body input[name=loanType]:checked').attr("id");
						
						//User
						var selectedUser = $(".modal-body #userSelected").children(":selected").attr("id");
						
						//Reservation
						var reservation = $(".modal-body #reservation").prop('checked');
						
						//Equipment
						var assets = [];
						var assetsRemoved = [];
						//Get the <a> tag from each item in the equipment table. This stores all the details about the item
						//including the asset id, asset tag and whether the item is being added or modified for a booking
						$('.modal-body #equipmentTable td:last-child a').each(function() {
							//First lets establish if this a new item being added or item being removed from a booking
							var creationType = $(this).attr("data-creation-type");
							if(creationType == "create" || (creationType == "modify" && $(this).hasClass('removeFromCart'))){
								//Adding a new asset
								if(jQuery.inArray($(this).attr("id"), assets) == -1) {
									//The item is not currently in the new assets array so lets add it in
									assets.push($(this).attr("id"));
								}
							}else if(creationType == "modify"){
								if(jQuery.inArray($(this).attr("id"), assetsRemoved) == -1) {
									//The item is not currently in the new assets array so lets add it in
									assetsRemoved.push($(this).attr("id"));
								}
							}
						});
						
						//Additional Details
						var additionalDetails = $(".modal-body #additionalDetails").val();

						//Booking to modify
						var modifyBookingID = -1;
						if(bookingOrModify == "modify"){
							modifyBookingID = $(".modal-body #bookingToModify").children(":selected").attr("id").split("-")[1];
						}

						//Reason for modification
						var reasonForModify = $(".modal-body #reasonForModification").val();

						//Loan Start Date
						var loanStartDate;
						var loanEndDate;
						var loanStartPeriod;
						var loanEndPeriod;
						if(bookingPeriod == "loanTypeMulti"){
							loanStartPeriod = "9:00";
							loanEndPeriod = "15:30";
							loanStartDate = $('#loanStartDate', '.bootbox').val();
							loanEndDate = $('#loanEndDate', '.bootbox').val();

							//Loan start date not filled in
							if(loanStartDate.length == 0){
								errorFound = true;
								$(".modal-body #loanStartDateLabel").css("color", "red");
							} else{
								$(".modal-body #loanStartDateLabel").css("color", "black");
							}

							//Loan end date not filled in
							if(loanEndDate.length == 0){
								errorFound = true;
								$(".modal-body #loanEndDateLabel").css("color", "red");
							} else{
								$(".modal-body #loanEndDateLabel").css("color", "black");
							}
						} else if(bookingPeriod == "loanTypeSingle"){
							loanStartPeriod = $('#loanStartTime', '.bootbox').val();
							loanEndPeriod = $('#loanEndTime', '.bootbox').val();
							loanStartDate = $('#loanDate', '.bootbox').val();
							loanEndDate = $('#loanDate', '.bootbox').val();

							//Loan Date not filled in
							if(loanStartDate.length == 0){
								errorFound = true;
								$(".modal-body #loanDateLabel").css("color", "red");
							} else{
								$(".modal-body #loanDateLabel").css("color", "black");
							}

							if(loanStartTime.length == 0){
								errorFound = true;
								$(".modal-body #loanStartTimeLabel").css("color", "red");
							} else{
								$(".modal-body #loanStartTimeLabel").css("color", "black");
							}

							if(loanEndTime.length == 0){
								errorFound = true;
								$(".modal-body #loanEndTimeLabel").css("color", "red");
							} else{
								$(".modal-body #loanEndTimeLabel").css("color", "black");
							}
						}

						//Booking Type not filled in
						if(bookingType == undefined){
							errorFound = true;
							$(".modal-body #bookingTypeLabel").css("color", "red");
						} else {
							$(".modal-body #bookingTypeLabel").css("color", "black");
						}

						//Booking Period not filled in
						if(bookingPeriod == undefined){
							errorFound = true;
							$(".modal-body #bookingPeriodLabel").css("color", "red");
						} else {
							$(".modal-body #bookingPeriodLabel").css("color", "black");
						}

						//User not selected
						if($(".modal-body #userSelected").children(":selected").attr("id") == undefined){
							errorFound = true;
							$(".modal-body #userSelectedLabel").css("color", "red");
						} else{
							$(".modal-body #userSelectedLabel").css("color", "black");
						};

						//No Equipment selected
						console.log($('#equipmentTable tr').length);
						if($('#equipmentTable tr').length == 2){
							errorFound = true;
							$(".modal-body #equipmentTableLabel").css("color", "red");
						} else {
							$(".modal-body #equipmentTableLabel").css("color", "black");
						}

						//No reason given for modification
						if(bookingOrModify == "modify"){
							if($('#reasonForModification').length == 0){
								errorFound = true;
								$(".modal-body #reasonForModification").css("color", "red");
							} else {
								$(".modal-body #reasonForModification").css("color", "black");
							}
						}

						if(errorFound == false){
							//Send ajax request to the server to save to database and then update the table on the website accordingly
							jQuery.ajax({
								type: "POST",
								url: "<?php echo base_url(); ?>" + "index.php/manageloans/insertBooking",
								data: {assets: assets, loanStartDate: loanStartDate, loanEndDate: loanEndDate, additionalDetails: additionalDetails, loanStartPeriod: loanStartPeriod, loanEndPeriod: loanEndPeriod, bookingType: bookingType, bookingPeriod: bookingPeriod, selectedUser: selectedUser, reservation: reservation, bookingOrModify: bookingOrModify, assetsRemoved: assetsRemoved, modifyBookingID: modifyBookingID, reasonForModify: reasonForModify},
								dataType: 'json',
								success: function(objJSON) {
									var severity = "";
									$.each(objJSON, function(index, element) {
										console.log(element)

										if(element == "Success"){
											severity = "success"
										} else if(element == "Warning"){
											severity = "warning"
										} else if(element == "Danger"){
											severity = "danger";
										} else {
											//Output the message
											if(severity == "success"){
												toastr.success('Booking created');

												//Wait 5 seconds than redirect back to homepage
												jQuery.ajax({
													type: "POST",
													url: "<?php echo base_url(); ?>" + "index.php/manageLoans/getListOfLoans",
													success: function(message){
														$("#loansTable").html(message);
														init();
													}
												});
											}else if(severity == "warning" || severity == "danger"){
												toastr.error('The following assets could not be booked. ' + element);
											}
										}
									});
								}
							});
						}
					}
				},
				{
					label: "Cancel",
					className: "btn btn-danger pull-right",
				}
				],
				show: false,
				onEscape: function() {
				modal.modal("hide");
				}
			});

			modal.modal("show");
			$(".modal-body #singleDayBooking").hide();
			$(".modal-body #multiDayBooking").hide();

			modal.on("shown.bs.modal", function() {
				var currentDate = new Date();
				$('.datetimepicker6').datetimepicker({
					format: "YYYY-MM-DD",
					minDate: currentDate
				});
				$('.datetimepicker7').datetimepicker({
					format: "YYYY-MM-DD",
					minDate: currentDate
				});
				$('.datetimepicker8').datetimepicker({
					useCurrent: false,
					format: "YYYY-MM-DD",
					minDate: currentDate
				});
				$(".datetimepicker7").on("show.datetimepicker", function (e) {
					$('.datetimepicker8').datetimepicker('minDate', e.date);
				});
				$(".datetimepicker8").on("show.datetimepicker", function (e) {
					$('.datetimepicker7').datetimepicker('maxDate', e.date);
				});
				$('.datetimepicker9').datetimepicker({
					useCurrent: true,
					format: "HH:mm",
				});
				$('.datetimepicker10').datetimepicker({
					useCurrent: true,
					format: "HH:mm",
					minDate: $('.datetimepicker9').datetimepicker('minDate', e.date),
				});
				$(".datetimepicker9").on("show.datetimepicker", function (e) {
					$('.datetimepicker10').datetimepicker('minDate', e.date);
				});
				$(".datetimepicker10").on("show.datetimepicker", function (e) {
					$('.datetimepicker9').datetimepicker('maxDate', e.date);
				});
			});
		});

		//Show Multi-Day Booking
		$(document).on('change', '#loanTypeMulti', function() {
			$('#equipmentTable tbody > tr').remove();
			$('#equipmentSelected option').remove();
			if($(this).is(':checked')){
				$(".modal-body #multiDayBooking").show();
				$(".modal-body #singleDayBooking").hide();
				loanType = "multi";
			}
		});

		//Show Single Day Booking
		$(document).on('change', '#loanTypeSingle', function() {
			$('#equipmentTable tbody > tr').remove();
			$('#equipmentSelected option').remove();
			if($(this).is(':checked')){
				$(".modal-body #singleDayBooking").show();
				$(".modal-body #multiDayBooking").hide();
				loanType = "single";
			}
		});

		//Loan Date Changed
		$(document).on('input', '#loanDate', function(e) {
			selectLoanDateFilled = true
			checkDateInformation()
		});

		//Loan Start Date Changed
		$(document).on('input', '#loanStartDate', function(e) {
			loanStartDateFilled = true
			checkDateInformation()
		});

		//Loan End Date Changed
		$(document).on('input', '#loanEndDate', function(e) {
			loanEndDateFilled = true
			checkDateInformation()
		});

		//Loan Start Period Changed
		$(document).on('input', '#loanStartTime', function(e) {
			startPeriodFilled = true
			endPeriodFilled = false
			checkDateInformation()
		});

		//Loan End Period Changed
		$(document).on('input', '#loanEndTime', function(e) {
			endPeriodFilled = true
			checkDateInformation()
		});

		function checkDateInformation(){
			if(equipmentCheck == false){
				equipmentCheck = true;

				//Clear equipment table if populated
				$('#equipmentTable tbody > tr').remove();
				console.log("In checkDateInformation");
				if(loanType == "multi"){
					console.log("In Multi");
					if(loanStartDateFilled && loanEndDateFilled){
						var startDate = $('#loanStartDate', '.bootbox').val();
						var endDate = $("#loanEndDate", '.bootbox').val()
						console.log("All Information Filled Out. Sending Ajax Request...")
						console.log(startDate)
						console.log(endDate)
						jQuery.ajax({
							type: "POST",
							url: "<?php echo base_url(); ?>" + "index.php/manageloans/getAvaliableEquipementMulti",
							data: {startDate: startDate, endDate: endDate},
							dataType: "json",
							success: function(objJSON) {
								if (objJSON){
									//Output the list of assets which are avalaible to be loaned out
									console.log(objJSON)

									//Clear the current shopping cart
									$('#equipmentSelected option').remove();
									$(".modal-body #equipmentSelected").append('<option>Please select equipment...');

									for (var i = 0, len = objJSON.length; i < len; ++i) {
										var asset = objJSON[i];
										$(".modal-body #equipmentSelected").append('<option data-asset-tag="' + asset.AssetTag + '" id="' + asset.AssetID + '">' + asset.AssetName + ' (' + asset.AssetTag + ')');
									}

									equipmentCheck = false;

									if(queueEquipmentCheck){
										queueEquipmentCheck = false;
										checkDateInformation()
									}
								}
							}
						});
					}else{
						equipmentCheck = false;

						if(queueEquipmentCheck){
							queueEquipmentCheck = false;
							checkDateInformation()
						}
					}
				}else if(loanType == "single"){
					console.log("In Single");
					if(selectLoanDateFilled && startPeriodFilled && endPeriodFilled){
						var loanDate = $('#loanDate', '.bootbox').val();
						var startPeriod = $('#loanStartTime', '.bootbox').val();
						var endPeriod = $('#loanEndTime', '.bootbox').val();
						console.log("All Information Filled Out. Sending Ajax Request...")
						console.log(loanDate)
						console.log(startPeriod)
						console.log(endPeriod)

						//Clear the current shopping cart
						$('#equipmentSelected option').remove();
						$(".modal-body #equipmentSelected").append('<option>Loading...');

						jQuery.ajax({
							type: "POST",
							url: "<?php echo base_url(); ?>" + "index.php/manageloans/getAvaliableEquipementSingle",
							data: {loanDate: loanDate, startPeriod: startPeriod, endPeriod: endPeriod},
							dataType: "json",
							success: function(objJSON) {
								if (objJSON){
									//Output the list of assets which are avalaible to be loaned out
									$('#equipmentSelected option').remove();
									$(".modal-body #equipmentSelected").append('<option>Please select equipment...');

									for (var i = 0, len = objJSON.length; i < len; ++i) {
										var asset = objJSON[i];
										$(".modal-body #equipmentSelected").append('<option data-asset-tag="' + asset.AssetTag + '" id="' + asset.AssetID + '">' + asset.AssetName + ' (' + asset.AssetTag + ')');
									}
									
									equipmentCheck = false;

									if(queueEquipmentCheck){
										queueEquipmentCheck = false;
										checkDateInformation()
									}
								}
							}
						});
					}else{
						equipmentCheck = false;

						if(queueEquipmentCheck){
							queueEquipmentCheck = false;
							checkDateInformation()
						}
					}
				}
			}else{
				queueEquipmentCheck = true;
			}
		}

		//Equipment Selected
		$(document).on('input', '#equipmentSelected', function(e) {
			var item = $(this).val();
			var id = $(".modal-body #equipmentSelected").children(":selected").attr("id");
			var assetTag = $(".modal-body #equipmentSelected").children(":selected").attr("data-asset-tag");

			//Add to shopping cart
			$('#equipmentTable tbody').append('<tr><td>' + item + '<td><td><a data-creation-type="create" data-asset-tag="' + assetTag + '" id="' + id + '" class="removeFromCart" href="#">Remove</a></td></tr>');

			//Remove from equipment list
			console.log(this);
			$('option:selected', this).remove();
		});

		//Remove From Cart
		$(document).on('click', '.removeFromCart', function(){
			var id = $(this).attr("id");
			var assetTag = $(this).attr("data-asset-tag");
			var creationType = $(this).attr("data-creation-type");
			var item = $(this).closest('tr').find('td:eq(0)').html();

			console.log(creationType);

			if(creationType == "create"){
				//Add back to equipment Selection
				$(".modal-body #equipmentSelected").append('<option data-asset-tag="' + assetTag + '" id="' + id + '">' + item + '</option>');

				//Remove from shopping cart
				var selectedID = $(this).closest('tr').attr("id");
				$(this).closest('tr').remove();
			}else if(creationType == "modify"){
				var selectedID = $(this).closest('tr').attr("id");

				//Strike through item and colour red to indicate it is going to be removed
				$(this).closest('tr').find("td:first").wrap("<strike>");
				$(this).closest('tr').find("td:first").css("color","red");

				//Change text from Remove to Add Back
				$(this).closest('tr').find("td:last").find("a").text("Add Back");
				$(this).closest('tr').find("td:last").find("a").addClass("addBackToCart");
				$(this).closest('tr').find("td:last").find("a").removeClass("removeFromCart");
			}

			//$('#' + selectedID).remove();

			var assets = [];
			$('.modal-body #equipmentTable td:last-child a').each(function() {
				//console.log(this);
				if(jQuery.inArray($(this).attr("id"), assets) == -1) {
					console.log("Pushing " + $(this).attr("id"));
					assets.push($(this).attr("id"));
				}
			});
		});

		//Add Back to Cart
		$(document).on('click', '.addBackToCart', function(){
			var id = $(this).attr("id");
			var assetTag = $(this).attr("data-asset-tag");
			var creationType = $(this).attr("data-creation-type");
			var item = $(this).closest('tr').find('td:eq(0)').html();

			var selectedID = $(this).closest('tr').attr("id");

			//Strike through item and colour red to indicate it is going to be removed
			$(this).closest('tr').find("td:first").unwrap();
			$(this).closest('tr').find("td:first").css("color","black");

			//Change text from Remove to Add Back
			$(this).closest('tr').find("td:last").find("a").text("Remove");
			$(this).closest('tr').find("td:last").find("a").removeClass("addBackToCart");
			$(this).closest('tr').find("td:last").find("a").addClass("removeFromCart");
		});

		//Modify user "Select user To Modify" clicked so load in relevant information
		$(document).on('change', '#bookingToModify', function(){
			var bookingID = $('#bookingToModify', '.bootbox').children(":selected").attr("id").split("-")[1];
			console.log(bookingID);
			//Fetch information about this bookingID from the database and return as JSON

			var dialog = bootbox.dialog({
				message: '<p class="text-center mb-0"><i class="fa fa-spin fa-cog"></i> Fetching Loan...</p>',
				closeButton: false
			});

			jQuery.ajax({
				type: "POST",
				url: "<?php echo base_url(); ?>" + "index.php/manageloans/getBookingByID",
				data: {bookingID: bookingID},
				success: function(message) {
					dialog.modal('hide');
			 		var obj = jQuery.parseJSON(message);
					console.log(obj);

					//Single Day/Multi Day Booking
					if(obj.loanStartDate == obj.loanEndDate){
						//Single Day Booking

						//Check the box
						$("#loanTypeSingle", '.bootbox').prop("checked", true);

						//Show appropriate controls
						$('#equipmentTable tbody > tr').remove();
						$('#equipmentSelected option').remove();
						$("#singleDayBooking", '.bootbox').show();
						$("#multiDayBooking", '.bootbox').hide();
						loanType = "single";

						//Fill out controls
						$("#loanDate", '.bootbox').val(obj.loanStartDate);
						$("#loanStartTime", '.bootbox').val(obj.loanStartPeriod);
						$("#loanEndTime", '.bootbox').val(obj.loanEndPeriod);
						selectLoanDateFilled = true
						startPeriodFilled = true
						endPeriodFilled = true

						//Populate Equipment list
						$.each(obj.assets, function(key,value) {
							$.each(value, function(assetName,assetIds) {
								var ids = assetIds.split(',');
								var assetTag = ids[0];
								var assetID = ids[1];

								console.log(assetTag);
								console.log(assetID);

								//Add to shopping cart
								$('#equipmentSelected option[id=' + assetID + ']', '.bootbox').prop('selected', true);
								$('#equipmentTable tbody', '.bootbox').append('<tr><td>' + assetName + '<td><td><a data-creation-type="modify" data-asset-tag="' + assetTag + '" id="' + assetID + '" class="removeFromCart" href="#">Remove</a></td></tr>');
							});
						});

						//Output the list of assets which are avalaible to be loaned out
						$('#equipmentSelected option', '.bootbox').remove();
						$(".modal-body #equipmentSelected", '.bootbox').append('<option>Please select equipment...');

						for (var i = 0, len = obj.equipment.length; i < len; ++i) {
							var asset = obj.equipment[i];
							$(".modal-body #equipmentSelected", '.bootbox').append('<option data-asset-tag="' + asset.AssetTag + '" id="' + asset.AssetID + '">' + asset.AssetName + ' (' + asset.AssetTag + ')');
						}
					}else{
						//Multi Day Booking

						//Check the box
						$("#loanTypeMulti", '.bootbox').prop("checked", true);

						//Show appropriate controls
						$('#equipmentTable tbody > tr').remove();
						$('#equipmentSelected option').remove();
						$("#multiDayBooking", '.bootbox').show();
						$("#singleDayBooking", '.bootbox').hide();
						loanType = "multi";

						//Fill out controls
						$("#loanStartDate", '.bootbox').val(obj.loanStartDate);
						$("#loanEndDate", '.bootbox').val(obj.loanEndDate);
						loanStartDateFilled = true
						loanEndDateFilled = true

						//Populate Equipment list
						$.each(obj.assets, function(key,value) {
							$.each(value, function(assetName,assetIds) {
								var ids = assetIds.split(',');
								var assetTag = ids[0];
								var assetID = ids[1];

								console.log(assetTag);
								console.log(assetID);

								//Add to shopping cart
								$('#equipmentSelected option[id=' + assetID + ']', '.bootbox').prop('selected', true);
								$('#equipmentTable tbody', '.bootbox').append('<tr><td>' + assetName + '<td><td><a data-creation-type="modify" data-asset-tag="' + assetTag + '" id="' + assetID + '" class="removeFromCart" href="#">Remove</a></td></tr>');
							});
						});

						//Clear the current shopping cart
						$('#equipmentSelected option', '.bootbox').remove();
						$(".modal-body #equipmentSelected", '.bootbox').append('<option>Please select equipment...');

						for (var i = 0, len = obj.equipment.length; i < len; ++i) {
							var asset = obj.equipment[i];
							$(".modal-body #equipmentSelected", '.bootbox').append('<option data-asset-tag="' + asset.AssetTag + '" id="' + asset.AssetID + '">' + asset.AssetName + ' (' + asset.AssetTag + ')');
						}
					}

					//Select the user
					$('#userSelected option[id=' + obj.userID + ']', '.bootbox').prop('selected', true);

					//Add additional details
					$("#additionalDetails", '.bootbox').val(obj.additionalNotes);	
					
					//Check if reservation
					if(obj.loanStatus == "Reserved"){
						console.log("Reserved");
						$('#reservation', '.bootbox').prop('checked', true);
					}else{
						$('#reservation', '.bootbox').prop('checked', false);
					}

					//Reason for modification
					$("#reasonForModification", '.bootbox').val(obj.reasonForModify);	
				}
			});
		});

		$(document).on('hidden.bs.modal', '.modal', function () {
			$('.modal:visible').length && $(document.body).addClass('modal-open');
		});

		init()
	});

</script>
    <!-- Create Booking -->
    <div class="form-content createBooking" style="display: none;">
        <form>
            <!-- Booking Type -->
            <!-- <label id="bookingTypeLabel">Booking Type</label><br>
            <label class="radio-inline"><input type="radio" id="bookingLoan" name="bookingType">Loan</label>
            <label class="radio-inline"><input type="radio" id="bookingSetup" name="bookingType">Setup</label><br>  -->

			<div id="selectBooking">
				<label>Select booking id to modify</label>
				<select class="form-control" id="bookingToModify">
					<?php
						//Get a list of user names & users tags currently in db
						$query = $this->db->query("SELECT * FROM loans WHERE loanStatus='Booked' OR loanStatus='Reserved' OR loanStatus='Overdue' ORDER BY LoanBookingID ASC");
						echo "<option>Please select a booking to modify...</>";
						foreach ($query->result() as $row)
						{
							echo "<option id='Modify-{$row->LoanBookingID}'>{$row->LoanBookingID}</>";
						}
					?>
				</select>
			</div>

            <!-- Booking Type -->
            <label id="bookingPeriodLabel">Booking Period</label><br>
            <label class="radio-inline"><input type="radio" id="loanTypeSingle" name="loanType">Single Day</label>
            <label class="radio-inline"><input type="radio" id="loanTypeMulti" name="loanType">Multiple Days</label><br>

            <div id="singleDayBooking">
                <!-- Loan Date -->
                <label id="loanDateLabel">Date</label>
                <div class="input-group date datetimepicker6" data-target-input="nearest">
                    <input id="loanDate" type="text" class="form-control datetimepicker-input" data-target=".datetimepicker6"/>
                    <div class="input-group-append" data-target=".datetimepicker6" data-toggle="datetimepicker">
                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                    </div>
                </div>

                <!-- Loan Start Period -->
                <label id="loanStartTimeLabel">Start Time</label>
				<div class="input-group date datetimepicker9" data-target-input="nearest">
					<input id="loanStartTime" type="text" class="form-control datetimepicker-input" data-target=".datetimepicker9">
					<div class="input-group-append" data-target=".datetimepicker9" data-toggle="datetimepicker">
						<div class="input-group-text"><i class="far fa-clock"></i></div>
					</div>
				</div>

                <!-- Loan End Period -->
                <label id="loanEndTimeLabel">End Time</label>
				<div class="input-group date datetimepicker10" data-target-input="nearest">
					<input id="loanEndTime" type="text" class="form-control datetimepicker-input" data-target=".datetimepicker10">
					<div class="input-group-append" data-target=".datetimepicker10" data-toggle="datetimepicker">
						<div class="input-group-text"><i class="far fa-clock"></i></div>
					</div>
				</div>
            </div>

            <div id="multiDayBooking">
                <!-- Loan Start Date -->
                <label id="loanStartDateLabel">Start Date</label>
                <div class="input-group date datetimepicker7" data-target-input="nearest">
                    <input id="loanStartDate" type="text" class="form-control datetimepicker-input" data-target=".datetimepicker7"/>
                    <div class="input-group-append" data-target=".datetimepicker7" data-toggle="datetimepicker">
                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                    </div>
                </div>

                <!-- Loan End Date -->
                <label id="loanEndDateLabel">End Date</label>
                <div class="input-group date datetimepicker8" data-target-input="nearest">
                    <input id="loanEndDate" type="text" class="form-control datetimepicker-input" data-target=".datetimepicker8"/>
                    <div class="input-group-append" data-target=".datetimepicker8" data-toggle="datetimepicker">
                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                    </div>
                </div>
            </div>

            <!-- User Selected -->
            <label id="userSelectedLabel">User</label>
            <select class="form-control" id="userSelected">
                <option>Please select a teacher...</option>
                <?php echo $this->ManageLoans_model->getStaff() ?>
            </select>

            <!-- Equipment -->
            <label id="equipmentTableLabel">Equipment</label>
            <select class="form-control" id="equipmentSelected">
            </select>

            <div id="equipmentList">
                <table class="table" id="equipmentTable">
                    <thead>
                        <tr>
                            <th scope="col">Item</th>
                            <th scope="col">Remove</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>

            <!-- Additional Details -->
            <label>Additional details</label>
            <textarea class="form-control" id="additionalDetails"></textarea>

			<!-- Reservation -->
			<hr>
			<div class="form-check">
			<input class="form-check-input" type="checkbox" name="reservation" value="reserved" id="reservation">
			<label class="form-check-label" for="defaultCheck1">
				Reservation
			</label>
			</div>

			<!-- Reasons For Modification -->
			<div id="reasonForModificationDiv">
				<label>Reason for Modification</label>
				<textarea class="form-control" id="reasonForModification"></textarea>
			</div>
        </form>
    </div>