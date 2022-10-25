<?php
require_once(__DIR__."/../php/cerdasly.php");
$core       = new Core();
$names_list = false;
if(isset($_POST["search_name"])){
    $names_list =  $core->searchUser($_POST["search_name"]);   
}
?>
<html>
    <head>
        <title>Cerdasly - Lupa Password</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="/styles/styles.css" rel="stylesheet">
        <link href="/styles/components.css" rel="stylesheet">
        <link href="/styles/ui.css" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.3/font/bootstrap-icons.css" rel="stylesheet">
        <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
        <link href="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css" rel="stylesheet">    
        <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    </head>
    <body>
        <div style="padding-top: 20px;height: 100%;">
            <center><img
                src="/cerdasly.png"
                height="70"
                alt="Cerdasly Logo"
                loading="lazy"
            /></center>
            <div class="lay-container-sm container" style="padding-top: 0px;">
                <div class="box-a m-5" style="background-color: white;">
                    <center>
                        <h4><b>Saya Lupa Akun</a></b></h4>
                        <p class="text-muted">Anda dapat mencari akun anda</p>
                    </center>
                    <div class="form-group">
                        <div class="input-group cps-input-group">
                            <span class="input-group-addon"><span class="bi bi-person-circle"></span></span>
                            <input class="form-control" id="ui_search_name" placeholder="Username atau nama akun" name="search_name">
                        </div> 
                        <small class="text-muted">Ketik nama akun anda untuk mendapatkan email anda</small>
                    </div>
                        <button type="submit" class="btn btn-danger cps-btn" style="width: 100%;border-radius: 15px;" onclick="search_name()">Cari</button><br>
                        <center  style="margin-top: 11px;"><small><a class="link-primary" href="/register"> Saya lupa email saya</a></small></center>
            </div>
<?=    var_dump(CRDSLY_IMG_PROFILE_DIR."/".$this->getImgByUsername("cerdaslyteacher")); ?>

                <div id="ui_search_name_container">
    <?php
        if($names_list != false):
            foreach ($names_list as $val):
    ?>
            <div class="ui_circular_wrapper list-card-x">
                <div class="ui_circular_image-x30">
                    <img onclick="window.location = '/profile/<?= $v['username'] ?>'" src="<?= $core->getImgByUsername($val['username']); ?>" class="ui_circled_image-x30">
                </div>
                <span class="text-muted" onclick="window.location = '/profile/<?= $val['username'] ?>'">
                    <?= $val["realname"]; ?>
                </span>
            </div>
    <?php
            endforeach;
        endif;
    ?>
                </div>
            </div>
        </div>
        <script>
            function search_name(){
                $.ajax({
                    url: "#",
                    type: "POST",
                    data: {
                        search_name: $("#ui_search_name").val()
                    },
                    success: function(data){
                        $("#ui_search_name_container").html($(data).find("#ui_search_name_container").html())
                    }
                })
            }
        </script>
    </body>
</html>
