<?php
    require_once(__DIR__."/../php/cerdasly.php");
    require_once(__DIR__."/../php/functions.php");
    $core         = new Core();
    $user_login   = false;
    $info_message = "";
    if(isset($_GET["action"]) == "error"){
        $info_message = "Anda harus login sebelum menggunakan aplikasi <i>Cerdasly</i>";
    }
    if(isset($_POST["login_email"]) && isset($_POST["login_password"])){
        $email = $core->getEmailByUsername($_POST["login_email"]);
        if(!preg_match("/\@/",$_POST["login_email"]) && ($core->login($email , $_POST["login_password"]) != false)){
            setcookie("email",  $email);
            setcookie("pass",  $_POST["login_password"]);
            header("location: /");
        }
        else if($core->login($_POST["login_email"], $_POST["login_password"]) != false){
            setcookie("email", trim($_POST["login_email"]));
            setcookie("pass",  $_POST["login_password"]);
            header("location: /");
        }
        else {
            $info_message = 'Tidak dapat masuk, coba cek kembali kata sandi dan email akun yang kamu gunakan';        
        }
    }
?>
<head>
    <title>Cerdasly - Masuk</title>
    <meta name="description" content="Ayo login Cerdasly untuk mengakses banyak soal dan berdiskusi tugas sekolah kamu">
    <meta name="title" content="Cerdasly - Masuk">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="/styles/styles.css" rel="stylesheet">
    <link href="/styles/components.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.3/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css" rel="stylesheet">    
    <style>
        a:hover {
            text-decoration: none;
        }
    </style>
</head>
<body>
<div style="padding-top: 20px;height: 100%;" class="shapedividers_com-4184">
    <center><img
        src="/cerdasly.png"
        height="70"
        alt="Cerdasly Logo"
        loading="lazy"
      /></center>
    <div class="lay-container-sm container " style="padding-top: 0px;">
        <form action="#" method="post" class="box-a m-5" style="background-color: white;">
            <center class="mb-3"><h4><b>Masuk dengan akunmu <br> atau <a href="/register">daftar disini</a></b></h4></center>
            <?= msgcreate($info_message); ?>
            <div class="form-group">
                <div class="input-group cps-input-group">
                    <span class="input-group-addon"><span class="bi bi-person-circle"></span></span>
                    <input class="form-control" id="email" placeholder="Email atau username akun anda" name="login_email" style="border-left: none;">
                </div> 
                 <small class="text-primary"><a href="/resetpass">Lupa username dan email akun saya</a></small>
            </div>
            <div class="form-group">
                <div class="input-group cps-input-group">
                    <span class="input-group-addon"><span class="bi bi-key"></span></span>
                    <input type="password" class="form-control" style="border-right: none;" id="password" placeholder="Password akun anda" name="login_password">
                    <span style="border-left: none; " class="input-group-addon" id="ui_view_password" onclick="viewPassword()" ><span class="bi bi-eye-slash" id="ui_eye_icon"></span></span>
                </div>
                <small class="text-primary"><a href="/resetpass">Lupa password akun saya</a></small>
            </div>
            <button type="submit" class="btn btn-danger cps-btn" style="width: 100%;border-radius: 15px;">Masuk</button><br>
            <center  style="margin-top: 11px;"><small>Belum punya akun? <a class="link-primary" href="/register"> Ayo daftar disini</a></small></center>
        </form>
    </div>
    <div class="flex lay-container-sm container " >
        <center>
            <a href="public/company.php" class="mb-5" style="float: left;border-bottom: 1px dotted #337AB7;">Ketentuan dan Persyaratan</a>
            <a href="public/company.php" class="mb-5" style="float: right;border-bottom: 1px dotted #337AB7;">FAQ</a>
        </center>
    </div>
</div>

<script>
    function viewPassword(){
        if($("#password").attr("type") == "password"){
            $("#ui_eye_icon").attr("class", "bi bi-eye")
            $('#password').attr('type', 'text')
        }
        else {
            $("#ui_eye_icon").attr("class", "bi bi-eye-slash")
            $('#password').attr('type', 'password')
        }
    }
</script>
</body>