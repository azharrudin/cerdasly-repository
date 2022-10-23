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
    $login_button             = strlen($profile_image) > 0 ? "" : "<a style=\"float: right;\" class=\"btn btn-primary btn-sm lay_button_login\" href='/login'>Masuk</a>";
    $unreadNotificationsTotal = count($core->getUnreadNotifications($user)); 
    $space = $space ? '<div class="ui_navspace"></div>' : "";
    return <<<EOF
            <div class="fixed-top ui_navbar navbar-fixed-top" style="item-align: center;width: 100%;z-index:999;">
                <img
                    src="/logo.png"
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
                <div class="lay-mobile-only w-100" style="overflow-x: scroll;background: white;margin-bottom: 4px;height: fixed;position: fixed;z-index: 999;border-top: 1px solid rgb(230, 230, 230);border-bottom: 1px solid rgb(230, 230, 230)">
                    <button class="ui_mobile_navigation"><h4><a href="/" class="text-muted"><span class="bi bi-house-door"></span></a></h4></button>
                    <button class="ui_mobile_navigation"><h4><a  onclick="ui_ranklist()" class="text-muted"><span class="bi bi-trophy"></span></a></h4></button>
                    <button class="ui_mobile_navigation"><h4><a class="text-muted"><span class="bi bi-newspaper"></span></a></h4></button>
                    <button class="ui_mobile_navigation"><h4><a><span class="bi bi-search"></span></a></h4></button>
                </div>    
            </div>
            $space
        EOF;
      
    }
?>