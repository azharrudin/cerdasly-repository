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
                <a class="btn btn-danger btn-sm" type="button"  href="/ask" style="float: right;margin-right: 5px; ">
                    Bertanya
                </a>
                <span class='desktop-only' style="float: right;margin-right: 10px;"> <button class="position-relative ui_mobile_navigation-b ">
                    <h4 class="bi bi-bell"></h4>
                    <span class="position-absolute translate-middle badge rounded-pill bg-danger" style="top: 10px;left: 23px;">
                    $unreadNotificationsTotal</span>
                      <span class="visually-hidden">unread messages</span>
                </span></div>
               
                <div class="lay-mobile-only w-100" style="background: white;margin-bottom: 4px;height: fixed;position: fixed;z-index: 999;border-top: 1px solid rgb(230, 230, 230);border-bottom: 1px solid rgb(230, 230, 230);width: 100vw;margin-top: 45px;">
                    <button class="ui_mobile_navigation"><h4><a href="/" class="text-muted"><span class="bi bi-house-door"></span></a></h4></button>
                    <button class="ui_mobile_navigation"><h4><a  onclick="ui_ranklist()" class="text-muted"><span class="bi bi-trophy"></span></a></h4></button>
                    <button class="ui_mobile_navigation"><h4><a class="text-muted"><span class="bi bi-newspaper"></span></a></h4></button>
                    <span> <button class="position-relative ui_mobile_navigation-b">
                    <h4 class="bi bi-bell"></h4>
                    <span class="position-absolute translate-middle badge rounded-pill bg-danger" style="top: 10px;left: 23px;">
                    $unreadNotificationsTotal
                    </span>
                    </button></span>
                </div>    
            </div>
            $space
        EOF;
      
    }
?>