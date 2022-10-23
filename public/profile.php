<?php 
require_once(__DIR__."/../php/cerdasly.php");
require_once(__DIR__."/../php/functions.php");
require_once(__DIR__."/components/navbar.php");
//------------------------------------------------------------------
$core     = new Core();
$notlogin = false;
$user = "";
if(isset($_GET["logout"])){
    setcookie("email", "", time()-10, "/");
    setcookie("pass", "", time()-10, "/");
    header("Location: /");
}
if(strlen($core->getImgByUsername($_GET["user"])) < 1 ){
    http_response_code(404);
    $notlogin = '
    <div style="height: 100%;margin-top: 1vh">
        <div class="container alert alert-danger" style="margin-bottom: 0px;text-align: center;">
            <p>Akun tidak ditemukan</p>
        </div>
    </div>';
}
else {
    $user_realname  = $core->getRealnameByUsername($_GET["user"]); 
    $user_about     = $core->getAboutByUsername($_GET["user"]);
}
if(strlen($core->getImgByUsername($_GET["user"])) < 1 && isset($_COOKIE["email"]) && !isset($_POST["request_check_only"]) && strlen($_GET["user"]) < 1){
    header("Location: /profile/".$core->getUsername($_COOKIE["email"]));
}
//------------------------------------------------------------------
?>
<!doctype html>
<html>
    <head>
        <title>Profil <?=  @$user_realname ? $user_realname : "- Tidak Ditemukan"?></title>
        <!--- favicon dan thumbnail website --->
        <meta content="<?= $user_about; ?>" name="description"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="apple-touch-icon" sizes="180x180" href="/favicon/apple-touch-icon.png">
        <link rel="icon" type="image/png" sizes="32x32" href="/favicon/favicon-32x32.png">
        <link rel="icon" type="image/png" sizes="16x16" href="/favicon/favicon-16x16.png">
        <link rel="manifest" href="/favicon/site.webmanifest">
        <link rel="mask-icon" href="/favicon/safari-pinned-tab.svg" color="#5bbad4">
        <meta name="msapplication-TileColor" content="#603cba">
        <meta name="theme-color" content="#ffffff">
        <!--- library dan framework yang dibutuhkan --->
        <link href="/styles/styles.css" rel="stylesheet">
        <link href="/styles/ui.css" rel="stylesheet">
        <link href="/styles/components.css" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.3/font/bootstrap-icons.css" rel="stylesheet">
        <link href="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css" rel="stylesheet">
        <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
        <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    </head>
