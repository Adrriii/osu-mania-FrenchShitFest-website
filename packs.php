
<style>

body {
	background-image: linear-gradient(blue, rgb(139, 239, 0));
	font-size: 150%;
}

</style>
<head>
	<title>ShitManiaFest Packs</title>
</head>
<?php

include_once('database.php');
$datadonnee = new Database();

$packs = $datadonnee->fast("SELECT * FROM `pack` order by `saison` DESC, `edition` DESC");

$s = null;
foreach($packs as $pack) {
	if($s != $pack["saison"]) {
		$s = $pack["saison"];
		echo "<br><h3>Saison ".$pack["saison"]."</h3><br>";
	}
	echo $pack["edition"]. " > ". ($pack["lien"] == null ? "DL" : "<a href='".$pack["lien"]."' target='_blank'>DL</a>")." - ".$pack["titre"] . " Édition<br>";
}
