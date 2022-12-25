<?php
    require_once(__DIR__."/components/navbar.php");
    require_once(__DIR__."/../php/cerdasly.php");
    require_once(__DIR__."/../php/functions.php");
    $core           = new Core();
    $userLogin        = false;
    $currentuserimg = '<a href="/login" type="button" class="btn btn-primary" style="float: right;margin-right: 5px;">Login</a>';
    if((isset($_COOKIE["email"]) && $_COOKIE["pass"]) && ($core->login($_COOKIE["email"], $_COOKIE["pass"])) != false){ 
        $userLogin        = true;
        $user             = $core->getUsername($_COOKIE["email"]);
        $currentuserimg   = "<img onclick='window.location = \"/profile/\"' src='".$core->getImg($_COOKIE['email'])."' height=35 style='border-radius: 100%;max-width: 35px;'>";
    }
    if(($core->login($_COOKIE["email"], $_COOKIE["pass"])) == false){
        header("Location: login.php");
    }
    if(isset($_POST["ntfreadall"])){
        $core->notificationReadAll($user);
    }
    $max = isset($_POST["max"]) ? intval($_POST["max"]) : 15;
    $allNotifications           = $core->getAllNotifications($user, ($max-15 < 0 ? 0 : $max-15), $max);
    $unreadedNotifications      = $core->getUnreadNotifications($user);
    $unreadedNotificationsTotal = count($unreadedNotifications); 
?>
<!doctype html>
<html>
    <head>
        <title>Cerdasly - Notifikasi</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="/styles/styles.css" rel="stylesheet">
        <link href="/styles/ui.css" rel="stylesheet">
        <link href="/styles/components.css" rel="stylesheet">
        <meta name="description" content="Notifikasi akun kamu">
        <meta name="title" content="Cerdasly - Masuk">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.3/font/bootstrap-icons.css" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-0evHe/X+R7YkIZDRvuzKMRqM+OrBnVFBL6DOitfPri4tjfHxaWutUpFmBp4vmVor" crossorigin="anonymous">
        <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.css" rel="stylesheet">
        <link href="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css" rel="stylesheet">
        <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.js"></script>
        <style>
            .notification-icon {
                width: 40px;
                height: 40px;
                margin-right: 10px;
                border-radius: 100%;
            }
            .ui_notification_container_left_button {
                background: none;
                border: none;
                margin: 5px;
                max-width: 50%;
            }
        </style>
    </head>
    <body>
       <?= navigationBar($user); ?>
      
       <div>
            <div class="lay-container-smaller">
                <button class="text-dark ui_notification_container_left_button">Notifikasi</button>
                <button id="btnreadall" style="float: right;border: none;margin: 5px;max-width: 50%;">Baca Semua</button>
            </div>
            <div class="lay-container-smaller" id="ntfcontainer">
                <div class="text-muted" id="notificationemptymsg"><center>Anda belum memiliki notifikasi</center></div>
          
                <?php
                    foreach($allNotifications as $val):
                        if(strpos($val["code"], $notificationCode["answered"]) !== false){

                            $link    = str_replace($notificationCode["answered"], "" ,$val["code"]);
                            $title   = "<b>Pertanyaan anda sudah dijawab</b>";
                            $bgcolor = intval($val['readed']) ? "#FFFFFF" : '#D8D8D8';
                            $link = "/question/".$link;
                        }
                        
                        if(strpos($val["code"], $notificationCode["commented"]) !== false){
                            $link    = str_replace($notificationCode["commented"], "" ,$val["code"]);
                            $title   = "<b>Jawaban anda mendapat komentar</b>";
                            $bgcolor = intval($val['readed']) ? "#FFFFFF" : '#D8D8D8';
                            $link = "/comments/".$link;
                        }
                ?>
                    <div class="card-question" id="notificationcard" style="display: flex;background-color: <?= $bgcolor ?>;border-radius: 1px;margin-bottom: 0.1px;margin-top: 0.1px">
                        <div style="margin-top: 2px;">
                            <img src="<?= $core->getImgByUsername($val['actor']); ?>" class="notification-icon">
                        </div>
                        <div>
                            <li style="list-style-type: none;"><?= $title ?></li>
                            <a href='<?= $link; ?>&readed_id=<?= $val['id'] ?>&user=<?= $val['username'] ?>' style="text-decoration: none;" class="text-muted"><?= $val["content"]; ?></a>
                        </div>
                    </div>
                <?php
                    endforeach;
                ?>
            </div>
       </div>
        <script>
            var page = 11
            function getDocHeight() {
                var D = document;
                return Math.max(
                    D.body.scrollHeight, D.documentElement.scrollHeight,
                    D.body.offsetHeight, D.documentElement.offsetHeight,
                    D.body.clientHeight, D.documentElement.clientHeight
                );
            }
            $(window).scroll(function() {
                if($(window).height() + $(window).scrollTop() == getDocHeight()) {
                    ntf.loadmore()
                }
            })
            if($("#notificationcard").length > 0){
                $("#notificationemptymsg").hide()
            }
            $("#btnreadall").click(function(){
                ntf.readall()
            })
            error = false
            var ntf = {
                readall: function(){
                    $.ajax({
                        url: "#",
                        type: "POST",
                        data: {
                            ntfreadall: true
                        },
                        success: function (data, status){
                           $("#ntfcontainer").html($(data).find("#ntfcontainer").html())
                          
                        }
                       
                    })
                },
                loadmore: function(){
                    $.ajax({
                        url: "/notifications.php",
                        data: {
                            max: page
                        },
                        type: "GET",
                        success: function (data, status){
                            if($("#useranswers").html() != $(data).find("#useranswers").html()) $("#loadsign").hide()
                            else $("#loadsign").html("Telah mencapai batas")
                            page += 16
                            $("#useranswers").html($(data).find("#useranswers").html())
                        },
                        beforeSend: function(){
                            $("#loadsign").html("Memuat...")
                        },
                        error: function(jqXHR, textStatus, errorThrown){
                            error = true
                            $("#loadsign").html("<div class='alert alert-danger mt-2'>Sudah mencapai batas atau coba periksa kembali perangkat kamu</div>")
                        }
                    })
                }
            }
        </script>
    </body>