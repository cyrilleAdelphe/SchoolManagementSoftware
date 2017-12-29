@if($user_role == 'student')
    @include('student.views.view')
@else
    @include('guardian.views.view')
@endif