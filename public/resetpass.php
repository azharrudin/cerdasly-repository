<?php
require_once(__DIR__ . "/../php/cerdasly.php");
require_once(__DIR__ . "/../php/functions.php");
require_once(__DIR__ . "/../php/email.php");
$core      = new Core();
$x         = "";
$islogin   = false;
$changepw      = false;
$message_alert = "";
if (isset($_POST["email"]) && isset($_POST["pass"])) {
    if ($core->login($_POST["email"], $_POST["pass"]) != false) {
        setcookie("email", $_POST["email"]);
        setcookie("pass", $_POST["pass"]);
        header("location: index.php");
    }
}

if (isset($_POST["recovery_email"])) {
    if ($core->emailExist($_POST["recovery_email"])) {
        $c                         = generator(5);
        $verification_message      = "Hai, untuk mengubah kata sandi akun Cerdasly kamu, masukkan kode verifikasi: <b>$c</b>";
        sendVerification($_POST["recovery_email"], $c, $verification_message);
        session_start();
        $_SESSION["recovery_code"]   = $c;
        $_SESSION["recovery_email"]  = $_POST["recovery_email"];
        $changepw                    = true;
    } else $message_alert = "<div class='alert alert-danger'>Email tidak ada atau belum ada, silahkan daftar terlebih dahulu </div>";
}
if (isset($_POST["recovery_newpass"]) && isset($_POST["recovery_code"])) {
    session_start();
    if ($_POST["recovery_code"] == $_SESSION["recovery_code"]) {
        $core->changePassword($_SESSION["recovery_email"], $_POST["recovery_newpass"]);
        $message_alert = "<div class='alert alert-success'>Password berhasil diubah, silahkan kembali ke halaman <a href='/login'>login</a></div>";
    }
}
?>

<head>
    <title>Cerdasly - Lupa Password</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="/styles/styles.css" rel="stylesheet">
    <link href="/styles/components.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.3/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css" rel="stylesheet">
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    <div style="padding-top: 20px;height: 100%;" class="shapedividers_com-4184">
        <center><img src="/cerdasly.png" height="70" alt="Cerdasly Logo" loading="lazy" /></center>
        <?= $x ?>
        <div class="lay-container-sm container" style="padding-top: 0px;">
            <form action="#" method="post" class="box-a m-5" style="background-color: white;">
                <center>
                    <h4><b>Saya Lupa Password</a></b></h4>
                    <p class="text-muted">Anda dapat mengatur ulang kata sandi anda</p>
                </center>
                <?= $message_alert ?>
                <div class="form-group">
                    <div class="input-group cps-input-group">
                        <span class="input-group-addon"><span class="bi bi-person-circle"></span></span>
                        <input value="<?= @$_POST["recovery_email"]  ?>" type="email" class="form-control" id="email" placeholder="Email anda" name="recovery_email" style="border-left: none;" <?= $changepw ? "disabled" : "" ?>>
                    </div>
                    <small class="text-muted">Ketik Email anda dengan benar karena kami akan mengirim notifikasi konfirmasi kepada anda</small>
                </div>
                <?php
                if ($changepw) :
                ?>
                    <div class="form-group">
                        <div class="input-group cps-input-group">
                            <span class="input-group-addon"><span class="bi bi-envelope-check"></span></span>
                            <input type="password" class="form-control" id="password" placeholder="Kode verifikasi" name="recovery_code">
                        </div>
                        <small class="text-muted">Tidak lebih dari 5 digit</small>
                    </div>
                    <div class="form-group">
                        <div class="input-group cps-input-group">
                            <span class="input-group-addon"><span class="bi bi-key"></span></span>
                            <input type="password" class="form-control" id="password" placeholder="Password Baru" name="recovery_newpass">
                        </div>
                        <small class="text-muted">Password baru, kami sarankan agar tidak menggunakan password lama</small>
                    </div>
                <?php
                endif;
                ?>
                <button type="submit" class="btn btn-danger cps-btn" style="width: 100%;border-radius: 15px;">Masuk</button><br>
                <center style="margin-top: 11px;"><small><a class="link-primary" href="/register"> Saya lupa email saya</a></small></center>
            </form>
        </div>
    </div>
</body>