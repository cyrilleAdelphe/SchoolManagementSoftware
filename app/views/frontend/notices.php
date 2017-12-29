<?php 

$notice = DashboardController::dashboardGetNotice();
?>
<?php 
if (isset($notice)){

  echo ' <div class="notice section parallax nomargin notopborder dark" data-stellar-background-ratio="0.3">';
  echo  '<div class="nTitle">'.$notice->title.'</div>';
  echo $notice->body;
  if (isset($notice->created_at)){
      echo '
      <div class="nTime" style="margin-top: 10px; color: #5cb85c">
      Posted at: <i class="fa fa-fw fa-clock-o"></i>';
      echo HelperController::dateTimePrettyConverter($notice->created_at);
      echo '</div>';
  }
  echo "</div>";
}
?>