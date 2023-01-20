<?php
require_once(__DIR__ . "/../php/cerdasly.php");
//-----------------------------------------------
$Core = new Core();
$UserLogin = false;
$max = isset($_GET["max"]) ? intval($_GET["max"]) : 15;
$answer = $Core->getAnswerByID($_GET["id"]);
if ((isset($_COOKIE["email"]) && $_COOKIE["pass"]) && ($Core->login($_COOKIE["email"], $_COOKIE["pass"])) != false) {
    $UserLogin = true;
    $user      = $Core->getUsername($_COOKIE["email"]);
    if (isset($_GET["readed_id"]) && @$_GET["user"] == $user) {
        header("Location: /comments/" . $_GET["id"]);
        $Core->readNotification($_GET["readed_id"]);
    }
}
//-----------------------------------------------
if (isset($_POST["comment"]) && $UserLogin) {
    $comment = strip_tags($_POST["comment"]);
    $Core->addComment($comment, $_GET["id"], $Core->getUsername($_COOKIE["email"]));
    $Core->addNotification(
        $Core->getRealnameByUsername($user) . " memberikan komentar <b>'" . substr($comment, 0, 40) . "'</b>",
        $answer["username"],
        $notificationCode["commented"] . $_GET["id"],
        $user
    );
}
$json = $Core->getComments(trim($_GET["id"]), ($max - 15 < 0 ? 0 : $max - 15), $max);
if (isset($_POST["comment_delete"]) && isset($_POST["comment_id"]) && $UserLogin && $Core->getCommentById($_POST["comment_id"])["username"] == $user) {
    $Core->deleteComment(trim($_POST["comment_id"]));

    $json = $Core->getComments(trim($_GET["id"]), ($max - 15 < 0 ? 0 : $max - 15), $max);
}

?>
<!DOCTYPE html>
<html>

<head>
    <title>Komentar - <?= isset($answer["answer"]) ? substr(strip_tags($answer["answer"]), 0, 50) : "Jawaban Tidak ada" ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content=" <?= isset($answer["answer"]) ? substr(strip_tags($answer["answer"]), 0, 300) : "Jawaban Tidak ada" ?>">
    <meta name="robots" content="noindex, nofollow">

    <link href="/styles/styles.css" rel="stylesheet">
    <link href="/styles/components.css" rel="stylesheet">
    <link href="/styles/ui.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-0evHe/X+R7YkIZDRvuzKMRqM+OrBnVFBL6DOitfPri4tjfHxaWutUpFmBp4vmVor" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.css" rel="stylesheet">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.js"></script>
    <style>

    </style>
</head>

