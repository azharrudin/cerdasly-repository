<?php
require_once(__DIR__."/../../../vendor/autoload.php");
use Aws\S3\S3Client;
define("UPLIB_API_KEY", "EZAe34EvrGXYtNV1029m3LkN10eF");
define('AWS_KEY', 'AKIAVPPJMR5L3QHNOA5J');
define('AWS_SECRET_KEY', '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');
class UPLib {
  public $client;
  function __construct(){
    $this->client = new S3Client([
      'region' => 'ap-southeast-1',
      'version' => '2006-03-01',
      "retries" => 0,
      'credentials' => [
          'key' => "AKIAVPPJMR5L3QHNOA5J",
          'secret' => "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"
      ],
  ]);
}
  function minifyimage($source_url, $destination_url, $quality){
      $info = getimagesize($source_url);
      if ($info['mime'] == 'image/jpeg') $image = @imagecreatefromjpeg($source_url);
      elseif ($info['mime'] == 'image/gif') $image = @imagecreatefromgif($source_url);
      elseif ($info['mime'] == 'image/png') $image = @imagecreatefrompng($source_url);
      @imagejpeg($image, $destination_url, $quality);
      return $destination_url;  
  }
  function uploadimage($file, $key){
    $this->client->putObject(array(
      'Bucket'       => "cerdasly",
      'Key'          => $key,
      'SourceFile'   => $file,
    
    ));
  }
  function deleteObject($key){
    if($key == "answer_images/none"){
      return;
    }
    $this->client->deleteObject("cerdasly", $key
    );
  }
  function geturl($key){
    $cmd = $this->client->getCommand('GetObject', [
      'Bucket' => 'cerdasly',
      'Key'    => $key,
     ]);
     $request = $this->client->createPresignedRequest($cmd, '+120 minutes');
     return  (string) $request->getUri();
  }

}
?>