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
<title>Loja Exemplo - Contato</title>
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
<style>
#info                                     {width:45.5%;float:left;margin-left: .5%}

form#contact                               {width:48%;padding:1%;position:relative;float:right;border-radius:3px;}
form#contact > input[type="text"]          {width:98%;height:60px;padding:1%;margin-bottom:10px;border-radius:3px;border:none;font-family:'open sans', arial, helvetica, sans-serif;font-size:100%;color:#000;}
form#contact > textarea                    {width:98%;height:90px;padding:1%;margin-bottom:10px;border-radius:3px;border:none;font-family:'open sans', arial, helvetica, sans-serif;font-size:100%;color:#000;}
form#contact > input[type="submit"]        {width:100%;height:80px;margin-bottom:10px;border-radius:3px;border:none;border-bottom:solid 5px #111;background:#444;font-family:'open sans', arial, helvetica, sans-serif;font-size:100%;color:#fff;}
form#contact > input[type="submit"]:hover  {cursor:pointer;background:#111;}
</style>
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
        
        <div id="produtos" style="margin-top:40px;">
        <h2>Fale Conosco</h2>

        <div id="info">
    
        <?php 
        $estatics = $con->prepare("SELECT * FROM estaticas ORDER BY id DESC LIMIT 1");
        $estatics->execute();
        $estatic = $estatics->fetch(PDO::FETCH_ASSOC);

        echo '<p>'.$estatic["quemsomos"].'</p>';
        ?>

        </div>


        <?php
    if(!isset($_POST["submit"]))
    {
    ?>

    <form id="contact" action="contato.php" method="POST">
    <input type="text" name="name" placeholder="Digite seu nome" />
    <input type="text" name="email" placeholder="Digite seu e-mail" />
    <input type="text" name="phone" placeholder="Digite seu telefone" />
    <input type="text" name="city" placeholder="Digite sua cidade" />
    <input type="text" name="uf" placeholder="Digite seu Estado" />

    <textarea name="text" placeholder="Como podemos ajudar?"></textarea>
    <input type="submit" name="submit" value="ENVIAR" />
    </form>

    <?php
    }
    else{
        
    //Just get the headers if we can or else use the SERVER global
    if ( function_exists( 'apache_request_headers' ) ) {
    $headers = apache_request_headers();
    }
    else {
    $headers = $_SERVER;
    }
    
    //Get the forwarded IP if it exists
    if ( array_key_exists( 'X-Forwarded-For', $headers ) && filter_var( $headers['X-Forwarded-For'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 ) ) {
    $the_ip = $headers['X-Forwarded-For'];
    }
    elseif ( array_key_exists( 'HTTP_X_FORWARDED_FOR', $headers ) && filter_var( $headers['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 ) ) {
    $the_ip = $headers['HTTP_X_FORWARDED_FOR'];
    }
    else {
    $the_ip = filter_var( $_SERVER['REMOTE_ADDR'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 );
    }
    
    $to = 'contato@stylusbeauty.com.br';
    $subject = 'Contato via site';
    $name = $_POST["name"];
    $email = $_POST["email"];
    $message = $_POST["text"];
    
    $text = '<p>FORMULÁRIO ENVIADO VIA: www.stylusbeauty.com.br</p>
    <p>Nome: '.$name.'
    <p>Email: '.$email.'
    <p>Telefone: '.$email.'
    <p>Cidade: '.$email.'
    <p>Estado: '.$email.'
    <p>Mensagem:<br /><br />'.$message.'</p>
    <hr>
    <p>IP: '.$the_ip.'</p>';
    
    $headers = "MIME-Version: 1.1\r\n";
    $headers .= "Content-type: text/html; charset=utf-8\r\n";
    $headers .= "From:" .$email. "\r\n"; // remetente
    $headers .= "Return-Path: $email \r\n"; // return-path
    
    mail($to,$subject,$text,$headers,$the_ip);
    echo "<p id='warm'>Mensagem enviada com sucesso! Obrigado.</p>";
    
    }
    ?>
        </div>
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