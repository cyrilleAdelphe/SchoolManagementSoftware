<?php


session_start();

$fb = new Facebook\Facebook([
    'app_id' => '1124103491004922',   
    'app_secret' => 'eacf1608a02a18488676370b7f6dc287',
    'default_graph_version' => 'v2.2',
]);

// Check to see if we already have an accessToken ?
if (Cookie::get('token')) {
    //Session::put('token', $_SESSION['facebook_access_token'])
    $accessToken = Cookie::get('token');
    $response = $fb->get('/609902389086153/photos?fields=images.type(large)', $accessToken); // particular album id ko lagi
         // $response = $fb->get('/me/albums?fields=picture');// all album ko lagi

           // $response = $fb->get('/me/albums');

         
       $user = $response->getGraphEdge()->asArray();

        
       echo '<pre>'; print_r($user);

} else {
    // We don't have the accessToken
    // But are we in the process of getting it ? 
    if (isset($_REQUEST['code'])) {
        $helper = $fb->getRedirectLoginHelper();
        try {
            $accessToken = $helper->getAccessToken();
            } catch(Facebook\Exceptions\FacebookResponseException $e) {
              // When Graph returns an error
              echo 'Graph returned an error: ' . $e->getMessage();
              exit;
        } catch(Facebook\Exceptions\FacebookSDKException $e) {
              // When validation fails or other local issues
              echo 'Facebook SDK returned an error: ' . $e->getMessage();
            exit;
        }

        if (isset($accessToken)) {
              // Logged in!
              //Session::put('token', (string) $accessToken);
              Cookie::queue('facebook_token',(string) $accessToken, 10);

              // Now you can redirect to another page and use the
              // access token from $_SESSION['facebook_access_token']

              $response = $fb->get('/609902389086153/photos?fields=images.type(large)', $accessToken); // particular album id ko lagi
         // $response = $fb->get('/me/albums?fields=picture');// all album ko lagi

           // $response = $fb->get('/me/albums');

         
       $user = $response->getGraphEdge()->asArray();
       echo '<pre>'; print_r($user);

         // foreach($user as $u)
         // {
         //      echo "<img src='".$u['images'][0]['source']."'/>";

         // }
        }           
    } else {
        // Well looks like we are a fresh dude, login to Facebook!
        $helper = $fb->getRedirectLoginHelper();
        $permissions = ['email','user_photos']; // optional
        $loginUrl = $helper->getLoginUrl('http://localhost/ssm/facebook', $permissions);

        echo '<a href="' . $loginUrl . '">Log in with Facebook!</a>';
    }

}