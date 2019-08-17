<?php

// wa une database mdr
class Database {

    private $pdo;
    private $result;

    public function __construct($db = "shitfest"){
        try {
            $this->pdo = new PDO("mysql:host=localhost;dbname=$db;charset=utf8", "shitfest", trim(file_get_contents("/var/osu/shitfestpass"))); /// mdr c securiser
        } catch(Exception $e){
            die("Connexion à la base de données ECHOUAGE de b.");
        }
    }

    public function query($sql, $opt = null, $mode = PDO::FETCH_BOTH){
        try {
            $query = $this->pdo->prepare($sql);
            $query->execute($opt);
            $this->result = $query->fetchAll($mode);
            return true;
        } catch(Exception $e){
            return false;
        }
    }

    public function getResult($i = null){
        if($i == null){
            return $this->result;
        }
        if(isset($this->result[$i])){
            return $this->result[$i];
        }
        return false;
    }

    public function fast($sql, $opt = null, $i = null, $mode = PDO::FETCH_BOTH){
        $this->query($sql, $opt, $mode);
        return $this->getResult($i);
    }
}
$datadonnee = new Database();
        session_start();

    if (isset($_SESSION['usr_logged']) && $_SESSION['usr_logged'] && ($_SESSION["usr_name"] == "Cunu" || $_SESSION["usr_name"] == "Adri")) {
        if(isset($_REQUEST["ACTION_INSENSER"])) {
            switch($_REQUEST["ACTION_INSENSER"]) {
                case "0":
                    // metre a jour les misajour
                    $doner = [
                        ":n" => $_REQUEST["number"],
                        ":t" => $_REQUEST["titre"],
                        ":m" => $_REQUEST["creator"],
                        ":p" => $_REQUEST["artist"],
                    ];
                    $datadonnee->fast("UPDATE current_edition SET number = :n, titre = :t, creator = :m, artist = :p", $doner);
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
            }
        }
    }
$INFORMATION = $datadonnee->fast("SELECT * FROM current_edition")[0]; // enorme issime
$editionNumber = $INFORMATION["number"];
$editionTitre = $INFORMATION["titre"];
$editionTexte = "$editionTitre Edition";
$packArtist = "Various Artists";
$packTitle = "FrenchShitFest Paquetage $editionNumber ($editionTexte)";
$packCreator = $INFORMATION["creator"];
$edition = "s$editionNumber";

$packopen = $INFORMATION["open"] == 1;
$packdownload = $INFORMATION["download"] == 1;

if ((isset($_REQUEST["pack"]) && $packdownload) || (isset($_REQUEST["ACTION_INSENSER"]) && isset($_SESSION['usr_logged']) && $_SESSION['usr_logged'] && ($_SESSION["usr_name"] == "Cunu" || $_SESSION["usr_name"] == "Adri") && $_REQUEST["ACTION_INSENSER"] == 3))  {
    $file_name = "$packArtist - $packTitle ($packCreator).osz";

    if (!is_dir("/var/osu/shitfest/$edition")) {
        mkdir("/var/osu/shitfest/$edition");
    }
    if (!is_dir("/var/osu/shitfest/$edition/tmp")) {
        mkdir("/var/osu/shitfest/$edition/tmp");
    }
    if (!is_dir("/var/osu/shitfest/$edition/pack")) {
        mkdir("/var/osu/shitfest/$edition/pack");
    }

    $dst_file = "/var/osu/shitfest/$edition/tmp/$file_name";
    $cmd = "cd '/var/osu/shitfest/$edition/pack/';zip -0r '$dst_file' '.'";
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
        echo "FrenchShitFest n°$editionNumber $editionTexte";
        ?>
        <img class="satourn" src="http://cdn.shopify.com/s/files/1/1061/1924/products/Poop_Emoji_7b204f05-eec6-4496-91b1-351acc03d2c7_grande.png?v=1480481059" width="60px" height="40px">
    </div>
    <div class="upload satourn"><img class="satourn" src="http://cdn.shopify.com/s/files/1/1061/1924/products/Poop_Emoji_7b204f05-eec6-4496-91b1-351acc03d2c7_grande.png?v=1480481059" width="70px" height="40px">
        <?php

        if($packopen) {
        if (isset($_FILES['file'])) {
            if ($_FILES['file']['error']) {
                echo "error " . $_FILES['file']['error'];
            } else {
                $uploaddir = '/var/osu/shitfest/' . $edition . '/tmp/';
                $uploadfile = $uploaddir . basename($_FILES['file']['name']);
                shell_exec("rm \"" . $uploaddir . "*\"");

                if (move_uploaded_file($_FILES['file']['tmp_name'], $uploadfile)) {

                    shell_exec("unzip \"" . $uploadfile . "\" -d $uploaddir");

                    $files = glob("$uploaddir*.osu");
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
                    echo "File size: " . $_FILES['file']['size'] . ". Max size : " . ini_get('upload_max_filesize') . "<br> (or cannot move to $uploadfile";
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
        <div class="cunupanelDEUX satourn">
            actions insenser
            <form>
                numero edition <input type="text" name="number" id="number" value="<?php echo $editionNumber ?>"><br>
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
                    $y_a_t_il_quelqu_un_qui_se_cache_dans_le_noir = true;
                    if(explode("-", explode("]", $n)[0])[0] != "") {
                    echo explode("-", explode("]", $n)[0])[0] . "<br>";
                    } else {
                        echo explode("-", explode("]", $n)[0])[0] .explode("-", explode("]", $n)[0])[1] . "<br>";
                    }
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
        <a href="https://osu.ppy.sh/s/960544"><img class="satourn" class="satourn" src="http://cdn.shopify.com/s/files/1/1061/1924/products/Poop_Emoji_7b204f05-eec6-4496-91b1-351acc03d2c7_grande.png?v=1480481059"/></a>
        <a href="CUNU OMGFDOMG PACK 2 WHEN"><img class="satourn" class="satourn" src="PUTAIN"/></a>
        <a href="https://osu.ppy.sh/s/977678"><img class="satourn" class="satourn" src="https://cdn.drawception.com/images/panels/2018/2-13/AypBaBreLw-2.png"/></a>
        <a href="https://osu.ppy.sh/s/991882"><img class="satourn" class="satourn" src="no_image_edition" title="tamer" alt="no image edition"/></a>
        <a href="https://osu.ppy.sh/s/999478"><img class="satourn" class="satourn" src="https://i.ytimg.com/vi/5QvgLlFyeok/hqdefault.jpg"/></a>
        <a href="https://osu.ppy.sh/s/1010740"><img class="satourn" class="satourn" src="https://b.ppy.sh/thumb/1010740l.jpg?update=2019-07-28%2018:11:32"/></a>
        <a href="https://osu.ppy.sh/s/1014492"><img class="satourn" class="satourn" src="https://www.sciencesetavenir.fr/assets/img/2016/06/23/cover-r4x3w1000-5c4095e068d41-pieds.jpg2"/></a>
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
