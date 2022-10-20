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
    $login_button             = strlen($profile_image) > 0 ? "" : "<a style=\"float: right;margin-left: 6px\" class=\"btn btn-primary btn-sm ml-1\" href='/login'>Masuk</a>";
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
                $login_button
                <a style="float: right;$profile_image_exist" href="/profile/" id="profile" class="ui_circular_image-x35">
                    $profile_image_button
                </a>
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
                <button class="ui_mobile_navigation"><h4><a href="/" class="text-muted"><span class="bi bi-house-door"></span></a></h4></button>
                <button class="ui_mobile_navigation"><h4><a href="/" class="text-muted"><span class="bi bi-trophy"></span></a></h4></button>
                <button class="ui_mobile_navigation"><h4><a class="text-muted"><span class="bi bi-newspaper"></span></a></h4></button>
                <button class="ui_mobile_navigation"><h4><a><span class="bi bi-search"></span></a></h4></button>
            </div>
        EOF;
      
    }
?>