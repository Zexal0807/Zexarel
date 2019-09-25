<?php
class ZDownloader{

  private $path;
  private $ext;
  private $type;
  private $new;

  private $speed;

  public function __construct($filename, $new){
    $this->path = $filename;
    $this->ext = pathinfo($filename, PATHINFO_EXTENSION);
    $this->type = ZDownloader::$MIME_TYPE[$this->ext];
    $this->ext = ".".$this->ext;
    $this->new = $new;

    $this->speed = filesize($filename);
  }

  public function setSpeedLimit($s){
    $this->speed = $s*1024;
  }

  public function download(){
    header('Content-Type: '.$this->type);
    header('Content-Length: '.filesize($this->path));
    header('Content-Disposition: attachment;filename='.$this->new.$this->ext);
    header('Cache-Control: max-age=0');

    flush();
    $file = fopen($this->path, "r");
    while(!feof($file)){
      echo fread($file, $this->speed);
      flush();
      sleep(1);
    }
    fclose($file);
  }

  public static $MIME_TYPE = [
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
    'docx' => 'application/msword',
    'xlsx' => 'application/vnd.ms-excel',
    'pptx' => 'application/vnd.ms-powerpoint',


    // open office
    'odt' => 'application/vnd.oasis.opendocument.text',
    'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
  ];

}

?>
