<?php
    $stop = isset($_REQUEST["stop"])?$_REQUEST["stop"]:0;

    $in = isset($_REQUEST["q"])?$_REQUEST["q"]:"";

    $range =  isset($_REQUEST["q"])?50:500;
    $table="twitter";
    $min=-1;

    $search =  isset($_REQUEST["q"])?"and  LOWER( `Tweettext`)  regexp('".$_REQUEST["q"]."') ":"";
    $sql="SELECT `permalink`, `Tweettext`  FROM $table order by favorites DESC";

    #SELECT `images`, `permalink`, `Tweet text`, `time`,`favorites`,`retweets` FROM `$table` WHERE `images` != '' $search and (favorites>$min and   retweets>$min) ORDER BY (`favorites`*`retweets`) DESC  limit $stop,$range;";

#(favorites>$min and   retweets>$min)
    #$sql="SELECT `images`, `permalink`, `Tweet text`, `time`,`favorites`,`retweets` FROM `$table` WHERE `images` != '' and (favorites>$min or retweets>$min) ORDER BY (`favorites`*`retweets`) DESC  limit $stop,$range;";
    #$sql = "SELECT `images`, `permalink`, `Tweet text`, `time` FROM `twitter` WHERE `images` !='' order by engagements desc limit $stop,100;";
    $sql2 = "SELECT `images`, `permalink`, `Tweettext`, `time` FROM `$table` WHERE `images` !='' $search and (favorites>$min and   retweets>$min) ";

    function connect( $dbName )
    {
      do {
        $databaseResponse = mysql_connect(
        "localhost", "root", "" );
	

      } while( $databaseResponse === false );

      @ $selectResult = mysql_select_db( $dbName ) or dieFunc();
    }

    function countQuery( $query, $db )
    {
        if( $db != "" ) connect( $db );
        else connect( "spaceman" );

        $result= mysql_query( $query );
        $err   = mysql_error();
        if( $err != "" ) echo "error=$err  ";

        $ret = array();

        $x=0;

        return mysql_affected_rows();

    }

    $xcount = countQuery($sql2, "spaceman" );
  function query( $query, $db )
    {

        global $xcount,$range;

        if( $db != "" ) connect( $db );
        else connect( "spaceman" );

        $result= mysql_query( $query );
        $err   = mysql_error();
        if( $err != "" ) echo "error=$err  ";


      return $result;
    }

    function executeQuery( $query, $db )
    {

        global $xcount,$range,$table;

        if( $db != "" ) connect( $db );
        else connect( "spaceman" );

        $result= mysql_query( $query );
        $err   = mysql_error();
        if( $err != "" ) echo "error=$err  ";

        $ret = array();

        $x=0;

        while (($row=mysql_fetch_assoc($result))!==false) {

          $x++;
          $txt = preg_split("/(\n| |\t|download:|case:)/",$row["Tweettext"]);

          $text = array();

          $link = "";

          $ll=0;
          foreach($txt as $k1 => $v1){

              if(preg_match("/(http|https):\/\/t.co/",$v1)){

              $old = explode("http",trim($v1));

              $v1 = trim(`curl -I "http$old[1]"  2>&1 |egrep location | sed -s "s/location: //"`);

              if(preg_match("/twitter/",$v1)){
                  $v1="http".$old[1];
              }
              $update="UPDATE $table SET Tweettext2 = REPLACE(Tweettext,'http".$old[1]."','$v1') where permalink='".$row["permalink"]."'";
              echo "\n",$update;


              #query($update,"spaceman");

              }



            }

        }
       $result = mysql_affected_rows();
        mysql_close();


        return $ret;

}

  ?>


<?php

  $rounds = executeQuery($sql, "spaceman" );
print_r($rouds);
  ?>
