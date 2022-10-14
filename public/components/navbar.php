<?php
require_once(__DIR__."/../../php/cerdasly.php");
require_once(__DIR__."/../../php/functions.php");  
function navigationBar($user, $space = true){
    $core                     = new Core();
    $profile_image            = $core->getImgByUsername($user);
    $profile_image_button     = strlen($profile_image) > 0 
    ? "<img onclick=\"window.location = '/profile/'\" src='$profile_image' height=35 style='border-radius: 100%;max-width: 35px;object-fit: cover;' loading='lazy'>" 
    : "";
    $profile_image_exist      = strlen($profile_image) > 0 ? "" : "display: none;";
    $login_button             = strlen($profile_image) > 0 ? "" : "<a style=\"float: right;margin-left: 6px\" class=\"btn btn-primary btn-sm ml-1\" href='/'>Masuk</a>";
    $unreadNotificationsTotal = count($core->getUnreadNotifications($user)); 
    $space = $space ? '<div style="margin-top: 46px;"></div>' : "";
    return <<<EOF
            <div class="fixed-top ui_navbar" style="item-align: center">
                <img
                    src="/cerdasly.png"
                    height="40"
                    alt="Cerdasly Logo"
                    loading="lazy"
                    id="logo"
                />
                <a style="float: right;$profile_image_exist" id="profile" class="ui_circular_image-x35">
                    $profile_image_button
                </a>
                $login_button
                <div class="ui_navbar_right_side">
                    <a href="/notifications"><span class="bi bi-bell ui_navbar_notification_bell"></span></a>
                    <a class="btn btn-danger btn-sm" type="button"  href="/ask">
                        Bertanya
                    </a>
                    <span class="badge badge-pill badge-danger ui_navbar_notification_badge">$unreadNotificationsTotal</span>
                </div>          
            </div>
            $space
            <div class="lay-mobile-only" style="overflow-x: scroll;background: white;margin-bottom: 4px;height: fixed;">
                <button style="background: none;border: none;width: 24vw;margin:0;padding:0"><h4><a><span class="bi bi-trophy"></span></a></h4></button>
                <button style="background: none;border: none;width: 24vw;margin:0;padding:0"><h4><span class="bi bi-list"></span></h4></button>
                <button style="background: none;border: none;width: 24vw;margin:0;padding:0"><h4><span class="bi bi-newspaper"></span></h4></button>
                <button style="background: none;border: none;width: 24vw;margin:0;padding:0"><h4><span class="bi bi-search"></span></h4></button>
            </div>
        EOF;
      
    }
?>