<aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
      <!-- Sidebar user panel -->
      <div class="user-panel">
  <div class="pull-left">
    <a href="../images/<?php echo (!empty($user['photo'])) ? $user['photo'] : 'profile.jpg'; ?>" target="_blank">
      <img src="../images/<?php echo (!empty($user['photo'])) ? $user['photo'] : 'profile.jpg'; ?>" class="img-circle" alt="User Image" style="width: 35px; height: 35px; margin-left: -1px;">
    </a>
  </div>
  <div class="pull-left info">
    <p><?php echo $user['firstname'].' '.$user['lastname']; ?></p>
    <a><i class="fa fa-circle text-success"></i> Online</a>
  </div>
  <div class="clearfix"></div>
</div>

      <!-- sidebar menu: : style can be found in sidebar.less -->
      <ul class="sidebar-menu" data-widget="tree">
        <li class="header">REPORTS</li>
        <li class=""><a href="home.php"><i class="fa fa-dashboard"></i> <span>Dashboard</span></a></li>
        <li class="header">MANAGE</li>
        
        <li><a href="attendance.php"><i class="fa fa-calendar"></i> <span>Attendance</span></a></li>
        <li class="treeview">
          <a href="#">
            <i class="fa fa-users"></i>
            <span>Employees</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="employee.php"><i class="fa fa-circle-o"></i> Employee List</a></li>
            <li><a href="overtime.php"><i class="fa fa-circle-o"></i> Overtime</a></li>
            <!-- <li><a href="cashadvance.php"><i class="fa fa-circle-o"></i> Cash Advance</a></li> -->
            <li><a href="schedule.php"><i class="fa fa-circle-o"></i> Schedules</a></li>
            <li><a href="position.php"><i class="fa fa-suitcase"></i> Positions</a></li>
          </ul>
        </li>
        <!-- <li><a href="deduction.php"><i class="fa fa-file-text"></i> Deductions</a></li> -->
        <li class="header">PRINTABLES</li>
        <!-- <li><a href="payroll.php"><i class="fa fa-files-o"></i> <span>Payroll</span></a></li> -->
        <li><a href="payroll.php"><i class="fa fa-info-circle"></i> <span>Summary</span></a></li>
        <li><a href="schedule_employee.php"><i class="fa fa-clock-o"></i> <span>Schedule</span></a></li>
        <li><a href="qr_code.php"><i class="fa fa-clock-o"></i> <span>QR Code</span></a></li>
      </ul>
    </section>
    <!-- /.sidebar -->
  </aside>
  