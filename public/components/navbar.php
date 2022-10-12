<?php
require_once(__DIR__."/../../php/cerdasly.php");
require_once(__DIR__."/../../php/functions.php");  
function navigationBar($user, $space = true){
    $core                     = new Core();
    $profile_image            = $core->getImgByUsername($user);
    $profile_image            = strlen($profile_image) > 0 
    ? "<img onclick=\"window.location = '/profile/'\" src='$profile_image' height=35 style='border-radius: 100%;max-width: 35px;object-fit: cover;' loading='lazy'>" 
    : "";
    $login_button             =  strlen($profile_image) > 0 ? "": "<button style=\"float: right\" class=\"btn btn-primary btn-sm\">Masuk</button>";
    $unreadNotificationsTotal = count($core->getUnreadNotifications($user)); 
    $space = $space ? '<div style="margin-top: 46px;"></div>' : "";
    return <<<EOF
            <div class="fixed-top ui_navbar">
                <img
                    src="/cerdasly.png"
                    height="40"
                    alt="Cerdasly Logo"
                    loading="lazy"
                    id="logo"
                />
                <a style="float: right;" id="profile" class="ui_circular_image-x35">
                    $profile_image
                </a>
                $login_button
                <div class="ui_navbar_right_side">
                    <a href="/notifications" ><span class="bi bi-bell ui_navbar_notification_bell"></span></a>
                    <a class="btn btn-danger btn-sm" type="button"  href="/ask">
                        Bertanya
                    </a>
                <span class="badge badge-pill badge-danger ui_navbar_notification_badge">$unreadNotificationsTotal</span>
                </div>          
            </div>
            $space
            <div class="lay-mobile-only" style="overflow-x: scroll;background: white;margin-bottom: 4px;">
                <button style="background: none;border: 1px solid #F0F0F0;width: 25vw;margin:0;padding:0"><h4><span class="bi bi-trophy"></span></h4></button>
                <button style="background: none;border: 1px solid #F0F0F0;width: 25vw;margin:0;padding:0"><h4><span class="bi bi-list"></span></h4></button>

            </div>
        EOF;
      
    }
?>