  <?php
    $sql = "SELECT `images`, `permalink`, `Tweet text`, `time` FROM `twitter` WHERE `images` !='' order by engagements desc";

    function connect( $dbName )
    {
      do {
        $databaseResponse = mysql_connect(
        "localhost", "root", "" );

      } while( $databaseResponse === false );

      @ $selectResult = mysql_select_db( $dbName ) or dieFunc();
    }

    function executeQuery( $query, $db )
    {
        if( $db != "" ) connect( $db );
        else connect( "spaceman" );

        $result= mysql_query( $query );
        $err   = mysql_error();
        if( $err != "" ) echo "error=$err  ";

        $ret = array();

        while (($row=mysql_fetch_assoc($result))!==false) {
          $images = explode(",",$row["images"]);
          $img = $images[0];

$go =  explode("\n",$row["Tweet text"]);
$gox = array_shift($go);
$go = implode("\n",$go);

  $ret[0] .= <<<EOF
  <li>

                <figure>

                  <img src="{$img}" alt="{$row["Tweet text"]}"/>
                  <figcaption style="overflow:hidden;">
                      <h5>{$gox}</h5>
                    <p>{$go}</p>

                  </figcaption>
                  <a href="{$row["permalink"]}" target="_blank" style="color:red;font-size:10px;">permalink</a>
                </figure>
              </li>

EOF;


$ret[1] .= <<<EOF
<li>
                <figure>
                  <figcaption>
                    <a href="{$row["permalink"]}" target="_blank" style="color:red;font-size:10px;">permalink</a><h5>{$gox}</h5>
                    <p>{$go}</p>
                  </figcaption>
                  <img src="{$img}" alt="{$img}" style="height:auto;max-height:500px;"/>
                </figure>
              </li>
EOF;


        }
        #mysql_close();

        $result = mysql_affected_rows();
        mysql_close();
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
      <link rel="stylesheet" type="text/css" href="/assets/css/news.css" />
      <link rel="stylesheet" type="text/css" href="/assets/css/newscomponent.css" />
      <script src="/assets/js/modernizr.custom.js"></script>
      <style>


      </style>
    </head>
    <body>
      <div class="container">
        <header class="clearfix">
          <span>space-man</span>
          <h1>News Gallery</h1>
          <nav>
            <a href="http://space-man.de" class="bp-icon bp-icon-next" data-info="go to http://space-man.de"><span>Home</span></a>
          </nav>
        </header>
        <div id="grid-gallery" class="grid-gallery">
          <section class="grid-wrap">
            <ul class="grid">
              <li class="grid-sizer"></li><!-- for Masonry column width -->
  <?php
  $rounds = executeQuery($sql, "spaceman" );
  print($rounds[0]);
  ?>

            </ul>
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
      <script>
        new CBPGridGallery( document.getElementById( 'grid-gallery' ) );
      </script>
    </body>
  </html>
