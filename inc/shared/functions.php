<?php 
function echoActiveClassIfRequestMatches($requestUri){
  $current_file_name = basename($_SERVER['REQUEST_URI'], ".php");

  if ($current_file_name == $requestUri) {
    echo 'text-link';  
  } else {
    echo 'text-light';
  }
}
?>