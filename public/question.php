<?php 
require_once(__DIR__."/../php/cerdasly.php");
require_once(__DIR__."/../php/functions.php");
require_once(__DIR__."/../php/tools/libtools.php");
require_once(__DIR__."/components/navbar.php");
require_once(__DIR__."/components/favicon.php");
//-------------------------------------------
// initialize variables and objects
//-------------------------------------------
$Core      = new Core();
$UserLogin = false;
$currentanswer  = array();
if((isset($_COOKIE["email"]) && $_COOKIE["pass"]) && ($Core->login($_COOKIE["email"], $_COOKIE["pass"])) != false){
    $UserLogin = true;
    $user      = $Core->getUsername($_COOKIE["email"]); 
    if(isset($_GET["readed_id"]) && @$_GET["user"] == $user){
        header("Location: /question/".$_GET["id"]);
        $Core->readNotification($_GET["readed_id"]);
    }
}
if(isset($_POST["answer_vote"]) && isset($_POST["question_username"]) && $user == $_POST["question_username"]){
    $Core->voteAnswer($_POST["answer_id"], 1);
}
if(isset($_POST["answer_disvote"])  && isset($_POST["question_username"]) && $user == $_POST["question_username"]){
    $Core->voteAnswer($_POST["answer_id"], 0);
}
$question = $Core->getQuestion($_GET["id"]);
if(isset($_GET["id"]) && $question != false){
    $best_answer = $Core->getBestAnswer($_GET["id"]);
    if(!isset($best_answer["answer"])) $best_answer["answer"] = "Pertanyaan ini belum dijawab";
}
$is_question_exist = isset($_GET["id"]) && $Core->getQuestion(trim($_GET["id"]));
if($is_question_exist == false) http_response_code(404);
?>
<!DOCTYPE html>
<html>
    <head>
        <title><?= substrwords(strip_tags($question["title"]), 10); ?></title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta content="<?= substrwords(strip_tags($best_answer["answer"]), 30); ?>" name="description"/>
        <meta content="title" name="<?= substrwords(strip_tags($question["title"]), 10); ?>">
        <meta name="robots" content="index">
        <meta name="keywords" content="kunci jawaban, penjelasan, pertanyaan">
        <?= faviconImg() ?>
        <link href="/styles/styles.css" rel="stylesheet">
        <link href="/styles/ui.css" rel="stylesheet">
        <link href="/styles/components.css" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.3/font/bootstrap-icons.css" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-0evHe/X+R7YkIZDRvuzKMRqM+OrBnVFBL6DOitfPri4tjfHxaWutUpFmBp4vmVor" crossorigin="anonymous">
        <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.css" rel="stylesheet">
        <link href="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css" rel="stylesheet">
        <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/clipboard.js/2.0.10/clipboard.min.js"></script>
        <script src="/js/clipper.js"></script>
        <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-6098201334142651"
     crossorigin="anonymous"></script>
        <script>
            const Popup = Swal.mixin({
                toast: true,
                position: 'top',
                showConfirmButton: false,
                timer: 3500
            });
        </script>
        <script src="/js/ui.js"></script>
        <style>
            .swal2-popup {
                font-size: 1rem;
            }
            body {
                background-color:rgb(253, 253, 253);
            }
            .box {
                border: 2px solid rgb(230, 230, 230);
                padding: 6px;
                border-radius: 5px;
            }
            .question-box {
                padding:  1px;
            }
            .question-box .lay-container-smaller {
                padding: 6px;
                border-radius: 5px;
                background-color: white;
                border: 2px solid rgb(230, 230, 230);   
            }
            .answer-box {
                padding: 1px;
            }
            .answer-box .lay-container-smaller {
                padding: 6px;
                margin-bottom: 1px;
                margin-top: 1px;
                border-radius: 5px;
                background-color: white;
                border: 2px solid rgb(230, 230, 230);
            }
            .other-question {
                max-width: 100%;
                position: relative;
                text-decoration: dotted;
                margin-bottom:10px;
            }
            .side-left-height {
                min-height: 95vh;
            }
            @media (max-width: 460px) and (min-width: 200px){
                .side-left-height {
                    min-height:auto;
                }   
            }
        </style>
    </head>
    <body>
        <?= navigationBar(isset($user) ? $user:""); ?>
        <div id="errorpopup" style="display: none"></div>
        <div  class="lay-mobile-only ui_question_search">
            <div class="form-group " style="margin-bottom: 5px;">
                <div class="input-group cps-input-group">
                    <span class="input-group-addon"><span class="bi bi-search"></span></span>
                    <input class="form-control" style="border-right: none;border-left: 1px" id="ui_question_search" placeholder="cari pertanyaan atau tugas anda disini..." name="email">
                </div> 
            </div>
        </div>
    <?php
        if($is_question_exist != false):
            $libtools           = new Libtools();
            $attachment         = $libtools->UploadAttachmentTools();
            $v                  = $Core->getQuestion($_GET["id"]);
            $realname           = $Core->getRealnameByUsername($v["username"]);
            $answers            = $Core->getAnswers($_GET["id"]);
            $alreadyanswer      = false;  
            $sfk = array();
            foreach($answers as $ans)
                if($ans["username"] == trim($Core->getUsername(@$_COOKIE["email"]))){
                    $alreadyanswer   = true;
                    $sfk = $ans;
                }
            if(isset($_POST["ask"]) && $UserLogin){
                if(count($answers) > 2){
                    echo <<<EOF
                        <div id="errorpopupdummy" style="display: none">
                            <script>Popup.fire({
                                text: 'Jawaban sudah mencapai jumlah maksimal (2 jawaban)',
                            });
                            </script>
                        </div>
                    EOF;
                }
                else if(!$alreadyanswer) {
                    if($user != $v["username"]){
                        $attachment_try     =  $Core->addAnswer($_POST["ask"], $user, trim($_GET["id"]));                       
                        $stripedQuestionContent = substr(strip_tags($v["title"]), 0, 35);
                        $Core->addNotification(
                            "Pertanyaan <b>'".$stripedQuestionContent."...'</b> telah dijawab oleh ".$Core->getRealnameByUsername($user), 
                            $v["username"],
                            $notificationCode["answered"].$v["id"],
                            $user
                        );
                        if(is_array($attachment_try)){
                            $attachment_try = $attachment_try["error_message"];
                            echo <<<EOF
                                <script>
                                    Popup.fire({
                                        text: "$attachment_try"
                                    });
                                </script>
                                EOF;
                        }
                    }
                    $answers                = $Core->getAnswers($_GET["id"]);
                    
                }
                else {
                    $attachment_remove  = isset($_POST["attachment_remove"]) && $_POST['attachment_remove'] == "1"  ? true : false; 
                    $attachment_try     = $attachment->upload_answer_attachment($sfk["id"]);
                    $attachment_success = !is_array($attachment_try) ? $attachment_try : $sfk["attachment"];
                    $attachment_isarray = is_array($attachment_try);
                    if($attachment_remove){
                        $attachment->deleteAnswerAttachment($Core->getAnswerByID($sfk["id"])["attachment"]);
                        $currentanswer["answer"] = $Core->updateAnswer($sfk["id"], $_POST["ask"], "none");
                    }
                    else {
                        if(!isset($_FILES["upload_answer_attachment"]) && $sfk["attachment"] !="none")
                            $currentanswer["answer"] = $Core->updateAnswer($sfk["id"], $_POST["ask"], $sfk["attachment"]);
                        else
                            $currentanswer["answer"] = $Core->updateAnswer($sfk["id"], $_POST["ask"], $attachment_success);
                        if(is_array($attachment_try)){
                            $attachment_try = $attachment_try["error_message"];
                            echo <<<EOF
                                <script>
                                    Popup.fire({
                                        text: "$attachment_try"
                                    });
                                </script>
                                EOF;
                        }
                    }

                }
            }
            if(isset($_POST["ask"]) && !$UserLogin){
                echo '
                    <div id="errorpopupdummy" style="display: none">
                        <script>
                            Popup.fire({
                                text: "Anda harus login terlebih dahulu untuk dapat menjawab soal ini",
                            });
                        </script>
                    </div>
                    ';
            }
            if(isset($_POST["question_delete"])){
                if($UserLogin && trim($user) == $v["username"]){
                    $Core->deleteQuestion($_GET["id"]);
                    echo "<script>window.location='/'</script>";
                }
            }
            if(isset($_POST["answer_delete"])){
                if($UserLogin && trim($user) == $_POST["answer_username"] && $Core->getAnswerByID($_POST["answer_id"])["username"] == $user){
                    $Core->deleteAnswer($_POST["answer_id"]);
                    $answers = $Core->getAnswers($_GET["id"]);
                }
            }
            foreach($answers as $ans)
                if($ans["username"] == @$user){
                    $alreadyanswer   = true;
                    $currentanswer   = $ans;
                }
    ?>
    
        <div class="side-left ui_question_layout_left_sidebar" style="border-radius: 6px;">
            <div id="ui_other_question">
                <h4>Pertanyaan Baru</h4>
    <?php
        $recent = $Core->getRecentQuestion(0, 10);
        foreach($recent as $r){
            $rAnswer = substr($r["title"], 0, 80);
            $rAnswer = str_replace("<br>", " ", $rAnswer);
            if(strlen($r["title"]) > 80){
                $rAnswer .= "...";
            }
            echo "<div class='other-question'><a href='/question/".$r["id"]."' class='text-muted other-question'>".strip_tags(trim($rAnswer))."?</a></div>";
        }
    ?>
            </div>
        </div>
        <div class="side-right side-left-height" id="sideright">
            <div class="question-box">
                <div class="list-card-x" style="background: white;border-radius: 6px;">
                    <div style="margin-bottom: 5px;margin-top: 5px;">
                        <span class="<?= categorytoicon($v['category']); ?> link-danger">
                            <?= $v['category'] ?>
                        </span>
                    </div>
                    <div class="ui_circular_wrapper">
                        <div class="ui_circular_image-x30">
                            <img onclick="window.location = '/profile/<?= $v['username'] ?>'" src="<?= $Core->getImgByUsername($v['username']); ?>" class="ui_circled_image-x30">
                        </div>
                        <span class="text-muted" onclick="window.location = '/profile/<?= $v['username'] ?>'">
                            <?= $realname." &#183; ".getndate($v["postdate"]); ?>
                        </span>
                    </div>
                    <form action="#" id="question-delete-form" method="POST">
                        <input type="hidden" name="question_delete" value="delete">
                    </form>
        <?php
                    if($v["attachment"] != "none"):
        ?>            
                    <center>
                    <img src="/data/question_attachment/<?= $v["attachment"] ?>" alt="Lampiran" class="ui_image_attachment">
                            <p></p>
                    </center>
        <?php
                    endif;
        ?>
                    <div class="quebox" style="word-wrap: break-word;">
                        <p><?= $v["title"]; ?></p>
                    </div>
                    <a class="readmore-q">Lanjutkan</a>
    <?php
                    if(isset($_COOKIE["email"])):
                        if($UserLogin && trim($Core->getUsername($_COOKIE["email"])) == $v["username"]):
    ?>
                <div class="dropup">
                    <a class="btn btn-default" type="button" data-toggle="dropdown" style="float: right;border: none;">
                        <span class="bi bi-three-dots-vertical"></span>
                        </a>
                    <ul class="dropdown-menu dropdown-menu-right">
                        <li><a id="question-delete-button"><span class="bi bi-trash-fill"></span>Hapus Pertanyaan</a></li>
                    </ul>
                </div>
    <?php               endif;
                    endif;
    ?>
                <div id="questionansweredcard">
    <?php     if(isset($currentanswer["answer"])): ?>
                    <span type="submit" class="btn btn-default btn-sm bi bi-check-circle"> dijawab</span>
    <?php     else: ?>
                    <span type="submit" class="btn btn-danger btn-sm bi bi-pencil-square" id="jawab"></span>
    <?php     endif; ?>
                </div>
            </div>
        </div>
        <!-- bagian kode html tambah jawaban -->
        <div id="kotak-jawab" class="list-card-x mb-2">
            <form action="#" method="post" id="jawaban" class="ui_question_answer_box" >
                <textarea id="summernote" name="ask" ><?= isset($currentanswer["answer"]) ? $currentanswer["answer"] : ""; ?></textarea>
                <p class="text-muted">Gambar (.png/.jpg/.jpeg):</p> <input type="file" name="upload_answer_attachment" id="ui_attachment">
                <div class="custom-control custom-checkbox" style="display: flex;">
                    <input type='checkbox' class="custom-control-input" name="attachment_remove" id="attachment_remove" value='1' onclick="document.getElementById('attachment_remove').checked ? document.getElementById('ui_attachment').disabled = true: document.getElementById('ui_attachment').disabled = false ">
                    <span class="custom-control-label" for="attachment_remove">&nbsp;tanpa lampiran</span>
                </div><br>
            </form>
            <input  type="submit" 
                    class="btn btn-danger btn-sm cps-btn" 
                    value="<?= isset($currentanswer['answer']) ? 'edit' : 'jawab'; ?> "
                    onclick="submitanswer()" style="width: 100%;background: #D9543F;"
                    id="answerbutton">
        </div>
        <!-- bagian kode html kotak penjawab -->
        <div id="answers">
    <?php       
                if(count($Core->getAnswers($v["id"])) < 1): 
    ?>
                  <div class="alert alert-danger list-card-x">
                        Belum ada jawaban, jadilah yang pertama
                    </div>
                <div class="container">
                  
    <?php       else: 
                    foreach($Core->getAnswers($v["id"]) as $m ):
                        $answererRealname = $Core->getRealnameByUsername($m["username"]);
    ?>
            <div class="answer-box">
   
                <div class="list-card-x <?= intval($Core->getVoted(trim($m["id"]))) > 0 ? 'border-danger':'' ?>" style="word-wrap: break-word;z-index: 2;" >
                    <img src="<?= $Core->getImgByUsername($m['username']); ?>" height=30 width=30 style="margin-right: 3px;" class="ui_profile_icon-x30">
                    <span class="text-muted" onclick="window.location = '/profile/<?= $m['username']?>'">
                        <?= $Core->getRealnameByUsername($m["username"])." &#183 ".getndate($m["postdate"]); ?>
                    </span>
                    <form action="#" id="answer-delete-form" method="POST">
                        <input type="hidden" name="answer_delete" value="delete">
                        <input type="hidden" name="answer_username" value="<?=  $m['username']; ?>">
                        <input type="hidden" name="answer_id" value="<?=  $m['id']; ?>">
                    </form>
                    <form action="#" id="disvote-form" method="POST">
                        <input type="hidden" name="answer_disvote" value="disvote">
                        <input type="hidden" name="answer_id" value="<?=  $m['id']; ?>">
                        <input type="hidden" name="question_username" value="<?=  $v['username']; ?>">
                    </form>
                    <form action="#" id="vote-form" method="POST">
                        <input type="hidden" name="answer_vote" value="vote">
                        <input type="hidden" name="answer_id" value="<?=  $m['id']; ?>">
                        <input type="hidden" name="question_username" value="<?=  $v['username']; ?>">
                    </form>        
    <?php
                        if(isset($_COOKIE["email"])):
                            if($UserLogin && trim($Core->getUsername($_COOKIE["email"])) == $v["username"]): 
                                if(intval($Core->getVoted(trim($m["id"]))) < 1):           
    ?> 
                        <a class="btn btn-default" type="button" id="vote-button" style="float: right;border:none;">
                            <span class="bi bi-star"></span>
                        </a>
    <?php
                                else:
    ?>
                        <a class="btn btn-default" type="button" id="disvote-button" style="float: right;border:none;">
                            <span class="bi bi-star-fill"></span>   
                        </a>              
    <?php
                                endif;
                            endif;
                        endif;
                        if($m["attachment"] != "none"):
                            $answer_attachment_type = strtolower(pathinfo($m["attachment"], PATHINFO_EXTENSION));
                            $answer_attachment_file = substr($m["attachment"],0, strlen($m["attachment"]));

                            
    ?>
                    <center>
    <?php
                        if($answer_attachment_type == "png"):
    ?>
                            <img src="/data/answer_attachment/<?= $answer_attachment_file ?>" alt="Lampiran" class="ui_image_attachment">
                            <p></p>
    <?php
                        endif;
    ?>
                    </center>
    <?php
                        endif;
    ?>
                    <div class="ansbox" id="<?= $m["id"] ?>">
                        <?= $m["answer"]; ?>
                    </div>
                    <a class="readmore-a" style="display: inline-block;">Lanjutkan</a>
                    <div class="dropup">
                        <a class="btn btn-default" type="button" data-toggle="dropdown" style="float: right;border:none;">
                            <span class="bi bi-three-dots-vertical"></span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-right">
                            <li>
    <?php       if(isset($_COOKIE["email"])):
                    if($UserLogin && trim($Core->getUsername($_COOKIE["email"])) == $m["username"]):
    ?>
                                <a id="answer-delete-button"><span class="bi bi-trash-fill" ></span> Hapus Jawaban</a>
                                <a id="answer-edit-button">  <span class="bi bi-pencil-fill" ></span> Edit Jawaban</a>
    <?php
                    endif;
                endif;
    ?>
                                <a onclick="copy('<?= $m['id'] ?>')"><span class="bi bi-clipboard" ></span> Salin Jawaban</a>                          
                            </li>
                        </ul>
                    </div>
    <?php          
                $comment = $Core->getRecentComments($m["id"], 1);
                    if(isset($comment[0])):
                        $img = $Core->getImgByUsername($comment[0]["username"]);
    ?>          
                    <br>
                    <div>
                        <div id="comments" class="card-question mt-3">
                            <img src="<?=$img?>" height="20" width="20" style="border-radius: 100%;"><a href="/comments/<?= $m['id']; ?>" class="text-muted"> <?= $comment[0]["content"]; ?></a>
                        </div>
                    </div>
               
    <?php           
                    else:
    ?>
                    <br>
                    <div>
                        <div id="comments" class="card-question mt-3">
                            <a class="text-muted"  style="margin-bottom: 0px;" href="/comments/<?=$m['id'];?>">Belum ada komentar, klik untuk menambahkan</a>
                        </div>
                    </div>
    <?php
                    endif;

    ?>
                <div style="clear: both"></div>

                </div>

            </div>
    <?php
                endforeach;    
            endif; 
    ?>
        </div><div class="lay-mobile-only list-card-x" id="ui_mobile_only_other_question" style="word-wrap: break-word;border-radius: 6px;">

