<?php 
    require_once(__DIR__."/../php/cerdasly.php");
    require_once(__DIR__."/../php/tools/libtools.php");
    //-------------------------------------
    $core       = new Core();
    $libtools   = new Libtools();
    $attachment = $libtools->UploadAttachmentTools();
    $islogin    = false;
    if(!isset($_COOKIE["email"]) || !isset($_COOKIE["pass"])){
        header("Location: /login");
    }
    if(($core->login($_COOKIE["email"], $_COOKIE["pass"])) == false){
        header("Location: /login");
    }
    else if($core->login($_COOKIE["email"], $_COOKIE["pass"]) == true)
        $islogin = true;
    if(isset($_POST["question"]) && isset($_POST["category"]) && $islogin){
        $core->addQuestion(
            $_POST["question"],
            $core->getUsername($_COOKIE["email"]),
            $_POST["category"],
        );
        header("Location: /");
    }
?>
<html>
    <head>
        <title>Cerdasly - Tambah Pertanyaan</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
        <!------------------------------------>
        <script>
            const Popup = Swal.mixin({
                toast: true,
                position: 'top',
                showConfirmButton: false,
                timer: 3200
            });
        </script>
        <!------------------------------------>
    </head>
<div class="lay-container" style="margin-top: 2%;padding: 3%;">  
     <div class="ui_ask_guide_alert">
            <div class="alert alert-danger" style="max-width: 100%;">
                <p>Tolong ketik soal dengan jelas dan sopan agar dapat dimengerti oleh komunitas Cerdasly</p>
        </div>
        <form action="#" method="POST" id="qask" enctype="multipart/form-data" data-reply-form>
            <textarea id="summernote" name="question"></textarea>
            <input type="hidden" value="Matematika" name="category" id="category">
            <div class="dropdown" style="margin-left:0%;">
                <button class="btn btn-secondary dropdown-toggle" type="button" id="dropc" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Matematika
                </button>
                <div id="menu" class="dropdown-menu">
                    
                    <div id="menuItems"></div>
                    <div id="empty" class="dropdown-header">Tidak ditemukan</div>
                </div>
            </div>
            <p class="text-muted">Lampiran berupa gambar/dokumen (.png/.jpg/.pdf/):</p>
            <input type="file" name="question_attachment"><br>
            <input type="submit" class="btn btn-danger" value="Tanya" name="ask">
        </form>
    </div>
</div>
<script>
$(document).ready(function() {
    $('#summernote').summernote({
        placeholder: "Ketik pertanyaanmu disini",
        tabsize: 2,
        height: 170,
        toolbar: [
            ['font', ['bold', 'underline', 'italic', 'clear']],
            ['para', ['paragraph']],
    ]
    });
});
let names  = [
    "Matematika", 
    "Bahasa Indonesia", 
    "Bahasa Inggris", 
    "Bahasa Daerah", 
    "Bahasa Asing", 
    "Ekonomi",
    "Hukum", 
    "Kimia", 
    "Komputer", 
    "Arsitektur",
    "PPKN",
    "Psikologi",
    "PJOK",
    "Kedokteran", 
    "Kedokteran Hewan", 
    "Akuntansi", 
    "Farmasi", 
    "Filsafat", 
    "Fisika", 
    "Politik",
    "Seni Budaya", 
    'Sains',
    "Sosial", 
    "Sastra", 
    "Sejarah",
    "Agama Islam",
    "Agama Hindu",
    "Agama Kristen",
    "Agama Buddha",
    "Agama Katolik",
    "Spiritual",
    "Lainnya"
]
let search = document.getElementById("searchb")
let items = document.getElementsByClassName("dropdown-item")
function buildDropDown(values) {
    let contents = []
    for (let name of values) {
     contents.push('<input type="button" class="dropdown-item" type="button" value="' + name + '"/>')
    }
    $('#menuItems').append(contents.join(""))
    $('#empty').hide()
}
window.addEventListener('input', function () {
    filter(search.value.trim().toLowerCase())
})
//If the user clicks on any item, set the title of the button as the text of the item
$('#menuItems').on('click', '.dropdown-item', function(){
    $('#dropc').text($(this)[0].value)
    $("#category").val($(this)[0].value);
    $("#dropc").dropdown('toggle');
})
buildDropDown(names)
function submitquestion(){
    if($($("#summernote").summernote("code")).text().length <= 20){
        Popup.fire({
            icon: 'error',
            text: 'Pertanyaan anda terlalu pendek! setidaknya harus lebih dari 20 huruf',
        });
    }
    else $('#qask')[0].submit();
}
</script>