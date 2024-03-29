<?php
require_once(__DIR__ . "/../php/cerdasly.php");
require_once(__DIR__ . "/../php/functions.php");
require_once(__DIR__ . "/components/navbar.php");
require_once(__DIR__ . "/components/favicon.php");
//--------------------------------------
$core           = new Core();
$islogin        = false;
//--------------------------------------
if ((isset($_COOKIE["email"]) && $_COOKIE["pass"]) && ($core->login($_COOKIE["email"], $_COOKIE["pass"])) != false) {
    $islogin        = true;
    $user = $core->getUsername($_COOKIE["email"]);
}
//--------------------------------------
if (isset($_GET["limit"])) :
    $limit = $_GET["limit"];
else :
    $limit = 10;
endif;

?>

<head>
    <title>Cerdasly - Beranda</title>
    <!--- favicon dan thumbnail website --->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Tanya dan lihat pertanyaan dan tugas sekolah terbaru dari para pengguna Cerdasly disini">
    <?= faviconImg(); ?>
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://cerdasly.com">
    <meta property="og:title" content="Cerdasly Beranda">
    <meta property="og:description" content="Tanya dan lihat pertanyaan dan tugas sekolah terbaru dari para pengguna Cerdasly disini">
    <meta property="og:image" content="/icon/favicon-32x32.png">
    <!--- library dan framework yang dibutuhkan --->
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
    <link rel="stylesheet" href="https://maxst.icons8.com/vue-static/landings/line-awesome/line-awesome/1.3.0/css/line-awesome.min.css">
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
    <script src="/js/ui.js"></script>
    <style>
        @media (max-width: 460px) and (min-width: 200px) {
            .search-question-box {
                margin-top: 5%;
            }
        }

        a:hover {
            text-decoration: none;
        }

        .ui_name_label {
            margin-left: 5px;
        }

        .ui_ocr_preview {
            max-width: 30vw;
        }
        .ui_side_left {
            position: fixed;
            ;z-index: 999;
            width: 17%;
        }
    </style>
</head>
<?php
$list = $core->getRecentQuestion("0", $limit);
if (isset($_GET["search"]))
    $list = $core->searchQuestion(buildregex($_GET["search"]), "0", $limit);
?>