</div></div>
        
        </div><textarea id="dummy"></textarea>
        <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-6098201334142651"
     crossorigin="anonymous"></script>
<!-- Unit Code. A10B0 -->
<ins class="adsbygoogle"
     style="display:block"
     data-ad-client="ca-pub-6098201334142651"
     data-ad-slot="7867008779"
     data-ad-format="auto"
     data-full-width-responsive="true"></ins>


     <footer class="text-center text-lg-start bg-light text-muted" >
  <!-- Section: Links  -->
  <section class="">
    <div class="container text-center text-md-start mt-5">
      <!-- Grid row -->
      <div class="row mt-3"  id="ui_footer">
        <!-- Grid column -->
        <div class="col-md-3 col-lg-4 col-xl-3 mx-auto mb-4">
          <!-- Content -->
          <h6 class="text-uppercase fw-bold mb-4">
            <i class="fas fa-gem me-3">Cerdasly</i>
          </h6>
          <p>
          Tempat Digital untuk Berbagi dan Belajar Pengetahuan Baru :)
          </p>
        </div>
        <!-- Grid column -->

        <!-- Grid column -->
        <div class="col-md-2 col-lg-2 col-xl-2 mx-auto mb-4">
          <!-- Links -->
          <h6 class="text-uppercase fw-bold mb-4">
            Produk
          </h6>
          <p>
            <a href="#!" class="text-reset">Cerdasly Learn (coming soon)</a>
          </p>
          <p>
            <a href="#!" class="text-reset">Cerdasly Forum</a>
          </p>
          <p>
            <a href="#!" class="text-reset">Cerdasly Fact</a>
          </p>
        </div>
        <!-- Grid column -->

        <!-- Grid column -->
        <div class="col-md-3 col-lg-2 col-xl-2 mx-auto mb-4">
          <!-- Links -->
          <h6 class="text-uppercase fw-bold mb-4">
            Bantuan
          </h6>
          <p>
            <a href="#!" class="text-reset">Ketentuan</a>
          </p>
          <p>
            <a href="#!" class="text-reset">Pengguna</a>
          </p>
          <p>
            <a href="#!" class="text-reset">Laporan</a>
          </p>
          <p>
            <a href="#!" class="text-reset">FAQ</a>
          </p>
        </div>
        <!-- Grid column -->

        <!-- Grid column -->
        <div class="col-md-4 col-lg-3 col-xl-3 mx-auto mb-md-0 mb-4">
          <!-- Links -->
          <h6 class="text-uppercase fw-bold mb-4">Hubungi</h6>
          <p><i class="fas fa-home me-3"></i> Jakarta Pusat, Petamburan 1 10260, Indonesia</p>
          <p>
            <i class="fas fa-envelope me-3"></i>
            contact@cerdasly.com
          </p>
        </div>
        <!-- Grid column -->
      </div>
      <!-- Grid row -->
    </div>
  </section>
  <!-- Section: Links  -->

  <!-- Copyright -->
  <div class="text-center text-white p-4" style="background-color: rgba(220,53,69,0.8);">
    <?= date("Y") ?> Copyright -
    <a class="text-reset fw-bold" href="https://cerdasly.com/">Cerdasly App</a>
  </div>
  <!-- Copyright -->
