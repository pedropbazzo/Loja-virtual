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

    $sub = $prod['price'] * $qtd;
    $total += $prod['price'] * $qtd;

endforeach;
endif;

if(!empty($_GET["catg"])):
$catg = $_GET["catg"];
endif;

if(!empty($_GET["subcatg"])):
$subcatg = $_GET["subcatg"];
endif;

if(!empty($_GET["prod"])):
$prod = $_GET["prod"];
endif;
?>

<!doctype html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=1300px,initial-scale=-3">
<title>Loja Exemplo <?php echo'- ARRUMAR O NOME AQUIIIIIIIIIIIIIIIIIIIII'; ?></title>
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
	  auto: false,
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
#menu_subcatg li a#<?php echo $subcatg ?>{color:#000 !important;/*font-weight: bold !important;*/}
.bxslider, .bx-pager-item {display:none !important;}
</style>
<?php
endif;
if(!empty($prod)):
?>
<style>
.bxslider, .bx-pager-item {display:none !important;}
</style>
<?php
endif;
?>

</head>
<body>
<header>
    <?php include("header.php"); ?>
</header>
<section>
    	<ul class="bxslider">
        <?php
        $banners = $con->prepare("SELECT * FROM banners WHERE catg='$catg' ORDER BY id DESC");
        $banners->execute();
        while($banner = $banners->fetch(PDO::FETCH_ASSOC)):
        ?>
        <a href="<?php echo $url; ?>/<?php echo $banner["catg"]; ?>"><li style="height:350px;background:#1E90FF url(<?php echo $url; ?>/img/banners/<?php echo''.$banner["img"].''; ?>) 50% 50% no-repeat;background-size:cover;"></li></a>
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

            echo'<li><a id="'.$subcatgf["subcatg"].'" href="'.$url.'/'.$subcatgf["catg"].'/'.$subcatgf["subcatg"].'">'.$subcatgf["name"].'</a></li>';

            endwhile;
            endif;

            endwhile;
            ?>

        </ul>
        
        <div id="produtos">
        
        <!--<p>PRODUTOS</p>-->
        
        <?php
        if( (!empty($catg)) AND (empty($subcatg)) AND (empty($prod)) ):
    		$prods = $con->prepare("SELECT * FROM produtos WHERE catg='$catg' ORDER BY ord ASC");
            $prods->execute();
            while($prod = $prods->fetch(PDO::FETCH_ASSOC)):
    		?> 

            <div id="prod">
            <a href="<?php echo $prod["catg"].'/'.$prod["subcatg"].'/'.$prod["id"].''; ?>"><figure style="background:#fff url(img/products/<?php echo''.$prod["photo"].''; ?>) top center no-repeat;background-size:100% auto;"></figure></a>
            <h3><?php echo''.$prod["name"].''; ?></h3>
            <!--<a href="<?php echo $prod["catg"].'/'.$prod["subcatg"].'/'.$prod["id"].''; ?>"><span><?php echo $prod["catg"]; ?></span></a>-->
            <h3>R$ <?php echo str_replace('.',',',''.$prod["price"].''); ?></h3>
            <?php
                $price = ''.$prod["price"].'';
                $parc = ''.$prod["parc"].'';
                $parcel = $price / $parc;
             
             if($parc==1):
                echo'<span>no boleto bancário</span>';
            else:
                echo'<span>à vista ou '.$prod["parc"].'x de R$ '.number_format($parcel, 2, ',','.').' no cartão de crédito</span>';
            endif;
            
            ?>
            <form id="buy" action="<?php echo''.$prod["catg"].'/'.$prod["subcatg"].'/'.$prod["id"].''; ?>" method="POST">
                <input type="submit" value="MAIS DETALHES" id="buy"/>
            </form>
        </div>

            <?php
    		endwhile;
        elseif( (!empty($catg)) AND (!empty($subcatg)) AND (empty($prod)) ):
            $prods = $con->prepare("SELECT * FROM produtos WHERE catg='$catg' AND subcatg='$subcatg' ORDER BY ord ASC");
            $prods->execute();
            while($prod = $prods->fetch(PDO::FETCH_ASSOC)):
            ?> 

            <div id="prod">
            <a href="<?php echo $prod["subcatg"].'/'.$prod["id"]; ?>"><figure style="background:#fff url(../img/products/<?php echo''.$prod["photo"].''; ?>) top center no-repeat;background-size:100% auto;"></figure></a>
            <h3><?php echo''.$prod["name"].''; ?></h3>
            <!--<a href="<?php echo $prod["subcatg"].'/'.$prod["id"]; ?>"><span><?php echo''.$prod["catg"].''; ?></span></a>-->
            <h3>R$ <?php echo str_replace('.',',',''.$prod["price"].''); ?></h3>
            <?php
                $price = ''.$prod["price"].'';
                $parc = ''.$prod["parc"].'';
                $parcel = $price / $parc;
             
             if($parc==1):
                echo'<span>no boleto bancário</span>';
            else:
                echo'<span>à vista ou '.$prod["parc"].'x de R$ '.number_format($parcel, 2, ',','.').' no cartão de crédito</span>';
            endif;
            
            ?>
            <form id="buy" action="<?php echo''.$prod["subcatg"].'/'.$prod["id"].''; ?>" method="POST">
                <input type="submit" value="MAIS DETALHES" id="buy"/>
            </form>
            </div>

            <?php
            endwhile;
        elseif( (!empty($catg)) AND (!empty($subcatg)) AND (!empty($prod)) ):
            $prods = $con->prepare("SELECT * FROM produtos WHERE catg='$catg' AND id='$prod'");
            $prods->execute();
            while($prod = $prods->fetch(PDO::FETCH_ASSOC)):
            ?> 

            <div id="details">
            <figure>
            <div class="sp-wrap">
            
            <?php
            if(!empty($prod["photo"])):
            ?>
            <a href="<?php echo $url; ?>/img/products/<?php echo''.$prod["photo"].''; ?>"><img src="<?php echo $url; ?>/img/products/<?php echo''.$prod["photo"].''; ?>" alt=""></a>
            <?php
            endif;

            if(!empty($prod["photo2"])):
            ?>
            <a href="<?php echo $url; ?>/img/products/<?php echo''.$prod["photo2"].''; ?>"><img src="<?php echo $url; ?>/img/products/<?php echo''.$prod["photo2"].''; ?>" alt=""></a>
            <?php
            endif;
            
            if(!empty($prod["photo3"])):
            ?>
            <a href="<?php echo $url; ?>/img/products/<?php echo''.$prod["photo3"].''; ?>"><img src="<?php echo $url; ?>/img/products/<?php echo''.$prod["photo3"].''; ?>" alt=""></a>
            <?php
            endif;
            ?>

            </div>
            </figure>
            
            <div class="xxx" style="width:660px;float:left;margin:10px 10px 0 10px;">

            <h3 style="color:#1E90FF;">Mais detalhes</h3>
            <p><?php echo''.$prod["description"].''; ?></p><br><br>




            
            <p style="line-height:82px;">Comentários:</p>

            <?php
            $comments = $con->prepare("SELECT * FROM comentarios WHERE id_prod=:id_prod ORDER BY id ASC");
            $comments->BindValue(":id_prod",$prod["id"]);
            $comments->execute();
            
            if($comments):
            ?>
            
            <?php
            while($comment=$comments->fetch(PDO::FETCH_ASSOC)):
            ?>
            
            <div id="asks">
                <h3><?php echo''.$comment["name"].''; ?></h3>
                <p><?php echo''.$comment["message"].''; ?></p>
            </div>
            
            <?php
            endwhile;
            else:
            endif;
            ?>
            <p style="line-height:82px;">Deixe o seu comentário:</p>
            
            </div>
            
            <form id="comments" action="<?php echo $url; ?>/settings/comments_register.php" method="POST">
                <input type="hidden" name="id_prod" value="<?php echo $prod["id"]; ?>"/>
                <input type="text" name="name" placeholder="Seu nome..."/>
                <input type="text" name="email" style="margin-left:10px;" placeholder="Seu email..."/>
                <textarea name="message" placeholder="Deixe sua pergunta ou mensagem..."></textarea>
                <input type="submit" name="comments_register" value="COMENTAR"/>
            </form>
        
        </div>
        
        <aside>

            <div id="" style="width:280px;float:left;padding:10px;">
                <h2><?php echo $prod["name"]; ?></h2>
            </div>
                         
            <div id="" style="width:280px;float:left;padding:10px;">
            <span><?php echo $prod["caption"]; ?></span>
            <h3>R$ <?php echo str_replace('.',',',''.$prod["price"].''); ?></h3>
            <?php
                $price = $prod["price"];
                $parc = $prod["parc"];
                $parcel = $price / $parc;
             
             if($parc==1):
                echo'<span>no boleto bancário</span>';
            else:
                echo'<span>à vista ou '.$prod["parc"].'x de R$ '.number_format($parcel, 2, ',','.').' no cartão de crédito</span>';
            endif;
            
            ?>
            </div>
        
            <hr/>
            
            <form id="checkout" action="<?php echo $url; ?>/carrinho" method="POST">
                <input type="hidden" name="acao" value="add">
                <input type="hidden" name="id" value="<?php echo $prod["id"]; ?>">

                <input type="submit" name="checkout" value="ADICIONAR AO CARRINHO">
            </form>

        </aside>

            <?php
            endwhile;
        endif;
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