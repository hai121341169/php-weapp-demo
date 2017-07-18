<?php
header('Content-type: text/json');
if($_GET["x"] == 123){
  
  echo json_encode('{"data":"999"}');
  
} else{
  
  echo json_encode('{"data":"0"}');
}
?>