</footer>

        <script>
            $("#ui_mobile_only_other_question").html($("#ui_other_question").html())
            document.getElementById('attachment_remove').checked = true 
            document.getElementById('ui_attachment').disabled = true 
        $("#dummy").hide()
        function copy(containerid) {
            $("#dummy").show()
            $("#dummy").html($("#"+containerid).text().trim().replaceAll("<br/>", "\n"))
            var content = document.getElementById("dummy");
            content.select();
            document.execCommand('copy');
            $("#dummy").hide()
        }
        $(document).each(function() {
            $('#summernote').summernote({
                placeholder: "Ketik jawabanmu disini",
                tabsize: 2,
                height: 170,
                toolbar: [
                    ['font', ['bold', 'underline', 'italic', 'strikethrough', 'superscript', 'subscript', 'clear']],
                    ['para', ['ul','ol','paragraph']],
                    ['insert', ['picture']],
                ],
                disableResizeEditor: true,
                callbacks: {
                    onPaste: function (e) {
                        var bufferText = ((e.originalEvent || e).clipboardData || window.clipboardData).getData('Text');
                        e.preventDefault();
                        setTimeout(function () {
                            document.execCommand('insertText', false, bufferText);
                        }, 10);
                    }
                }
            });
        });
        init()
        function init(){
            disvote_init()
            vote_init()
            readmore_answer_init()
            readmore_question_init()
            answer_delete_init()
            edit_answer_init()
            $("#kotak-jawab").hide()
            $("#jawab").click(function(){
                console.log(0)
                if($("#kotak-jawab").css('display') == 'none')
                    $("#kotak-jawab").show()
                else $("#kotak-jawab").hide()
            })
        }
        function edit_answer_init(){
            $('a[id^="answer-edit-button"]').each(function(){
                $(this).click(function(){
                    $("#kotak-jawab").show()
                })
            })
        }
        function readmore_answer_init(){
            $(".readmore-a").each(function(){
                    var answer = $(this).parent().find(".ansbox").html()
                    if($(this).parent().find(".ansbox").height() < 300){
                        $(this).hide()
                    }
                    $(this).parent().find(".ansbox").html(
                        clip(answer, 3000, {html: true, maxLines: 20})
                    )
                    $(this).click(function(){
                        $(this).hide()
                        $(this).parent().find(".ansbox").html(answer)
                    })
                }
            )
        }
        function readmore_question_init(){
            $(".readmore-q").each(function(){
                    var answer = $(this).parent().find(".quebox").html()
                    if($(this).parent().find(".quebox").height() < 300){
                        $(this).hide()
                    }
                    $(this).parent().find(".quebox").html(
                        clip(answer, 3000, {html: true, maxLines: 20})
                    )
                    $(this).click(function(){
                        $(this).hide()
                        $(this).parent().find(".quebox").html(answer)
                    })
                }
            )
        }
        function answer_delete_init(){
            $('a[id^="answer-delete-button"]').each(function(){
                $(this).click(function(){
                var deleteform = $(this).parent().parent().parent().parent().find("#answer-delete-form")
                $("#answers").animate({ opacity: 0.5 }, 100);
                $.ajax({
                        url:  $(deleteform).attr("action"),
                        type: $(deleteform).attr("method"),
                        data: $(deleteform).serialize(),
                        success: function(data){
                            $("#answers").animate({ opacity: 1 }, 100);
                            $("#answers").html($(data).closest("#sideright").find("#answers").html())
                            $("#questionansweredcard").html($(data).find("#questionansweredcard").html())
                            $("#errorpopup").html($(data).closest("#errorpopupdummy").html())
                            $("#answerbutton").val($(data).find("#answerbutton").val())
                            init()
                        }.bind({delete: deleteform})
                    });
                })
            })
        }
        function disvote_init(){
            $('a[id^="disvote-button"]').each(function(){
                $(this).click(function(){
                    var voteform =$(this).parent().find("#disvote-form")
                    $(voteform).parent().parent().animate({ opacity: 0.5 }, 100);
                    $.ajax({
                        url: $(voteform).attr("action"),
                        type: $(voteform).attr("method"),
                        data:$(voteform).serialize(),
                        success:function(data){
                            $(voteform).parent().parent().animate({ opacity: 1 }, 100);
                            $("#answers").html($(data).closest("#sideright").find("#answers").html())
                            $("#errorpopup").html($(data).closest("#errorpopupdummy").html())
                        init()
                        }.bind({voteform: voteform})
                    });
                })
            })
        }
        function vote_init(){
            $('a[id^="vote-button"]').each(function(){
                $(this).click(function(){
                    var voteform = $(this).parent().find("#vote-form")
                    $(voteform).parent().parent().animate({ opacity: 0.5 }, 100);
                        $.ajax({
                            url: $(voteform).attr("action"),
                            type: $(voteform).attr("method"),
                            data:$(voteform).serialize(),
                            success:function(data){
                                $(voteform).parent().parent().animate({ opacity: 1 }, 100);
                                $("#answers").html($(data).closest("#sideright").find("#answers").html())
                                $("#errorpopup").html($(data).closest("#errorpopupdummy").html())
                                init()
                            }.bind({voteform: voteform})
                        });    
                })
            })
        }
        $('#ui_question_search').keypress(function (e) {
            var key = e.which;
            if(key == 13){
                $(this).trigger("enterKey");
                window.location = "/search/"+$("#ui_question_search").val()
                return false;  
            }
        });  
        $("#question-delete-form").hide()
        $("#question-delete-button").click(function(){
            $("#question-delete-form").submit()
        })
        $('#soal').keypress(function (e) {
                var key = e.which;
                if(key == 13){
                    window.location = "/search/"+$("#soal").val()
                    return false;  
                }
            });
            d = {}
        function submitanswer(){
            if($("<p>"+$("#summernote").summernote("code")+"</p>").text().length <= 20){
                Popup.fire({
                    text: 'Jawaban anda terlalu pendek karena jawaban setidaknya harus memiliki lebih dari 20 karakter',
                });
            }
            else {
                var answerform = $("#summernote").parent()
                $("#answers").animate({ opacity: 0.5 }, 100);
                var formData = new FormData();
                formData.append('ask', $('#summernote').summernote('code'));
                formData.append('attachment_remove',document.getElementById('attachment_remove').checked ? "1" : "0");
                formData.append('upload_answer_attachment', $('#ui_attachment').get(0).files[0]);
                d = formData
                $.ajax({
                    url:  $(answerform).attr("action"),
                    type: $(answerform).attr("method"),
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(data){
                        d = data
                        $("#answers").animate({ opacity: 1 }, 100);
                        $("#answers").html($(data).closest("#sideright").find("#answers").html())
                        $("#errorpopup").html($(data).closest("#errorpopupdummy").html())
                        $("#questionansweredcard").html($(data).find("#questionansweredcard").html())
                        $("#answerbutton").val($(data).find("#answerbutton").val())
                        init()
                    }.bind({answerform: answerform})
                });    
            };
            
        }
        </script>
        <?php 
            else: 
        ?>
        
            <div class="container">
                <div class="lay-container-smaller alert alert-danger">Tidak ada pertanyaan ditemukan, coba periksa kembali ID pertanyaanmu</div>
            </div>
        <?php endif; ?>
    </body>
</html>