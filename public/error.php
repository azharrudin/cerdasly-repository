<?php 
    require_once(__DIR__."/../php/cerdasly.php");
    require_once(__DIR__."/../php/tools/libtools.php");
    require_once(__DIR__."/components/navbar.php");
    //-------------------------------------
    $core       = new Core();
    $libtools   = new Libtools();
    $islogin    = false;
    $user = "";
    if(isset($_COOKIE["email"]) && isset($_COOKIE["pass"])){
        if(($core->login($_COOKIE["email"], $_COOKIE["pass"])) == false){
            $user = $core->getUsername($_COOKIE["email"]);
        }
        else if($core->login($_COOKIE["email"], $_COOKIE["pass"]) == true)
        $islogin = true;
    }
   
?>
<html>
    <head>
        <title>Tidak Ditemukan - Cerdasly</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="404 Sumber daya tidak ditemukan">
        <!--- library dan framework yang dibutuhkan --->
        <link href="/styles/styles.css" rel="stylesheet">
        <link href="/styles/ui.css" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.3/font/bootstrap-icons.css" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-0evHe/X+R7YkIZDRvuzKMRqM+OrBnVFBL6DOitfPri4tjfHxaWutUpFmBp4vmVor" crossorigin="anonymous">
        <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.css" rel="stylesheet">
        <link href="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css" rel="stylesheet">
        <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.js"></script>
        <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    </head>
    <body>
        <?= navigationBar($user); ?>
        <center>
            <div style="padding-top: 5px;">
                <div class="alert alert-danger w-50">
                    404 sumber daya tidak ditemukan
                </div>
            </div>
        </center>
    </body>
</html>