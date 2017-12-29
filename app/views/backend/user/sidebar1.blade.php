<aside class="main-sidebar">
        <!-- sidebar: style can be found in sidebar.less -->
        <section class="sidebar">
          <!-- Sidebar user panel -->
          <div class="user-panel">
            <div class="pull-left image">
              <img src="{{asset('/sms/assets/img/user2-160x160.jpg')}}" class="img-circle" alt="User Image" />
            </div>
            <div class="pull-left info">
              <p>{{Auth::user()->user()->name}}</p>

              <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
            </div>
          </div>
          <!-- search form -->
          <form action="#" method="get" class="sidebar-form">
            <div class="input-group">
              <input type="text" name="q" class="form-control" placeholder="Search..."/>
              <span class="input-group-btn">
                <button type='submit' name='search' id='search-btn' class="btn btn-flat"><i class="fa fa-search"></i></button>
              </span>
            </div>
          </form>
          <!-- /.search form -->
          <!-- sidebar menu: : style can be found in sidebar.less -->
          <ul class="sidebar-menu">
            <li class="header">MAIN NAVIGATION</li>
            <li class="active treeview">
              <a href="#">
                <i class="fa fa-dashboard"></i> <span>Dashboard</span>
              </a>
            </li>
            <li class="treeview">
              <a href="#">
                <i class="fa ion-university"></i>
                <span>Academic Year</span>
                <i class="fa fa-angle-left pull-right"></i>
              </a>
              <ul class="treeview-menu">
                <li><a href="#"><i class="fa fa-circle-o"></i>Primary</a></li>
                <li><a href="#"><i class="fa fa-circle-o"></i>Secondary</a></li>
                <li><a href="#"><i class="fa fa-circle-o"></i>Higher Secondary</a></li>
              </ul>
            </li>
            <li class="treeview">
              <a href="#">
                <i class="fa ion-ios-book"></i>
                <span>Subjects</span>
                <i class="fa fa-angle-left pull-right"></i>
              </a>
              <ul class="treeview-menu">
                <li><a href="#"><i class="fa fa-circle-o"></i>Primary</a></li>
                <li><a href="#"><i class="fa fa-circle-o"></i>Secondary</a></li>
                <li><a href="#"><i class="fa fa-circle-o"></i>Higher Secondary</a></li>
              </ul>
            </li>
            <li class="treeview">
              <a href="#">
                <i class="fa ion-android-contacts"></i>
                <span>Students</span>
                <i class="fa fa-angle-left pull-right"></i>
              </a>
              <ul class="treeview-menu">
                <li><a href="#"><i class="fa fa-circle-o"></i> New Admission</a></li>
                <li><a href="#"><i class="fa fa-circle-o"></i> View Students</a></li>
                <li><a href="#"><i class="fa fa-circle-o"></i>Report Card</a></li>
                <li><a href="#"><i class="fa fa-circle-o"></i>Bulk Upload</a></li>
              </ul>
            </li>
            <li class="treeview">
              <a href="#">
                <i class="fa ion-ios-people"></i>
                <span>Teachers</span>
              </a>
            </li>
            <li class="treeview">
              <a href="#">
                <i class="fa ion-person-stalker"></i>
                <span>Parents</span>
              </a>
            </li>
            <li class="treeview">
              <a href="#">
                <i class="fa ion-android-people"></i>
                <span>General Staff</span>
              </a>
            </li> 
            <li class="treeview">
              <a href="#">
                <i class="fa ion-university"></i>
                <span>Exam Manager</span>
                <i class="fa fa-angle-left pull-right"></i>
              </a>
              <ul class="treeview-menu">
                <li><a href="#"><i class="fa fa-circle-o"></i>Exam List</a></li>
                <li><a href="#"><i class="fa fa-circle-o"></i>Manage Grades</a></li>
                <li><a href="#"><i class="fa fa-circle-o"></i>Manage Marks</a></li>
                <li><a href="#"><i class="fa fa-circle-o"></i>Bulk Upload Marks</a></li>
              </ul>
            </li> 
            <li class="treeview">
              <a href="#">
                <i class="fa ion-trophy"></i>
                <span>Events Manager</span>
              </a>
            </li>
            <li class="treeview">
              <a href="#">
                <i class="fa ion-ios-albums"></i>
                <span>Library Manager</span>
              </a>
            </li>
            <li class="treeview">
              <a href="#">
                <i class="fa ion-android-bus"></i>
                <span>Transportation</span>
              </a>
            </li> 
            <li class="treeview">
              <a href="#">
                <i class="fa ion-ios-home"></i>
                <span>Dormitory</span>
              </a>
            </li>
            <li class="treeview">
              <a href="#">
                <i class="fa ion-chatbubbles"></i>
                <span>Messages</span>
              </a>
            </li>  
            <li class="treeview">
              <a href="#">
                <i class="fa ion-ios-gear"></i>
                <span>Settings</span>
                <i class="fa fa-angle-left pull-right"></i>
              </a>
              <ul class="treeview-menu">
                <li><a href="#"><i class="fa fa-circle-o"></i>General Settings</a></li>
                <li><a href="#"><i class="fa fa-circle-o"></i>Language Manager</a></li>
              </ul>
            </li>        
          </ul>
        </section>
        <!-- /.sidebar -->
      </aside>