</html>
<body>
    <?php 
        //--------------------------------
        // Proses setup kode PHP di page ini
        //--------------------------------
        $islogin = false;
        $currentuserimg = '
        <a href="/login" type="button" class="btn btn-primary" style="float: right;margin-right: 5px;">Login</a>
        ';
        //--------------------------------
        // Proses validasi telah login atau belum
        //--------------------------------
        if((isset($_COOKIE["email"]) && $_COOKIE["pass"]) && ($core->login($_COOKIE["email"], $_COOKIE["pass"])) != false){
            $user = $core->getUsername($_COOKIE['email']);
            $islogin        = true;
            $currentuserimg = "<img onclick='window.location = \"/profile/\"' src='".$core->getImg($_COOKIE['email'])."' height=35 style='border-radius: 100%;max-width: 35px;'>";
        }
        //--------------------------------
        // Proses mengganti nama asli pengguna
        // Dengan mengganti nilai `realname` di database
        //--------------------------------
        if(isset($_POST["realname"]) &&  $core->getUsername($_COOKIE["email"]) == trim($_GET["user"]) && $islogin){ 
            if((strlen($_POST["realname"]) > 20 || strlen($_POST["realname"]) < 4) ){
                echo "<script>Swal.fire({
                    icon: 'error',
                    text: 'Nama anda tidak valid',
                    });</script>";
            }
            else if(!preg_match("/^[0-9A-Za-z\s\_]+$/", $_POST["realname"])){
                echo "<script>Swal.fire({
                    icon: 'error',
                    text: 'Nama anda tidak valid',
                    });</script>";
            }
            else {
                $core->changeRealname($core->getRealnameByEmail($_COOKIE["email"]),$_POST["realname"]);
            }
        }
        if(isset($_POST["about"]) &&  $core->getUsername($_COOKIE["email"]) == trim($_GET["user"]) && $islogin){ 
            if((strlen($_POST["about"]) > 200 || strlen($_POST["about"]) < 20) ){
                echo "<script>Swal.fire({
                    icon: 'error',
                    text: 'Nama anda tidak valid',
                    });</script>";
            }
            else {
                $core->updateAboutByUsername($core->getUsername($_COOKIE["email"]), strip_tags($_POST["about"]));
            }
        }
        if(isset($_FILES["imgfile"])){
            $target_dir  = $userimgdir;
            $target_file = $target_dir . $core->getImgCode($_COOKIE["email"]);
            $uploadOk = 1;
            $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
            if(isset($_FILES["imgfile"])) {
            $check = getimagesize($_FILES["imgfile"]["tmp_name"]);
                if($check !== false) {
                    $uploadOk = 1;
                } else {
                    echo "<script>Swal.fire({
                        icon: 'error',
                        text: 'File gambar anda tidak valid',
                        });</script>";
                    $uploadOk = 0;
                }
            }
            if( $_FILES["imgfile"]['type']!= 'image/jpeg' && $_FILES["imgfile"]['type']!= 'image/png')
                $uploadOk = 0;

            if ($_FILES["imgfile"]["size"] > CRDSLY_IMG_PROFILE_MAX) {
                echo <<<EOF
                    <script>
                        Swal.fire({
                            icon: 'error',
                            text: 'File gambar anda terlalu besar (maks: 5mb)',
                        });
                    </script>";  
                EOF;
                $uploadOk = 0;
            }
            if($uploadOk){
                if (move_uploaded_file($_FILES["imgfile"]["tmp_name"], $target_file)){
                    echo "<script>window.location = window.location</script>";
                }
            } else {
                echo "<script>Swal.fire({
                    icon: 'error',
                    text: 'File gambar anda tidak valid',
                    });</script>";
            }
        }
    ?>
