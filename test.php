<?php

if($_GET["x"] == 123){
  exit(json_encode("{data:999}"));
  
} else{
  
  exit(json_encode("{data:0}"));
}

?>