<aside class="main-sidebar">
  <!-- sidebar: style can be found in sidebar.less -->
  <section class="sidebar">
    <!-- Sidebar user panel -->
    <div class="user-panel">
      <div class="pull-left image">
        
        {{-- /////// AdminDashboard-v1-changed-made-here ///////// --}}
        @if(strlen($photo))
          <img src="{{Config::get('app.url').'app/modules/employee/assets/images/'. $photo}}" class="img-circle" alt="User Image"/>
        @else
          <img src="{{asset('sms/assets/img/pic.png')}}" class="img-circle" alt="User Image"/>
        @endif
        {{-- /////// AdminDashboard-v1-changed-made-here ///////// --}}
      </div>
      <div class="pull-left info">
        {{-- /////// AdminDashboard-v1-changed-made-here ///////// --}}
        <p>{{$employee_name}}</p>
        {{-- /////// AdminDashboard-v1-changed-made-here ///////// --}}

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
    <ul class="sidebar-menu scrollbar-dynamic">
      <li class="header">MAIN NAVIGATION</li>
      <li class="treeview">
        <a href="{{URL::route('admin-home')}}">
          <i class="fa fa-dashboard"></i> <span>Dashboard</span>
        </a>      </li>
      
      <li class="treeview">
        <a href="#">
          <i class="fa fa-globe"></i>
          <span>Frontend website</span>
          <i class="fa fa-angle-left pull-right"></i>
        </a>
        
          <ul class="treeview-menu">
          @if(AccessController::checkPermission('menu', 'can_create'))  
          <li class="treeview-item"><a href="{{URL::route('menu-create-get')}}"><i class="fa fa-circle-o"></i>Menu manager</a></li>
          @endif
          @if(AccessController::checkPermission('articles', 'can_view')) 
          <li class="treeview-item"><a href="{{URL::route('articles-main-get')}}"><i class="fa fa-circle-o"></i>Page manager</a></li>
          @endif
          @if(AccessController::checkPermission('list', 'can_view')) 
          <li class="treeview-item"><a href="{{URL::route('list-create-get')}}"><i class="fa fa-circle-o"></i>Side List manager</a></li>
          @endif
          @if(AccessController::checkPermission('list', 'can_create')) 
          <li class="treeview-item"><a href="{{URL::route('blocks-create-get')}}"><i class="fa fa-circle-o"></i>Bottom Blocks manager</a>
          @endif

          @if(AccessController::checkPermission('list', 'can_create')) 
          <li class="treeview-item"><a href="{{URL::route('slides-create-get')}}"><i class="fa fa-circle-o"></i>Image slide</a> </li>
          @endif
          @if(AccessController::checkPermission('gallery', 'can_create')) 
          <li class="treeview-item"><a href="{{URL::route('gallery-create-get')}}"><i class="fa fa-circle-o"></i>Photo Gallery manager</a> </li>
          @endif
          @if(AccessController::checkPermission('gallery', 'can_edit')) 
          <li class="treeview-item"><a href="{{URL::route('video-gallery-config-get')}}"><i class="fa fa-circle-o"></i>Video Gallery manager</a> </li>
          @endif
          
          <li class="treeview-item"><a href="{{URL::route('contact-us-config-get')}}"><i class="fa fa-circle-o"></i>Contact</a> </li>
          @if(AccessController::checkPermission('gallery', 'can_view')) 
          <li class="treeview-item"><a href="{{URL::route('testimonial-list')}}"><i class="fa fa-circle-o"></i>Testimonials</a> </li>
          @endif
          @if(AccessController::checkPermission('gallery', 'can_view')) 
          <li class="treeview-item"><a href="{{URL::route('general-downloads-main')}}"><i class="fa fa-circle-o"></i>General Downloads</a> </li>
          @endif
        </ul>
        
      </li>
      

      @if(AccessController::checkPermission('books', 'can_view'))       
      @if ( HelperController::checkAdminGroup('Librarian') )
      <li class="treeview">
        <a href="{{URL::route('books-list')}}">
          <i class="fa ion-ios-albums"></i>
        
          <span>Library Manager</span>
        </a>
      </li>   
      @endif
      @endif

     @if(AccessController::checkPermission('academic-session', 'can_view'))
      <li class="treeview">
        <a href="{{URL::route('academic-session-list')}}">
          <i class="fa ion-university"></i>
          <span>Academic Year</span>
        </a>
      </li>
      @endif

      @if(AccessController::checkPermission('classes', 'can_view'))
      <li class="treeview">
        <a href="#">
          <i class="fa ion-university"></i>
          <span>Class Manager</span>
          <i class="fa fa-angle-left pull-right"></i>
        </a>
        <ul class="treeview-menu">
        @if(AccessController::checkPermission('classes', 'can_view'))
          <li class="treeview-item"><a href="{{URL::route('classes-list')}}"><i class="fa fa-circle-o"></i>Class list</a></li>
        @endif
        @if(AccessController::checkPermission('section', 'can_create'))
          <li class="treeview-item"><a href="{{URL::route('section-create-get')}}"><i class="fa fa-circle-o"></i>Create sections</a></li>
        @endif
        @if(AccessController::checkPermission('class-section', 'can_view'))
          <li class="treeview-item"><a href="{{URL::route('class-section-list')}}"><i class="fa fa-circle-o"></i>Assign section</a></li>
        @endif
        </ul>
      </li>  
      @endif

      @if(AccessController::checkPermission('subject', 'can_view'))
      <li class="treeview">
        <a href="{{URL::route('subject-list')}}">
          <i class="fa ion-ios-book"></i>
          <span>Subjects</span>
        </a>              
      </li>
      @endif


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
                @if(AccessController::checkPermission('student', 'can_view'))
                <li class="treeview-item"><a href="{{URL::route('student-list')}}"><i class="fa fa-circle-o"></i>Create/View List</a></li> 
                @endif
                @if(AccessController::checkPermission('houses', 'can_view'))
                <li class="treeview-item"><a href="{{URL::route('list-house')}}"><i class="fa fa-circle-o"></i>Create House</a></li>
                @endif
                @if(AccessController::checkPermission('ethnicity', 'can_view'))
                <li class="treeview-item"><a href="{{ URL::route('ethnicity-list') }}"><i class="fa fa-circle-o"></i>Create Ethnicity</a></li>
                @endif
                @if(AccessController::checkPermission('student', 'can_show_report'))
                <li class="treeview-item"><a href="{{ URL::route('student-report-get')}}"><i class="fa fa-circle-o"></i>Generate Report</a></li>
                @endif
              </ul>
            </li>

            @if(AccessController::checkPermission('guardian', 'can_view'))
            <li class="treeview-item">
              <a href="{{URL::route('guardian-list')}}">
                <i class="fa fa-circle-o"></i>
                <span>Parents</span>
              </a>
            </li>
            @endif

         @if(AccessController::checkPermission('employee', 'can_view'))
            <li class="treeview">
              <a href="#"><i class="fa fa-circle-o"></i>School Staff <i class="fa fa-angle-left pull-right"></i></a>
              <ul class="treeview-menu">
              @if(AccessController::checkPermission('employee', 'can_view'))
                <li class="treeview-item"><a href="{{URL::route('employee-list')}}"><i class="fa fa-circle-o"></i> Create Staff</a></li>
              @endif
               @if(AccessController::checkPermission('teacher', 'can_view'))
                <li class="treeview-item"><a href="{{URL::route('teacher-list')}}"><i class="fa fa-circle-o"></i> Create Teacher</a></li>
              @endif
              </ul>
            </li>
          @endif
          </ul>
         </li>
        
    @if(AccessController::checkPermission('pdr', 'can_view'))
      <li class="treeview">   
        <a href="{{URL::route('pdr-list')}}">
          <i class="fa ion-compose"></i>
          <span>Daily Student Progress</span>
        </a>
      </li>
    @endif
    @if(AccessController::checkPermission('cas', 'can_view'))
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
      @endif


   @if(AccessController::checkPermission('exam-configuration', 'can_view'))
      <li class="treeview">
        <a href="#">
          <i class="fa ion-university"></i>
          <span>Exam Manager</span>
          <i class="fa fa-angle-left pull-right"></i>
        </a>
        <ul class="treeview-menu">
        @if(AccessController::checkPermission('exam-configuration', 'can_view'))
          <li class="treeview-item"><a href="{{URL::route('exam-configuration-list')}}"><i class="fa fa-circle-o"></i>Exam Configuration</a></li>
        @endif
        @if(AccessController::checkPermission('exam-details', 'can_view'))
          <li class="treeview-item"><a href="{{URL::route('exam-details-list')}}"><i class="fa fa-circle-o"></i>Exam Details</a></li>
        @endif
        @if(AccessController::checkPermission('exam-marks', 'can_view'))
          <li class="treeview-item"><a href="{{URL::route('exam-marks-update-get')}}"><i class="fa fa-circle-o"></i>Manage Marks</a></li>
        @endif
        @if(AccessController::checkPermission('cas', 'can_view'))
          <li class = "treeview-item"><a href="{{URL::route('remark-setting-list')}}"><i class="fa fa-circle-o"></i>Remark Settings</a></li>
        @endif
        @if(AccessController::checkPermission('report', 'can_view'))
          <li class="treeview-item">
            <a href="{{URL::route('report-list')}}"><i class="fa fa-circle-o"></i>Progress Report</a>
          </li> 
        @endif
        </ul>
      </li> 
      @endif

   @if(AccessController::checkPermission('billing', 'can_view'))
      
      <li class="treeview">
        <a href="#">
          <i class="fa fa-fw fa-money"></i>
          <span>Billing Manager</span>
          <i class="fa fa-angle-left pull-right"></i>
        </a>
          
        <ul class="treeview-menu">
        @if(AccessController::checkPermission('billing', 'can_create_fee'))
          <li class = "treeview-item">
            <a href="{{URL::route('billing-create-fee-get')}}">
             <i class="fa fa-circle-o"></i>
              Create fee
            </a>
          </li>
          @endif
          @if(AccessController::checkPermission('billing', 'can_create_extra_fee'))
          <li class = "treeview-item">
            <a href="{{URL::route('billing-create-extra-fee-get')}}">
              <i class="fa fa-circle-o"></i>
              Create Extra fees
            </a>
          </li>
          @endif
          @if(AccessController::checkPermission('billing', 'can_generate_fee'))
          <li class="treeview-item">
            <a href="{{URL::route('billing-generate-fee-get')}}">
              <i class="fa fa-circle-o"></i>
              Generate fee
            </a>
          </li>
          @endif
          @if(AccessController::checkPermission('billing', 'can_view_invoice'))
          <li class="treeview-item">
            <a href="{{URL::route('billing-direct-invoice-get')}}">
              <i class="fa fa-circle-o"></i>
              Direct Invoice
            </a>
          </li>
          @endif
          @if(AccessController::checkPermission('billing', 'can_receive_payment'))
          <li class="treeview-item">
            <a href="{{URL::route('billing-recieve-payment-get')}}">
              <i class="fa fa-circle-o"></i>
              Receive payment
            </a>
          </li>
          @endif
          @if(AccessController::checkPermission('billing', 'can_view_statement'))
          <li class="treeview-item">
            <a href="{{URL::route('billing-statement')}}">
             <i class="fa fa-circle-o"></i>
              Statement
            </a>
          </li>
          @endif
          @if(AccessController::checkPermission('billing', 'can_view_transaction'))
          <li class="treeview-item">
            <a href="{{URL::route('billing-transaction-list')}}">
              <i class="fa fa-circle-o"></i>
              Transaction list
            </a>
          </li>
          @endif
          @if(AccessController::checkPermission('billing', 'can_view_tax_report'))
          <li class="treeview-item">
            <a href="{{URL::route('billing-tax-report')}}">
              <i class="fa fa-circle-o"></i>
              Tax report
            </a>
          </li>
          @endif
          @if(AccessController::checkPermission('billing', 'can_view_income_report'))
          <li class="treeview-item">
            <a href="{{URL::route('billing-income-report')}}">
              <i class="fa fa-circle-o"></i>
              Income report
            </a>
          </li>
          @endif
          @if(AccessController::checkPermission('billing', 'can_view_remaining_due_list'))
          <li class = "treeview-item">
            <a href="{{URL::route('billing-remaining-due-list')}}">
              <i class="fa fa-circle-o"></i>
              Remaining Dues
            </a>
          </li>
          @endif
          @if(AccessController::checkPermission('billing', 'can_set_opening_balance'))
          <li class = "treeview-item">
            <a href="{{URL::route('billing-opening-balance-get')}}">
              <i class="fa fa-circle-o"></i>
              Opening Balance
            </a>
          </li>
          @endif
          @if(AccessController::checkPermission('billing', 'can_view_invoice_list'))
          <li class = "treeview-item">
            <a href="{{URL::route('billing-invoice-list')}}">
              <i class="fa fa-circle-o"></i>
              Invoice List
            </a>
          </li>
          @endif
          @if(AccessController::checkPermission('billing', 'can_set_late_fee'))
          <li class = "treeview-item">
            <a href="{{URL::route('billing-late-fee-get')}}">
              <i class="fa fa-circle-o"></i>
              Late Fee Setting
            </a>
          </li>
          @endif
           @if(AccessController::checkPermission('billing', 'can_print_fee'))
          <li class = "treeview-item">
            <a href="{{URL::route('billing-list-view-fee-print-get')}}">
              <i class="fa fa-circle-o"></i>
              Fee Print
            </a>
          </li>
          @endif
          @if(AccessController::checkPermission('billing', 'can_print_fee'))
          <li class = "treeview-item">
            <a href="{{URL::route('billing-fee-print-organization-get')}}">
              <i class="fa fa-circle-o"></i>
              Organization Fee Print
            </a>
          </li>
          @endif
          @if(AccessController::checkPermission('billing', 'can_view_receipt_list'))
          <li class = "treeview-item">
            <a href="{{URL::route('billing-get-receipt-list')}}">
              <i class="fa fa-circle-o"></i>
              Receipt List
            </a>
          </li>
          @endif
          @if(AccessController::checkPermission('billing', 'can_view_receipt_transaction'))
          
          <li class = "treeview-item">
            <a href="{{URL::route('billing-show-receipts-get')}}">
              <i class="fa fa-circle-o"></i>
              Receipt Transaction
            </a>
          </li>
          @endif
        </ul>
      </li>
           
      @endif
      
      @if(AccessController::checkPermission('billing', 'can_view'))
      <li class="treeview">
        <a href="#">
          <i class="fa fa-dollar" aria-hidden="true"/></i>
          <span>Discount manager</span>
          <i class="fa fa-angle-left pull-right"></i>
        </a>
        <ul class="treeview-menu">
        @if(AccessController::checkPermission('billing', 'can_view'))
            <li class = "treeview-item">
              <a href="{{URL::route('billing-discount-list')}}">
                <i class="fa fa-circle-o"></i>
                Discounts
              </a>
            </li>
        @endif
        @if(AccessController::checkPermission('billing', 'can_view_organization'))
            <li class = "treeview-item">
              <a href="{{URL::route('billing-discount-organization-list')}}">
                <i class="fa fa-circle-o"></i>
                <span>Create Organizations</span>
              </a>
            </li>
        @endif
        @if(AccessController::checkPermission('billing', 'can_create_discount'))
            <li class = "treeview-item">
              <a href="{{URL::route('billing-discount-create-flat-discounts-get')}}">
                <i class="fa fa-circle-o"></i>
                <span>Create Flat Discounts</span>
              </a>
            </li>
        @endif
        </ul>
      </li>
        @endif

      @if(AccessController::checkPermission('expense_manager', 'can_view'))
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
        @endif
      
     @if(AccessController::checkPermission('daily-routine', 'can_view'))
      <li class="treeview">
        <a href="{{URL::route('daily-routine-list')}}">
          <i class="fa fa-book"></i>
          <span>Daily Rotine</span>
        </a>
      </li> 
      @endif

      @if(AccessController::checkPermission('attendance', 'can_create'))
      <li class="treeview">
      @if(HelperController::checkAdminGroup('teacher'))
        <a href="{{URL::route('attendance-create-teacher')}}">
      @else
        <a href="{{URL::route('attendance-create-get')}}">
      @endif
          <i class="fa ion-stats-bars"></i>
          <span>Daily attendance</span>
        </a>
      </li>
      @endif

      @if(AccessController::checkPermission('assignments', 'can_get_study_materials'))
       <li class="treeview">
        <a href="{{URL::route('assignments-files')}}">
          <i class="fa ion-compose"></i>
          <span>Study Materials</span>
        </a>
      </li>
      @endif
      
       @if(AccessController::checkPermission('report', 'can_view'))
       <li class="treeview">
        <a href="{{URL::route('report-list')}}">
          <i class="fa fa-fw fa-file-text-o"></i>
          <span>Progress report</span>
        </a>
      </li> 
        @endif


      @if(AccessController::checkPermission('events', 'can_view'))
       <li class="treeview">
        <a href="{{URL::route('events-list')}}">
          <i class="fa ion-trophy"></i>
          <span>Events Manager</span>
        </a>
      </li>
      @endif

      @if(AccessController::checkPermission('extra-activity', 'can_view'))
       <li class="treeview">
        <a href="{{URL::route('extra-activity-list')}}">
          <i class="fa fa-rocket"></i>
          <span>Extra Activities Manager</span>
        </a>
      </li>
      @endif

      @if(AccessController::checkPermission('staff-request', 'can_view'))
      <li class="treeview">
        <a href="{{URL::route('staff-request-list')}}">
          <i class="fa ion-ios-albums"></i>
          <span>Staff Request</span>
        </a>
      </li>
      @endif
      @if(AccessController::checkPermission('dormitory-student', 'can_create'))
      <li class="treeview">
        <a href="{{URL::route('dormitory-student-create-get')}}">
          <i class="fa ion-ios-home"></i>
          <span>Hostel Manager</span>
        </a>
      </li>
      @endif

       @if(AccessController::checkPermission('transportation', 'can_view'))
      <li class="treeview">
        <a href="{{URL::route('transportation-list')}}">
          <i class="fa ion-android-bus"></i>
          <span>Transportation</span>
        </a>
      </li>  
      @endif

      @if(AccessController::checkPermission('settings', 'can_view'))

      <li class="treeview">
        <a href="#">
          <i class="fa ion-ios-gear"></i>
          <span>Settings</span>
          <i class="fa fa-angle-left pull-right"></i>
        </a>
        <ul class="treeview-menu">
        @if(AccessController::checkPermission('settings', 'can_view'))
          <li class="treeview-item"><a href="{{URL::route('settings-general-get')}}"><i class="fa fa-circle-o"></i>General </a></li>
        @endif
        @if(AccessController::checkPermission('access-control', 'can_view')) 
          <li class="treeview-item"><a href="{{URL::route('access-list')}}"><i class="fa fa-circle-o"></i>Access Control</a></li>
        @endif
        </ul>
      </li>
      @endif

    </ul>
  </section>
  <!-- /.sidebar -->
</aside>