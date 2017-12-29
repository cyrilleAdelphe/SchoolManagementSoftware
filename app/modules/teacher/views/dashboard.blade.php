@extends('backend.teacher.main')

@section('content')

        <!-- Main content -->
        <section class="content">
          <!-- Small boxes (Stat box) -->
          <div class="row">
            <div class="col-lg-3 col-xs-6">
              <!-- small box -->
              <div class="small-box bg-aqua">
                <div class="inner">
                  <h3>8</h3>
                  <p>Upcoming Events</p>
                </div>
                <div class="icon">
                  <i class="ion ion-trophy"></i>
                </div>
                <a href="#" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
              </div>
            </div><!-- ./col -->
            <div class="col-lg-3 col-xs-6">
              <!-- small box -->
              <div class="small-box bg-green">
                <div class="inner">
                  <h3>2</h3>
                  <p>Today's Task</p>
                </div>
                <div class="icon">
                  <i class="ion ion-compose"></i>
                </div>
                <a href="#" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
              </div>
            </div><!-- ./col -->
            <div class="col-lg-3 col-xs-6">
              <!-- small box -->
              <div class="small-box bg-yellow">
                <div class="inner">
                  <h3>25</h3>
                  <p>Presence Teachers</p>
                </div>
                <div class="icon">
                  <i class="ion ion-person-stalker"></i>
                </div>
                <a href="#" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
              </div>
            </div><!-- ./col -->
            <div class="col-lg-3 col-xs-6">
              <!-- small box -->
              <div class="small-box bg-red">
                <div class="inner">
                  <h3>845</h3>
                  <p>Presence Students</p>
                </div>
                <div class="icon">
                  <i class="ion ion-android-contacts"></i>
                </div>
                <a href="#" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
              </div>
            </div><!-- ./col -->
          </div><!-- /.row -->
          <!-- Main row -->
          <div class="row">
            <!-- Left col -->
            <!-- right col (We are only adding the ID to make the widgets sortable)-->
         
          </div><!-- /.row (main row) -->

        </section><!-- /.content -->
@stop 

@section('custom-js')

<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->

    <!-- Morris.js charts -->
     <script src="http://cdnjs.cloudflare.com/ajax/libs/raphael/2.1.0/raphael-min.js"></script> 
     <script src="{{asset('sms/plugins/morris/morris.min.js')}}" type="text/javascript"></script> 
    <!-- Sparkline -->
     <script src="{{asset('sms/plugins/sparkline/jquery.sparkline.min.js')}}" type="text/javascript"></script> 
    <!-- jvectormap -->
     <script src="{{asset('sms/plugins/jvectormap/jquery-jvectormap-1.2.2.min.js')}}" type="text/javascript"></script> 
     <script src="{{asset('sms/plugins/jvectormap/jquery-jvectormap-world-mill-en.js')}}" type="text/javascript"></script> 
    <!-- jQuery Knob Chart -->
     <script src="{{asset('sms/plugins/knob/jquery.knob.js')}}" type="text/javascript"></script> -->
    <!-- daterangepicker -->
     <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.10.2/moment.min.js" type="text/javascript"></script> 
     <script src="{{asset('sms/plugins/daterangepicker/daterangepicker.js')}}" type="text/javascript"></script> 
    <!-- datepicker -->
     <script src="{{asset('sms/plugins/datepicker/bootstrap-datepicker.js')}}" type="text/javascript"></script> 
    <!-- Bootstrap WYSIHTML5 -->
     <script src="{{asset('sms/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js')}}" type="text/javascript"></script> 
    <!-- Slimscroll -->
     <script src="{{asset('sms/plugins/slimScroll/jquery.slimscroll.min.js')}}" type="text/javascript"></script> 
    <!-- FastClick -->
     <script src="{{asset('sms/plugins/fastclick/fastclick.min.js')}}"></script> 
     <!-- AdminLTE App -->
@stop