<body>
    <template id="ui_ocr">
        <p class="text-muted">Potong gambar sejelas mungkin, semakin jelas semakin baik, jangan upload gambar besar (eg. satu halaman)</p>
        <button id="ui_ocr_scan_button" class="btn btn-primary mb-3">scan</button>
        <div>
            <img id="ui_ocr_preview" class="ui_ocr_preview">
        </div>
    </template>
    <?= navigationBar(isset($user) ? $user : ""); ?>
    <div class="body">
        <div class="side-left mt-3">
            <div id="ui_side_left" class="ui_side_Left">
                <div class="homepage-left-sidebar shadow">
                    <div id="ui_userinfo">

                        <?php
                        if (!$islogin) :
                        ?>
                            <div>
                                <a class="text text-primary mb-1 w-100" href="/login">
                                    Klik disini untuk masuk
                                </a>
                            </div>
                        <?php
                        else :
                            $total_voted_answer   = $core->m->query("SELECT count(*) AS names FROM answers WHERE `username` LIKE " . $core->m->quote($user) . " AND `votes`>0")->fetch()["names"];
                            $total_question = $core->m->query("SELECT count(*) AS names FROM questions WHERE `username` LIKE " . $core->m->quote($user))->fetch()["names"];
                            $total_answer   = $core->getAnswerCountByUsername($user)[0]["total"];
                            $total_voted_answer = intval($total_voted_answer) != 0 ? $total_voted_answer : 1;
                            $rank = getranks($total_voted_answer, $total_answer, $total_question);
                            $ttl  = (intval($total_answer) + intval($total_question)) * intval($total_voted_answer);
                        ?>
                            <div class="ui_circular_wrapper mb-1">
                                <div class="ui_circular_image-x30">
                                    <img onclick="window.location = '/profile/<?= $user ?>'" src="<?= $core->getImgByUsername($user); ?>" class="ui_circled_image-x30" loading="lazy" alt="foto profil">
                                </div>
                                <span class="text-muted" onclick="window.location = '/profile/<?= $user ?>'">
                                    <?= $core->getRealnameByUsername($user); ?> (<?= $rank["category"] . "-" . $rank["star"] ?>)
                                </span>
                            </div>

                            <span class="text-muted"><i class="las la-pen" style="font-size: 20px;color: red;"></i> <?= $total_answer ?> Jawaban</span><br>
                            <span class="text-muted"><i class="las la-comment" style="font-size: 20px;color: blue;"></i> <?= $core->m->query("SELECT count(*) AS names FROM comments WHERE `username` LIKE " . $core->m->quote($user))->fetch()["names"] ?> Komentar</span><br>
                            <span class="text-muted "><i class="lar la-question-circle" style="font-size: 20px;color: green;"></i> <?= $total_question ?> Pertanyaan</span>
                            <br><br>
                            <div style="display: flex;"><b class="ml-2 p-2 text-dark" style="background-color:moccasin;border-radius: 10px;"><?= strtoupper($rank["category"]) ?> <?= $rank["star"] ?></b>
                                <span class="p-2"><?= $ttl ?> points</span>
                            </div>

                        <?php
                        endif;

                        ?>
                    </div>
                </div>
                <div class="homepage-left-sidebar mt-1 shadow" style="height: 250px;overflow-y: scroll;">
                    <center><h5>Mata pelajaran</h5></center>
                   <?php
                        foreach($categories as $val):
                            $n = categorytoicon8($val);
                   ?>
                    <div  class="card-question mt-3" style="width: 100%"><i style="color: <?= $n[1]?>" class="<?= $n[0] ?>"></i><?= $val ?></div>
                   <?php endforeach;  ?>
                </div>
                <div class="mt-1 shadow" style="border: 1px solid rgb(230, 230, 230);padding: 10px;background: white;border-radius: 6px;" id="ui_ranklist">
                    <p class="text-muted text-center h5">Top 5 User</p>
                    <?php
                    $bss = $core->getBestUser();
                    foreach ($bss as $b) {
                        $img = $core->getImgByUsername($b["username"]);
                        echo "<div style='display: flex;list-style-type: none;'>
                                <img style='float: left;' class='userimg' src='$img' alt='foto profil'/>&nbsp; <li style='max-width: 100%;'><span class='h5'>" . $b["realname"] . "</span><br><a class='text-muted' href='/profile/" . $b["username"] . "' style='float: right;'>@" . $b["username"] . "</a></div></li>";
                    }
                    ?>
                </div>
            </div>
        </div>
        <div class="side-right">
            <div>
                <div class="desktop-content-container lay-mobile-only homepage-left-sidebar shadow" id="ui_userinfo_mobile" style="margin-top: 45px;">

                </div>
                <div class="card-question desktop-content-container shadow" style="border-radius: 5px;z-index: 999;" id="searchbox">
                    <div class="form-group mb-0">
                        <div class="input-group cps-input-group">
                            <span onclick='$("#ocr_upload_form").click()' class="input-group-addon"><span class="bi bi-camera-fill"></span></span>
                            <input class="form-control" placeholder="Tugas anda atau pertanyaan anda" style="border-left: none;" id="ui_search_question">
                        </div>
                        <input type="file" id="ocr_upload_form" style="display: none;" onclick="this.value = null" onchange="preview_image(event)">
                    </div>
                </div>
                <div id="ctn">
                    <?php
                    foreach ($list as $l) :
                        $topAnswer = $core->getBestAnswer($l["id"]);
                        $n = categorytoicon8($l["category"]);
                    ?>
                        <div class="card-question desktop-content-container" style="overflow: hidden;">
                            <a style="text-decoration: none;" href="/profile/<?= $l['username']; ?>">
                                <span><h4 style="color: <?= $n[1] ?>" class="<?= $n[0] ?>">&nbsp;</h4><span class="p-2" style="background-color: darksalmon;color: white;border-radius: 10px"><?= $l["category"] ?></span></span>
                            </a>
                            <p class="text-muted"><?= getndate($l["postdate"]); ?></p>
                            <?php
                            if ($l["attachment"] != "none") :
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
                                    <?= preg_replace("/<br\W*?\/>/", " ", strip_tags($l["title"], "<br>")); ?>
                                </a>
                                <?php
                                if (isset($topAnswer["answer"])) :
                                ?>
                                    <div class="card-question">
                                        <div class="ui_circular_wrapper">
                                            <div class="ui_circular_image-x30">
                                                <img onclick="window.location = '/profile/<?= $topAnswer['username'] ?>'" src="<?= $core->getImgByUsername($topAnswer['username']); ?>" class="ui_circled_image-x30" loading="lazy" alt="Foto Profil">
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
                                            <?= preg_replace("/<br\W*?\/>/", " ", strip_tags(@$topAnswer["answer"], "<br>")); ?>
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
    <!-- Footer -->
  
        <?php require_once(__DIR__."/components/footer.php"); ?>
    <!-- Footer -->
    <script>
        $("#ui_userinfo_mobile").html($("#ui_userinfo").html())
        $('#ui_search_question').keypress(function(e) {
            var key = e.which;
            if (key == 13) {
                $(this).trigger("enterKey");
                window.location = "/search/" + $("#ui_search_question").val()
                return false;
            }
        });
        var windw = this;
        $.fn.followTo = function(elem, elemfrom) {
            var $this = this,
                $window = $(windw),
                $bumper = $(elem),
                initalTop = $this.css("top")
            setPosition = function() {
                var bumperPos = $(elem).offset().top
                thisHeight = $window.outerHeight()
                if ($window.scrollTop() > (bumperPos - thisHeight)) {
                    $this.css({
                        position: 'absolute',
                        top: (bumperPos - thisHeight),
                        "z-index": 0
                    });


                } else {

                    $this.css({
                        position: 'fixed',
                        top: initalTop
                    });

                }
            }.bind({
                elem: elem,
                $window: $window
            });
            $window.resize(function() {
                if (pos == undefined) return
                bumperPos = pos.offset().top;
                thisHeight = $this.outerHeight();
                setPosition();
            });
            $window.scroll(setPosition);
            setPosition();
        };
        $("#ui_side_left").followTo("#ui_footer", $("#ui_left_side"))

        function show_ocr(result) {
            Swal.fire({
                html: $("#ui_ocr").html(),
                showConfirmButton: false
            })
            var p = $("#ui_ocr_preview").croppie({
                viewport: {
                    width: 100,
                    height: 100
                },
                boundary: {
                    width: 250,
                    height: 250
                },
                showZoomer: true,
                enableOrientation: true,
                enableResize: true,
                mouseWheelZoom: 'ctrl'
            })
            p.croppie('bind', {
                url: result,
            })
            $("#ui_ocr_scan_button").click(function() {

                p.croppie('result', 'blob').then(function(blob) {
                    Swal.fire({
                        html: "<h5>Sabar cok, sedang memindai...</h5><h5><b>Semakin panjang soalnya semakin lama prosesnya</b></h5>",
                        showConfirmButton: false
                    })
                    Tesseract.recognize(
                            blob,
                            'eng',
                        )
                        .then(({
                            data: {
                                text
                            }
                        }) => {
                            Swal.close()
                            console.log(0)
                            if (text.length < 5) {
                                Swal.fire({
                                    html: "<h5>OCR tidak dapat memindai tulisan atau soal kamu, coba gunakan gambar yang lebih kecil dan jelas</h5>",
                                    confirmButtonText: 'OK',
                                    confirmButtonColor: "#D9534F",
                                })
                            } else {
                                Swal.fire({
                                    html: "<h5><b>Ingin mencari: </b>" + text + "?</h5>",
                                    confirmButtonText: 'Cari Sekarang',
                                    confirmButtonColor: "#D9534F",
                                }).then(function(result) {
                                    location.href = "/search/" + encodeURIComponent(text)
                                }.bind({
                                    text: text
                                }))
                            }
                        })
                })
            })
        }

        function preview_image(event) {
            var reader = new FileReader();
            reader.onload = function() {
                show_ocr(reader.result)
            }
            console.log(event.target.files[0])
            reader.readAsDataURL(event.target.files[0]);
            URL.revokeObjectURL()
        }
        $('div[id^="topanswer"]').each(function() {
            $(this).text(clip($(this).text(), 300))
        })
        var a = 20
        $("#homepage_loadmore").click(function() {
            $("#homepage_loadmore").html("Memuat...")
            $.ajax({
                url: "<?= isset($_GET['search']) ? '/search/' . htmlspecialchars($_GET['search']) : ''; ?>/limit/" + (a),
                type: "GET",
                success: function(data) {
                    $("#homepage_loadmore").html("Selanjutnya")
                    a += 10
                    $("#ctn").html($(data).find("#ctn").html())
                },
                complete: function() {
                    $("#homepage_loadmore").html("Selanjutnya")
                },
                error: function() {
                    $("#homepage_loadmore").html("Selanjutnya")
                }

            })
        })
        $(".question-title").each(function() {
            $(this).text(clip($(this).text(), 150, {
                html: true
            }))
        })
    </script>