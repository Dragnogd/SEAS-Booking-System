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
		<a id="addAsset" href="#" class="nav-link">
		  <i class="nav-icon fas fa-book"></i>
		  <p>
			Add Asset
		  </p>
		</a>
	  </li>
	  <li class="nav-item">
		<a id="deleteAsset" href="#" class="nav-link">
		  <i class="nav-icon fas fa-book"></i>
		  <p>
			Delete Asset
		  </p>
		</a>
	  </li>
	  <li class="nav-item">
		<a id="modifyAsset" href="#" class="nav-link">
		  <i class="nav-icon fas fa-book"></i>
		  <p>
			Modify Asset
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
		  <div id="assetsTable" class="card-body table-responsive p-0">
			<?php
				echo $this->ManageAssets_model->getListOfAssets();
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
            //Add new asset to database
            $('#addAsset').on('click', function (e) {
                var modal = bootbox.dialog({
                    message: $(".addAsset").html(),
                    size: "large",
                    title: "Add New Asset",
                    buttons: [
                    {
                        label: "Save",
                        className: "btn btn-primary pull-right",
                        callback: function(result) {
                            //Get the data that was input into each field
                            var assetName = $('#assetName', '.bootbox').val();
                            var assetDescription = $('#assetDescription', '.bootbox').val();
                            var assetTag = $('#assetTag', '.bootbox').val();
                            var assetLocation = $('#assetLocation','.bootbox').val();
                            var validationError = true;

                            //Send ajax request to the server to save to database and then update the table on the website accordingly
                            jQuery.ajax({
                                type: "POST",
                                url: "<?php echo base_url(); ?>" + "index.php/manageassets/insertNewAsset",
                                async: false,
                                data: {assetName: assetName, assetDescription: assetDescription, assetTag: assetTag, assetLocation: assetLocation},
                                success: function(message) {
                                    //If message returned is "success" then insert new asset into table and add to other dropdowns
                                    if(message == "Success"){
                                        validationError = false;
                                        toastr.success(assetName + ' has been created');

                                        //We need to get the ID of the asset we have just created
                                        jQuery.ajax({
                                            type: "POST",
                                            url: "<?php echo base_url(); ?>" + "index.php/manageassets/getAssetID",
                                            data: {assetTag: assetTag},
                                            success: function(assetID) {
                                                var obj = JSON.parse(assetID);
                                                $("#assetsTable > tbody").append("<tr id='" + obj.AssetID + "'><td>" + assetName + "</td><td>" + assetDescription + "</td><td>" + assetTag + "</td><td>" + assetLocation + "</td></tr>");

                                                //Add to the delete asset dropdown
                                                $("<option id='Delete-" + obj.AssetID + "'>" + assetName + " (" + assetTag + ")</option>").appendTo("#assetToDelete");
                                                $("<option id='Modify-" + obj.AssetID + "'>" + assetName + " (" + assetTag + ")</option>").appendTo("#assetToModify");
                                            }
                                        });

                                        //Update Table
                                        jQuery.ajax({
                                            type: "POST",
                                            url: "<?php echo base_url(); ?>" + "index.php/manageassets/getListOfAssets",
                                            success: function(message){
                                                $("#assetsTable").html(message);
                                                modal.modal("hide");
                                                init();
                                            }
                                        });

                                    }else{
                                        $('#errorText','.bootbox').html(message + "<br>");
                                    }
                                },
                            });

                            if(validationError == true){
                                return false;
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
            });

            //Delete asset from database
            $('#deleteAsset').on('click', function (e) {
                var modal = bootbox.dialog({
                    message: $(".deleteAsset").html(),
                    size: "large",
                    title: "Delete Asset",
                    buttons: [
                    {
                        label: "Save",
                        className: "btn btn-primary pull-right",
                        callback: function(result) {
                            //Get the data that was input into each field
                            var assetID = $(".modal-body #assetToDelete").children(":selected").attr("id").split("-")[1];
                            var assetName = $(".modal-body #assetToDelete").val();

                            //Send ajax request to the server to save to database and then update the table on the website accordingly
                            jQuery.ajax({
                                type: "POST",
                                url: "<?php echo base_url(); ?>" + "index.php/manageassets/deleteAsset",
                                data: {assetID: assetID},
                                success: function(message) {
                                    //If message returned is "success" then insert new asset into table
                                    if(message == "Success"){
                                        //Remove asset from the table that was just deleted
                                        $("#" + assetID).remove();


                                        //Remove asset from ModifyAsset List
                                        $("#Modify-" + assetID).remove();
                                        $("#Delete-" + assetID).remove();

                                        //Update Table
                                        jQuery.ajax({
                                            type: "POST",
                                            url: "<?php echo base_url(); ?>" + "index.php/manageassets/getListOfAssets",
                                            success: function(message){
                                                $("#assetsTable").html(message);
                                                init();
                                            }
                                        });

                                        toastr.success(assetName + " has been deleted");
                                    }else{
                                        toastr.error(message);
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

        function init(){
            //Modify asset in database
            $('#modifyAsset').on('click', function (e) {
                var modal = bootbox.dialog({
                    message: $(".modifyAsset").html(),
                    size: "large",
                    title: "Modify Asset",
                    buttons: [
                    {
                        label: "Save",
                        className: "btn btn-primary pull-right",
                        callback: function(result) {
                            //Get the data that was input into each field
                            var assetName = $('#assetNewName', '.bootbox').val();
                            var assetDescription = $('#assetNewDescription', '.bootbox').val();
                            var assetTag = $('#assetNewTag', '.bootbox').val();
                            var assetLocation = $('#assetNewLocation','.bootbox').val();
                            var assetID = $('#assetNewID','.bootbox').val();

                            //Send ajax request to the server to save to database and then update the table on the website accordingly
                            jQuery.ajax({
                                type: "POST",
                                url: "<?php echo base_url(); ?>" + "index.php/manageassets/updateAsset",
                                data: {assetID: assetID, assetName: assetName, assetDescription: assetDescription, assetTag: assetTag, assetLocation: assetLocation},
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
                                        //Modify Asset in Table
                                        $("#" + assetID).remove();
                                        $("#assetsTable > tbody").append("<tr id='" + assetID + "'><td>" + assetName + "</td><td>" + assetDescription + "</td><td>" + assetTag + "</td><td>" + assetLocation + "</td></tr>");


                                        //Modify Asset in Delete Asset
                                        $("#Delete-" + assetID).remove();
                                        $("#Modify-" + assetID).remove();
                                        $("<option id='Modify-" + assetID + "'>" + assetName + " (" + assetTag + ")</option>").appendTo("#assetToModify");
                                        $("<option id='Delete-" + assetID + "'>" + assetName + " (" + assetTag + ")</option>").appendTo("#assetToDelete");
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

            //Modify asset "Select Asset To Modify" clicked so load in relevant information
            $(document).on('click', '#assetToModify', function(e){
                var assetID = $(".modal-body #assetToModify").children(":selected").attr("id").split("-")[1];

                //Fetch information about this assetTag from the database and return as JSON
                jQuery.ajax({
                    type: "POST",
                    url: "<?php echo base_url(); ?>" + "index.php/manageassets/getAsset",
                    data: {assetID: assetID},
                    success: function(message) {
                        var obj = jQuery.parseJSON(message);
                        $("#assetNewName", '.bootbox').val(obj.AssetName);
                        $("#assetNewDescription", '.bootbox').val(obj.AssetDescription);
                        $("#assetNewTag", '.bootbox').val(obj.AssetTag);
                        $("#assetNewLocation", '.bootbox').val(obj.AssetLocation);
                        $("#assetNewID", '.bootbox').val(assetID);
                    }
                });

            });
        }

        $(document).ready(function () {
            init();
        });
    </script>

    <!-- Add Asset -->
    <div class="form-content addAsset" style="display:none;">
        <form>
            <!-- Error -->
            <span id="errorText"></span>

            <!-- Name -->
            <label>Asset Name</label>
            <input class="form-control" id="assetName" />

            <!-- Description -->
            <label>Asset Description</label>
            <textarea class="form-control" id="assetDescription" rows="4"></textarea>

            <!-- Asset Tag -->
            <label>Asset Tag</label>
            <input class="form-control" id="assetTag" />

            <!-- Asset Location -->
            <label>Asset Location</label>
            <select class="form-control" id="assetLocation">
                <option>Ranmore</option>
                <option>Bradley</option>
            </select>
        </form>
    </div>

    <!-- Delete Asset -->
    <div class="form-content deleteAsset" style="display:none;">
        <form>
            <!-- Lists of assets to delete -->
            <label>Asset Location</label>
            <select class="form-control" id="assetToDelete">
                <?php
                    //Get a list of asset names & assets tags currently in db
                    $query = $this->db->query("SELECT AssetID, AssetName, AssetTag FROM assets");
                    foreach ($query->result() as $row)
                    {
                        echo "<option id='Delete-{$row->AssetID}'>{$row->AssetName} ({$row->AssetTag})";
                    }
                ?>
            </select>
        </form>
    </div>

    <!-- Modify Asset -->
    <div class="form-content modifyAsset" style="display:none;">
        <form>
            <!-- Select Asset to Modify -->
            <label>Select Asset To Modify</label>
            <select class="form-control" id="assetToModify">
                <?php
                    //Get a list of asset names & assets tags currently in db
                    $query = $this->db->query("SELECT AssetID, AssetName, AssetTag FROM assets");
                    foreach ($query->result() as $row)
                    {
                        echo "<option id='Modify-{$row->AssetID}'>{$row->AssetName} ({$row->AssetTag})";
                    }
                ?>
            </select>

            <!-- Name -->
            <label>Asset Name</label>
            <input class="form-control" id="assetNewName" />

            <!-- Description -->
            <label>Asset Description</label>
            <textarea class="form-control" id="assetNewDescription" rows="4"></textarea>

            <!-- Asset Tag -->
            <label>Asset Tag</label>
            <input class="form-control" id="assetNewTag" />

            <!-- Asset Location -->
            <label>Asset Location</label>
            <select class="form-control" id="assetNewLocation">
                <option>Ranmore</option>
                <option>Bradley</option>
            </select>

            <!-- Asset ID -->
            <input id="assetNewID" type="hidden" />
        </form>
    </div>