<?php
require_once(__DIR__ . "/../../../vendor/autoload.php");

use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\HeadingPermalink\HeadingPermalinkExtension;
use League\CommonMark\Extension\TableOfContents\TableOfContentsExtension;
use League\CommonMark\MarkdownConverter;

function load_markdown($markdown)
{
    // Define our config...
    $config = [
        'table_of_contents' => [
            'html_class' => 'table-of-contents',
            'position' => 'placeholder',
            'placeholder' => '[TOC]',
        ],
    ];

    // Create an environment using the config...
    $environment = new Environment($config);

    // Register the core CommonMark parsers and renderers...
    $environment->addExtension(new CommonMarkCoreExtension());

    // Register the Table of Contents extension (this extension requires the HeadingPermalinkExtension!)
    $environment->addExtension(new HeadingPermalinkExtension());
    $environment->addExtension(new TableOfContentsExtension());


    $output = (new MarkdownConverter($environment))
        ->convert($markdown)
        ->getContent();
    return $output;
}
?>

<!DOCTYPE html>
<html>

<head>
  <title>Pusat Bantuan - Cerdasly </title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta content="Pusat bantuan Cerdasly - Artikel lengkap pemecahan masalah aplikasi Cerdasly" name="description" />
  <meta content="Pusat Bantuan - Cerdasly" name="title">
  <meta content="index" name="robots">
  <meta content="bantuan, help, cara, cerdasly" name="keywords">
  <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
  <style>
    body {
      background-color: #F2F6FA !important;
    }

    .ui_input_transparent {
      border: none;
      width: 100%;
    }

    .ui_input_transparent:focus {
      border: none;
      outline: none;
    }

    .card-question {
      background-color: white;
      border-radius: 15px;
      border: 1px solid rgb(230, 230, 230);
      padding: 10px;
      margin-bottom: 2px;
      margin-top: 2px;
      box-shadow: 0 2px 3px rgba(10, 10, 10, .1), 0 0 0 1px rgba(10, 10, 10, .1);
    }

    /* vietnamese */
    @font-face {
      font-family: 'Questrial';
      font-style: normal;
      font-weight: 400;
      font-display: swap;
      src: url(https://fonts.gstatic.com/s/questrial/v18/QdVUSTchPBm7nuUeVf70sSFlq20.woff2) format('woff2');
      unicode-range: U+0102-0103, U+0110-0111, U+0128-0129, U+0168-0169, U+01A0-01A1, U+01AF-01B0, U+1EA0-1EF9, U+20AB;
    }

    /* latin-ext */
    @font-face {
      font-family: 'Questrial';
      font-style: normal;
      font-weight: 400;
      font-display: swap;
      src: url(https://fonts.gstatic.com/s/questrial/v18/QdVUSTchPBm7nuUeVf70sCFlq20.woff2) format('woff2');
      unicode-range: U+0100-024F, U+0259, U+1E00-1EFF, U+2020, U+20A0-20AB, U+20AD-20CF, U+2113, U+2C60-2C7F, U+A720-A7FF;
    }

    /* latin */
    @font-face {
      font-family: 'Questrial';
      font-style: normal;
      font-weight: 400;
      font-display: swap;
      src: url(https://fonts.gstatic.com/s/questrial/v18/QdVUSTchPBm7nuUeVf70viFl.woff2) format('woff2');
      unicode-range: U+0000-00FF, U+0131, U+0152-0153, U+02BB-02BC, U+02C6, U+02DA, U+02DC, U+2000-206F, U+2074, U+20AC, U+2122, U+2191, U+2193, U+2212, U+2215, U+FEFF, U+FFFD;
    }

    .parent {
      display: flex;
      justify-content: center;
      height: 80vh;
      /* or any desired height */
    }
    .ui_search {
        width: 50%;
    }
    @media (max-width: 460px) and (min-width: 200px){
      .ui_quicklink {
        max-width: 100px;
      }
      .ui_search {
        width: 80%;
      }
    }
  
    * {
      font-family: 'Questrial', sans-serif;
    }
  </style>
</head>

<body>
  <nav class="navbar navbar-expand-lg navbar-light">
    <a class="navbar-brand" href="#"> <img src="/logo.png" width="100" height="40" alt=""></a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav mr-auto">
        <li class="nav-item active">
          <a class="nav-link" href="#"> Beranda <span class="sr-only">(current)</span></a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#"> Forum </a>
        </li>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            Map Situs
          </a>
          <div class="dropdown-menu" aria-labelledby="navbarDropdown">
            <a class="dropdown-item" href="#">Peraturan</a>
            <a class="dropdown-item" href="#">Akun</a>
            <a class="dropdown-item" href="#">Hak Cipta</a>
          </div>
        </li>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            Link Cepat
          </a>
          <div class="dropdown-menu" aria-labelledby="navbarDropdown">
            <a class="dropdown-item" href="#">Hak cipta jawaban anda</a>
            <a class="dropdown-item" href="#">Merubah informasi akun</a>
            <a class="dropdown-item" href="#">Menghapus akun</a>
            <a class="dropdown-item" href="#">Melaporkan akun atas pelanggaran</a>
          </div>
        </li>
      </ul>

      <form class="form-inline my-2 my-lg-0">
        <input class="form-control mr-sm-2" type="search" placeholder="Search" aria-label="Search">
        <button class="btn btn-outline-success my-2 my-sm-0" type="submit">Search</button>
      </form>
    </div>
  </nav>
  <div class="parent mt-2"> 
    <div class="ui_search">
      <div class="card-question" style="width: 100%">
        <h3 class="mb-0" >Akun</h3>
        <h5 class="text-muted" style="border-bottom: 1px dotted grey;">informasi dasar tentang akun anda</h5>
        <a href="" class="link link-primary">›› Cara mengganti informasi dasar Akun</a><br>
        <a href="" class="link link-primary">›› Cara mengganti email dan password Akun </a><br>
        <a href="" class="link link-primary">›› Cara mengganti email dan password Akun jika lupa password Akun </a><br>
        <a href="" class="link link-primary">›› Cara menghapus Akun secara permanen</a><br><br>
        <h3 class="mb-0" >Privasi dan Keamanan</h3>
        <h5 class="text-muted" style="border-bottom: 1px dotted grey;">cara bagaimana mengembalikan akun anda yang diretas</h5>
        <a href="" class="link link-primary">›› Cara mengganti informasi dasar Akun</a><br>
        <a href="" class="link link-primary">›› Cara mengganti email dan password Akun </a><br>
        <a href="" class="link link-primary">›› Cara mengganti email dan password Akun jika lupa password Akun </a><br>
        <a href="" class="link link-primary">›› Cara menghapus Akun secara permanen</a><br><br>
        <h3 class="mb-0">Peraturan dan Ketentuan</h3>
        <h5 class="text-muted" style="border-bottom: 1px dotted grey;">wajib dibaca agar tidak terjadi kesalahpahaman</h5>
        <a href="" class="link link-primary">›› Apa jawaban dan pertanyaan yang terdapat dapat disalin ke media lain?</a><br>
        <a href="" class="link link-primary">›› Apa saja data saya yang situs ini simpan? </a><br>
        <a href="" class="link link-primary">›› Apa yang boleh atau tidak boleh dilakukan?  </a><br>
        <a href="" class="link link-primary">›› Apa situs ini digunakan untuk menyontek para pelajar?  </a><br>
      </div>
      
    </div><br>

  </div>
</body>

</html>