<!-- BAGIAN BAR NAVIGASI -->
<?= navigationBar($user, true) ?>
    <?php 
        if(gettype($notlogin) != "boolean")
            die($notlogin);
    ?>
    <div id="dm-accdel">

    </div>
    <form method="POST" action="#" enctypnave="multipart/form-data" id="imgupload">
        <input type="file" id="imgfile" name="imgfile" accept="image/*" style="display: none;" onchange="submit_()"> 
    </form>
    <div>
        <center class="card-question lay-container-smaller"  style="overflow: auto;">
            <div>
                <div class="cps-img-container ui_circular_image-x75" id="imgprofile">
                    <img
                        src="<?= $core->getImgByUsername($_GET["user"]); ?>"
                        height="100"
                        alt="<?= strip_tags($_GET['user']); ?>"
                        loading="lazy" 
                        style="border-radius: 50%;max-width: 100px;"
                    />
    <?php               
                    if($islogin && $user == $_GET["user"]):
                        if(isset($_POST["deletepass"])){
                            $c = $core->deleteAccount($core->getUsername($_COOKIE["email"]), $_COOKIE["email"], trim($_POST["deletepass"]));
                            if(isset($_SERVER['HTTP_COOKIE']) && $c) {
                                $cookies = explode(';', $_SERVER['HTTP_COOKIE']);
                                foreach($cookies as $cookie) {
                                    $parts = explode('=', $cookie);
                                    $name  = trim($parts[0]);
                                    setcookie($name, '', time()-1000);
                                    setcookie($name, '', time()-1000, '/');
                                }
                                die("<div id='dm-accdel'><script>window.location.replace('/');</script></div>");
                            }
                        }
    ?>
                    <div class="cps-overlay">
                        <span class="bi bi-camera h3" onclick="$('#imgfile').click()" ></span>
                    </div>
    <?php           
                    endif; 
    ?>
                </div>
            </div>
            <div>

                <h3 id="realname" style="margin-bottom: 0px;margin-top: 2px;">
                    <?= $core->getRealnameByUsername($_GET["user"]); ?>
                </h3>
                <p class="form-text text-muted" style="margin-bottom:0px;" id="about"> <?= $core->getAboutByUsername($_GET["user"]) ?></p>
            </div>
            <?php
                if($islogin && $user == $_GET['user']):
            ?>
            <div class="dropup">
                <a class="btn btn-default" type="button" data-toggle="dropdown" style="float: right;border: none;">
                    <span class="bi bi-three-dots-vertical"></span>
                    </a>
                <ul class="dropdown-menu dropdown-menu-right">
                    <li><a class="bi bi-pencil-square" onclick="editprofiledialog()"> Edit Profil</a></li>
                    <li><a class="bi bi-gear" onclick="settingprofiledialog()"> Pengaturan</a></li>
                </ul>
            </div>
            <?php
                else:
            ?>
             <div class="dropup">
                <a class="btn btn-default" type="button" data-toggle="dropdown" style="float: right;border: none;">
                    <span class="bi bi-three-dots-vertical"></span>
                    </a>
                <ul class="dropdown-menu dropdown-menu-right">
                    <li><a class="bi bi-megaphone" onclick="settingprofiledialog()"> Laporkan</a></li>
                </ul>
            </div>
            <?php
                endif;
                if($islogin && $user == $_GET['user']):
            ?>
            <div id="editinfo" style="display: none;">
                <div class="form-group" id="group-chg-realname">
                    <label style="text-align:left;width: 100%;">Nama Anda</label>
                    <div class="input-group cps-input-group">
                        <span class="input-group-addon"><span class="bi bi-person-circle"></span></span>
                        <input  type="email" 
                                class="form-control" 
                                id="chg-realname" 
                                placeholder="Ganti Nama Anda" 
                                name="email" 
                                value="<?= $core->getRealnameByUsername($_GET["user"]);?>"
                                style="border-left: none;"
                        />
                    </div> 
                    <p class="form-text text-muted" style="text-align:left;width: 100%;" id="chg-realname-sub">Nama minimal 4 karakter dan maksimal 20 Karakter. </p>
                </div>
                <div class="form-group" id="group-chg-about">
                    <label style="text-align:left;width: 100%;">Tentang Anda:</label>
                    <textarea class="form-control" placeholder="Tentang anda" name="chg-about" aria-readonly="true" style="min-height: 100px;" id="chg-about"> <?= $core->getAboutByUsername($_GET["user"]) ?></textarea>
                    <p class="form-text text-muted" style="text-align:left;width: 100%;" id="chg-about-sub">Kolom tentang minimal 10 karakter dan maksimal 200 Karakter. </sp>
                </div>
                <div type="submit" class="btn btn-danger" style="width: 100%;" onclick="changeuserinfo(this)">
                    Simpan
                </div><br>
            </div>   
            <div id="setting" style="display: none;">
                <form>
                    <div class="form-group">
                        <label style="text-align:left;width: 100%;">Ganti Email</label>
                        <div class="input-group cps-input-group">
                            <span class="input-group-addon"><span class="bi bi-person-circle"></span></span>
                            <input  type="email" 
                                    class="form-control" 
                                    id="email" 
                                    placeholder="Ganti Email Anda" 
                                    name="email" 
                                    style="border-left: none;"
                                    value="<?= $core->getEmailByUsername($_GET['user']) ?>"
                            />
                        </div> 
                        <p class="form-text text-muted"  style="text-align:left;width: 100%;">gunakan hanya email yang aktif</p>
                    </div>
                    <div class="form-group">
                        <label style="text-align:left;width: 100%;">Password Baru</label>
                        <div class="input-group cps-input-group">
                            <span class="input-group-addon"><span class="bi bi-shield-lock"></span></span>
                            <input type="password" class="form-control" id="email" placeholder="Password" name="email" style="border-left: none;">
                        </div> 
                        <p class="form-text text-muted"  style="text-align:left;width: 100%;">password terdiri dari minimal 4 huruf dan maksimal 20 huruf</p>
                    </div>
                    <div class="form-group">
                        <label style="text-align:left;width: 100%;">Password anda saat ini</label>
                        <div class="input-group cps-input-group">
                            <span class="input-group-addon"><span class="bi bi-key"></span></span>
                            <input  class="form-control" id="email" placeholder="Password" name="email" style="border-left: none;"  required/>
                        </div> 
                        <p class="form-text link-primary"  style="text-align:left;width: 100%;">diperlukan untuk mengubah pengaturan anda</p>
                    </div>
                    <button type="submit" class="btn btn-danger" style="width: 100%;">Simpan</button><br>
                    <a class="link-primary" style="margin-top: 8px;" href="/logout">Keluar dari akun ini</a><br>
                    <a class="link-primary" style="margin-top: 5px;" onclick="deleteaccountdialog()">Saya ingin menghapus akun</a>
                </form>
            </div>   
            <div id="delete" style="display: none;">
                <form>
                    <div class="form-group">
                        <p class="form-text text-muted"  style="text-align:left;width: 100%;"><u>Akun anda akan dihapus selamanya, dan tidak bisa diulang</u>. Masukan password anda untuk melanjutkan</p>
                        <label style="text-align:left;width: 100%;">Password</label>
                        <div class="input-group cps-input-group">
                            <span class="input-group-addon"><span class="bi bi-shield-lock"></span></span>
                            <input  type="password" 
                                    class="form-control" 
                                    id="deletepw" 
                                    placeholder="Password Anda" 
                                    name="email" 
                                    style="border-left: none;"
                            />
                        </div> 
                    </div>
                </form>
                <button type="submit" class="btn btn-danger" style="width: 100%;" onclick="deleteaccount(this)">Hapus</button><br>

            </div>   
            <?php endif; ?>
        </center>
        <div class="ml-0">
            <div class="lay-container-smaller"  style="overflow: auto;" id="useranswers">
            <?php
                $max     = isset($_GET["max"]) ? intval($_GET["max"]) : 10;
                $answers = $core->getAnswersByUsername($_GET["user"], ($max-10 < 0 ? 0 : $max-10), $max); 
                if(count($answers) < 1):
            ?>
                <div class="alert alert-danger" id="ui_noanswer" style="overflow: hidden;margin-bottom: 5px;">
                    akun ini belum menjawab apapun
                </div>
            <?php
                endif;
                foreach($answers as $topAnswer):
                    $l = $core->getQuestion($topAnswer["questionid"]);
            ?>
                <div class="card-question" style="overflow: hidden;margin-bottom: 5px;">
                    <a class="link-danger" style="text-decoration: none;" href="/profile/<?= $l['username']; ?>">
                        <span class="<?= categorytoicon($l['category']) ?>"> <?= $l["category"] ?><span>
                    </a>
                    <p class="text-muted"><?= getndate($l["postdate"]); ?></p>
                    <div style="position: relative;z-index: 1; word-wrap: break-word;">
                        <a href='/question/<?= $l["id"]; ?>' style="color: black; text-decoration: none;" class="question-title">
                            <?= preg_replace("/<br\W*?\/>/"," ", strip_tags($l["title"], "<br>")); ?>
                        </a>
                        <div class="card-question">
                            <div class="ui_circular_wrapper">
                                <div class="ui_circular_image-x30">
                                    <img onclick="window.location = '/profile/<?= $topAnswer['username'] ?>'" src="<?= $core->getImgByUsername(@$topAnswer['username']); ?>" class="ui_circled_image-x30">
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
                    </div>
                </div>

            <?php endforeach; ?>
                </div>
            </div>
            
        </div>
        <h5 class="lay-container-smaller text-center text-center" id="loadsign">Memuat...</h5>
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
$("#loadsign").hide()
var page  = 20
var error = false
$(window).scroll(function() {
   if($(window).height() + $(window).scrollTop()  == getDocHeight()) {
        if(!error) $.ajax({
            url: "/profile/"+"<?=$_GET["user"] ?>"+'&max='+page,
            type: "GET",
            success: function (data, status){
                page += 10
                
                $("#useranswers").html($("#useranswers").html()+$(data).find("#useranswers").html())
                if($(data).find("#ui_noanswer").length < 1){
                    error = true
                    $("#loadsign").show()
                    $("#loadsign").html("<div class='alert alert-danger mt-2'>Sudah mencapai batas atau coba periksa kembali perangkat kamu</div>")
                    return
                }
            },
            beforeSend: function(){
                $("#loadsign").html("Memuat...")
            },
            error: function(jqXHR, textStatus, errorThrown){
                error = true
            }
        })

   }
});
        function editprofiledialog(){
            Swal.fire({
                html: $("#editinfo").html(),
                title: "Edit Informasi",
                showConfirmButton: false
            });
        }
        function settingprofiledialog(){
            Swal.fire({
                html: $("#setting").html(),
                title: "Pengaturan",
                showConfirmButton: false
            });
        }
        async function deleteaccountdialog(){
            Swal.fire({
                html: $("#delete").html(),
                title: "Penghapusan Akun",
                showConfirmButton: false
            });
        }
        function deleteaccount(s){
            $.ajax({
                url: '#',
                type: "POST",
                data: {
                    deletepass: $(s).parent().find("#deletepw").val(),
                },
                success: function (data, status){
                    $("#dm-accdel").html($(data).find("#dm-accdel").html())
                },
                complete: function(){
                    $(s).html("Simpan")
                    $(s).addClass("btn-danger")
                    Swal.close()
                }
            })
        }
        function changeuserinfo(s){
            var realname     = $(s).parent().find("#group-chg-realname").find("#chg-realname").val()
            var about        = $(s).parent().find("#group-chg-about").find("#chg-about").val() 
            $(s).html('Menyimpan')
            $(s).removeClass("btn-danger")
            if(!(/^[0-9A-Za-z\s\_]+$/.test(realname)) || (realname.length < 4  || realname.length > 20)){
                $(s).parent().find("#group-chg-realname").find("#chg-realname-sub").addClass("text-danger")
                $(s).html("Simpan")
                $(s).addClass("btn-danger")
            }
            else if(about.length < 10 || about.length > 200){
                $(s).parent().find("#group-chg-about").find("#chg-about-sub").addClass("text-danger")
                $(s).html("Simpan")
                $(s).addClass("btn-danger")
            }
            else {
                $.ajax({
                    url: '#',
                    type: "POST",
                    data: {
                        realname: realname,
                        about: about
                    },
                    success: function (data, status){
                        $("#realname").html(realname) 
                        $("#about").html(about) 
                    },
                    complete: function(){
                        $(s).html("Simpan")
                        $(s).addClass("btn-danger")
                        Swal.close()
                    }
                })
                $(s)
                    .parent()
                    .find("#group-chg-realname")
                    .find("#chg-realname-sub")
                    .removeClass("text-danger")
            }
        }
        $("#realname").click(async function(){
            const { value: realname } = await Swal.fire({
                input: 'text',
                title: 'Nama Lengkap Anda',
                showCancelButton: true,
                inputValidator: (value) => {
                        var re = /^[0-9A-Za-z\s\_]+$/;
                        if (!value)
                            return 'You need to write something!'
                        if(!re.test(value))
                            return "Anda hanya dapat menggunakan huruf, angka dan garis bawah"
                        if(value.length < 4)
                            return "Nama anda terlalu pendek (min. 4 huruf)"
                        if(value.length > 20)
                            return "Nama anda terlalu panjang (maks. 20 huruf)"
                    }
                })
            
            if(realname){
                $.ajax({
                    url: '#',
                    type: "POST",
                    data: {
                        realname: realname,
                    },
                    success: function (data, status){
                        $("#realname").html($(data).find("#realname").html()) 
                    }
                })
            }
        })
        function submit_(){
            var imageupdateform = $("#imgupload")
            var formData = new FormData();
            formData.append('imgfile', $('#imgfile')[0].files[0]);
            $("#imgprofile").animate({ opacity: 0.5 }, 100);
            $.ajax({
                url: $('#imgupload').attr("action"),
                type: $('#imgupload').attr("method"),
                data: new FormData($("#imgupload")[0]),
                processData: false,
                contentType: false,
                success: function (data, status)
                {
                    $("#imgprofile").html($(data).find("#imgprofile").html()) 
                    $("#imgprofile").animate({ opacity: 1 }, 100);
                },
                complete: function(){
                    $("#imgprofile").animate({ opacity: 1 }, 100);
                }
            });        
        }
        $('#soal').keypress(function (e) {
            var key = e.which;
            if(key == 13){
                $(this).trigger("enterKey");
                window.location = "/search/"+$("#soal").val()
                return false;  
            }
            });   
        </script>
</body>