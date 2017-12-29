@extends('gallery.views.tabs')

@section('tab-content')
<?php

    
        // Well looks like we are a fresh dude, login to Facebook!
        $helper = $fb->getRedirectLoginHelper();
        $permissions = ['email','user_photos']; // optional
        $loginUrl = $helper->getLoginUrl(URL::route('gallery-get-access-token'), $permissions);

        echo '<a href="' . $loginUrl . '">Log in with Facebook!</a>';

?>

@stop