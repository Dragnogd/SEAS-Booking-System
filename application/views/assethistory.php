<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="<?php echo site_url("manageloans"); ?>" class="brand-link">
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
          <!-- <li class="nav-item">
            <a id="createBooking" href="#" class="nav-link">
              <i class="nav-icon fas fa-book"></i>
              <p>
                New Loan
              </p>
            </a>
          </li> -->
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
                <label id="equipmentTableLabel">Assets</label>
                <select class="form-control" id="equipmentSelected">
                    <?php
                        $json = json_decode($this->AssetHistory_model->getListOfAssets());
                        foreach($json as $obj){
                            echo "<option value='{$obj->AssetTag}'>{$obj->AssetName} ({$obj->AssetTag})</option>";
                        }
                    ?>
                </select>
              </div>
            </div>
          </div>
          <!-- /.col-md-6 -->
        </div>
        <div class="row">
          <div class="col-lg-12">
            <div class="card">
              <div class="card-body table-responsive p-0">
                <div class="timeline">
                </div>
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

  <script>

    $(document).on('input', '#equipmentSelected', function(e) {
        var AssetTag = $("#equipmentSelected").val();
        jQuery.ajax({
            type: "POST",
            url: "<?php echo base_url(); ?>" + "index.php/assethistory/getAssetHistory",
            data: {AssetTag: AssetTag},
            dataType: "json",
            success: function(objJSON) {
              var timelineHTML = "";
              for (var i = 0, len = objJSON.length; i < len; ++i) {
									var loan = objJSON[i];

                  //Label
                  if(loan.LoanStatus == "Overdue"){
                    timelineHTML = timelineHTML + "<div class='time-label'><span class='bg-red'>Start: " + loan.LoanStartDate + " End: " + loan.LoanEndDate + "</div>"
                  }else if(loan.LoanStatus == "Booked"){
                    timelineHTML = timelineHTML + "<div class='time-label'><span class='bg-yellow'>Start: " + loan.LoanStartDate + " End: " + loan.LoanEndDate + "</div>"
                  }else{
                    timelineHTML = timelineHTML + "<div class='time-label'><span class='bg-green'>Start: " + loan.LoanStartDate + " End: " + loan.LoanEndDate + "</div>"
                  }


                  //Item
                  timelineHTML = timelineHTML + "<div><i class='fas fa-envelope bg-blue'></i><div class='timeline-item'><span class='time'><i class='fas fa-clock'></i>" + loan.LoanStartPeriod + " : " + loan.LoanEndPeriod + "</span><h3 class='timeline-header'><a href='#'>" + loan.Forename + " " + loan.Surname + "</a></h3><div class='timeline-body'>" + loan.AdditionalNotes + "</div></div></div>"

									$(".timeline").html(timelineHTML);
              }
            }
        });
    });

</script>