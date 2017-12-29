<aside class="main-sidebar">
  <!-- sidebar: style can be found in sidebar.less -->
  <section class="sidebar">
    <!-- Sidebar user panel -->
     <div class="user-panel">
      <div class="pull-left image">
        @if (File::exists(app_path(). '/modules/employee/assets/images/'. Auth::admin()->user()->id))
          <img src = "{{Config::get('app.url').'app/modules/employee/assets/images/'. Auth::admin()->user()->id}}" class="img-circle dynamicImage" alt="User Image">
        @else
          <img src="{{asset('/sms/assets/img/pic.png')}}" class="img-circle" alt="User Image" />
        @endif
        <script src="{{Config::get('app.url').'/app/modules/gallery/assets/js/dynamicImages.js'}}"></script>
      </div>
      <div class="pull-left info">
        <p>{{Auth::admin()->user()->name}}</p>

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
      <li class="treeview">
        <a href="{{URL::route('teacher-dashboard')}}">
          <i class="fa fa-dashboard"></i> <span>Dashboard</span>
        </a>
      </li>

      
      <li class="treeview">
        <a href="{{URL::route('attendance-create-teacher')}}">
          <i class="fa ion-stats-bars"></i>
          <span>Daily attendance</span>
        </a>
      </li>
        <li class="treeview">
        <a href="{{URL::route('teacher-update-marks')}}">
         <i class="fa fa-file-text" aria-hidden="true"></i>
          <span>Manage Marks</span>
        </a>
      </li>
      <li class="treeview">
        <a href="{{URL::route('teacher-cas-subtopics-list')}}">
         <i class="fa fa-file-text" aria-hidden="true"></i>
          <span>Subject Wise Daily Topics</span>
        </a>
      </li>
    </ul>
  </section>
  <!-- /.sidebar -->
</aside>