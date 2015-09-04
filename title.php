<?php

if(is_file( "/var/www/straped/mufon/mufon_json/".$_REQUEST['q'].".json")){
  $x = file_get_contents("/var/www/straped/mufon/mufon_json/".$_REQUEST['q'].".json");
}
$x= json_decode($x);

#print_r($x);

printf("\n");



foreach($x as $k=>$v){
  if(preg_match("/(city|country|region|Location|object|shape|features|event)/i",$k) && $v)
      printf("\n<li>%s</li>",preg_replace("/United States/","US",preg_replace("/(Undisclosed|Other)/","",$x->{$k})));
}

#if($x->{"Object Features"} != "Unknown") printf("\n<li>%s</li>",$x->{"Object Features"});

?>
