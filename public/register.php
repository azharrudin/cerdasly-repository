<html>
    <head>
        <title>Cerdasly - Bergabunglah Sekarang</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.3/font/bootstrap-icons.css" rel="stylesheet">
        <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
        <link href="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css" rel="stylesheet">    
        <link href="/styles/styles.css" rel="stylesheet">
        <link href="/styles/components.css" rel="stylesheet">
        <link href="/styles/ui.css" rel="stylesheet">
        <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <style>
            .ui_create_account_container {
                border: 2px solid rgb(230, 230, 230);
                border-radius: 5px;
                background-color: white;
            }
        </style>
    </head>
    <body>
    <center><img
        src="/cerdasly.png"
        height="70"
        alt="Cerdasly Logo"
        loading="lazy"
        style="margin-top: 20px;"
      />
    </center>
    <?php
        require_once(__DIR__."/../php/cerdasly.php");
        require_once(__DIR__."/../php/email.php");
        require_once(__DIR__."/../php/functions.php");
        $core = new Core();       
        $expr = ((
            isset($_POST["password"]) && 
            isset($_POST["realname"]) && 
            isset($_POST["email"]))   &&
            trim($_POST["password"]) != "" &&
            trim($_POST["realname"]) != "" &&
            trim($_POST["email"])    != ""
        );
        $ctrue = false;
        $msg   = "";
        if(isset($_POST["username"]) && isset($_POST["code"])){
            session_start();
            var_dump($_POST, $_SESSION);
            //-----------------------------------------
            // Validasi variabel username yang sudah ada 
            //-----------------------------------------
            if($core->usernameExist(trim($_POST["username"]))){
                $msg = "Username telah ada atau coba gunakan username lain";
            }
            //-----------------------------------------
            // Validasi username dan kode verifikasi
            //-----------------------------------------
            else if(strlen($_POST["username"]) >= 4 && strlen($_POST["username"]) <= 15 && trim($_POST["code"]) == @$_SESSION["code"]){                
                $core->createAccount(
                    trim($_POST["username"]), 
                    trim($_SESSION["realname"]), 
                    trim($_SESSION["email"]), 
                    trim($_SESSION["password"])
                );
                echo "<script>alert(\"Akun dibuat\");window.location.replace(\"/login\")</script>";
            
            }
            else {
                $msg             = "Coba periksa kode verifikasi yang kamu masukan atau coba hubungi kami jika masalah terus berlanjut";
                $ctrue           = true;
            }
            $_POST = array();
            session_unset();
            session_destroy();
        }
        //-----------------------------------------
        // Validasi variabel email yang sudah ada
        //-----------------------------------------
        if(isset($_POST["email"]) && $core->emailExist($_POST["email"])){
            $msg   = "Email yang anda pakai sudah digunakan atau coba gunakan email lain";
        }
        //-----------------------------------------
        // Validasi nilai variabel benar semua
        //-----------------------------------------
        if($expr && (strlen($_POST["realname"]) > 20 || strlen($_POST["realname"]) < 4)){
            $ctrue = true;
            $msg   = "Panjang nama lengkap anda hanya diizinkan minimal 4 karakter dan maksimal 20 karakter";
            
        }
        if($expr && (strlen($_POST["password"]) > 20 || strlen($_POST["password"]) < 4)){
            $ctrue = true;
            $msg   = "Panjang Kata sandi anda hanya diizinkan minimal 4 karakter dan maksimal 20 karakter";
        }
        if($expr &&  !preg_match("/^[0-9A-Za-z\s\_]+$/",$_POST["realname"])){
            $msg   = "Nama anda hanya diizinkan menggunakan huruf/angka, garis bawah dan spasi";
            $ctrue = true;
        }
        if($expr &&  !preg_match("/^[0-9A-Za-z\_]+$/",$_POST["password"])){
            $msg   = "Kata sandi anda hanya diizinkan menggunakan huruf/angka dan garis bawah";
            $ctrue = true;
        }
        if($expr && (!$core->emailExist($_POST["email"])) && $ctrue == false):
            session_start();
            $c = generator(5);
            $_SESSION["code"]     = sendVerification($_POST["email"], $c);
            $_SESSION["realname"] = $_POST["realname"];
            $_SESSION["email"]    = $_POST["email"];
            $_SESSION["password"] = $_POST["password"];
            
    ?>
        <!--- Tahap konfirmasi pembuatan akun --->
        <div class="lay-container-sm container" style="padding: 10px;">
            <form action="#" method="post" class="box-a p-5 pt-3 ml-5 mr ui_create_account_container">
                <center><h4><b>Daftarkan Saya</b></h4></center>
                <div class="alert alert-success" style="max-width: 100%;">
                    <p class="card-text">Kami telah mengirimkan kode verifikasi (5 digit), <i>refresh</i> browsermu jika tidak masuk kedalam emailmu</p>
                </div>
                <div class="form-group">
                    <div class="input-group cps-input-group">
                        <span class="input-group-addon"><span class="bi bi-phone"></span></span>
                        <input class="form-control" id="code" placeholder="Kode verifikasi" name="code">
                    </div>
                    <small class="form-text text-muted">Tidak lebih dari 5 digit</small>
                </div>
                <div class="form-group">
                   <div class="input-group cps-input-group">
                        <span class="input-group-addon"><span class="bi bi-person"></span></span>
                        <input class="form-control" id="username" placeholder="Username" name="username">
                    </div>
                    <small class="form-text text-muted" id="husername"></small>
                </div>
                <button type="submit" class="btn btn-danger" style="width: 100%">Buat</button>
                <center style="margin-top: 6px;"><small>Sudah punya akun? <a class="link-primary" href="/register">Batalkan</a></small></center>
            </form>
        </div>
        <!------------------------------------>
    <?php else: ?>
        <!--- Tahap awal pembuatan akun --->
        <div class="lay-container-sm container">
            <form action="#" class="box-a p-5 pt-3 ml-5 mr" method="post" style="background-color: white;">
                <center><h4><b>Daftarkan Saya</b></h4></center>                
                <?= msgcreate($msg); ?>
                <div class="form-group">
                    <div class="input-group cps-input-group">
                        <span class="input-group-addon"><span class="bi bi-person-circle"></span></span>
                        <input class="form-control" id="realname" placeholder="Nama lengkap anda" name="realname" required />
                    </div>
                    <small class="form-text text-muted" id="subrealname">Minimal 4 karakter dan maksimal 20 karakter juga hanya angka, garis bawah dan spasi di perbolehkan</small>
                </div>
                <div class="form-group">
                    <div class="input-group cps-input-group">
                        <span class="input-group-addon"><span class="bi bi-key"></span></span>
                        <input type="password" class="form-control" id="password" placeholder="Password" name="password" required />
                    </div>
                    <small class="form-text text-muted" id="subpassword">Minimal 4 karakter dan maksimal 20 karakter, juga hanya angka, garis bawah dan spasi di perbolehkan</small>
                </div>
                <div class="form-group">
                    <div class="input-group cps-input-group">
                        <span class="input-group-addon"><span class="bi bi-envelope"></span></span>
                        <input class="form-control" placeholder="Email anda" name="email" type="email" required />
                    </div>
                    <small class="form-text text-muted" id="subrealname">Gunakan email yang valid untuk kode verifikasi</small>
                </div>    
                <button type="submit" class="btn btn-danger" style="width: 100%;">Lanjut</button>
                <center style="margin-top: 6px;"><small>Sudah punya akun? <a class="link-primary" href="/login">Klik disini untuk masuk</a></small></center>
            </form>
            <script>
              
            </script>
        </div>
        
        <!------------------------------------>
    <?php endif; ?>
    <script>
        function realname_on(){
            var re = /^[0-9A-Za-z\s\_]+$/;
            var temp = `Minimal 4 karakter dan maksimal 20 karakter, hanya angka, garis bawah dan spasi di perbolehkan`;
            if($("#realname").val().length == 0){
                $("#subrealname").addClass("text-muted");
                $("#subrealname").removeClass("text-danger");
                $("#subrealname").html(temp);
                
            }
            else if($("#realname").val().length < 4){
                $("#subrealname").addClass("text-danger");
                $("#subrealname").removeClass("text-muted");
                $("#subrealname").html("Minimal 4 karakter")
            }
            else if($("#realname").val().length > 20){
                $("#subrealname").addClass("text-danger");
                $("#subrealname").removeClass("text-muted");
                $("#subrealname").html("Maksimal 20 karakter")
            }
            else if(!re.test($("#realname").val())){
                $("#subrealname").addClass("text-danger");
                $("#subrealname").html("hanya angka, garis bawah dan spasi di perbolehkan");
            }
            else { 
                $("#subrealname").addClass("text-muted")
                $("#subrealname").removeClass("text-danger")
                $("#subrealname").html(temp)
            }

        }
        function password_on(){
            var re = /^[0-9A-Za-z\_]+$/;
            var temp = `Minimal 4 karakter dan maksimal 20 karakter, hanya angka, garis bawah dan spasi di perbolehkan`;
            if($("#password").val().length == 0){
                $("#subpassword").addClass("text-muted");
                $("#subpassword").removeClass("text-danger");
                $("#subpassword").html(temp);
            }
            else if($("#password").val().length < 4){
                $("#subpassword").addClass("text-danger")
                $("#subpassword").removeClass("text-muted")
                $("#subpassword").html("Minimal 4 karakter")
            }
            else if($("#password").val().length > 20){
                $("#subpassword").addClass("text-danger")
                $("#subpassword").removeClass("text-muted")
                $("#subpassword").html("Maksimal 20 karakter")
            }
            else if(!re.test($("#password").val())){
                $("#subpassword").addClass("text-danger")
                $("#subpassword").removeClass("text-muted")
                $("#subpassword").html("hanya angka, garis bawah dan spasi di perbolehkan");
            }
            else {
                $("#subpassword").addClass("text-muted");
                $("#subpassword").removeClass("text-danger");
                $("#subpassword").html(temp);
            }

        }
        function username_on(){
            var re = /^[0-9a-z\_]+$/;
            var v = `Minimal 4 huruf dan maksimal 20 huruf kecil, hanya angka dan garis bawah di perbolehkan`
            
            if($("#username").val().length == 0){
                $("#husername").addClass("text-danger")
                $("#husername").removeClass("text-muted")
                $("#husername").html(v);
            }
            else if($("#username").val().length < 4){
                $("#husername").html("Minimal 4 karakter")
                $("#husername").addClass("text-muted")
                $("#husername").removeClass("text-danger")
            }
            else if($("#username").val().length > 20){
                $("#husername").html("Maksimal 20 karakter")
                $("#husername").addClass("text-muted")
                $("#husername").removeClass("text-danger")
            }
            else if(!re.test($("#username").val())){
                $("#husername").html("Hanya huruf, angka dan garis bawah")
                $("#husername").addClass("text-muted")
                $("#husername").removeClass("text-danger")
            }
            else {
                $("#husername").addClass("text-muted")
                $("#husername").removeClass("text-danger")
                $("#husername").html("Sedang memeriksa username...")
                $.ajax({
                    url: "/profile/"+$("#username").val(),
                    method: "POST",
                    data: {
                        request_check_only: true
                    },
                    complete: function(xhr, textStatus){
                        if(xhr.status == 200){
                            $("#husername").html("<p class='bi bi-x-circle'>Username tidak tersedia</p>")
                            $("#husername").addClass("text-danger")
                        } else {
                            $("#husername").html("<p class='bi bi-check-circle'>Username tersedia</p>")
                            $("#husername").addClass("text-success")

                        }
                    }
                    
                })
            }

        }
       
        if($("#realname").length > 0){
            realname_on()
            password_on()
            $("#realname").on("input", realname_on);
            $("#password").on("input", password_on);
        }
        else {
            username_on()
            $("#username").on("input", username_on);
        }
    </script>
    </body>
</html>