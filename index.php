<?php
  require_once( 'config.php' );
  session_start();
  $user = null;
  $error = '';

  $db = new PDO('sqlite:possiDL.db');
    if ( !$db || $db == null ) die( "error opening database" );

  if ( isset( $_SESSION['token'] ) && isset( $_SESSION[ 'userid' ] ) ) {
    $stmt = $db->query( "SELECT * FROM users WHERE id = '" . $_SESSION['userid'] . "'" );
    if ( $stmt != null ) {
      $user = $stmt->fetch(PDO::FETCH_OBJ);
      if ( $_SESSION['token'] != sha1( $user->username . SECRET ) )
        $user = null;
    }
  }

  if ( isset( $_GET[ 'permission'] ) ) {
    $error = "an admin needs to whitelist your account before you can download files";
  }

  $path = '';
  if ( isset( $_GET['dir'] ) && !preg_match( '/\.\./', $_GET['dir']) ) {
    $path = $_GET[ 'dir' ];
    if ( !is_dir( FILE_DIR . $path ) )
      $path = '';
  }
?>
<!DOCTYPE html>
<html>
<head>
  <title>possiDL</title>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
  <link rel="stylesheet" type="text/css" href="style.css" />
</head>
<body>
  <a href="https://github.com/lethemfindus/possiDL"><img style="position: absolute; top: 0; left: 0; border: 0;" src="https://s3.amazonaws.com/github/ribbons/forkme_left_white_ffffff.png" alt="Fork me on GitHub"></a>
  <header>
    <h1 id="title">possiDL</h1>
    <p>
      A simple file directory server in PHP.
    </p>
  </header>
  <article>
  <?php
    if ( $error ) echo '<div class="error">' . $error . '</div>';
    $dir = opendir( FILE_DIR . $path );
    while ( $file = readdir( $dir ) ) {
      $ffile = $path . $file;
      if ( preg_match( '/^\.($|[^.])/', $file ) )
          continue;
      if ( $file == '..' ) { if ( $path == '' || $path == '/' ) continue; ?>
      <div class="file"><a href="index.php?dir=<?php echo $matches[1] ?>">../</a><span class="right">(one level up)</span></div>
      <?php } else if ( !is_dir( FILE_DIR . $ffile ) ) {  ?>
      <div class="file"><?php echo $file ?> <span class="right"><a href="download.php?file=<?php echo $ffile ?>">view</a> | <a href="download.php?force&file=<?php echo $ffile ?>">download</a></span></div>
      <?php } else { ?>
      <div class="file"><a href="index.php?dir=<?php echo $ffile ?>/"><?php echo $file ?>/</a></div>
      <?php }
    }
  ?>
  </article>
  <footer>
    <span id="left">
      <?php if ( $user )  { ?><a href="login.php?logout">logout <?php echo $user->username ?></a>
      <?php } else { ?><a href="login.php">log in</a><?php } ?>
    </span>
    possiDL by <a href="http://lethemfind.us/community/user/4085-1nsignia/">S0lll0s aka 1nsignia</a><br/>
    Visit our friendly community at <a href="http://lethemfind.us">lethemfind.us</a>
  </footer>
</body>
</html>
