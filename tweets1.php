<?php
    $stop = isset($_REQUEST["stop"])?$_REQUEST["stop"]:0;

    $in = isset($_REQUEST["q"])?$_REQUEST["q"]:"";

    $range =  isset($_REQUEST["q"])?50:500;
    $table="twitter";
    $min=-1;

    $search =  isset($_REQUEST["q"])?"and  LOWER( `Tweettext`)  regexp('".$_REQUEST["q"]."') ":"";
    $sql="SELECT `images`, `permalink`, `Tweettext`, `time`,`favorites`,`retweets` FROM `$table` WHERE `images` != '' $search and (favorites>$min and   retweets>$min) ORDER BY (`favorites`*`retweets`) DESC  limit $stop,$range;";

#(favorites>$min and   retweets>$min)
    #$sql="SELECT `images`, `permalink`, `Tweettext`, `time`,`favorites`,`retweets` FROM `$table` WHERE `images` != '' and (favorites>$min or retweets>$min) ORDER BY (`favorites`*`retweets`) DESC  limit $stop,$range;";
    #$sql = "SELECT `images`, `permalink`, `Tweettext`, `time` FROM `twitter` WHERE `images` !='' order by engagements desc limit $stop,100;";
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


    function executeQuery( $query, $db )
    {

        global $xcount,$range;

        if( $db != "" ) connect( $db );
        else connect( "spaceman" );

        $result= mysql_query( $query );
        $err   = mysql_error();
        if( $err != "" ) echo "error=$err  ";

        $ret = array();

        $x=0;

        while (($row=mysql_fetch_assoc($result))!==false) {

          $x++;
          $images = explode(",",$row["images"]);
          $img = $images[0];

          $txt = preg_split("/(\n| |\t|download:)/",$row["Tweettext"]);

          $text = array();

          $link = "";

          $ll=0;
          foreach($txt as $k1 => $v1){
            if(preg_match("/http/",$v1)) {


              if(!preg_match("/twitter/",$v1) && !strstr($link,$v1)){
              $link .= sprintf("\n<a href='%s' target='_blank'><small>%s</small></a>&nbsp;", $v1,strlen($v1)<20?$v1:substr($v1,0,20)."...");
            }



          #]$link .= sprintf("\n<a href='%s' target='_blank'><small>%s</small></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;", $v1, $v1);

            }else{
              $text[] = " $v1";
            }
          }

#$go =  explode("\n",$txt);
$gox = array_shift($text);
$go = implode(" ",$text);

  $ret[0] .= <<<EOF
  <li>

                <figure>

                  <a href="{$row["permalink"]}" target="_blank" style="color:red;font-size:10px;">
                  <img src="{$img}" alt="{$img}" style="width:100%; height:auto;"/>
                  </a>

                  <figcaption style="overflow:hidden;">

                    <p>
                    {$gox} {$go}</p>
                    <small>{$link}</small>
                  </figcaption>

                  <a href="{$row["permalink"]}" target="_blank" style="color:red;font-size:10px;">permalink</a>

                    <i class="yellow fa fa-fw fa-star-o"></i><span>{$row["favorites"]}</span>
                  <a href="{$row["permalink"]}" target="_blank" class="pull-right" style="color:red;font-size:10px;">{$row["time"]}</a>

                </figure>
              </li>

EOF;


$ret[1] .= <<<EOF
<li>
                <figure>
                  <figcaption>
                    <a href="{$row["permalink"]}" target="_blank" style="color:red;font-size:10px;">permalink</a>
                    <a href="{$row["permalink"]}" target="_blank" class="pull-right" style="color:red;font-size:10px;">{$row["time"]}</a>
                    <p>{$gox} {$go}</p>
                    <small>{$link}</small>
                  </figcaption>
                  <a href="{$row["permalink"]}" target="_blank" style="color:red;font-size:10px;">
                  <img src="{$img}" alt="{$img}" style="width:100%;overflow-y:scroll;max-height:100%;"/>
                  </a>
                </figure>
              </li>
EOF;


        }
        #mysql_close();
$ret[2]=$x;

        $result = mysql_affected_rows();
        mysql_close();



          $slinks = "";


  foreach(range(0,round($xcount / $range )-2) as $l => $lv){
    $ret[3].=sprintf("<a href='tweets.php?stop=%s'>%s</a> |",$lv*$range,1+$lv);


  }
  $ret[3].=sprintf("<a href='tweets.php?stop=%s'>%s</a>",++$lv*$range,++$lv);

        return $ret;
    }


  ?>


  <!DOCTYPE html>
  <html lang="en" class="no-js">
    <head>
      <meta charset="UTF-8" />
      <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <title>http://space-man.de news</title>
      <meta name="description" content="News Gallery" />
      <meta name="keywords" content="news, alien, ufos , truth" />
      <meta name="author" content="santex" />
      <link rel="shortcut icon" href="../favicon.ico">

      <link rel="stylesheet" type="text/css" href="/assets/css/bootstrap.css" />
      <link href="/assets/fonts/font-awesome/css/font-awesome.min.css" rel="stylesheet">
      <link rel="stylesheet" type="text/css" href="/assets/css/news.css" />
      <link rel="stylesheet" type="text/css" href="/assets/css/newscomponent.css" />
      <script src="/assets/js/modernizr.custom.js"></script>
      <style>
        .red{color:#ff0000;}
        .yellow{color:#fff000;}
      small{
        color:#000;

      }
      a{
        color:#000;
      }

#global-tax-box {
background: hsl(0, 100%, 100%);
border-radius: 5px 5px 0 0;
border: 1px solid hsl(345, 27%, 74%);
bottom: -3px;
color: hsl(0, 0%, 0%);
font-size: 0.85em;
opacity: 1;
max-height:15%;
min-height:32px;
padding: 4px 4px 1px;
x-overflow: hidden;
position: fixed;
width:90%;
left: 5%;
bottom:5px;
text-align: left;
y-overflow:scrolling;
z-index: 9999;
zoom:110%;
}

      </style>
    </head>
    <body>
      <div class="container">

      <header class="clearfix">
<div class="pull-left">    <h1>news gallery</h1></div>
          <nav>

            <div class="home"><a href="http://space-man.de" class="bp-icon bp-icon-next" data-info="go to http://space-man.de"><span>Home</span></a></div>
          </nav>
        </header>
      </div>
<center>
    <div class="input-group">
      <input class="form-control input-lg" type="text" id="glyph-search" placeholder="Search the tweets">
      <span class="input-group-addon"><i class="fa fa-fw fa-lg fa-search"></i></span>
    </div>
</center>
      <div>

<center>

<?php

  $rounds = executeQuery($sql, "spaceman" );
  $nav = $rounds[3];

echo $nav;
  ?>


</center>
<hr>
        <div id="grid-gallery" class="grid-gallery">
          <div class="red info-keys icon">Press<br>images for twitter<br>text to slide</div>

          <section class="grid-wrap">
            <ul class="grid">
              <li class="grid-sizer"></li><!-- for Masonry column width -->
  <?php


  print($rounds[0]);
  ?>

            </ul>

 <center>

<?php


  echo $nav;
  ?>

</center>

          </section><!-- // grid-wrap -->
          <section class="slideshow">
            <ul>
<?php print($rounds[1]); ?>
            </ul>
            <nav>
              <span class="icon nav-prev"></span>
              <span class="icon nav-next"></span>
              <span class="icon nav-close"></span>
            </nav>
            <div class="info-keys icon">Navigate<br>with arrow keys</div>

          </section><!-- // slideshow -->
        </div><!-- // grid-gallery -->
      </div>
      <script src="/assets/js/imagesloaded.pkgd.min.js"></script>
      <script src="/assets/js/masonry.pkgd.min.js"></script>
      <script src="/assets/js/news.js"></script>
      <script src="/assets/js/cbpGridGallery.js"></script>
      <script>
        new CBPGridGallery( document.getElementById( 'grid-gallery' ) );
      </script>

<br />
<br />
<br />       <center>
<div id="global-tax-box">if you like to help?  <b>bitcoins</b> go here: <b>1FaWma1n8JSQiAY2WrMvjZn1q7Eu5mmt7F</b> &nbsp; &nbsp; download
image & videos &nbsp; NASA:
<a href="https://www.hq.nasa.gov/alsj/a17/AS17-136-20863.jpg" ><i class="red fa  fa-external-link-square"></i></a>
<a href="http://eol.jsc.nasa.gov/DatabaseImages/ISD/highres/AS09/AS09-23-3500.JPG" ><i class="red fa  fa-external-link-square"></i></a>
<a href="https://www.hq.nasa.gov/alsj/a16/AS16-112-18214.jpg" ><i class="red fa  fa-external-link-square"></i></a>
<a href="https://t.co/cL7N4HnyHB" ><i class="red fa  fa-external-link-square"></i></a>
<a href="https://t.co/e7woQvOX4y" ><i class="red fa  fa-external-link-square"></i></a>
<a href="https://t.co/w8yATJtX7I" ><i class="red fa  fa-external-link-square"></i></a>
<a href="https://t.co/FO5m74hOjp" ><i class="red fa  fa-external-link-square"></i></a>
<a href="https://t.co/v3mX9UZEym" ><i class="red fa  fa-external-link-square"></i></a>
<a href="https://t.co/NA4cMQxGO8" ><i class="red fa  fa-external-link-square"></i></a>
UFO's
<a href="http://space-man.de/straped/bootstrap/img/ufos/1QFToHU8Di4.mp4" target="_new"><i class="red fa  fa-external-link-square"></i></a>
<a href="http://space-man.de/straped/bootstrap/img/ufos/1uk5pjNfhA8.mp4" target="_new"><i class="red fa  fa-external-link-square"></i></a>
<a href="http://space-man.de/straped/bootstrap/img/ufos/3-sjsx_lvsg.mp4" target="_new"><i class="red fa  fa-external-link-square"></i></a>
<a href="http://space-man.de/straped/bootstrap/img/ufos/34818_submitter_file1__Copy_of_VID_20111222_103632-1.mp4" target="_new"><i class="red fa  fa-external-link-square"></i></a>
<a href="http://space-man.de/straped/bootstrap/img/ufos/3fkL398iDl0.mp4" target="_new"><i class="red fa  fa-external-link-square"></i></a>
<a href="http://space-man.de/straped/bootstrap/img/ufos/4Q_vbv_WOdM.mp4" target="_new"><i class="red fa  fa-external-link-square"></i></a>
<a href="http://space-man.de/straped/bootstrap/img/ufos/4lOQpSfj-w0.mp4" target="_new"><i class="red fa  fa-external-link-square"></i></a>
<a href="http://space-man.de/straped/bootstrap/img/ufos/87PRVP4EQAo.mp4" target="_new"><i class="red fa  fa-external-link-square"></i></a>
<a href="http://space-man.de/straped/bootstrap/img/ufos/8__j4vYDlhQ.mp4" target="_new"><i class="red fa  fa-external-link-square"></i></a>
<a href="http://space-man.de/straped/bootstrap/img/ufos/ADBQ1wrC5h8.mp4" target="_new"><i class="red fa  fa-external-link-square"></i></a>
<a href="http://space-man.de/straped/bootstrap/img/ufos/AWkKyL4n6jE.mp4" target="_new"><i class="red fa  fa-external-link-square"></i></a>
<a href="http://space-man.de/straped/bootstrap/img/ufos/AkLKZxFdOKQ.mp4" target="_new"><i class="red fa  fa-external-link-square"></i></a>
<a href="http://space-man.de/straped/bootstrap/img/ufos/C3nlO_gNA6g.mp4" target="_new"><i class="red fa  fa-external-link-square"></i></a>
<a href="http://space-man.de/straped/bootstrap/img/ufos/CnXm0U_L8Fs.mp4" target="_new"><i class="red fa  fa-external-link-square"></i></a>

<a href="http://space-man.de/straped/bootstrap/img/ufos/Hfy2JMcyMyA.mp4" target="_new"><i class="red fa  fa-external-link-square"></i></a>
<a href="http://space-man.de/straped/bootstrap/img/ufos/KKn9IcgtYQ8.mp4" target="_new"><i class="red fa  fa-external-link-square"></i></a>
<a href="http://space-man.de/straped/bootstrap/img/ufos/KOXcxC25-H8.mp4" target="_new"><i class="red fa  fa-external-link-square"></i></a>
<a href="http://space-man.de/straped/bootstrap/img/ufos/KdtIVndBmr4.mp4" target="_new"><i class="red fa  fa-external-link-square"></i></a>
E.T's:
<a href="http://space-man.de/straped/bootstrap/img/ets/0aRn6uiMxDI.mp4"><i class="red fa  fa-external-link-square"></i>
<a href="http://space-man.de/straped/bootstrap/img/ets/0lwv2WTaSyc.mp4"><i class="red fa  fa-external-link-square"></i>
<a href="http://space-man.de/straped/bootstrap/img/ets/0vBP7V6D9BM.mp4"><i class="red fa  fa-external-link-square"></i>
<a href="http://space-man.de/straped/bootstrap/img/ets/4rzt2taM2Qc.mp4"><i class="red fa  fa-external-link-square"></i>
<a href="http://space-man.de/straped/bootstrap/img/ets/5LFKz72ZB4A.mp4"><i class="red fa  fa-external-link-square"></i>
<a href="http://space-man.de/straped/bootstrap/img/ets/6MVpnOpCXFM.mp4"><i class="red fa  fa-external-link-square"></i>
<a href="http://space-man.de/straped/bootstrap/img/ets/7-0iOfYF6o0.mp4"><i class="red fa  fa-external-link-square"></i>
<a href="http://space-man.de/straped/bootstrap/img/ets/7ns7MRtCMw0.mp4"><i class="red fa  fa-external-link-square"></i>
<a href="http://space-man.de/straped/bootstrap/img/ets/8SXb64S59kM.mp4"><i class="red fa  fa-external-link-square"></i>
<a href="http://space-man.de/straped/bootstrap/img/ets/C25RTDXFNWY.mp4"><i class="red fa  fa-external-link-square"></i>
<a href="http://space-man.de/straped/bootstrap/img/ets/CD4ISlzA9RM.mp4"><i class="red fa  fa-external-link-square"></i>
<a href="http://space-man.de/straped/bootstrap/img/ets/CSHIEQNEaP8.mp4"><i class="red fa  fa-external-link-square"></i>
<a href="http://space-man.de/straped/bootstrap/img/ets/FkNfNUJl6dI.mp4"><i class="red fa  fa-external-link-square"></i>
<a href="http://space-man.de/straped/bootstrap/img/ets/G2q45FCWECY.mp4"><i class="red fa  fa-external-link-square"></i>
<a href="http://space-man.de/straped/bootstrap/img/ets/G4besX1rz0o.mp4"><i class="red fa  fa-external-link-square"></i>
<a href="http://space-man.de/straped/bootstrap/img/ets/IchHe98_0qk.mp4"><i class="red fa  fa-external-link-square"></i>
<a href="http://space-man.de/straped/bootstrap/img/ets/JVvAwr85KT0.mp4"><i class="red fa  fa-external-link-square"></i>
<a href="http://space-man.de/straped/bootstrap/img/ets/KD5N-ojrJUw.mp4"><i class="red fa  fa-external-link-square"></i>
<a href="http://space-man.de/straped/bootstrap/img/ets/MkqGaVkmr5A.mp4"><i class="red fa  fa-external-link-square"></i>
<a href="http://space-man.de/straped/bootstrap/img/ets/NCt2Frq2iW8.mp4"><i class="red fa  fa-external-link-square"></i>
<a href="http://space-man.de/straped/bootstrap/img/ets/P1AP5xQ3PdE.mp4"><i class="red fa  fa-external-link-square"></i>
<a href="http://space-man.de/straped/bootstrap/img/ets/QgYQSKcrCfE.mp4"><i class="red fa  fa-external-link-square"></i>
<a href="http://space-man.de/straped/bootstrap/img/ets/b5EtXDOfUXA.mp4"><i class="red fa  fa-external-link-square"></i>
<a href="http://space-man.de/straped/bootstrap/img/ets/d_uqAZW5YF0.mp4"><i class="red fa  fa-external-link-square"></i>
<a href="http://space-man.de/straped/bootstrap/img/ets/fMv85lRAmuk.mp4"><i class="red fa  fa-external-link-square"></i>
<a href="http://space-man.de/straped/bootstrap/img/ets/ghqlXdJ4etI.mp4"><i class="red fa  fa-external-link-square"></i>

</div>
<script>






var param =  URLParser(url).getParam('param');



$(document).ready(function() {



  $("#snippets").append("<b>snippets</b>");
  var snipp = $("#snippets");
  //loadURL("/ajax/all",snipp);
  //loadURL("/ajax/all",snipp);

  $("#datafiles").append("<b></b>");
  var datafiles = $("#datafiles");
 // loadURL("/data/all",datafiles);


    // RESET WIDGETS
    $('#snippets li').click(function(e) {

        var $this = $(this);


  document.location.href=$(this).text();
        e.preventDefault();

    });
    });

</script>
      </center>
    </body>
  </html>
