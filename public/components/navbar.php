<?php
require_once(__DIR__."/../../php/cerdasly.php");
require_once(__DIR__."/../../php/functions.php");  
function navigationBar($user, $space = true){
    $core                     = new Core();
    $profile_image            = $core->getImgByUsername($user);
    $profile_image            = strlen($profile_image) > 0 
    ? "<img onclick=\"window.location = '/profile/'\" src='$profile_image' height=35 style='border-radius: 100%;max-width: 35px;object-fit: cover;'>" 
    : "<button class=\"btn btn-primary btn-sm\">Masuk</button>";
    $unreadNotificationsTotal = count($core->getUnreadNotifications($user)); 
    $space = $space ? '<div style="margin-top: 50px;"></div>' : "";
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
                <div class="ui_navbar_right_side">
                    <a href="/notifications" ><span class="bi bi-bell ui_navbar_notification_bell"></span></a>
                    <a class="btn btn-danger btn-sm" type="button"  href="/ask">
                        Bertanya
                    </a>
                <span class="badge badge-pill badge-danger ui_navbar_notification_badge">$unreadNotificationsTotal</span>
                </div>          
            </div>
            $space
        EOF;
      
    }
?>