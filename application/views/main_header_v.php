<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>P.O.A.S</title>
	<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
	<link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
	<link rel="stylesheet" href="./bootstrap/plugins/fontawesome-free/css/all.min.css">
	<link rel="stylesheet" href="./bootstrap/dist/css/adminlte.min.css">
	<link rel="stylesheet" href="./bootstrap/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">
	<link rel="stylesheet" href="./bootstrap/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
	<link rel="stylesheet" href="./bootstrap/plugins/overlayScrollbars/css/OverlayScrollbars.min.css">
	<link rel="stylesheet" href="./bootstrap/plugins/daterangepicker/daterangepicker.css">
	<link rel="stylesheet" href="./bootstrap/plugins/summernote/summernote-bs4.min.css">
  <!-- DataTables -->
  <link rel="stylesheet" href="./bootstrap/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
  <link rel="stylesheet" href="./bootstrap/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
  <link rel="stylesheet" href="./bootstrap/plugins/datatables-buttons/css/buttons.bootstrap4.min.css">

	<script src="./bootstrap/plugins/jquery/jquery.min.js"></script>
	<script src="./bootstrap/plugins/jquery-ui/jquery-ui.min.js"></script>
	<script src="./bootstrap/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
	<script src="./bootstrap/plugins/chart.js/Chart.min.js"></script>
	<script src="./bootstrap/plugins/sparklines/sparkline.js"></script>
	<script src="./bootstrap/plugins/jquery-knob/jquery.knob.min.js"></script>
	<script src="./bootstrap/plugins/moment/moment.min.js"></script>
	<script src="./bootstrap/plugins/daterangepicker/daterangepicker.js"></script>
	<script src="./bootstrap/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>
	<script src="./bootstrap/plugins/summernote/summernote-bs4.min.js"></script>
	<script src="./bootstrap/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
	<script src="./bootstrap/dist/js/adminlte.js"></script>
<!-- DataTables  & Plugins -->
<script src="./bootstrap/plugins/datatables/jquery.dataTables.min.js"></script>
<script src="./bootstrap/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="./bootstrap/plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
<script src="./bootstrap/plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
<script src="./bootstrap/plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
<script src="./bootstrap/plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
<script src="./bootstrap/plugins/jszip/jszip.min.js"></script>
<script src="./bootstrap/plugins/pdfmake/pdfmake.min.js"></script>
<script src="./bootstrap/plugins/pdfmake/vfs_fonts.js"></script>
<script src="./bootstrap/plugins/datatables-buttons/js/buttons.html5.min.js"></script>
<script src="./bootstrap/plugins/datatables-buttons/js/buttons.print.min.js"></script>
<script src="./bootstrap/plugins/datatables-buttons/js/buttons.colVis.min.js"></script>

</head>
<body class="hold-transition sidebar-mini layout-fixed sidebar-collapse">
<!-- <body class="hold-transition  "> -->
<div class="wrapper">
  <!-- <div class="preloader flex-column justify-content-center align-items-center">
    <img class="animation__shake" src="./bootstrap/dist/img/AdminLTELogo.png" alt="AdminLTELogo" height="60" width="60">
  </div> -->
  <nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
      <!-- <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
      </li> -->
      <li class="nav-item d-none d-sm-inline-block">
        <a href="javascript:void(0)" class="nav-link">Public Opinion Analysis System</a>
      </li>
      <li class="nav-item d-none d-sm-inline-block">
        <a href="#" class="nav-link">P.O.A.S</a>
      </li>
    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
      <li class="nav-item">
        <div class="input-group input-group-sm">
          <select class='form-control' name='select_ISP'>
            <option value='brd_fetScore'>遠傳</option>
            <option value='brd_chtScore'>中華</option>
            <option value='brd_twnScore'>台灣大哥大</option>
            <option value='brd_twsScore'>台灣之星</option>
            <option value='brd_gtScore'>亞太</option>
          </select>
        </div>
      </li>
     </ul>
  </nav>

 
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">