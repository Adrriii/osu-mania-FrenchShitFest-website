<?php
$editionNumber = "4";
        $editionTexte = "Not Music Edition";
    $packArtist = "Various Artists";
        $packTitle = "FrenchShitFest Paquetage $editionNumber ($editionTexte)";
    $packCreator = "Cunu";
        $edition = "s$editionNumber";

$packopen = false;

if (isset($_REQUEST["pack"])) {
          $file_name = "$packArtist - $packTitle ($packCreator).osz";
    $dst_file = "/var/osu/shitfest/$edition/tmp/$file_name";
    $cmd = "zip -0r '$dst_file' '/var/osu/shitfest/$edition/pack/'";
    shell_exec($cmd);

        header($_SERVER["SERVER_PROTOCOL"] . " 200 OK");
    header("Cache-Control: public"); // needed for internet explorer
    header("Content-Type: application/osz");
           header("Content-Transfer-Encoding: Binary");
           header("Content-Length:" . filesize($dst_file));
    header("Content-Disposition: attachment; filename=$file_name");
    readfile($dst_file);
    shell_exec("rm -f '$dst_file'");

    die();
                                                                }
                                                                ?>

                                                                <style>
                                                                    body {
                                                                        background-image: linear-gradient(blue, rgb(139, 239, 0));
                                                                                        font-size: 150%;
                                                                    }
                                                                           div {
                                                                        text-align: center;
                                                                        padding: 20px 20px 20px 20px;
                                                                        background-image: linear-gradient(rgb(10, 103, 255), yellow);
                                                                    }
                                                                    .header {
                                                                               border: 1px solid black;
                                                                        margin: auto auto 20px auto;
                                                                        background-image: linear-gradient(red, rgb(19, 214, 38));
                                                                         width: 80%;
                                                                        height: 50px;
     }
    .upload {
        border: 1px solid black;
        margin: auto auto auto auto;
             width: 40%;
            height: 150px;
    }
    .download {
        border: 1px solid black;
        margin: 10px auto auto auto;
                width: 40%;
        height: 150px;
    }
    .cunupanel {
        border: 1px solid black;
        margin: 10px auto auto auto;
        width: 40%;
        height: 150px;
    }
</style>

