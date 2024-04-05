<?php
// echo phpinfo(); // attention ne pas décommenter (par accident)
set_time_limit(100);
// wa une database mdr
include_once('database.php');

$datadonnee = new Database();
        session_start();

    if (isset($_SESSION['usr_logged']) && $_SESSION['usr_logged'] && ($_SESSION["usr_name"] == "Cunu" || $_SESSION["usr_name"] == "Adri")) {
        if(isset($_REQUEST["ACTION_INSENSER"])) {
            switch($_REQUEST["ACTION_INSENSER"]) {
                case "0":
                    // metre a jour les misajour
                    $doner = [
                        ":n" => $_REQUEST["number"],
                        ":s" => $_REQUEST["saison"],
                        ":t" => $_REQUEST["titre"],
                        ":m" => $_REQUEST["creator"],
                        ":p" => $_REQUEST["artist"],
                    ];
                    $datadonnee->fast("UPDATE current_edition SET number = :n, saison = :s, titre = :t, creator = :m, artist = :p", $doner);
                    break;
                case "1":
                    $doner = [
                        ":o" => $_REQUEST["open"],
                        ];
                    // metre a jour uplode
                    $datadonnee->fast("UPDATE current_edition SET open = :o", $doner);
                    break;
                case "2":
                    // metre a jour donlode
                    $doner = [
                        ":o" => $_REQUEST["download"],
                        ];
                    // metre a jour uplode
                    $datadonnee->fast("UPDATE current_edition SET download = :o", $doner);
                    break;
				case "4":
					// ajoute pack
                    $doner = [
                        ":s" => $_REQUEST["SAISON"],
                        ":e" => $_REQUEST["EDITION"],
                        ":t" => $_REQUEST["TITRE"],
                        ];
					$datadonnee->fast("INSERT INTO pack (`saison`,`edition`,`titre`) VALUES (:s, :e, :t)", $doner);
					break;
            }
        }
    }
$INFORMATION = $datadonnee->fast("SELECT * FROM current_edition")[0]; // enorme issime
$editionNumber = $INFORMATION["number"];
$saisonNumber = $INFORMATION["saison"];
$editionTitre = $INFORMATION["titre"];
$editionTexte = "$editionTitre" . ($editionTitre ? " Edition" : "");
$packArtist = "Various Artists";
$packTitle = "FrenchShitFest$saisonNumber Paquetage $editionNumber" . ($editionTitre ? " ($editionTexte)" : "");
$packCreator = $INFORMATION["creator"];
$season = "s$saisonNumber";
$edition = "e$editionNumber";
$full = "$season/$edition";

$packopen = $INFORMATION["open"] == 1;
$packdownload = $INFORMATION["download"] == 1;

if ((isset($_REQUEST["pack"]) && $packdownload) || (isset($_REQUEST["ACTION_INSENSER"]) && isset($_SESSION['usr_logged']) && $_SESSION['usr_logged'] && ($_SESSION["usr_name"] == "Cunu" || $_SESSION["usr_name"] == "Adri") && $_REQUEST["ACTION_INSENSER"] == 3))  {
    $file_name = "$packArtist - $packTitle ($packCreator).osz";

    mkdir("/var/osu/shitfest/$season", 0777, true);
    mkdir("/var/osu/shitfest/$full", 0777, true);
    mkdir("/var/osu/shitfest/$full/tmp", 0777, true);
    mkdir("/var/osu/shitfest/$full/pack", 0777, true);

    $dst_file = "/var/osu/shitfest/$full/tmp/$file_name";
    $cmd = "cd '/var/osu/shitfest/$full/pack/';zip -0r '$dst_file' '.'";
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
        min-height: 150px;
    }
    .download {
        border: 1px solid black;
        margin: 10px auto auto auto;
        width: 40%;
        min-height: 150px;
    }
    .cunupanel {
        border: 1px solid black;
        margin: 10px auto auto auto;
        width: 40%;
        min-height: 150px;
    }
    .cunupanelDEUX {
        border: 1px solid black;
        margin: 10px auto auto auto;
        width: 40%;
        min-height: 150px;
    }
</style>

