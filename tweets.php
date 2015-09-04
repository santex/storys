<?php
    $stop = isset($_REQUEST["stop"])?$_REQUEST["stop"]:0;

                $in = isset($_REQUEST["q"])?" and Tweettext regexp('".$_REQUEST["q"]."') ":"";

    $range = 500;
    $table="twitter13";
    $min=1;

    $sql="SELECT `images`, `permalink`, `Tweettext`, `time`,`engagements`,`favorites`,`retweets` FROM `$table` WHERE `images` != '' $in  and (favorites>$min or retweets>$min or engagements>$min*5) ORDER BY (`engagements`) DESC  limit $stop,$range;";

    //;"

    #$sql="SELECT `images`, `permalink`, `Tweettext`, `time`,`favorites`,`retweets` FROM `$table` WHERE `images` != '' and (favorites>$min or retweets>$min) ORDER BY (`favorites`*`retweets`) DESC  limit $stop,$range;";
    #$sql = "SELECT `images`, `permalink`, `Tweettext`, `time` FROM `twitter` WHERE `images` !='' order by engagements desc limit $stop,100;";
    $sql2 = "SELECT `images`, `permalink`, `Tweettext`, `time` FROM `$table` WHERE `images` !=''  $in and (favorites>$min or retweets>$min or engagements>$min*5) ";

    function connect( $dbName )
    {
      do {
        $databaseResponse = mysql_connect(
        #"localhost", "monty", "some_pass" );

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

        global $xcount,$range,$in;

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

          foreach($images as $kk => $img){
          #img = $images[0];

          if(!trim($img) || $kk >4) continue;

          $txt = preg_split("/(\n| |\t|download:|yt:|case:)/",$row["Tweettext"]);

          $text = array();

          $links = array();

        $link = "";
          $ll=0;
          foreach($txt as $k1 => $v1){
            if(preg_match("/http/",$v1)) {

              if(!preg_match("/(http|https):\/\/(twitter)/",$v1) &&
                 !preg_match("/space-man.de$/",$v1) && !strstr($link,$v1))
              $link .= sprintf("\n<a href='%s' target='_blank'><small>%s</small></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;", $v1, $v1);
              $links[]=$v1;

            }else{
              $text[] = " $v1";
            }
          }

#$go =  explode("\n",$txt);
#
$gox = array_shift($text);
$go = implode(" ",$text);
$li = isset($links[1])?$links[1]:isset($links[0])?$links[0]:$row["permalink"];
  $ret[0] .= <<<EOF
  <li>

                <figure>

                  <a href="{$li}" target="_blank" style="color:red;font-size:10px;">
                  <img src="{$img}" alt="{$img}" style="width:100%; height:auto;"/>
                  </a>

                  <figcaption style="overflow:hidden;">
                    <p>
                    {$gox} {$go}</p>
                    <small>{$link}</small>
                  </figcaption>

                  <a href="{$row["permalink"]}" target="_blank" style="color:red;font-size:10px;">permalink</a>

                    <i class="yellow fa fa-fw fa-star-o"></i><span>{$row["engagements"]}</span>
                  <a href="{$row["permalink"]}" target="_blank" class="pull-right" style="color:red;font-size:10px;">{$row["time"]}</a>

                </figure>
              </li>

EOF;


$ret[1] .= <<<EOF
<li>
                <figure>
                  <figcaption>
                    <a href="{$li}" target="_blank" style="color:red;font-size:10px;">permalink</a>
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

        }
        #mysql_close();
$ret[2]=$x;

        $result = mysql_affected_rows();
        mysql_close();



          $slinks = "";


    $aa=range(0,round($xcount / $range )-2);
  foreach($aa as $l => $lv){
    if(!$in)
    $ret[3].=sprintf("<a href='tweets.php?stop=%s'>%s</a> |",$lv*$range,1+$lv);


  }if(!$in)
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
      <script src="//code.jquery.com/jquery-1.11.3.min.js"></script>
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


  <form action="http://localhost/cool/GridGallery/tweets.php" method="get" id="myForm">
    <div class="input-group">
      <input class="form-control input-lg" style="width:90%" type="text" id="glyph-search" name="q" placeholder="Search for an icon...">
      <span class="input-group-addon" onclick='document.getElementById("myForm").submit();'><i class="fa fa-fw fa-lg fa-search" ></i></span>
    </div>
  </div>
</form>

          <!--// nav>
            <a href="http://space-man.de" class="bp-icon bp-icon-next" data-info="go to http://space-man.de"><span>Home</span></a>
          </nav //-->
        </header>
      </div>
      <div>

<center>

<?php

  $rounds = executeQuery($sql, "spaceman" );
  $nav = $rounds[3];

echo "",$nav;
  ?>


</center>

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
            <div class="info-keys icon">Navigate with arrow keys</div>

          </section><!-- // slideshow -->
        </div><!-- // grid-gallery -->
      </div>
      <script src="/assets/js/imagesloaded.pkgd.min.js"></script>
      <script src="/assets/js/masonry.pkgd.min.js"></script>
      <script src="/assets/js/news.js"></script>
      <script src="/assets/js/cbpGridGallery.js"></script>
       <script type="text/javascript">

       var gl = new CBPGridGallery( document.getElementById( 'grid-gallery' ) );

//setInterval(function({  gl.next(); },1000);

  // PAGE RELATED SCRIPTS
  function hide_divs(search) {
    $("li").hide();

    $("p").each( function(){
      var text = $(this).text().toLowerCase();
        if(text.match(search.toLowerCase())){
          $(this).closest('li').show();
        }
      });

//     var gl = new CBPGridGallery( document.getElementById( 'grid-gallery' ) );

  }

  function show_all() {
    $("section").show()
  }

  $("#glyphx-search").keyup(function() {
    var search = $.trim(this.value);
    if (search === "") {
      show_all();
    }
    else {
      hide_divs(search);
    }
  });

      </script>
      <center>
<?php
  require_once "down.php";
?>
      </center>
    </body>
  </html>