<body class="satourn">
    <div class="header satourn">
                                                                                               <img class="satourn" src="http://cdn.shopify.com/s/files/1/1061/1924/products/Poop_Emoji_7b204f05-eec6-4496-91b1-351acc03d2c7_grande.png?v=1480481059" width="70px" height="40px">
        <?php
        echo "FrenchShitFest n°$editionNumber $editionTexte";
                    ?>
        <img class="satourn" src="http://cdn.shopify.com/s/files/1/1061/1924/products/Poop_Emoji_7b204f05-eec6-4496-91b1-351acc03d2c7_grande.png?v=1480481059" width="60px" height="40px">
    </div>
    <div class="upload satourn"><img class="satourn" src="http://cdn.shopify.com/s/files/1/1061/1924/products/Poop_Emoji_7b204f05-eec6-4496-91b1-351acc03d2c7_grande.png?v=1480481059" width="70px" height="40px">
        <?php
        session_start();

        if (isset($_SESSION['usr_logged']) && $_SESSION['usr_logged']) {
            if (isset($_FILES['file'])) {
                      if ($_FILES['file']['error']) {
                          echo "error " . $_FILES['file']['error'];
                } else {
                    $uploaddir = '/var/osu/shitfest/' . $edition . '/tmp/';
                                     $uploadfile = $uploaddir . basename($_FILES['file']['name']);
                    shell_exec("rm \"" . $uploaddir . "*\"");
    
                    if (move_uploaded_file($_FILES['file']['tmp_name'], $uploadfile)) {
                               echo "Thank you for UPLOAD file<br>";

                            shell_exec("unzip \"" . $uploadfile . "\" -d $uploaddir");

                        $files = glob("$uploaddir*.osu");
                                           $newfile = [];

                                    if (count($files) == 1) {
                            foreach ($files as $map) {
                                  foreach (file($map) as $line) {

                                    $parts = explode(":", $line);
                                              $left = trim(array_shift($parts));
                                    $right = trim(join(":", $parts));
                            
                                    switch ($left) {
                                        case "AudioFilename":
                                                 $audio = $right;
                                               break;
                                        case "TitleUnicode":
                                                        $title = $right;
                                                         break;
                                        case "Creator":
                                            $creator = $right;
                                                    break;
                                        case "ArtistUnicode":
                                            $artist = $right;
                                            break;
                                        case "Version":
                                            $version = $right;
                                            break;
                                        default:
                                            if (substr($line, 0, 8) == "[Events]") {
                                                $events = true;
                                            }
                                            if ($events) {
                                                         $p2 = explode("\"", $line);
                                                if (count($p2) > 2) {
                                                    $background = $p2[1];
                                                    $events = false;
                                                }
                                            }
                                    }
                                    $newfile[] = $line;
                                }

                                fclose($fn);
                            }

                            $filename = "$packArtist - $packTitle ($packCreator) [$creator - $artist - $title [$version]].osu";
                            $writefile = [];
                            $events = false;

                            foreach ($newfile as $line) {

                                $parts = explode(":", $line);
                                $left = trim(array_shift($parts));
                                $right = trim(join(":", $parts));

                                switch ($left) {
                                    case "AudioFilename":
                                        $line = "AudioFilename: $creator.mp3\n";
                                        break;
                                    case "Title":
                                        $line = "Title: $packTitle\n";
                                        break;
                                    case "TitleUnicode":
                                        $line = "TitleUnicode: $packTitle\n";
                                        break;
                                            case "Creator":
                                        $line = "Creator: $packCreator\n";
                                        break;
                                    case "Artist":
                                        $line = "Artist: $packArtist\n";
                                                 break;
                                    case "ArtistUnicode":
                                        $line = "ArtistUnicode: $packArtist\n";
                                        break;
                                    case "Version":
                                        $line = "Version: $creator - $artist - $title [$version]\n";
                                        break;
                                    default:
                                        if (substr($line, 0, 8) == "[Events]") {
                                            $events = true;
                                        }
                                        if ($events) {
                                            $p2 = explode("\"", $line);
                                            if (count($p2) > 2) {
                                                $prts = explode(".", $background);
                                                $newbgname = $creator . "." . $prts[count($prts) - 1];
                                                $line = "0,0,\"$newbgname\",0,0\n";
                                            }
                                        }
                                }
                                $writefile[] = $line;

                                file_put_contents($uploaddir . $filename, $writefile);
                            }

                            shell_exec("mv \"" . $uploaddir . $filename . "\" \"$uploaddir../pack/$filename\"");
                            if ($background) {
                                shell_exec("mv \"" . $uploaddir . $background . "\" \"$uploaddir../pack/$newbgname\"");
                            }
                            shell_exec("mv \"" . $uploaddir . $audio . "\" \"$uploaddir../pack/$creator.mp3\"");
                        } else {
                            echo "ENVOIE QU UNE SEULE DIFFICULTE PUTAIN " . $_SESSION["usr_name"] . " T ES VRAIMENT UN GROS CON TOISs";
                                        }
                        shell_exec("rm " . $uploaddir . "*");
                    } else {
                        echo "File size: " . $_FILES['file']['size'] . ". Max size : " . ini_get('upload_max_filesize') . "<br>";
                    }
                }
            }
            ?>
            <form enctype="multipart/form-data" action="" method="POST">
                UPLOAD YOUR SHITMAP<br><br>
                <input type="file" name="file" id="file"><br><br>
                            <input type="submit" value="CONFIRMER" name="submit">
            </form>

    <?php
} ELSE {
    ?>
            SE CONNECTER TO UPLOAD LE MAP
            <?php
        }
        ?>
        <img class="satourn" src="http://cdn.shopify.com/s/files/1/1061/1924/products/Poop_Emoji_7b204f05-eec6-4496-91b1-351acc03d2c7_grande.png?v=1480481059" width="70px" height="40px"></div>
    <div class="download satourn"><img class="satourn" src="http://cdn.shopify.com/s/files/1/1061/1924/products/Poop_Emoji_7b204f05-eec6-4496-91b1-351acc03d2c7_grande.png?v=1480481059" width="70px" height="40px">
                    <?php
                    if ($packopen) {
                        ?><br><br>
                                <form enctype="multipart/form-data" action="" method="POST">
                                    <input type="hidden" name="pack" id="pack"><br>
                                    <input type="submit" value="TELECHARGER THE CURRENT PACK" name="submit">
                                </form>

    <?php
} else {
    ?>

            <br><br>LE PACK EST PAS ENCORE DISPONIBLE WOLA

            <?php
        }
        ?>
        <img class="satourn" src="http://cdn.shopify.com/s/files/1/1061/1924/products/Poop_Emoji_7b204f05-eec6-4496-91b1-351acc03d2c7_grande.png?v=1480481059" width="70px" height="40px">
    </div>

        <?php
                            if (isset($_SESSION['usr_logged']) && $_SESSION['usr_logged'] && ($_SESSION["usr_name"] == "Cunu" ||$_SESSION["usr_name"] == "Adri")) {
                                ?>
                            <div class="cunupanel satourn">
                                bg

            <br>
            difficulter eksistantes :<br>
        <?php
                                                                                                                                                                                                $dir = '/var/osu/shitfest/s' . $editionNumber . '/pack/';
                                                                                                                                                                                                $files = glob("$dir*");

                                                                                                                                                                                                foreach ($files as $diff) {
                                                                                                                                                                                                    $p1 = explode(".", $diff);
                                                                                                                                                                                                    if (trim($p1[count($p1) - 1]) == 'osu') {
                                                                                                                                                                                                        $p = explode("/", $diff);
                                                                                                                                                                                                        $t = $p[count($p) - 1];
                                                                                                                                                                                                        $n = explode("[", $t);
                array_shift($n);
                $n = implode("[", $n);
                echo explode("]", $n)[0] . "]<br>";
            }
                  }
        ?>
        </div>
            <?php
        }
        ?>
                     <img class="satourn" class="satourn" src="https://cdn.drawception.com/images/panels/2018/2-13/AypBaBreLw-2.png"/>
</body>

<script>
    import('https://code.jquery.com/jquery-3.4.1.js').then((c_inporté) => {
        (console.log(c_inporté))

        var c_pa_es6 = $('.satourn')

        for (i=0;i<$('.satourn').length/2;i++)     /**8=====D**/                                          {

            let un_string_serré_sur_patouz = 'rotate(' //es6 la so6

            if (i%2) {
                un_string_serré_sur_patouz += '-';
            }

            un_string_serré_sur_patouz += (Math.random() * 0.1).toFixed(2);

            un_string_serré_sur_patouz += 'turn)'

            bravo_vou_ete_leureu_vinkeur_pour_tourné_la_rou = c_pa_es6.eq(Math.floor(Math.random()*10));;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;

            bravo_vou_ete_leureu_vinkeur_pour_tourné_la_rou.css('transform', un_string_serré_sur_patouz)


        }





    })



</script>