<body class="satourn">
    <div class="header satourn">
        <img class="satourn" src="http://cdn.shopify.com/s/files/1/1061/1924/products/Poop_Emoji_7b204f05-eec6-4496-91b1-351acc03d2c7_grande.png?v=1480481059" width="70px" height="40px">
        <?php
        echo "FrenchShitFest$saisonNumber $editionTexte";
        ?>
        <img class="satourn" src="http://cdn.shopify.com/s/files/1/1061/1924/products/Poop_Emoji_7b204f05-eec6-4496-91b1-351acc03d2c7_grande.png?v=1480481059" width="60px" height="40px">
		</br>
		<a href="https://osudaily.net/shitfest/packs.php">voir tous les packs</a>
    </div>
    <div class="upload satourn"><img class="satourn" src="http://cdn.shopify.com/s/files/1/1061/1924/products/Poop_Emoji_7b204f05-eec6-4496-91b1-351acc03d2c7_grande.png?v=1480481059" width="70px" height="40px">
        <?php

        if($packopen) {

        if (isset($_FILES['file'])) {
            if ($_FILES['file']['error']) {
                echo "error " . $_FILES['file']['error'];
                if($_FILES['file']['error'] == 1) { echo "le maximume c ".ini_get('upload_max_filesize'); }
            } else {
                $packdir = "/var/osu/shitfest/$full/pack/";
                $uploaddir = "/var/osu/shitfest/$full/upload/";
                $workdir = "/var/osu/shitfest/$full/tmp/";
                mkdir($packdir, 0777, true);
                mkdir($uploaddir, 0777, true);
                mkdir($workdir, 0777, true);
                $uploadfile = $uploaddir . basename($_FILES['file']['name']);
                
                if(file_exists($uploadfile)) {
                    shell_exec("rm $uploadfile");
                }

                if (move_uploaded_file($_FILES['file']['tmp_name'], $uploadfile)) {

                    shell_exec("unzip \"" . $uploadfile . "\" -d $workdir");
                    $nbimg = count(glob("$workdir*.png")) + count(glob("$workdir*.jpg")) + count(glob("$workdir*.jpeg"));
                    if ($nbimg <= 1) {
                        if(count(glob("$workdir*.osb")) == 0) {
                            $files = glob("$workdir*.osu");
                            $newfile = [];

                            if (count($files) == 1) {
                                echo "Thank you for UPLOAD file<br>";
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
                                    
                                    shell_exec("rm \"$map\"");
                                }

                                $filename = "$creator.osu";
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
                                            $line = "Version: $creator - $title\n";
                                            break;
                                        case "BeatmapID":
                                            $line = "BeatmapID:0\n";
                                            break;
                                        case "BeatmapSetID":
                                            $line = "BeatmapSetID:-1\n";
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
                                                    $events = false;
                                                }
                                            }
                                    }
                                    $writefile[] = $line;

                                }
                                file_put_contents($workdir . $filename, $writefile);
                                
                                shell_exec("mv \"" . $workdir . $filename . "\" \"$uploaddir../pack/$filename\"");
                                if ($background) {
                                    shell_exec("mv \"" . $workdir . $background . "\" \"$uploaddir../pack/$newbgname\"");
                                }
                                shell_exec("mv \"" . $workdir . $audio . "\" \"$uploaddir../pack/$creator.mp3\"");
                                
                                shell_exec("mv $workdir*  \"$workdir../pack/\"");
                            } else {
                                echo "ENVOIE QU UNE SEULE DIFFICULTE PUTAIN " . $_SESSION["usr_name"] . " T ES VRAIMENT UN GROS CON TOISs tu a envoaient ".count($files)." mape alors que fallait 1 §";
                            }
                        } else {
                            echo "Veuillez placer les éléments de storyboard dans le fichier .osu après [Event] et non dans un fichier .osb. Merci.";
                        }
                    } else {
                        echo "trop dimage $nbimg, stp met dans un sous dossier!!";
                    }
                    shell_exec("rm -rf " . $workdir . "*");
                } else {
                    echo "File size: " . $_FILES['file']['size'] . ". Max size : " . ini_get('upload_max_filesize') . "<br> (or cannot move from ".$_FILES['file']['tmp_name']." to $uploadfile because ".$_FILES["file"]["error"] . " more info ";
                    print_r($_FILES["file"]);
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
        }else {
            ?>
        on peut pas uploader voilà c comme ça
        <?php
        }
        ?>
        <img class="satourn" src="http://cdn.shopify.com/s/files/1/1061/1924/products/Poop_Emoji_7b204f05-eec6-4496-91b1-351acc03d2c7_grande.png?v=1480481059" width="70px" height="40px"></div>
    <div class="download satourn"><img class="satourn" src="http://cdn.shopify.com/s/files/1/1061/1924/products/Poop_Emoji_7b204f05-eec6-4496-91b1-351acc03d2c7_grande.png?v=1480481059" width="70px" height="40px">
        <?php
        if ($packdownload) {
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
    if (isset($_SESSION['usr_logged']) && $_SESSION['usr_logged'] && ($_SESSION["usr_name"] == "Cunu" || $_SESSION["usr_name"] == "Adri")) {
        ?>
        <div class="cunupanel satourn">
            bg

            <br>
            difficulter eksistantes :<br>
            <?php
            $dir = '/var/osu/shitfest/' . $full . '/pack/';
            $files = glob("$dir*");

            foreach ($files as $diff) {
                $p1 = explode(".", $diff);
                if (trim($p1[count($p1) - 1]) == 'osu') {
                    $ps = explode("/",$p1[0]);
                    echo $ps[count($ps)-1] . "<br>";
                }
            }
            ?>
        </div>
        <div class="cunupanelDEUX satourn">
            actions insenser
            <form>
                numero edition <input type="text" name="number" id="number" value="<?php echo $editionNumber ?>"><br>
                saison edition <input type="text" name="saison" id="saison" value="<?php echo $saisonNumber ?>"><br>
                titre edition <input type="text" name="titre" id="titre" value="<?php echo $editionTitre ?>"><br>
                creatore <input type="text" name="creator" id="creator" value="<?php echo $packCreator ?>"><br>
                arist <input type="text" name="artist" id="artist" value="<?php echo $packArtist ?>"><br>
                <input type="hidden" name="ACTION_INSENSER" value="0">
                <input type="submit" name="s" value="metre a joure">
            </form>
        <form>
                <input type="hidden" name="ACTION_INSENSER" value="1">
                <?php echo $packopen ? "interdir" : "autoriser" ?> upload ?<input type="submit" name="open" id="open" value="<?php echo $packopen ? 0 : 1 ?>"><br>
                </form>
            <form>
                <input type="hidden" name="ACTION_INSENSER" value="2">
                <?php echo $packdownload ? "interdir" : "autoriser" ?> download ?<input type="submit" name="download" id="download" value="<?php echo $packdownload ? 0 : 1 ?>"><br>
            </form>
            <form>
                <input type="hidden" name="ACTION_INSENSER" value="4">
                <input type="hidden" name="SAISON" value="<?php echo $saisonNumber ?>">
                <input type="hidden" name="EDITION" value="<?php echo $editionNumber ?>">
                <input type="hidden" name="TITRE" value="<?php echo $editionTitre ?>">
                sauvegarder edition<input type="submit" name="save" id="save" value="oui"><br>
            </form>
            <form>
                <input type="hidden" name="ACTION_INSENSER" value="3">
                force download <input type="submit" name="fdownload" id="download" value="DL"><br>
            </form>

        </div>
        <?php
    } else {
        ?>
        <div class="cunupanel satourn">
            <br>
            ont deja uploder leur mape :<br>
            <?php
            $y_a_t_il_quelqu_un_qui_se_cache_dans_le_noir = false;           
            
            $dir = '/var/osu/shitfest/' . $full . '/pack/';
            $files = glob("$dir*");

            foreach ($files as $diff) {
                $p1 = explode(".", $diff);
                if (trim($p1[count($p1) - 1]) == 'osu') {
                    $ps = explode("/",$p1[0]);
                    $y_a_t_il_quelqu_un_qui_se_cache_dans_le_noir = true;
                    echo $ps[count($ps)-1] . "<br>";
                }
            }

            if(!$y_a_t_il_quelqu_un_qui_se_cache_dans_le_noir) {
                echo "persone depecher vous enculer";
            }
            ?>
        </div>
        <?php
    }
    ?>
    <div style="display:flex" display="💪">
        <a href="https://osu.ppy.sh/s/960544"><img class="satourn" class="satourn" src="http://cdn.shopify.com/s/files/1/10 61/1924/products/Poop_Emoji_7b204f05-eec6-4496-91b1-351acc03d2c7_grande.png?v=1480481059"/></a>
        <a href="CUNU OMGFDOMG PACK 2 WHEN"><img class="satourn" class="satourn" src="PUTAIN"/></a>
        <a href="https://osu.ppy.sh/s/977678"><img class="satourn" class="satourn" src="https://cdn.drawception.com/images/panels/2018/2-13/AypBaBreLw-2.png"/></a>
        <a href="https://osu.ppy.sh/s/991882"><img class="satourn" class="satourn" src="no_image_edition" title="tamer" alt="no image edition"/></a>
        <a href="https://osu.ppy.sh/s/999478"><img class="satourn" class="satourn" src="https://i.ytimg.com/vi/5QvgLlFyeok/hqdefault.jpg"/></a>
        <a href="https://osu.ppy.sh/s/1010740"><img class="satourn" class="satourn" src="https://b.ppy.sh/thumb/1010740l.jpg?update=2019-07-28%2018:11:32"/></a>
        <a href="https://osu.ppy.sh/s/1014492"><img class="satourn" class="satourn" src="https://www.sciencesetavenir.fr/assets/img/2016/06/23/cover-r4x3w1000-5c4095e068d41-pieds.jpg2"/></a>
		<a href="https://osu.ppy.sh/beatmapsets/1014492#mania/2123225"><img class="satourn" class="satourn" src="./RESSOURCE(image par exemple)(mais pas que)/lvmp.png"/></a>
		<a href="https://osu.ppy.sh/beatmapsets/1023149#mania/2140274"><img class="satourn" class="satourn" src="./RESSOURCE(image par exemple)(mais pas que)/fr.png"/></a>
		<a href="https://osu.ppy.sh/beatmapsets/1045830#mania/2186309"><img class="satourn" class="satourn" src="./RESSOURCE(image par exemple)(mais pas que)/impossible.png"/></a>
		<a href="https://osu.ppy.sh/beatmapsets/1080536#mania/2260617"><img class="satourn" class="satourn" src="./RESSOURCE(image par exemple)(mais pas que)/nnno.png"/></a>
    </div>
    <div style="display:flex" display="💪💪">
        <a href="https://osu.ppy.sh/beatmapsets/1135508#mania/2371504"><img class="satourn" class="satourn" src="./RESSOURCE(image par exemple)(mais pas que)/coronq.png"/></a>
        <a href="https://osu.ppy.sh/beatmapsets/1153022#mania/2406567"><img class="satourn" class="satourn" src="./RESSOURCE(image par exemple)(mais pas que)/ww.png"/></a>
        <a href="https://osu.ppy.sh/beatmapsets/1178088#mania/2456906"><img class="satourn" class="satourn" src="./RESSOURCE(image par exemple)(mais pas que)/violen.png"/></a>
        <a href="https://osu.ppy.sh/beatmapsets/1217654#mania/2533477"><img class="satourn" class="satourn" src="./RESSOURCE(image par exemple)(mais pas que)/amour.png"/></a>
        <a href="https://osu.ppy.sh/beatmapsets/1232712#mania/2562740"><img class="satourn" class="satourn" src="./RESSOURCE(image par exemple)(mais pas que)/ete.png"/></a>
        <a href="https://osu.ppy.sh/beatmapsets/1276334#mania/2651804"><img class="satourn" class="satourn" src="./RESSOURCE(image par exemple)(mais pas que)/olalasacommens.png"/></a>
        <a href="https://osu.ppy.sh/beatmapsets/1290473#mania/2678835"><img class="satourn" class="satourn" src="./RESSOURCE(image par exemple)(mais pas que)/spook.png"/></a>
        <a href="https://existe.pas.com"><img class="satourn" class="satourn" src=""/>?</a>
        <a href="https://osu.ppy.sh/beatmapsets/1510753#mania/3093655"><img class="satourn" class="satourn" src="./RESSOURCE(image par exemple)(mais pas que)/pl.png"/></a>
        <a href="https://osu.ppy.sh/beatmapsets/1627463#mania/3322550"><img class="satourn" class="satourn" src="./RESSOURCE(image par exemple)(mais pas que)/tp.png"/></a>
    </div>
    <div style="display:flex" display="💪💪💪">
		<a href="https://osu.ppy.sh/beatmapsets/1681940#mania/3436318"><img class="satourn" class="satourn" src="./RESSOURCE(image par exemple)(mais pas que)/0221.png"/></a>
    </div>
</body>

<script>
    import('https://code.jquery.com/jquery-3.4.1.js').then((c_inporté) => {
        (console.log(c_inporté))

        var c_pa_es6 = $('.satourn')

        for (i = 0; i < $('.satourn').length / 2; i++)     /**8=====D**/ {

            let un_string_serré_sur_patouz = 'rotate(' //es6 la so6

            if (i % 2) {
                un_string_serré_sur_patouz += '-';
            }

            un_string_serré_sur_patouz += (Math.random() * 0.1).toFixed(2);

            un_string_serré_sur_patouz += 'turn)'

            bravo_vou_ete_leureu_vinkeur_pour_tourné_la_rou = c_pa_es6.eq(Math.floor(Math.random() * $('.satourn').length));
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;
            ;

            bravo_vou_ete_leureu_vinkeur_pour_tourné_la_rou.css('transform', un_string_serré_sur_patouz)


        }





    })



</script>