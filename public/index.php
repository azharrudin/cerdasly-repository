<?php 
    require_once(__DIR__."/../php/cerdasly.php");
    require_once(__DIR__."/../php/functions.php");
    require_once(__DIR__."/components/navbar.php");
    //--------------------------------------
    $core           = new Core();
    $islogin        = false;
    //--------------------------------------
    if((isset($_COOKIE["email"]) && $_COOKIE["pass"]) && ($core->login($_COOKIE["email"], $_COOKIE["pass"])) != false){ 
        $islogin        = true;
        $user = $core->getUsername($_COOKIE["email"]);
    }
    if(($core->login($_COOKIE["email"], $_COOKIE["pass"])) == false){
        header("Location: /login");
    }
    //--------------------------------------
    if(isset($_GET["limit"])):
        $limit = $_GET["limit"];
    else:
        $limit = 10;
    endif;

?>
<head>
    <title>Cerdasly - Beranda</title>
    <!--- favicon dan thumbnail website --->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="apple-touch-icon" sizes="180x180" href="/favicon/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon/favicon-16x16.png">
    <link rel="manifest" href="/favicon/site.webmanifest">
    <link rel="mask-icon" href="/favicon/safari-pinned-tab.svg" color="#5bbad4">
    <meta name="msapplication-TileColor" content="#603cba">
    <meta name="theme-color" content="#ffffff">
    <!--- library dan framework yang dibutuhkan --->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="/styles/styles.css" rel="stylesheet">
    <link href="/styles/components.css" rel="stylesheet">
    <link href="/styles/ui.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-0evHe/X+R7YkIZDRvuzKMRqM+OrBnVFBL6DOitfPri4tjfHxaWutUpFmBp4vmVor" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.css" rel="stylesheet">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/croppie/2.6.5/croppie.css" integrity="sha512-2eMmukTZtvwlfQoG8ztapwAH5fXaQBzaMqdljLopRSA0i6YKM8kBAOrSSykxu9NN9HrtD45lIqfONLII2AFL/Q==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/croppie/2.6.5/croppie.js" integrity="sha512-vUJTqeDCu0MKkOhuI83/MEX5HSNPW+Lw46BA775bAWIp1Zwgz3qggia/t2EnSGB9GoS2Ln6npDmbJTdNhHy1Yw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.js"></script>
    <script src='https://unpkg.com/tesseract.js@v2.1.0/dist/tesseract.min.js'></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="/js/clipper.js"></script>
    <script>
    const Popup = Swal.mixin({
        toast: true,
        position: 'top',
        showConfirmButton: false,
        timer: 3200
    });
    </script>   
    <style>
        @media (max-width: 460px) and (min-width: 200px){
            .search-question-box {
                margin-top: 5%;
            }
        }
        a:hover {
            text-decoration:none;
        }
        .ui_name_label {
                margin-left: 5px;
            }
    </style>
</head>
<?php
    $list = $core->getRecentQuestion("0", $limit);
    if(isset($_GET["search"]))
        $list = $core->searchQuestion(buildregex($_GET["search"]), "0", $limit);
?>
<body>
    <template id="ui_ocr">
        <p class="text-muted">Potong gambar sejelas mungkin, semakin jelas semakin baik, jangan upload gambar besar (eg. satu halaman)</p>
        <button id="ui_ocr_scan_button" class="btn btn-primary mb-3">scan</button>
        <div>
            <img id="ui_ocr_preview" style="max-width: 20vh;max-width: 30vw"> 
        </div>
        
    </template>
