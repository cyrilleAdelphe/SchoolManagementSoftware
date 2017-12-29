<header class="main-header">
        <!-- Logo -->
        <a href="index2.html" class="logo">
          <!-- mini logo for sidebar mini 50x50 pixels -->
          <span class="logo-mini"><b>{{SettingsHelper::getGeneralSetting('short_school_name')}}</b></span>
          <!-- logo for regular state and mobile devices -->
          <span class="logo-lg"><b>{{SettingsHelper::getGeneralSetting('long_school_name')}}</b></span>
        </a>
        <!-- Header Navbar: style can be found in header.less -->
        <nav class="navbar navbar-static-top" role="navigation">
          <!-- Sidebar toggle button-->
          <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
            <span class="sr-only">Toggle navigation</span>
          </a>
          <div class="navbar-custom-menu">
            <ul class="nav navbar-nav">             
            
              <!-- User Account: style can be found in dropdown.less -->
              <li class="dropdown user user-menu">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown">
            @if (File::exists(app_path(). '/modules/employee/assets/images/'. Auth::admin()->user()->id))
              <img src = "{{Config::get('app.url').'app/modules/employee/assets/images/'. Auth::admin()->user()->id}}" class="user-image dynamicImage" alt="User Image" />
            @else
              <img src="{{asset('/sms/assets/img/pic.png')}}" class="user-image" alt="User Image" />
            @endif
            
            <span class="hidden-xs">{{Auth::admin()->user()->name}}</span>
          </a>
          <ul class="dropdown-menu">
            <!-- User image -->
            <li class="user-header">
              @if (File::exists(app_path(). '/modules/employee/assets/images/'. Auth::admin()->user()->id))
                <img src = "{{Config::get('app.url').'app/modules/employee/assets/images/'. Auth::admin()->user()->id}}" class="img-circle dynamicImage" alt="User Image">
              @else
                <img src="{{asset('/sms/assets/img/pic.png')}}" class="img-circle" alt="User Image" />
              @endif
              <script src="{{Config::get('app.url').'/app/modules/gallery/assets/js/dynamicImages.js'}}"></script>
              <p>
                {{Auth::admin()->user()->name}}
              </p>
            </li>
                  <!-- Menu Footer-->
                  <li class="user-footer">
                    <div class="pull-left">
                      <a href="{{URL::route('teacher-profile')}}"  class="btn btn-default btn-flat">Profile</a>
                    </div>
                    <div class="pull-right">
                      <a href="{{URL::route('teacher-logout')}}" class="btn btn-default btn-flat">Sign out</a>
                    </div>
                  </li>
                </ul>
              </li>
            </ul>
          </div>
        </nav>
      </header>
