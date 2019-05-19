<?php
// Inicia uma sessão.
session_start();
// Cria conexão com o banco.
include("config.php");
// Chama função de conexão do arquivo 'config.php' acima.
$con=cnt();
// Chama função que direciona para a página restrita se já estiver logado (com uma SESSION aberta).
//$stay=in();
?>
<!doctype html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=1300px,initial-scale=-3">
<title>Loja Exemplo - <?php echo $_POST["find"] ?></title>
<link rel="stylesheet" href="css/body.css"/>
<!-- jQuery library (served from Google) -->
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
<!-- bxSlider Javascript file -->
<script src="js/jquery.bxslider.min.js"></script>
<!-- bxSlider CSS file -->
<link rel="stylesheet" href="css/jquery.bxslider.css"/>
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
</head>
<body>
<header>
    <?php include("header.php"); ?>
</header>
<section>


	<div class="container" style="margin-top:40px;">

	    
<?php

$prodstore=$_POST["wordpass"];
$pesquisa=$_POST['find'];

if(isset($pesquisa)&&!empty($prodstore)){
	
$stmt = $con->prepare("SELECT * FROM produtos WHERE name LIKE :letra");
$stmt->bindValue(':letra', '%'.$prodstore.'%', PDO::PARAM_STR);
$stmt->execute();
$resultados = $stmt->rowCount();



	if($resultados>=1){

	echo '<p style="width:100%;float:left;margin-top:40px;line-height:62px;">Existe(m) <b>'.$resultados.'</b> resultado(s) encontrado(s) para <b>'.$prodstore.'</b></p>';
	
		while($prod = $stmt->fetch(PDO::FETCH_ASSOC)){
			
			?>

        <div id="prod">
            <a href="<?php echo $url; ?>/<?php echo $prod["catg"].'/'.$prod["subcatg"].'/'.$prod["id"].''; ?>"><figure style="background:#fff url(img/products/<?php echo''.$prod["photo"].''; ?>) top center no-repeat;background-size:cover;"></figure></a>
            <h3><?php echo''.$prod["name"].''; ?></h3>
            <!--<a href="<?php echo $url; ?>/<?php echo $prod["catg"].'/'.$prod["subcatg"].'/'.$prod["id"].''; ?>"><span><?php echo $prod["name"]; ?></span></a>-->
            <h3>R$ <?php echo str_replace('.',',',''.$prod["price"].''); ?></h3>
            <?php
            	$price = $prod["price"];
				$parc = $prod["parc"];
				$parcel = $price / $parc;
			 
             if($parc==1):
				echo'<span>à vista no débito ou boleto bancário</span>';
            else:
				echo'<span>à vista ou '.$prod["parc"].'x de R$ '.number_format($parcel, 2, ',','.').' no cartão de crédito</span>';
			endif;
            
            ?>
            <!--<form id="detail" action="<?php echo''.$store["username"].''; ?>/<?php echo''.$prod["id"].''; ?>" method="POST">
            	<input type="submit" value="DETALHES"/>
            </form>-->
            <form id="buy" action="<?php echo $url; ?>/<?php echo $prod["catg"].'/'.$prod["subcatg"].'/'.$prod["id"].''; ?>" method="POST">
            	<input type="submit" value="MAIS DETALHES" id="buy"/>
        	</form>
        </div>

		<?php
		}

	}
	
	else{
	echo "<p>Não existem registros para <b>".$prodstore."</b>. <a href='javascript:history.back();'>Voltar à página anterior</a></p>";
	}



}
else{
echo "<p>Preencha o campo de pesquisa</p>";
}
?>








<figure id="banners">
        </figure>

</div>
</section>
<footer>
	<div class="container">
        <?php include("footer.php"); ?>
    </div>
</footer>
</body>
</html>