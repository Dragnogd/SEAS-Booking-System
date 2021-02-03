  <!-- Navbar -->
  <body class="hold-transition sidebar-mini">
  <div class="wrapper">
  <nav class="main-header navbar navbar-expand navbar-dark navbar-gray-dark">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
      </li>
      <li class="nav-item d-none d-sm-inline-block">
        <a class="nav-link" href="<?php echo site_url("manageloans"); ?>">Loans</a>
      </li>
      <li class="nav-item d-none d-sm-inline-block">
        <a class="nav-link" href="<?php echo site_url("managebookings"); ?>">Setups</a>
      </li>
	    <li class="nav-item d-none d-sm-inline-block">
        <a class="nav-link" href="<?php echo site_url("manageassets"); ?>">Assets</a>
      </li>
	    <li class="nav-item d-none d-sm-inline-block">
        <a class="nav-link" href="<?php echo site_url("manageaccounts"); ?>">Accounts</a>
      </li>
      <li class="nav-item d-none d-sm-inline-block">
        <a class="nav-link" href="<?php echo site_url("assethistory"); ?>">Asset History</a>
      </li>
    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
	  <li class="nav-item d-none d-sm-inline-block">
        <a class="nav-link" href="<?php echo site_url("settings"); ?>">Settings</a>
      </li>
    </ul>
  </nav>
  <!-- /.navbar -->