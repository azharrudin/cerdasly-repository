<?php
require_once(__DIR__ . "/../vendor/ezyang/htmlpurifier/library/HTMLPurifier.auto.php");
function htmlpurify($dirty_html){
    $config   = HTMLPurifier_Config::createDefault();
    $config->set("URI.AllowedSchemes", 
        array("data" => true)
    );
    $config->set('HTML.Allowed',"
        a[href|style],
        span[style],
        br[style],
        p[style],
        strike[style],
        ol[style],
        li[style],
        ul[style],
        center[style],
        u[style],
        b[style],
        i[style],
        sup[style],
        sub[style]
    ");
    $purifier = new HTMLPurifier($config);
    $c = $purifier->purify($dirty_html);
    return $c;
}