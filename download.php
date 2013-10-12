<?php
if ( !isset( $_GET['file'] ) )
  die( 'no file supplied' );

require_once( 'config.php' );
session_start();
$user = null;

$db = new PDO('sqlite:possiDL.db');
if ( !$db || $db == null )
  die( "error opening database" );

if ( isset( $_SESSION['token'] ) && isset( $_SESSION[ 'userid' ] ) ) { 
  $stmt = $db->query( "SELECT * FROM users WHERE id = '" . $_SESSION['userid'] . "'" );
  if ( $stmt != null ) {
    $user = $stmt->fetch(PDO::FETCH_OBJ);
    if ( $_SESSION['token'] != sha1( $user->username . SECRET ) ) {
      $user = null;
    }
  }
}

if ( $user == null && REQUIRE_LOGIN ) {
    header( 'Location: login.php?please' );
    die();
}
if ( $user != null && REQUIRE_WHITE && $user->white != 1 ) {
    header( 'Location: index.php?permission' );
    die();
}

// MIME TYPE FUNCTION
if ( !function_exists( 'mime_content_type' ) ) {

    function mime_content_type( $filename ) {
        $mime_types = array(
            'txt' => 'text/plain',
            'htm' => 'text/html',
            'html' => 'text/html',
            'php' => 'text/html',
            'css' => 'text/css',
            'js' => 'application/javascript',
            'json' => 'application/json',
            'xml' => 'application/xml',
            'swf' => 'application/x-shockwave-flash',
            'flv' => 'video/x-flv',

            // images
            'png' => 'image/png',
            'jpe' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'jpg' => 'image/jpeg',
            'gif' => 'image/gif',
            'bmp' => 'image/bmp',
            'ico' => 'image/vnd.microsoft.icon',
            'tiff' => 'image/tiff',
            'tif' => 'image/tiff',
            'svg' => 'image/svg+xml',
            'svgz' => 'image/svg+xml',

            // archives
            'zip' => 'application/zip',
            'rar' => 'application/x-rar-compressed',
            'exe' => 'application/x-msdownload',
            'msi' => 'application/x-msdownload',
            'cab' => 'application/vnd.ms-cab-compressed',

            // audio/video
            'mp3' => 'audio/mpeg',
            'qt' => 'video/quicktime',
            'mov' => 'video/quicktime',

            // adobe
            'pdf' => 'application/pdf',
            'psd' => 'image/vnd.adobe.photoshop',
            'ai' => 'application/postscript',
            'eps' => 'application/postscript',
            'ps' => 'application/postscript',

            // ms office
            'doc' => 'application/msword',
            'rtf' => 'application/rtf',
            'xls' => 'application/vnd.ms-excel',
            'ppt' => 'application/vnd.ms-powerpoint',

            // open office
            'odt' => 'application/vnd.oasis.opendocument.text',
            'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
        );
		$ext = strtolower( substr( strrchr( $filename, '.' ), 1 ) );
        if ( array_key_exists( $ext, $mime_types ) ) {
            return $mime_types[$ext];
        }
        elseif ( function_exists( 'finfo_open' ) ) {
            $finfo = finfo_open( FILEINFO_MIME );
            $mimetype = finfo_file( $finfo, $filename );
            finfo_close( $finfo );
            return $mimetype;
        }
        else {
            return 'text/plain';
        }
    }
}

$filename = FILE_DIR . $_GET['file'];

if ( !file_exists($filename) || preg_match( '/((\.\.\/)|(\/\.))/', $filename ) ) {
    die( "error opening file<span hidden>faggot/span>" );
}

$file = fopen( $filename, "rb");
if ( $file === false ) die( "error opening file" );

$mime = mime_content_type ( $filename );
if ( isset( $_GET[ 'force' ] ) )
    $mime = "other/force-download";

header( "Content-Type: " . $mime ); 

$buffer = "";
while ( !feof( $file ) ) {
  $buffer = fread($file, 1024*1024);
  echo $buffer;
  ob_flush();
  flush();
}
fclose($file);
?>