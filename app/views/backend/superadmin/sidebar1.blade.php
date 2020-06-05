<aside class="main-sidebar">
  <!-- sidebar: style can be found in sidebar.less -->
  <section class="sidebar">
    <!-- Sidebar user panel -->
    <div class="user-panel">
      <div class="pull-left image">
        @if (File::exists(app_path(). '/modules/superadmin/assets/images/'. Auth::superadmin()->user()->id))
          <img src = "{{Config::get('app.url').'app/modules/superadmin/assets/images/'. Auth::superadmin()->user()->id}}" class="img-circle dynamicImage" alt="User Image">
        @else
          <img src="{{asset('/sms/assets/img/pic.png')}}" class="img-circle" alt="User Image" />
        @endif
        <script src="{{Config::get('app.url').'/app/modules/gallery/assets/js/dynamicImages.js'}}"></script>
      </div>
      <div class="pull-left info">
        <p>{{Auth::superadmin()->user()->name}}</p>

        <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
      </div>
    </div>
    <!-- sidebar menu: : style can be found in sidebar.less -->
    <ul class="sidebar-menu scrollbar-dynamic">
      <li class="header">MAIN NAVIGATION</li>
      <li class="treeview">
        <a href="{{URL::route('superadmin-home')}}">
          <i class="fa fa-dashboard"></i> <span>Dashboard</span>
        </a>
      </li>
      <li class="treeview">
        <a href="#">
          <i class="fa fa-globe"></i>
          <span>Frontend website</span>
          <i class="fa fa-angle-left pull-right"></i>
        </a>
        <ul class="treeview-menu">
          <li class="treeview-item"><a href="{{URL::route('menu-create-get')}}"><i class="fa fa-circle-o"></i>Menu manager</a></li>
          <li class="treeview-item"><a href="{{URL::route('articles-main-get')}}"><i class="fa fa-circle-o"></i>Page manager</a></li>
          <li class="treeview-item"><a href="{{URL::route('list-create-get')}}"><i class="fa fa-circle-o"></i>Side List manager</a></li>
          <li class="treeview-item"><a href="{{URL::route('blocks-create-get')}}"><i class="fa fa-circle-o"></i>Bottom Blocks manager</a>
          <li class="treeview-item"><a href="{{URL::route('slides-create-get')}}"><i class="fa fa-circle-o"></i>Image slide</a> </li>
          <li class="treeview-item"><a href="{{URL::route('gallery-create-get')}}"><i class="fa fa-circle-o"></i>Photo Gallery manager</a> </li>
          <li class="treeview-item"><a href="{{URL::route('video-gallery-config-get')}}"><i class="fa fa-circle-o"></i>Video Gallery manager</a> </li>
          <li class="treeview-item"><a href="{{URL::route('contact-us-config-get')}}"><i class="fa fa-circle-o"></i>Contact</a> </li>
          <li class="treeview-item"><a href="{{URL::route('testimonial-list')}}"><i class="fa fa-circle-o"></i>Testimonials</a> </li>
          <li class="treeview-item"><a href="{{URL::route('general-downloads-main')}}"><i class="fa fa-circle-o"></i>General Downloads</a> </li>
        </ul>
      </li> 
      <li class="treeview">
        <a href="{{URL::route('academic-session-list')}}">
          <i class="fa ion-university"></i>
          <span>Academic Year</span>
        </a>
      </li>
      <li class="treeview">
        <a href="#">
          <i class="fa fa-fw fa-users"></i>
          <span>Class Manager</span>
          <i class="fa fa-angle-left pull-right"></i>
        </a>
        <ul class="treeview-menu">
          <li class="treeview-item"><a href="{{URL::route('classes-list')}}"><i class="fa fa-circle-o"></i>Class list</a></li>
          <li class="treeview-item"><a href="{{URL::route('section-create-get')}}"><i class="fa fa-circle-o"></i>Create sections</a></li>
          <li class="treeview-item"><a href="{{URL::route('class-section-list')}}"><i class="fa fa-circle-o"></i>Assign section</a></li>
          <!--<li class="treeview-item"><a href="{{URL::route('grade-update-get')}}"><i class="fa fa-circle-o"></i>Grade setting</a></li>-->
        </ul>
      </li>  
      <li class="treeview">
        <a href="{{URL::route('subject-list')}}">
          <i class="fa ion-ios-book"></i>
          <span>Subjects</span>
        </a>              
      </li>
      <li class="treeview">
          <a href="#">
            <i class="fa ion-ios-people"></i>
            <span>School Stakeholders</span>
            <i class="fa fa-angle-left pull-right"></i>
          </a>
          <ul class="treeview-menu">
            <li class="treeview">
              <a href="#"><i class="fa fa-circle-o"></i>Students <i class="fa fa-angle-left pull-right"></i></a>
              <ul class="treeview-menu">
                <li class="treeview-item"><a href="{{URL::route('student-list')}}"><i class="fa fa-circle-o"></i>Create/View List</a></li>
                <li class="treeview-item"><a href="{{URL::route('list-house')}}"><i class="fa fa-circle-o"></i>Create House</a></li>
                <li class="treeview-item"><a href="{{ URL::route('ethnicity-list') }}"><i class="fa fa-circle-o"></i>Create Ethnicity</a></li>
                <li class="treeview-item"><a href="{{ URL::route('student-report-get')}}"><i class="fa fa-circle-o"></i>Generate Report</a></li>
              </ul>
            </li>
            <li class="treeview-item">
              <a href="{{URL::route('guardian-list')}}">
                <i class="fa fa-circle-o"></i>
                <span>Parents</span>
              </a>
            </li>
            <li class="treeview">
              <a href="#"><i class="fa fa-circle-o"></i>School Staff <i class="fa fa-angle-left pull-right"></i></a>
              <ul class="treeview-menu">
                <li class="treeview-item"><a href="{{URL::route('employee-list')}}"><i class="fa fa-circle-o"></i> Create Staff</a></li>
                <li class="treeview-item"><a href="{{URL::route('teacher-list')}}"><i class="fa fa-circle-o"></i> Create Teacher</a></li>
              </ul>
            </li>
          </ul>
      </li>
      <li class="treeview">
        <a href="#">
          <i class="fa fa-fw fa-file-text-o"></i>
          <span>Continuous Assessment</span>
          <i class="fa fa-angle-left pull-right"></i>
        </a>
        <ul class="treeview-menu">
          <li class = "treeview-item">
            <a href="{{URL::route('cas-grade-settings-list')}}">
              <i class="fa fa-circle-o"></i>
              Grade Settings
            </a>
          </li>
          <li class = "treeview-item">
            <a href="{{URL::route('cas-sub-topics-list')}}">
              <i class="fa fa-circle-o"></i>
              Subject-wise Daily Topics
            </a>
          </li>
          <li class = "treeview-item">
            <a href="{{URL::route('cas-setting-get')}}">
              <i class="fa fa-circle-o"></i>
              CAS Settings
            </a>
          </li>
        </ul>
      </li>
       <li class="treeview">
        <a href="#">
          <i class="fa ion-university"></i>
          <span>Exam Manager</span>
          <i class="fa fa-angle-left pull-right"></i>
        </a>
        <ul class="treeview-menu">
          <li class="treeview-item"><a href="{{URL::route('exam-configuration-list')}}"><i class="fa fa-circle-o"></i>Exam Configuration</a></li>
          <li class="treeview-item"><a href="{{URL::route('exam-details-list')}}"><i class="fa fa-circle-o"></i>Exam Details</a></li>
          <li class="treeview-item"><a href="{{URL::route('exam-marks-update-get')}}"><i class="fa fa-circle-o"></i>Manage Marks</a></li>
          <li class = "treeview-item"><a href="{{URL::route('remark-setting-list')}}"><i class="fa fa-circle-o"></i>Remark Settings</a></li>
          <li class="treeview-item">
            <a href="{{URL::route('report-list')}}"><i class="fa fa-circle-o"></i>Progress Report</a>
          </li> 
        </ul>
      </li> 
       
      <li class="treeview">
        <a href="#">
          <i class="fa fa-fw fa-money"></i>
          <span>Billing Manager</span>
          <i class="fa fa-angle-left pull-right"></i>
        </a>

        <ul class="treeview-menu">
          <li class = "treeview-item">
            <a href="{{URL::route('billing-create-fee-get')}}">
             <i class="fa fa-circle-o"></i>
              Create fee
            </a>
          </li>
          <li class = "treeview-item">
            <a href="{{URL::route('billing-create-extra-fee-get')}}">
              <i class="fa fa-circle-o"></i>
              Create Extra fees
            </a>
          </li>
          <li class = "treeview-item">
            <a href="{{URL::route('billing-assign-fee-to-discount-get')}}">
              <i class="fa fa-circle-o"></i>
              Fee to Discount
            </a>
          </li>
          <li class="treeview-item">
            <a href="{{URL::route('billing-generate-fee-get')}}">
              <i class="fa fa-circle-o"></i>
              Generate fee
            </a>
          </li>
          <li class="treeview-item">
            <a href="{{URL::route('billing-direct-invoice-get')}}">
              <i class="fa fa-circle-o"></i>
              Direct Invoice
            </a>
          </li>
          <li class="treeview-item">
            <a href="{{URL::route('billing-direct-invoice-organization-get')}}">
              <i class="fa fa-circle-o"></i>
              Direct Invoice Organization
            </a>
          </li>
          <li class="treeview-item">
            <a href="{{URL::route('billing-recieve-payment-get')}}">
              <i class="fa fa-circle-o"></i>
              Receive payment
            </a>
          </li>
          <li class="treeview-item">
            <a href="{{URL::route('advance-billing-create-get')}}">
              <i class="fa fa-circle-o"></i>
              Receive Advance Payment
            </a>
          </li>
          
          <li class="treeview-item">
            <a href="{{URL::route('billing-statement')}}">
             <i class="fa fa-circle-o"></i>
              Statement
            </a>
          </li>
          <li class="treeview-item">
            <a href="{{URL::route('billing-transaction-list')}}">
              <i class="fa fa-circle-o"></i>
              Transaction list
            </a>
          </li>
          <li class="treeview-item">
            <a href="{{URL::route('billing-tax-report')}}">
              <i class="fa fa-circle-o"></i>
              Tax report
            </a>
          </li>
          <li class="treeview-item">
            <a href="{{URL::route('billing-income-report')}}">
              <i class="fa fa-circle-o"></i>
              Income report
            </a>
          </li>
          <li class = "treeview-item">
            <a href="{{URL::route('billing-remaining-due-list')}}">
              <i class="fa fa-circle-o"></i>
              Remaining Dues
            </a>
          </li>
          <li class = "treeview-item">
            <a href="{{URL::route('billing-opening-balance-get')}}">
              <i class="fa fa-circle-o"></i>
              Opening Balance
            </a>
          </li>
          <li class = "treeview-item">
            <a href="{{URL::route('billing-invoice-list')}}">
              <i class="fa fa-circle-o"></i>
              Invoice List
            </a>
          </li>
          <li class = "treeview-item">
            <a href="{{URL::route('billing-late-fee-get')}}">
              <i class="fa fa-circle-o"></i>
              Late Fee Setting
            </a>
          </li>
          <li class = "treeview-item">
            <a href="{{URL::route('billing-list-view-fee-print-get')}}">
              <i class="fa fa-circle-o"></i>
              Notice Bill
            </a>
          </li>
          <li class = "treeview-item">
            <a href="{{URL::route('billing-fee-print-organization-get')}}">
              <i class="fa fa-circle-o"></i>
              Organization Notice Bill
            </a>
          </li>
          <li class = "treeview-item">
            <a href="{{URL::route('billing-get-receipt-list')}}">
              <i class="fa fa-circle-o"></i>
              Receipt List
            </a>
          </li>
          <li class = "treeview-item">
            <a href="{{URL::route('billing-show-receipts-get')}}">
              <i class="fa fa-circle-o"></i>
              Receipt Transaction
            </a>
          </li>
        </ul>
      </li>
      <li class="treeview">
        <a href="#">
          <i class="fa fa-dollar" aria-hidden="true"/></i>
          <span>Discount manager</span>
          <i class="fa fa-angle-left pull-right"></i>
        </a>
        <ul class="treeview-menu">
            <li class = "treeview-item">
              <a href="{{URL::route('billing-discount-list')}}">
                <i class="fa fa-circle-o"></i>
                Discounts
              </a>
            </li>
            <li class = "treeview-item">
              <a href="{{URL::route('billing-discount-organization-list')}}">
                <i class="fa fa-circle-o"></i>
                <span>Create Organizations</span>
              </a>
            </li>
            <li class = "treeview-item">
              <a href="{{URL::route('billing-discount-create-flat-discounts-get')}}">
                <i class="fa fa-circle-o"></i>
                <span>Create Flat Discounts</span>
              </a>
            </li>
        </ul>
      </li>
      <li class="treeview">
          <a href="#">
            <i class="fa fa-dollar" aria-hidden="true"/></i>
            <span>Expense manager</span>
            <i class="fa fa-angle-left pull-right"></i>
          </a>
          <ul class="treeview-menu">
            <li class="treeview-item"><a href="{{ URL::route('expense-list')}}"><i class="fa fa-circle-o"></i>Expenses</a></li>
            <li class="treeview-item"><a href="{{ URL::route('accounts-list') }}"><i class="fa fa-circle-o"></i>Accounts</a></li>
            <li class="treeview-item"><a href="{{ URL::route('cash-list') }}"><i class="fa fa-circle-o"></i>Cash in hand</a></li>            
          </ul>
        </li>
      
      <li class="treeview">
        <a href="{{URL::route('daily-routine-list')}}">
          <i class="fa fa-book"></i>
          <span>Daily Rotine</span>
        </a>
      </li> 
      <li class="treeview">
        <a href="{{URL::route('attendance-create-get')}}">
          <i class="fa ion-stats-bars"></i>
          <span>Daily attendance</span>
        </a>
      </li>
      <li class="treeview">
        <a href="{{URL::route('assignments-files')}}">
          <i class="fa ion-compose"></i>
          <span>Study Materials</span>
        </a>
      </li>
      <!-- <li class="treeview">
        <a href="{{URL::route('pdr-list')}}">
          <i class="fa ion-compose"></i>
          <span>PDR</span>
        </a>
      </li> -->
      <li class="treeview">
        <a href="{{URL::route('events-list')}}">
          <i class="fa ion-trophy"></i>
          <span>Events Manager</span>
        </a>
      </li>

       <li class="treeview">
        <a href="{{URL::route('extra-activity-list')}}">
          <i class="fa fa-rocket"></i>
          <span>Extra Activities Manager</span>
        </a>
      </li>
      
      <li class="treeview">
        <a href="{{URL::route('books-list')}}">
          <i class="fa ion-ios-albums"></i>
          <span>Library Manager</span>
        </a>
      </li>
      <!--<li class="treeview">
        <a href="{{ URL::route('staff-request-staffs-history-list') }}">
          <i class="fa fa-fw fa-info"></i>
          <span>Staff Request</span>
        </a>
      </li>-->

      <!-- <li class="treeview">
        <a href="{{URL::route('message-list')}}">
          <i class="fa fa-fw fa-envelope"></i>
          <span>Messages</span>
        </a>
      </li>  -->    
       <li class="treeview">
        <a href="{{URL::route('dormitory-student-create-get')}}">
          <i class="fa ion-ios-home"></i>
          <span>Hostel Manager</span>
        </a>
      </li>
      <li class="treeview">
        <a href="{{URL::route('transportation-list')}}">
          <i class="fa ion-android-bus"></i>
          <span>Transportation</span>
        </a>
      </li>  
      <li class="treeview">
        <a href="#">
          <i class="fa ion-ios-gear"></i>
          <span>Settings</span>
          <i class="fa fa-angle-left pull-right"></i>
        </a>
        <ul class="treeview-menu">
          <!-- <li class="treeview-item"><a href="{{URL::route('create-group')}}"><i class="fa fa-circle-o"></i>Create Group</a></li> -->
          <li class="treeview-item"><a href="{{URL::route('settings-general-get')}}"><i class="fa fa-circle-o"></i>General Settings</a></li>
          
          <!-- <li class="treeview-item"><a href="#"><i class="fa fa-circle-o"></i>Language Manager</a></li> -->
          <li class="treeview-item"><a href="{{URL::route('access-list')}}"><i class="fa fa-circle-o"></i>Access Control</a></li>
          <!-- <li class="treeview-item"><a href="{{URL::route('download-manager-drive-config-get')}}"><i class="fa fa-circle-o"></i>Google Drive Control</a></li> -->
          <li class="treeview-item"><a href="{{URL::route('users-list')}}"><i class="fa fa-circle-o"></i>Manage Students &Parents</a></li>
        </ul>
      </li>        
    </ul>
  </section>
  <!-- /.sidebar -->
</aside>