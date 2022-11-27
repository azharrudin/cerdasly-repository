<?php
define("UPLIB_API_KEY", "EZAe34EvrGXYtNV1029m3LkN10eF");
class UPLib {
  function minifyimage($source_url, $destination_url, $quality){
      $info = getimagesize($source_url);
      if ($info['mime'] == 'image/jpeg') $image = imagecreatefromjpeg($source_url);
      elseif ($info['mime'] == 'image/gif') $image = imagecreatefromgif($source_url);
      elseif ($info['mime'] == 'image/png') $image = imagecreatefrompng($source_url);
      imagejpeg($image, $destination_url, $quality);
      return $destination_url;  
  }
  

}
?>