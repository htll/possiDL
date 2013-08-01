<?php
  require_once( 'config.php' );
  session_start();
  $user = null;
  $error = '';

  if ( isset( $_GET[ 'logout' ] ) ) {
    $_SESSION = array();
    session_destroy();
  }

  if ( isset( $_GET[ 'please'] ) ) {
    $error = "please log in before downloading files";
  }

  $db = new PDO('sqlite:possiDL.db');
    if ( !$db || $db == null ) die( "error opening database" );

  if ( isset( $_POST['login'] ) && isset( $_POST['username'] ) && isset( $_POST['password'] ) ) {
    $stmt = $db->query( "SELECT * FROM users WHERE username = '" . $_POST['username'] . "' AND password = '" . sha1( $_POST['password'] ) . "'" );
    if ( $stmt == null )
      $error = 'unable to log in, try again later';
    else {
      $user = $stmt->fetch(PDO::FETCH_OBJ);
      if ( $user == null )
        $error = 'wrong user / password combination';
      else {
        $_SESSION['token'] = sha1( $user->username . SECRET );
        $_SESSION['userid'] = $user->id;
        header("Location: index.php");
      }
    }
  } elseif ( isset( $_SESSION['token'] ) && isset( $_SESSION[ 'userid' ] ) ) { 
    $stmt = $db->query( "SELECT * FROM users WHERE id = '" . $_SESSION['userid'] . "'" );
    if ( $stmt != null ) {
      $user = $stmt->fetch(PDO::FETCH_OBJ);
      if ( $_SESSION['token'] != sha1( $user->username . SECRET ) )
        $user = null;
      header("Location: index.php");
    }
  }
?>
<!DOCTYPE html>
<html>
<head>
  <title>possiDL</title>
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
  <form action="#" method="post">
    <?php if ( $error != '' ) echo '<div class="error">' . $error . '</div>'; ?>
    username: <input type="text" name="username" /><br/>
    password: <input type="password" name="password" /><br/>
    <input type="submit" name="login" value="log in" />
  </form>
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
