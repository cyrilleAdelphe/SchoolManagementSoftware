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
                  {{-- /////// AdminDashboard-v1-changed-made-here ///////// --}}
                  @if(strlen($photo))
                    <img src="{{Config::get('app.url').'app/modules/employee/assets/images/'. $photo}}" class="user-image" alt="User Image"/>
                  @else
                    <img src="{{asset('sms/assets/img/pic.png')}}" class="user-image" alt="User Image"/>
                  @endif
                  <span class="hidden-xs">{{ $employee_name }}</span>
                  {{-- /////// AdminDashboard-v1-changed-made-here ///////// --}}
                </a>
                <ul class="dropdown-menu">
                  <!-- User image -->
                  <li class="user-header">
                    {{-- /////// AdminDashboard-v1-changed-made-here ///////// --}}
                    @if(strlen($photo))
                    <img src="{{Config::get('app.url').'app/modules/employee/assets/images/'. $photo}}" class="img-circle" alt="User Image"/>
                    @else
                      <img src="{{asset('sms/assets/img/pic.png')}}" class="img-circle" alt="User Image"/>
                    @endif
                    <p>
                      {{ $employee_name }}
                    </p>
                    {{-- /////// AdminDashboard-v1-changed-made-here ///////// --}}
                  </li>
                  <!-- Menu Footer-->
                  <li class="user-footer">
                    <div class="pull-left">
                <a href="#" data-toggle="modal" class="btn btn-default btn-flat" data-target="#change-password">
                  Change Details
                </a>
              </div>
                    <div class="pull-right">
                      <a href="{{URL::route('admin-logout')}}" class="btn btn-default btn-flat">Sign out</a>
                    </div>
                  </li>
                </ul>
              </li>
            </ul>
          </div>
        </nav>
      </header>
@include('backend.admin.change-password-modal')