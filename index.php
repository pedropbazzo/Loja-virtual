<?php
// Inicia uma sessão.
session_start();
// Cria conexão com o banco.
include("config.php");
// Chama função de conexão do arquivo 'config.php' acima.
$con=cnt();
// Chama função que direciona para a página restrita se já estiver logado (com uma SESSION aberta).
//$stay=in();
if(!empty($_SESSION['carrinho'])):
foreach($_SESSION['carrinho'] as $id => $qtd):

    $prods = $con->prepare("SELECT * FROM produtos WHERE id=$id");
    $prods->execute();
    $prod = $prods->fetch(PDO::FETCH_ASSOC);

    $name = $prod['name'];
    $price = $prod['price'];

    $sub = $prod['price'] * $qtd;

    $total += $prod['price'] * $qtd;

endforeach;
else:

endif;
?>
<!doctype html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=1300px,initial-scale=-3">
<title>Loja Exemplo</title>
<link rel="stylesheet" href="css/body.css"/>
<link href="//netdna.bootstrapcdn.com/font-awesome/4.1.0/css/font-awesome.min.css" rel="stylesheet">
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
    <ul class="bxslider">

        <?php
        $banners = $con->prepare("SELECT * FROM banners ORDER BY id DESC");
        $banners->execute();
        while($banner = $banners->fetch(PDO::FETCH_ASSOC)):
        ?>
        <a href="<?php echo $url; ?>/<?php echo $banner["catg"]; ?>"><li style="height:350px;background:#1E90FF url(img/banners/<?php echo $banner["img"]; ?>) 50% 50% no-repeat;background-size:cover;"></li></a>
        <?php
        endwhile;
		?>

    </ul>

	<div class="container">

    
        <ul id="menu_subcatg">

            <?php
            $categorias = $con->prepare("SELECT * FROM categorias ORDER BY name ASC");
            $categorias->execute();
            while($catgf = $categorias->fetch(PDO::FETCH_ASSOC)):

            echo'<li><a href="'.$url.'/'.$catgf["catg"].'" style="color:#1E90FF;">'.$catgf["name"].'</a></li>';

            $catgf = $catgf["catg"];

            if(!empty($catgf)):
            $subcategorias = $con->prepare("SELECT * FROM subcategorias WHERE catg='$catgf' ORDER BY subcatg ASC");
            $subcategorias->execute();
            while($subcatgf = $subcategorias->fetch(PDO::FETCH_ASSOC)):

            echo'<li><a href="'.$url.'/'.$subcatgf["catg"].'/'.$subcatgf["subcatg"].'">'.$subcatgf["name"].'</a></li>';

            endwhile;
            endif;

            endwhile;
            ?>

        </ul>
        
        <div id="produtos">
        <?php
		$prods = $con->prepare("SELECT * FROM produtos ORDER BY ordh DESC LIMIT 12");
        $prods->execute();
        while($prod = $prods->fetch(PDO::FETCH_ASSOC)):
        ?>



        <div id="prod">
            <a href="<?php echo $prod["catg"].'/'.$prod["subcatg"].'/'.$prod["id"]; ?>"><figure style="background:#fff url(img/products/<?php echo''.$prod["photo"].''; ?>) top center no-repeat;background-size:100% auto;"></figure></a>
            <h3><?php echo $prod["name"]; ?></h3>
            <!--<a href="<?php echo $prod["catg"].'/'.$prod["subcatg"].'/'.$prod["id"]; ?>"><span><?php echo''.$prod["catg"].''; ?></span></a>-->
            <h3>R$ <?php echo str_replace('.',',',''.$prod["price"].''); ?></h3>
            <?php
            	$price = ''.$prod["price"].'';
				$parc = ''.$prod["parc"].'';
				$parcel = $price / $parc;
			 
             if($parc==1):
				echo'<span>no boleto bancário</span>';
            else:
				echo'<span>'.$prod["parc"].'x de R$ '.number_format($parcel, 2, ',','.').' no cartão de crédito</span>';
			endif;
            
            ?>
            <form id="buy" action="<?php echo $prod["catg"].'/'.$prod["subcatg"].'/'.$prod["id"]; ?>" method="POST">
            	<input type="submit" value="MAIS DETALHES" id="buy"/>
        	</form>
        </div>


        
        <?php
		endwhile;
        ?>

        </div>


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