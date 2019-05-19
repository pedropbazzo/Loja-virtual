<?php
// Inicia uma sessão.
session_start();
// Cria conexão com o banco.
include("config.php");
// Chama função de conexão do arquivo 'config.php' acima.
$con=cnt();
// Chama função que direciona para a página restrita se já estiver logado (com uma SESSION aberta).
//$stay=in();
$catg = $_GET["catg"];
$subcatg = $_GET["subcatg"];
$prod = $_GET["prod"];
?>
<!doctype html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=1300px,initial-scale=-3">
<title>Loja Exemplo - Como Comprar</title>
<link rel="stylesheet" href="<?php echo $url; ?>/css/body.css"/>
<link href="//netdna.bootstrapcdn.com/font-awesome/4.1.0/css/font-awesome.min.css" rel="stylesheet">
<!-- jQuery library (served from Google) -->
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
<!-- bxSlider Javascript file -->
<script src="<?php echo $url; ?>/js/jquery.bxslider.min.js"></script>
<!-- bxSlider CSS file -->
<link rel="stylesheet" href="<?php echo $url; ?>/css/jquery.bxslider.css"/>
<!--[if lt IE 9]>
<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
<![endif]-->
<!-- CSS ====================================================== -->
<link rel="stylesheet" href="<?php echo $url; ?>/css/smoothproducts.css">
<!-- Demo CSS (don't use) -->
<script type="text/jscript">
$(document).ready(function(){
  $('.bxslider').bxSlider({
	  auto: true,
	  captions: true,
	  adaptiveHeight: true,
	  autoControls: true
	  });
});
</script>
<?php
if(!empty($catg)):
?>
<style>
#menu_catg li a#<?php echo $catg ?>{background:#999 !important;}
</style>
<?php
endif;
if(!empty($subcatg)):
?>
<style>
#menu_subcatg li a#<?php echo $subcatg ?>{color:#000 !important;font-weight: bold !important;}
</style>
<?php
endif;
?>
</head>
<body>
    <?php include_once("analyticstracking.php") ?>
<header>
    <?php include("settings/header.php"); ?>
</header>
<section>

	<div class="container">

        <ul id="menu_subcatg">

            <?php
            $categorias = $con->prepare("SELECT * FROM categorias ORDER BY name ASC");
            $categorias->execute();
            while($catgf = $categorias->fetch(PDO::FETCH_ASSOC)):

            echo'<li style="margin-top:15px;"><a href="'.$url.'/'.$catgf["catg"].'" style="color:#be3190;">'.$catgf["name"].'</a></li>';

            $catgf = $catgf["catg"];

            if(!empty($catgf)):
            $subcategorias = $con->prepare("SELECT * FROM subcategorias WHERE catg='$catgf' ORDER BY subcatg ASC");
            $subcategorias->execute();
            while($subcatgf = $subcategorias->fetch(PDO::FETCH_ASSOC)):

            echo'<li><a id="'.$subcatgf["subcatg"].'" href="'.$url.'/'.$subcatgf["catg"].'/'.$subcatgf["subcatg"].'">'.$subcatgf["name"].'</a></li>';

            endwhile;
            endif;

            endwhile;
            ?>

        </ul>
        
        <div id="produtos" style="margin:40px 0;">
        <h2>Como Comprar</h2><br>

        <?php 
        $estatics = $con->prepare("SELECT * FROM estaticas ORDER BY id DESC LIMIT 1");
        $estatics->execute();
        $estatic = $estatics->fetch(PDO::FETCH_ASSOC);

        echo '<p style="text-align:justify;">'.$estatic["comocomprar"].'</p>';
        ?>
        </div>
        

        
    </div>
</section>
<footer>
	<div class="container">
        <?php include("settings/footer.php"); ?>
    </div>
</footer>

<!-- JS ======================================================= -->
    <script type="text/javascript" src="<?php echo $url; ?>/js/smoothproducts.min.js"></script>
    <script type="text/javascript">
    /* wait for images to load */
    $(window).load(function() {
        $('.sp-wrap').smoothproducts();
    });
    </script>

</body>
</html>