<body>
    <div>
        <?php
        if (count($answer) < 1) :
            die("
                <p id='ui_commentnotexist' class='alert alert-danger lay-container-smaller mt-2' style='margin-bottom: 0px;text-align: center;''>Tidak ada jawaban dengan id " . $_GET['id'] . "</p>
            ");
        endif;
        ?>
        <div class="lay-container-smaller pt-2" style="padding-bottom: 0px;margin-top: 0px;">
            <div class="card-question" style="overflow: auto;">
                <div class="ui_circular_wrapper">
                    <div class="ui_circular_image-x30">
                        <img onclick="window.location = '/profile/<?= $answer['username'] ?>'" src="<?= $Core->getImgByUsername($answer['username']); ?>" class="ui_circled_image-x30">
                    </div>
                    <span class="text-muted" onclick="window.location = '/profile/<?= $answer['username'] ?>'">
                        <?= $Core->getRealnameByUsername($answer["username"]); ?> &#183
                        <?= getndate($answer["postdate"]); ?>
                    </span>
                </div>
                <form action="#" id="question-delete-form" method="POST">
                    <input type="hidden" name="question_delete" value="delete">
                </form>
                <div class="quebox" style="word-wrap: break-word;" id="answer">
                    <a href="/question/<?= $answer["questionid"] ?>" class="text-dark"><?= preg_replace("/<br\W*?\/>/", " ", substr(strip_tags(@$answer["answer"], "<br>"), 0, 400)); ?></a>
                </div>
                <div class="dropup">
                    <a class="btn btn-default" type="button" data-toggle="dropdown" style="float: right;border:none;">
                        <span class="bi bi-three-dots-vertical"></span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-right">
                        <li>

                            <a id="comment-delete-button" class="bi bi-megaphone">
                                Laporkan
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <div id="comments_container" style="margin-bottom: 20vh;">
        <div class="lay-container-smaller">
            <h5>&nbsp;Komentar:</h5>
        </div>
        <div id="comments">
            <?php

            foreach ($json as $key) :
            ?>
                <div class="lay-container-smaller card-question" style="margin-bottom:0px;margin-top: 0px;word-wrap:break-word;z-index: 0;">
                    <div class="ui_circular_wrapper">
                        <div class="ui_circular_image-x30">
                            <a><img src="<?= $Core->getImgByUsername($key['username']); ?>" class="ui_circled_image-x30"></a>
                        </div>
                        <span class="text-muted" onclick="window.location = '/profile/<?= $answer['username'] ?>'">
                            <?= $Core->getRealnameByUsername($key["username"]); ?> &#183
                            <?= getndate($key["postdate"]); ?>
                        </span>
                    </div>
                    <?= $key["content"]; ?>
                    <div class="dropup">
                        <a class="btn btn-default" type="button" data-toggle="dropdown" style="border:none;float: right;">
                            <span class="bi bi-three-dots-vertical"></span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-right">
                            <li>
                                <?php
                                if ($UserLogin && $Core->getUsername($_COOKIE["email"]) == $key["username"]) :
                                ?>
                                    <a id="comment-delete-button" onclick="commentdelete('<?= $key['id']; ?>')">
                                        <span class="bi bi-trash-fill"></span>
                                        Hapus Komentar
                                    </a>
                                <?php
                                endif;
                                ?>
                                <a id="comment-delete-button" <span class="bi bi-megaphone"></span>
                                    Laporkan
                                </a>

                        </ul>
                    </div>
                    <div style="clear: both"></div>
                </div>
            <?php
            endforeach;
            if (count($json) < 1) :
            ?>
                <div>
                    <p id="ui_nocomment" class="alert alert-danger lay-container-smaller mt-2" style="margin-bottom: 0px;text-align: center;">Belum ada komentar</p>
                </div>
            <?php
            endif;
            ?>
        </div>
        <div class="lay-container-smaller text-center" id="ui_loadmore"></div>
    </div>
    <?php
    if ($UserLogin) :
    ?>
        <div class="card-question lay-container-smaller fixed-bottom">
            <div style="display: flex;padding-bottom: 0px;">
                <div class="ui_circular_wrapper" style="align-items:inherit">
                    <div class="ui_circular_image-x30">
                        <img onclick="window.location = '/profile/<?= $v['username'] ?>'" src="<?= $Core->getImg($_COOKIE["email"]); ?>" class="ui_circled_image-x30">
                    </div>
                    <div class=" form-group input-group" style="max-width: 100%;">
                        <input class="form-control" id="comment" placeholder="tulis komentar disini">
                        <span class="input-group-addon" style="background-color: white;border-left: none;" onclick="postcomment(this)">
                            <span class="bi bi-send-fill"></span>
                        </span>
                    </div>
                </div>

            </div>
            <div class="text-right mb-0 "><span id="max-count">0</span>/200</div>
        </div>
    <?php
    endif;
    ?>
</body>
<script>
    function getDocHeight() {
        var D = document;
        return Math.max(
            D.body.scrollHeight, D.documentElement.scrollHeight,
            D.body.offsetHeight, D.documentElement.offsetHeight,
            D.body.clientHeight, D.documentElement.clientHeight
        );
    }
    $("#comment").on("input", function() {
        $("#max-count").text($("#comment").val().length)
    })

    var page = 25
    var d = {}
    var error = false
    $(window).scroll(function() {
        if ($(window).height() + $(window).scrollTop() == getDocHeight()) {
            if (!error) $.ajax({
                url: "/comments/" + "<?= $_GET["id"] ?>" + '&max=' + page,
                type: "GET",
                success: function(data, status) {
                    d = data
                    page += 10
                    if ($(data).find("#ui_nocomment").length < 1) {
                        error = true
                        $("#ui_loadmore").show()
                        $("#ui_loadmore").html("<div class='alert alert-danger mt-2'>Sudah mencapai batas atau coba periksa kembali perangkat kamu</div>")
                    }
                    $("#comments").append($(data).find("#comments").html())

                },
                beforeSend: function() {
                    $("#ui_loadmore").html("Memuat...")
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    error = true
                    $("#ui_loadmore").html("<div class='alert alert-danger mt-2'>Sudah mencapai batas atau coba periksa kembali perangkat kamu</div>")
                }
            })
        }
    })

    function commentdelete(s) {
        $("#comments").animate({
            opacity: 0.5
        }, 100);
        $.ajax({
            url: "#",
            type: "POST",
            data: {
                comment_delete: true,
                comment_id: s,
                comment_username: "<?= $user; ?>"
            },
            success: function(data) {
                d = data
                $("#comments").animate({
                    opacity: 1
                }, 100);
                $("#comments").html($(data).closest("#comments_container").find("#comments").html())
            },
            complete: function(data) {
                $("#comments").animate({
                    opacity: 1
                }, 100);
            }
        })
    }

    function postcomment(s) {
        var s = $(s).parent()
        $("#comments").animate({
            opacity: 0.5
        }, 100);
        $.ajax({
            url: "#",
            type: "POST",
            data: {
                comment: s.find("#comment").val(),
            },
            success: function(data) {
                d = data
                $("#comments").animate({
                    opacity: 1
                }, 100);
                $("#comments").html($(data).closest("#comments_container").find("#comments").html())
                s.find("#comment").val("")
            },
            complete: function(data) {
                $("#comments").animate({
                    opacity: 1
                }, 100);
            }
        })
    }
</script>

</html>