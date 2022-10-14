<?php
require_once(__DIR__."/../php/cerdasly.php");
header('Content-type: application/xml');
echo "<?xml version='1.0' encoding='UTF-8'?>"."\n";
echo "<urlset xmlns='http://www.sitemaps.org/schemas/sitemap/0.9'>"."\n";
echo " ";
if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on')   
   $url = "https://";   
else  
   $url = "http://";   
$url.= $_SERVER['HTTP_HOST'];   
$core  = new Core();
$links = $core->getRecentQuestion(0, 30);
foreach($links as $data){
    echo "<url>";
    echo "<loc>$url/question/".$data['id']."</loc>";
    echo "<lastmod>". date("20d-m-y H:i:s", strtotime($data["postdate"]))."</lastmod>";
    echo "<priority>1.00</priority>";
    echo "</url>";
}
echo "</urlset>";
?>