<?= navigationBar($user); ?>
<div class="body">
    <div class="side-left mt-3">
        <div style="position: fixed;z-index: 999;width: 17%">  
            <div class="homepage-left-sidebar">
                <span class="bi bi-newspaper"></span>
                <a href="/login/" class="text-muted sidebar-list">Baca blog kita disini</a><br>
                <span class="bi bi-megaphone"></span>
                <a href="/login/" class="text-muted sidebar-list">Ingin beriklan? baca cara beriklan disini</a><br>
                <span class="bi bi-book"></span>
                <a href="/login/" class="text-muted sidebar-list">Seorang pelajar? baca tentang tata tertib disini</a><br>
                <span class="bi bi-book"></span>
                <a href="/login/" class="text-muted sidebar-list">Seorang guru? baca tentang panduan untuk guru disini</a><br>
            </div>
            <div class="mt-1" style="border: 1px solid rgb(230, 230, 230);padding: 10px;background: white;">
                <p class="text-muted text-center h5">Top 5 User</p>
                <?php 
                    $bss = $core->getBestUser();
                    foreach($bss as $b){
                        $img = $core->getImgByUsername($b["username"]);
                        echo "<div style='display: flex;list-style-type: none;'>
                                <img style='float: left;' class='userimg' src='$img'/>&nbsp; <li style='max-width: 100%;'><span class='h5'>".$b["realname"]."</span><br><a class='text-muted' href='/profile/".$b["username"]."' style='float: right;'>@".$b["username"]."</a></div></li>";
                    } 
                ?>
            </div>
        </div>
    </div>
    <div class="side-right">
        <div>  
        <div class="card-question desktop-content-container" style="border-radius: 5px;z-index: 999;" id="searchbox">
                <div class="form-group mb-0">
                    <div class="input-group cps-input-group">
                        <span  onclick='$("#ocr_upload_form").click()' class="input-group-addon"><span class="bi bi-camera-fill"></span></span>
                        <input class="form-control" placeholder="Tugas anda atau pertanyaan anda" style="border-left: none;" id="ui_search_question">
                    </div> 
                   <input type="file" id="ocr_upload_form" style="display: none;" onclick="this.value = null" onchange="preview_image(event)">
                </div>
            </div>
            <div id="ctn">
    <?php
                foreach($list as $l):
                    $topAnswer = $core->getBestAnswer($l["id"]);
    ?>
            <div class="card-question desktop-content-container" style="overflow: hidden;">
                <a class="link-danger" style="text-decoration: none;" href="/profile/<?= $l['username']; ?>">
                    <span class="<?= categorytoicon($l['category']) ?>"> <?= $l["category"] ?><span>
                </a>
                <p class="text-muted"><?= getndate($l["postdate"]); ?></p>
        <?php
                if($l["attachment"] != "none"):
        ?>            
                    <center>
                    <img src="/data/question_attachment/<?= $l["attachment"] ?>" alt="Lampiran" class="ui_image_attachment" loading="lazy">
                            <p style="clear: both;"></p>
                    </center>
        <?php
                    endif;
        ?>
                <div class="ui_homepage_question_container">
                    <a href='/question/<?= $l["id"]; ?>' style="color: black; text-decoration: none;" class="question-title">
                        <?= preg_replace("/<br\W*?\/>/"," ", strip_tags($l["title"], "<br>")); ?>
                    </a>
    <?php 
                        if(isset($topAnswer["answer"])):
    ?>
                    <div class="card-question">
                        <div class="ui_circular_wrapper">
                            <div class="ui_circular_image-x30">
                                <img onclick="window.location = '/profile/<?= $topAnswer['username'] ?>'" src="<?= $core->getImgByUsername($topAnswer['username']); ?>" class="ui_circled_image-x30" loading="lazy">
                            </div>
                            <span class="text-muted" onclick="window.location = '/profile/<?= $topAnswer['username'] ?>'">
                            <?= $core->getRealnameByUsername($topAnswer["username"]); ?> &#183 
                            <a class="text-muted"><?= getndate($topAnswer["postdate"]); ?></a>
                            </span>
                        </div>
                     
                        <form action="#" id="question-delete-form" method="POST">
                            <input type="hidden" name="question_delete" value="delete">
                        </form>
                        <div class="quebox" style="word-wrap: break-word;" id="topanswer">
                            <?= preg_replace("/<br\W*?\/>/"," ", strip_tags( @$topAnswer["answer"], "<br>")); ?>
                        </div>
                    </div>
    <?php 
                        endif; 
    ?>
                </div>
                <br>
                <div class="ui_circular_wrapper">
                        <div class="ui_circular_image-x35">
                            <img onclick="window.location = '/profile/<?= $l['username'] ?>'" src="<?= $core->getImgByUsername($l['username']); ?>" class="ui_circled_image-x35" loading="lazy">
                        </div>
                    <span class="text-muted ui_name_label"><?= $l["username"]; ?></span>

                    <input type="submit" class="btn btn-danger" value="Jawab" name="answer" style="margin-left: auto;" onclick="location.href='/question/<?= $l["id"]; ?>'">
                </div>
               
            </div>
    <?php 
            endforeach; 
    ?>
        </div>
                <div class="desktop-content-container">
                    <center>
                        <a type="button" class="btn btn-danger sm mt-3 mb-3" id="homepage_loadmore" style="width: 80%;border-radius: 20px;">Selanjutnya</a>
                    </center>
                </div>
        </div>
    </div>    
