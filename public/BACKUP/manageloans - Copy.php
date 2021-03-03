<div class="wrapper">
        <!-- Sidebar  -->
        <nav id="sidebar">
            <div class="sidebar-header">
                <h3>IT Bookings</h3>
            </div>

            <ul class="list-unstyled components">
                <li>
                    <a id="createBooking" href="#">Create Booking</a>
                </li>
                <!-- <li>
                    <a id="modifyBooking" href="#">Modify Booking</a>
                </li> -->
            </ul>
        </nav>

        <!-- Page Content  -->
        <div id="content">
            <!-- Navigation -->
            <?php $this->load->view('templates/navbarAdmin'); ?>

            <!-- Output list of loans -->
            <?php
                echo $this->ManageLoans_model->getListOfLoans();
            ?>
        </div>
    </div>

    <script type="text/javascript">
        var selectLoanDateFilled = false;
        var loanStartDateFilled = false;
        var loanEndDateFilled = false;
        var startPeriodFilled = false;
        var loanEndDateFilled = false;
        var loanType = null;

        $(document).ready(function () {
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
                                    $.notify({
                                        // options
                                        icon: 'glyphicon glyphicon-warning-sign',
                                        message: message,
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

            //Create Booking
            $('#createBooking').on('click', function (e) {
                var modal = bootbox.dialog({
                    message: $(".createBooking").html(),
                    size: "large",
                    title: "Create Booking",
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
                            $('#equipmentTable td:last-child a').each(function() {
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
                            $('#equipmentTable td:last-child a').each(function() {
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
			$(document).on('input', '#selectStartPeriod', function(e) {
				startPeriodFilled = true
				endPeriodFilled = false
				checkDateInformation()

                //Repopulate the end periods
                $('.modal-body #selectEndPeriod').find('option').remove();
                $('.modal-body #selectEndPeriod').append("<option>Please select an end period...</option>");
                for(i=1; i<=12; i++){
                    switch(i) {
                        case 1:
                            $('.modal-body #selectEndPeriod').append("<option id='1'>Period 1</option>");
                            break;
                        case 2:
                            $('.modal-body #selectEndPeriod').append("<option id='2'>Period 2</option>");
                            break;
                        case 3:
                            $('.modal-body #selectEndPeriod').append("<option id='3'>Period 3</option>");
                            break;
                        case 4:
                            $('.modal-body #selectEndPeriod').append("<option id='4'>Period 4</option>");
                            break;
                        case 5:
                            $('.modal-body #selectEndPeriod').append("<option id='5'>Break</option>");
                            break;
                        case 6:
                            $('.modal-body #selectEndPeriod').append("<option id='6'>Period 5</option>");
                            break;
                        case 7:
                            $('.modal-body #selectEndPeriod').append("<option id='7'>Period 6</option>");
                            break;
                        case 8:
                            $('.modal-body #selectEndPeriod').append("<option id='8'>Lunch</option>");
                            break;
                        case 9:
                            $('.modal-body #selectEndPeriod').append("<option id='9'>Tutor Time</option>");
                            break;
                        case 10:
                            $('.modal-body #selectEndPeriod').append("<option id='10'>Period 7</option>");
                            break;
                        case 11:
                            $('.modal-body #selectEndPeriod').append("<option id='11'>Period 8</option>");
                            break;
                        case 12:
                            $('.modal-body #selectEndPeriod').append("<option id='12'>Afterschool</option>");
                            break;
                    }
                }

                //Stop the user selecting an end period which is lower than the start period.
                var startPeriod = $(".modal-body #selectStartPeriod").children(":selected").attr("id");

                $(".modal-body #selectEndPeriod > option").each(function() {
                    if(parseInt($(this).attr("id"), 10) < startPeriod){
                        $(this).remove();
                    }
                });
			});

            //Loan End Period Changed
            $(document).on('input', '#selectEndPeriod', function(e) {
				endPeriodFilled = true
				checkDateInformation()
			});

            function checkDateInformation(){
                //Clear equipment table if populated
                $('#equipmentTable tbody > tr').remove();

                console.log("In checkDateInformation");
				if(loanType == "multi"){
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
								}
							}
						});
					}
				}else if(loanType == "single"){
					if(selectLoanDateFilled && startPeriodFilled && endPeriodFilled){
						var loanDate = $('#loanDate', '.bootbox').val();
						var startPeriod = $('#selectStartPeriod', '.bootbox').val();
						var endPeriod = $('#selectEndPeriod', '.bootbox').val();
						console.log("All Information Filled Out. Sending Ajax Request...")
						console.log(loanDate)
						console.log(startPeriod)
						console.log(endPeriod)
						jQuery.ajax({
							type: "POST",
							url: "<?php echo base_url(); ?>" + "index.php/manageloans/getAvaliableEquipementSingle",
							data: {loanDate: loanDate, startPeriod: startPeriod, endPeriod: endPeriod},
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
								}
							}
						});
					}
				}
			}	

            //Equipment Selected
            $(document).on('input', '#equipmentSelected', function(e) {
                var item = $(this).val();
                var id = $(".modal-body #equipmentSelected").children(":selected").attr("id");
                var assetTag = $(".modal-body #equipmentSelected").children(":selected").attr("data-asset-tag");

                //Add to shopping cart
                $('#equipmentTable tbody').append('<tr><td>' + item + '<td><td><a data-asset-tag="' + assetTag + '" id="' + id + '" class="removeFromCart" href="#">Remove</a></td></tr>');

                //Remove from equipment list
                $('option:selected', this).remove();
			});            

            //Remove From Cart
            $(document).on('click', '.removeFromCart', function(){
                var id = $(this).attr("id");
                var assetTag = $(this).attr("data-asset-tag");
                var item = $(this).closest('tr').find('td:eq(0)').html();

                //Add back to equipment Selection
                $(".modal-body #equipmentSelected").append('<option data-asset-tag="' + assetTag + '" id="' + id + '">' + item + '</option>');

                //Remove from shopping cart
                $(this).closest('tr').remove();
            });

            //-----------------//
            //-Modify Booking-//
            //----------------//

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
        });
    </script>

    <!-- Create Booking -->
    <div class="form-content createBooking" style="display: none;">
        <form>
            <!-- Booking Type -->
            <label id="bookingTypeLabel">Booking Type</label><br>
            <label class="radio-inline"><input type="radio" id="bookingLoan" name="bookingType">Loan</label>
            <label class="radio-inline"><input type="radio" id="bookingSetup" name="bookingType">Setup</label><br> 

            <!-- Booking Type -->
            <label id="bookingPeriodLabel">Booking Period</label><br>
            <label class="radio-inline"><input type="radio" id="loanTypeSingle" name="loanType">Single Day</label>
            <label class="radio-inline"><input type="radio" id="loanTypeMulti" name="loanType">Multiple Days</label><br>        

            <div id="singleDayBooking">
                <!-- Loan Date -->
                <label id="loanDateLabel">Loan Date</label>
                <div class="input-group date datetimepicker6" data-target-input="nearest">
                    <input id="loanDate" type="text" class="form-control datetimepicker-input" data-target=".datetimepicker6"/>
                    <div class="input-group-append" data-target=".datetimepicker6" data-toggle="datetimepicker">
                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                    </div>
                </div>

                <!-- Loan Start Period -->
                <label id="selectStartPeriodLabel">Loan Start Period</label>
                <select class="form-control" id="selectStartPeriod">
                    <option>Please select a start period...</option>
                    <option id="1">Period 1</option>
                    <option id="2">Period 2</option>
                    <option id="3">Period 3</option>
                    <option id="4">Period 4</option>
                    <option id="5">Break</option>
                    <option id="6">Period 5</option>
                    <option id="7">Period 6</option>
                    <option id="8">Lunch</option>
                    <option id="9">Tutor Time</option>
                    <option id="10">Period 7</option>
                    <option id="11">Period 8</option>
                    <option id="12">Afterschool</option>
                </select>

                <!-- Loan End Period -->
                <label id="selectEndPeriodLabel">Loan End Period</label>
                <select class="form-control" id="selectEndPeriod">
                    <option>Please select an end period...</option>									
                    <option id="1">Period 1</option>
                    <option id="2">Period 2</option>
                    <option id="3">Period 3</option>
                    <option id="4">Period 4</option>
                    <option id="5">Break</option>
                    <option id="6">Period 5</option>
                    <option id="7">Period 6</option>
                    <option id="8">Lunch</option>
                    <option id="9">Tutor Time</option>
                    <option id="10">Period 7</option>
                    <option id="11">Period 8</option>
                    <option id="12">Afterschool</option>
                </select>
            </div>

            <div id="multiDayBooking">
                <!-- Loan Start Date -->
                <label id="loanStartDateLabel">Loan Start Date</label>
                <div class="input-group date datetimepicker7" data-target-input="nearest">
                    <input id="loanStartDate" type="text" class="form-control datetimepicker-input" data-target=".datetimepicker7"/>
                    <div class="input-group-append" data-target=".datetimepicker7" data-toggle="datetimepicker">
                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                    </div>
                </div>

                <!-- Loan End Date -->
                <label id="loanEndDateLabel">Loan End Date</label>
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
        </form>
    </div>
    
    <!-- Modify Booking -->
    <div class="form-content modifyBooking" style="display: none;">
        <form>
            <!-- Select Booking -->
            <label id="selectBookingIDLabel">Select Booking ID</label>
            <select class="form-control" id="selectBookingID">
                <?php
                    echo $this->ManageLoans_model->getBookings();
                ?>
            </select>            

            <!-- Booking Type -->
            <label id="bookingTypeLabel">Booking Type</label><br>
            <label class="radio-inline"><input type="radio" id="bookingLoan" name="bookingType">Loan</label>
            <label class="radio-inline"><input type="radio" id="bookingSetup" name="bookingType">Setup</label><br> 

            <!-- Booking Type -->
            <label id="bookingPeriodLabel">Booking Period</label><br>
            <label class="radio-inline"><input type="radio" id="loanTypeSingle" name="loanType">Single Day</label>
            <label class="radio-inline"><input type="radio" id="loanTypeMulti" name="loanType">Multiple Days</label><br>        

            <div id="singleDayBooking">
                <!-- Loan Date -->
                <label id="loanDateLabel">Loan Date</label>
                <div class="input-group date datetimepicker6" data-target-input="nearest">
                    <input id="loanDate" type="text" class="form-control datetimepicker-input" data-target=".datetimepicker6"/>
                    <div class="input-group-append" data-target=".datetimepicker6" data-toggle="datetimepicker">
                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                    </div>
                </div>

                <!-- Loan Start Period -->
                <label id="selectStartPeriodLabel">Loan Start Period</label>
                <select class="form-control" id="selectStartPeriod">
                    <option>Please select a start period...</option>
                    <option id="1">Period 1</option>
                    <option id="2">Period 2</option>
                    <option id="3">Period 3</option>
                    <option id="4">Period 4</option>
                    <option id="5">Break</option>
                    <option id="6">Period 5</option>
                    <option id="7">Period 6</option>
                    <option id="8">Lunch</option>
                    <option id="9">Tutor Time</option>
                    <option id="10">Period 7</option>
                    <option id="11">Period 8</option>
                    <option id="12">Afterschool</option>
                </select>

                <!-- Loan End Period -->
                <label id="selectEndPeriodLabel">Loan End Period</label>
                <select class="form-control" id="selectEndPeriod">
                    <option>Please select an end period...</option>									
                    <option id="1">Period 1</option>
                    <option id="2">Period 2</option>
                    <option id="3">Period 3</option>
                    <option id="4">Period 4</option>
                    <option id="5">Break</option>
                    <option id="6">Period 5</option>
                    <option id="7">Period 6</option>
                    <option id="8">Lunch</option>
                    <option id="9">Tutor Time</option>
                    <option id="10">Period 7</option>
                    <option id="11">Period 8</option>
                    <option id="12">Afterschool</option>
                </select>
            </div>

            <div id="multiDayBooking">
                <!-- Loan Start Date -->
                <label id="loanStartDateLabel">Loan Start Date</label>
                <div class="input-group date datetimepicker7" data-target-input="nearest">
                    <input id="loanStartDate" type="text" class="form-control datetimepicker-input" data-target=".datetimepicker7"/>
                    <div class="input-group-append" data-target=".datetimepicker7" data-toggle="datetimepicker">
                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                    </div>
                </div>

                <!-- Loan End Date -->
                <label id="loanEndDateLabel">Loan End Date</label>
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
        </form>
    </div>