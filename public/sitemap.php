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
    $m = DateTime::createFromFormat("d-m-y H:i:s", $data['postdate']);
    $m = $m->format(DATE_W3C);
    echo "<url>";
    echo "<loc>$url/question/".$data['id']."</loc>";
    echo "<lastmod>$m</lastmod>";
    echo "<priority>1.0</priority>";
    echo" <changefreq>always</changefreq>";
    echo "</url>";
}
echo "</urlset>";
