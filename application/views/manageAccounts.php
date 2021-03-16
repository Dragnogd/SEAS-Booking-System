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
		<a id="addUser" href="#" class="nav-link">
		  <i class="nav-icon fas fa-book"></i>
		  <p>
			Add User
		  </p>
		</a>
	  </li>
	  <li class="nav-item">
		<a id="deleteUser" href="#" class="nav-link">
		  <i class="nav-icon fas fa-book"></i>
		  <p>
			Delete User
		  </p>
		</a>
	  </li>
	  <li class="nav-item">
		<a id="modifyuser" href="#" class="nav-link">
		  <i class="nav-icon fas fa-book"></i>
		  <p>
			Modify User
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
		  <div class="card-body table-responsive p-0">
			<?php
				echo $this->ManageAccounts_model->getListOfUsers();
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
        $(document).ready(function () {
            //Add new user to database
            $('#addUser').on('click', function (e) {
                var modal = bootbox.dialog({
                    message: $(".addUser").html(),
                    size: "large",
                    title: "Add New User",
                    buttons: [
                    {
                        label: "Save",
                        className: "btn btn-primary pull-right",
                        callback: function(result) {
                            //Get the data that was input into each field
                            var userID;
                            var forename = $('#forename', '.bootbox').val();
                            var surname = $('#surname', '.bootbox').val();
                            var email = $('#email', '.bootbox').val();

                            //Send ajax request to the server to save to database and then update the table on the website accordingly
                            jQuery.ajax({
                                type: "POST",
                                url: "<?php echo base_url(); ?>" + "index.php/manageaccounts/insertNewUser",
                                data: {forename: forename, surname: surname, email: email},
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

                                    //If message returned is "success" then insert new user into table and add to other dropdowns
                                    if(message == "Success"){
                                        //Send ajax request to get userID
                                        jQuery.ajax({
                                            type: "POST",
                                            url: "<?php echo base_url(); ?>" + "index.php/manageaccounts/getUserID",
                                            data: {email: email},
                                            success: function(message) {
                                                userID = message;
                                                $("#usersTable > tbody").append("<tr id='" + userID + "'><td>" + forename + "</td><td>" + surname + "</td><td>" + email + "</td></tr>");

                                                //Add to the delete user dropdown
                                                $("<option id='Delete-" + userID + "'>" + forename + " " + surname + " (" + email + ")</option>").appendTo("#userToDelete");
                                                $("<option id='Modify-" + userID + "'>" + forename + " " + surname + " (" + email + ")</option>").appendTo("#userToModify");
                                            }
                                        });


                                    }
                                }
                            });
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
            });

            //Delete user from database
            $('#deleteUser').on('click', function (e) {
                var modal = bootbox.dialog({
                    message: $(".deleteUser").html(),
                    size: "large",
                    title: "Delete User",
                    buttons: [
                    {
                        label: "Save",
                        className: "btn btn-primary pull-right",
                        callback: function(result) {
                            //Get the data that was input into each field
                            // var userToDelete = $('#userToDelete', '.bootbox').val();
                            // var regExp = /\(([^)]+)\)/;
                            // var userID = regExp.exec(userToDelete)[1];

                            var userID = $('#userToDelete', '.bootbox').children(":selected").attr("id").split("-")[1];

                            //Send ajax request to the server to save to database and then update the table on the website accordingly
                            jQuery.ajax({
                                type: "POST",
                                url: "<?php echo base_url(); ?>" + "index.php/manageaccounts/deleteUser",
                                data: {userID: userID},
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

                                    //If message returned is "success" then insert new user into table
                                    if(message == "Success"){
                                        //Remove user from the table that was just deleted
                                        $("#" + userID).remove();


                                        //Remove user from Modifyuser List
                                        $("#Modify-" + userID).remove();
                                        $("#Delete-" + userID).remove();
                                    }
                                }
                            });
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
            });

            //Modify user in database
            $('#modifyuser').on('click', function (e) {
                var modal = bootbox.dialog({
                    message: $(".modifyuser").html(),
                    size: "large",
                    title: "Modify user",
                    buttons: [
                    {
                        label: "Save",
                        className: "btn btn-primary pull-right",
                        callback: function(result) {
                            //Get the data that was input into each field
                            var userID = $('#userNewUserID', '.bootbox').val();
                            var forename = $('#userNewForename', '.bootbox').val();
                            var surname = $('#userNewSurname', '.bootbox').val();
                            var email = $('#userNewEmail','.bootbox').val();

                            //Send ajax request to the server to save to database and then update the table on the website accordingly
                            jQuery.ajax({
                                type: "POST",
                                url: "<?php echo base_url(); ?>" + "index.php/manageaccounts/updateUser",
                                data: {userID: userID, forename: forename, surname: surname, email: email},
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

                                    //If message returned is "success" then insert new user into table and add to other dropdowns
                                    if(message == "Success"){
                                        //Modify user in Table
                                        $("#" + userID).remove();
                                        $("#usersTable > tbody").append("<tr id='" + userID + "'><td>" + forename + "</td><td>" + surname + "</td><td>" + email + "</td></tr>");


                                        //Modify user in Delete user
                                        $("#Delete-" + userID).remove();
                                        $("#Modify-" + userID).remove();
                                        $("<option id='Modify-" + userID + "'>" + forename + " " + surname + " (" + email + ")</option>").appendTo("#userToModify");
                                        $("<option id='Delete-" + userID + "'>" + forename + " " + surname + " (" + email + ")</option>").appendTo("#userToDelete");
                                    }
                                }
                            });
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
            });

            //Modify user "Select user To Modify" clicked so load in relevant information
            $(document).on('click', '#userToModify', function(e){
                var userID = $('#userToModify', '.bootbox').children(":selected").attr("id").split("-")[1];

                //Fetch information about this userID from the database and return as JSON
                jQuery.ajax({
                    type: "POST",
                    url: "<?php echo base_url(); ?>" + "index.php/manageaccounts/getuser",
                    data: {userID: userID},
                    success: function(message) {
                        var obj = jQuery.parseJSON(message);
                        $("#userNewUserID", '.bootbox').val(obj.UserID);
                        $("#userNewForename", '.bootbox').val(obj.Forename);
                        $("#userNewSurname", '.bootbox').val(obj.Surname);
                        $("#userNewEmail", '.bootbox').val(obj.Email);
                    }
                });

            });
        });
    </script>

    <!-- Add user -->
    <div class="form-content addUser" style="display:none;">
        <form>
            <!-- Forename -->
            <label>Forename</label>
            <input class="form-control" id="forename" />

            <!-- Surname -->
            <label>Surname</label>
            <textarea class="form-control" id="surname" rows="4"></textarea>

            <!-- Email -->
            <label>Email</label>
            <input class="form-control" id="email" />
        </form>
    </div>

    <!-- Delete user -->
    <div class="form-content deleteUser" style="display:none;">
        <form>
            <!-- Lists of users to delete -->
            <label>Users</label>
            <select class="form-control" id="userToDelete">
                <?php
                    //Get a list of user names & users tags currently in db
                    $query = $this->db->query("SELECT * FROM users");
                    foreach ($query->result() as $row)
                    {
                        echo "<option id='Delete-{$row->UserID}'>{$row->Forename} {$row->Surname} ({$row->Email})";
                    }
                ?>
            </select>
        </form>
    </div>

    <!-- Modify user -->
    <div class="form-content modifyuser" style="display:none;">
        <form>
            <!-- Select user to Modify -->
            <label>Select user To Modify</label>
            <select class="form-control" id="userToModify">
                <?php
                    //Get a list of user names & users tags currently in db
                    $query = $this->db->query("SELECT * FROM users");
                    foreach ($query->result() as $row)
                    {
                        echo "<option id='Modify-{$row->UserID}'>{$row->Forename} {$row->Surname} ({$row->Email})";
                    }
                ?>
            </select>

            <!-- User ID -->
            <label>User ID</label>
            <input readonly class="form-control" id="userNewUserID" />

            <!-- Forename -->
            <label>Forename</label>
            <input class="form-control" id="userNewForename" />

            <!-- Surname -->
            <label>Surname</label>
            <input class="form-control" id="userNewSurname" />

            <!-- Email -->
            <label>Email</label>
            <input class="form-control" id="userNewEmail" />

        </form>
    </div>