</div>
</div>
<script>
    $('#ui_search_question').keypress(function (e) {
        var key = e.which;
        if(key == 13){
            $(this).trigger("enterKey");
            window.location = "/search/"+$("#ui_search_question").val()
            return false;  
        }
    });  
    function show_ocr(result){
        Swal.fire({
            html: $("#ui_ocr").html(),
            showConfirmButton: false
        })
        var p = $("#ui_ocr_preview").croppie({
            viewport: { width: 100, height: 100 },
            boundary: { width: 250, height: 250 },
            showZoomer: true,
            enableOrientation: true,
            enableResize: true,
            mouseWheelZoom: 'ctrl'
        })
        p.croppie('bind', {
            url: result,
        })
        $("#ui_ocr_scan_button").click(function(){
           
            p.croppie('result','blob').then(function(blob) {
                Swal.fire({
                html: "<h5>Sabar cok, sedang memindai...</h5><h5><b>Semakin panjang soalnya semakin lama prosesnya</b></h5>",
                showConfirmButton: false
            })
                Tesseract.recognize(
                    blob,
                    'eng',
                )
                .then(({ data: { text } }) => {
                    Swal.close()
                    console.log(0)
                    if(text.length < 5){
                        Swal.fire({
                            html: "<h5>OCR tidak dapat memindai tulisan atau soal kamu, coba gunakan gambar yang lebih kecil dan jelas</h5>",
                            confirmButtonText: 'OK',
                            confirmButtonColor: "#D9534F",
                        })
                    }
                    else {
                        Swal.fire({
                            html: "<h5><b>Ingin mencari: </b>"+text+"?</h5>",
                            confirmButtonText: 'Cari Sekarang',
                            confirmButtonColor: "#D9534F",
                        }).then(function(result){
                            location.href = "/search/"+ encodeURIComponent (text) 
                        }.bind({text: text}))
                    }
                })
            })
        })
    }
    function preview_image(event) {
        var reader = new FileReader();
        reader.onload = function(){
            show_ocr(reader.result)
        }
        console.log(event.target.files[0])
        reader.readAsDataURL(event.target.files[0]);
        URL.revokeObjectURL()
    }
    $('div[id^="topanswer"]').each(function(){
        $(this).text(clip($(this).text(), 300))
    })
    var a = 20
    $("#homepage_loadmore").click(function(){
        $("#homepage_loadmore").html("Memuat...")
        $.ajax({
            url: "<?= isset($_GET['search']) ? '/search/'.htmlspecialchars($_GET['search']):''; ?>/limit/"+(a),
            type: "GET",
            success: function(data){
                $("#homepage_loadmore").html("Selanjutnya")
                a += 10
                $("#ctn").html($(data).find("#ctn").html())
            },
            complete: function(){
                $("#homepage_loadmore").html("Selanjutnya")
            },
            error: function(){
                $("#homepage_loadmore").html("Selanjutnya")
            }

        })
    }) 
    $(".question-title").each(function(){
        $(this).text(clip($(this).text(), 150,  {html: true}))
    })
</script>
