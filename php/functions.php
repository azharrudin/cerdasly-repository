<?php
class tools {
    function validatePassword($password){
        if(!preg_match("/^[0-9A-Za-z\_]+$/", $password))
            return false;
        elseif(strlen($password) > 20 || strlen($password) < 4)
            return false;
        else return true;
    }
}
$categories  = array(
    "Matematika", 
    "Bahasa Indonesia", 
    "Bahasa Inggris", 
    "Bahasa Daerah", 
    "Bahasa Asing", 
    "Ekonomi",
    "Hukum", 
    "Kimia", 
    "Komputer", 
    "Arsitektur",
    "PPKN",
    "Psikologi",
    "PJOK",
    "Kedokteran", 
    "Kedokteran Hewan", 
    "Akuntansi", 
    "Farmasi", 
    "Filsafat", 
    "Fisika", 
    "Politik",
    "Seni Budaya", 
    'Sains',
    "Sosial", 
    "Sastra", 
    "Sejarah",
    "Agama Islam",
    "Agama Hindu",
    "Agama Kristen",
    "Agama Buddha",
    "Agama Katolik",
    "Spiritual",
    "Lainnya"
);
/*-------------------------------------------------------------------------------
 * > used to store image in perfect square
 *-------------------------------------------------------------------------------
 */
function imgcrop($imgSrc){
    //getting the image dimensions
    list($width, $height) = getimagesize($imgSrc);
    //saving the image into memory (for manipulation with GD Library)
    if(exif_imagetype($imgSrc) == IMAGETYPE_PNG)
        $myImage = imagecreatefrompng($imgSrc);
    if(exif_imagetype($imgSrc) == IMAGETYPE_JPEG)
        $myImage = imagecreatefromjpeg($imgSrc);
    // calculating the part of the image to use for thumbnail
    if ($width > $height) {
        $y = 0;
        $x = ($width - $height) / 2;
        $smallestSide = $height;
    } else {
        $x = 0;
        $y = ($height - $width) / 2;
        $smallestSide = $width;
    }
    // copying the part into thumbnail
    $thumbSize = min($width,$height);
    $thumb = imagecreatetruecolor($thumbSize, $thumbSize);
    imagecopyresampled($thumb, $myImage, 0, 0, $x, $y, $thumbSize, $thumbSize, $smallestSide, $smallestSide);
    unlink($imgSrc);
    imagepng($thumb,$imgSrc);
    @imagedestroy($myImage);
    @imagedestroy($thumb);
}
/*-------------------------------------------------------------------------------
 * > used to get "n-" date ago
 *-------------------------------------------------------------------------------
 */
function getndate($datetime, $full = false) {
    $now  = DateTime::createFromFormat("d-m-y H:i:s", gmdate("d-m-y H:i:s"));
    $ago  = DateTime::createFromFormat("d-m-y H:i:s", $datetime);
    $diff = $now->diff($ago);
    $diff->w  = floor($diff->d / 7);
    $diff->d -= $diff->w * 7;
    $string = array(
        'y' => 'tahun',
        'm' => 'bulan',
        'w' => 'minggu',
        'd' => 'hari',
        'h' => 'jam',
        'i' => 'menit',
        's' => 'detik',
    );
    foreach ($string as $k => &$v) {
        if ($diff->$k) {
            $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? '' : '');
        } else {
            unset($string[$k]);
        }
    }

    if (!$full) $string = array_slice($string, 0, 1);
    return $string ? implode(', ', $string) . ' yang lalu' : 'baru saja';
}
/*-------------------------------------------------------------------------------
 * > used to convert category into bootstrap icon
 *-------------------------------------------------------------------------------
 */
function categorytoicon($cat){
    if(preg_match("/(sosial|hukum|politik|sejarah|ekonomi|ppkn)/", $cat))
        return "bi bi-bank2";
    else if(preg_match("/(bahasa)/", $cat))
        return "bi bi-translate";
    else if(preg_match("/(komputer)/", $cat))
        return "bi bi-pc-display";
    else if(preg_match("/(arsitektur)/", $cat))
        return "bi bi-building";
    else if(preg_match("/(seni|sastra)/", $cat))
        return "bi bi-pallete";
    else if(preg_match("/(agama|spiritual)/", $cat))
        return "bi bi-book";
    else if(preg_match("/(matematika|akuntansi)/", $cat))
        return "bi bi-pie-chart";
    else if(preg_match("/(kimia|fisika)/", $cat))
        return "bi bi-radioactive";
    else if(preg_match("/(kedokteran|psikologi|farmasi)/", $cat))
        return "bi bi-hospital";
    else 
        return "bi-globe";
}
/**
 * Type 
 */
$notificationCode = array(
    "approved"  => "APR",
    "answered"  => "ANS",
    "commented" => "CMT"
);
function msgcreate($text){
    return $text != "" ? '<div class="alert alert-danger" style="max-width: 100%;">
                <p class="card-text">'.$text.'</p>
            </div>' : '';
}
/*-------------------------------------------------------------------------------
 * > used to get ranks based on total voted answer
 *-------------------------------------------------------------------------------
 */
function getranks($totalvoted, $totalanswers, $totalquestion){
    $total = ($totalanswers+$totalquestion) * ($totalvoted);
    if($total < 100){
        return array(
            "category" => "newbie",
            "star" => 1
        );
    }
    else if($total < 200){
        return array(
            "category" => "newbie",
            "star" => 2
        );
    }
    else if($total < 200){
        return array(
            "category" => "newbie",
            "star" => 3
        );
    }
    else if($total < 300){
        return array(
            "category" => "amatir",
            "star" => 1
        );
    }
    else if($total < 400){
        return array(
            "category" => "amatir",
            "star" => 2
        );
    }
    else if($total < 500){
        return array(
            "category" => "amatir",
            "star" => 3
        );
    }
    else if($total < 700){
        return array(
            "category" => "master",
            "star" => 1
        );
    }
    else if($total < 900){
        return array(
            "category" => "master",
            "star" => 2
        );
    }
    else if($total < 1100){
        return array(
            "category" => "master",
            "star" => 3
        );
    }
    else if($total < 1400){
        return array(
            "category" => "grandmaster",
            "star" => 1
        );
    }
    else if($total < 1700){
        return array(
            "category" => "grandmaster",
            "star" => 3
        );
    }
    else if($total > 2000){
        return array(
            "category" => "expert",
            "star" => 0
        );
    }

}