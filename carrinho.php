<?php
// Inicia uma sessão.
session_start();
// Cria conexão com o banco.
include("config.php");
// Chama função de conexão do arquivo 'config.php' acima.
$con=cnt();
// Chama função que direciona para a página restrita se já estiver logado (com uma SESSION aberta).
//$stay=in();
//print_r($_SESSION);

if(isset($_POST["calcular_frete"])):
    $_SESSION["cep"] = $_POST["cep_buyer"];
    $_SESSION["total"] = $_POST["total"];
endif;

/**/

$id = $_POST["id"];
$cep_store = '13506742';
$cep_buyer = $_SESSION["cep"];
$weight_total = $_POST["weight_total"];

if(!empty($_SESSION["cep"])):
    //info do produto com agentes de frete
    //$sqlunic = $con->prepare("SELECT id FROM compradores ORDER BY id DESC LIMIT 1");
    //$sqlunic->execute();
    //$unique = $sqlunic->fetch(PDO::FETCH_ASSOC);
    //$unique_id = $unique["id"] + 1;

    //$cep_store = $_POST["cep_store"];
    //$cep_buyer = $_POST["cep_buyer"];

    // fim da info do produto com agentes de frete

    $parametros = array();
        
    // Código e senha da empresa, se você tiver contrato com os correios, se não tiver deixe vazio.
    $parametros['nCdEmpresa'] = '13258427'; //13258427
    $parametros['sDsSenha'] = '11455213'; //11455213

    // CEP de origem e destino. Esse parametro precisa ser numérico, sem "-" (hífen) espaços ou algo diferente de um número.
    $parametros['sCepOrigem'] = $cep_store;
    $parametros['sCepDestino'] = $cep_buyer;

    // O peso do produto deverá ser enviado em quilogramas, leve em consideração que isso deverá incluir o peso da embalagem.
    $parametros['nVlPeso'] = '0.30';

    // O formato tem apenas duas opções: 1 para caixa / pacote e 2 para rolo/prisma.
    $parametros['nCdFormato'] = '1';

    // O comprimento, altura, largura e diametro deverá ser informado em centímetros e somente números
    $parametros['nVlComprimento'] = '16';
    $parametros['nVlAltura'] = '5';
    $parametros['nVlLargura'] = '11';
    $parametros['nVlDiametro'] = '0';

    // Aqui você informa se quer que a encomenda deva ser entregue somente para uma determinada pessoa após confirmação por RG. Use "s" e "n".
    $parametros['sCdMaoPropria'] = 'n';

    // O valor declarado serve para o caso de sua encomenda extraviar, então você poderá recuperar o valor dela. Vale lembrar que o valor da encomenda interfere no valor do frete. Se não quiser declarar pode passar 0 (zero).
    $parametros['nVlValorDeclarado'] = '0';

    // Se você quer ser avisado sobre a entrega da encomenda. Para não avisar use "n", para avisar use "s".
    $parametros['sCdAvisoRecebimento'] = 's';

    // Formato no qual a consulta será retornada, podendo ser: Popup – mostra uma janela pop-up | URL – envia os dados via post para a URL informada | XML – Retorna a resposta em XML
    $parametros['StrRetorno'] = 'xml';

    // Código do Serviço, pode ser apenas um ou mais. Para mais de um apenas separe por virgula.
    $parametros['nCdServico'] = '41211,40096';//40096 - anterior normal (40010)

    $parametros = http_build_query($parametros);
    $url_correios = 'http://ws.correios.com.br/calculador/CalcPrecoPrazo.aspx';
    $curl = curl_init($url_correios.'?'.$parametros);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $dados = curl_exec($curl);
    $dados = simplexml_load_string($dados);

    // fim da info do produto com agentes de frete

endif;





if(!isset($_SESSION['carrinho'])):

    $_SESSION['carrinho'] = array();

endif;

$acao = $_POST["acao"];
$id = $_POST["id"];

// adiciona produto
if(isset($acao)):

    if($acao == 'add'):
     
        if(!isset($_SESSION['carrinho'][$id])):
    
            $_SESSION['carrinho'][$id] = 1;
                 $_SESSION["cupom"] = null; 
                 $_SESSION["cep"] = null; 


        else:
    
            $_SESSION['carrinho'][$id] += 1;
    
        endif;
    
    endif;

endif;

// deleta produto do carrinho
if($_GET['acao'] == 'del'):

    $id = $_GET['id'];
    if(isset($_SESSION['carrinho'][$id])):

        unset($_SESSION['carrinho'][$id]);

    endif;

endif;


//ALTERAR QUANTIDADE
 if($_GET['acao'] == 'up'){
    if(is_array($_POST['prod'])){
       foreach($_POST['prod'] as $id => $qtd){
          $id  = intval($id);
          $qtd = intval($qtd);
          if(!empty($qtd) || $qtd <> 0){
             $_SESSION['carrinho'][$id] = $qtd;
             $_SESSION["cupom"] = null; 
             $_SESSION["cep"] = null; 
          }else{
             unset($_SESSION['carrinho'][$id]);
          }
       }
    }
 }

//session_destroy();

?>

<!doctype html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=1300px,initial-scale=-3">
<title>Loja Exemplo - Seu Carrinho</title>
<link rel="stylesheet" href="css/body.css"/>
<link href="//netdna.bootstrapcdn.com/font-awesome/4.1.0/css/font-awesome.min.css" rel="stylesheet">
<style>
h2{font: 22px 'open sans';line-height:82px;}
h4{font: 16px 'open sans';color:#aa0000;}
a{font: 14px 'open sans';color:#333;line-height:58px;}
#moip{width:90%;float:left;margin:5%;}
#moip fieldset{width:100%;float:left;margin-top:10px;border:none;}
/*#moip label{width:80%;float:left;padding:15px 5%;margin:5px 5%;font: 14px 'open sans';color:#333;border:none;}*/
#moip input[type="text"]{width:90%;float:right;padding:15px 5%;border:none;background: #efefef;line-height:22px;}
#moip input[type="submit"]{width:100%;float:right;padding:20px 0;border:none;background:#f50;color:#fff;}

#cep_buyer{width:100%;float:left;}
#cep_buyer fieldset:nth-of-type(1){width:60%;float:left;border:none;}
#cep_buyer fieldset:nth-of-type(2){width:40%;float:left;border:none;}
#cep_buyer input[type="text"]{width:92%;float:left;border:none;padding:10px 4%;background:#efefef;line-height:24px;}
#cep_buyer input[type="submit"]{width:100%;float:right;padding:10px 0;border:none;background:#f50;color:#fff;line-height:24px;}

#voltar{width:100%;float:right;padding:25px 0;text-align:center;background:#999;color:#fff;line-height:24px;}
#finalizar{width:100%;float:right;padding:25px 0;text-align:center;background:#f60;color:#fff;line-height:24px;}
input[id="finalizar"]{width:100%;float:right;padding:25px 0;text-align:center;background:#f60;color:#fff;line-height:24px;border:none;}
button[id="finalizar"]{width:100%;float:right;padding:25px 0;text-align:center;background:#f60;color:#fff;line-height:24px;border:none;}

#up{width:100%;float:left;}
#up fieldset{width:100%;float:left;border:none;}
input#up{width:80%;float:left;border:none;padding:15px 10px;background:#efefef;}
#up input[type="submit"]{width:100%;float:right;padding:15px 0;border:none;background:#f50;color:#fff;}

#foo{
    display:block;
    width:100%;height:100%;float:left;position:absolute;top:0;left:0;right:0;bottom:0;background:rgba(55,55,55,0.5);
}
#fopo{
    width:100%;height:100%;float:left;position:absolute;top:0;left:0;right:0;bottom:0;background:rgba(55,55,55,0.5);
}
</style>
<!-- jQuery library (served from Google) -->
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
<!-- bxSlider Javascript file -->
<script src="js/jquery.bxslider.min.js"></script>
<!-- bxSlider CSS file -->
<link rel="stylesheet" href="css/jquery.bxslider.css"/>
<!--javascript:history.back();-->
<script>
$(function() {
  $('a[href*=#]:not([href=#])').click(function() {
    if (location.pathname.replace(/^\//,'') == this.pathname.replace(/^\//,'') && location.hostname == this.hostname) {

      var target = $(this.hash);
      target = target.length ? target : $('[name=' + this.hash.slice(1) +']');
      if (target.length) {
        $('html,body').animate({
          scrollTop: target.offset().top - 50
        }, 700);
        return false;
      }
    }
  });
});
</script>
<script type="text/javascript">
<!--
    function toggle_visibility(id) {
       var e = document.getElementById(id);
       if(e.style.display == 'none')
          e.style.display = 'block';
       else
          e.style.display = 'none';
    }
//-->
</script>
<script type='text/javascript' src='http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js'>
</script>
</head>
<body>
<script type='text/javascript'>

$(document).ready(function() { 
  $('input[name=paym]').change(function(){
    $('#payment').submit();
  });
  });

</script>

<header style="height:45px;">
    <div class="container">    
        <div id="logo" style="height:45px;">
            <a href="<?php echo $url; ?>"><img  style="width:45px" src="img/logo.png"/></a>
        </div>
    </div>
</header>
<section style="top:48px">
    <div class="container">

















<h2><i class='fa fa-shopping-cart' style="font-size:38px;margin-right:10px;margin-bottom:-5px;"></i> Seu Carrinho de Compras:</h2>

<table width="100%" cellpadding="10" cellspacing="10" border="0" bgcolor="#ffffff">
<tbody>

<?php
if(count($_SESSION['carrinho']) == 0):
?>

    <tr><td width='100%' style="display:table;float:left;"><p style="width:100%;float:left;padding:25px 0;text-align:center;background:#fff;">Ops! Seu carrinho está vazio. <a href="<?php echo $url; ?>">Adicione produtos da loja</a>.</p></td></tr>

<?php    
else:

    echo'<form id="uping" action="?acao=up" method="POST">

            <tr>
                <td width=35%><h4>Produto(s):</h4></td>
                <td width=20%><h4>Quantidade:</h4></td>
                <td width=15%><h4>R$ Unidade:</h4></td>
                <td width=15%><h4>R$ Subtotal:</h4></td>
                <td width=15%><h4></h4></td>
            </tr>';
            

    foreach($_SESSION['carrinho'] as $id => $qtd):

    $prods = $con->prepare("SELECT * FROM produtos WHERE id=$id");
    $prods->execute();
    $prod = $prods->fetch(PDO::FETCH_ASSOC);

    $name = $prod['name'];
    $photo = $prod['photo'];
    $price = $prod['price'];
    
    $sub = $prod['price'] * $qtd;
    
    $subtotal += $prod['price'] * $qtd;
    
    $weight = $prod['weight'];
    $weight_sub = $prod['weight'] * $qtd;
    $weight_tota += $prod['weight'] * $qtd;
    ?>

        <tr>
            <td><img style="width:80px;float:left;" src="img/products/<?php echo $photo; ?>"/><p style="font: 14px 'open sans';color:#333;line-height:58px;"><?php echo $name ?></p></td>
            <td><input type="text" size="3" name="prod[<?php echo $id ?>]" value="<?php echo $qtd; ?>"></p></td>
            <td><p>R$ <?php echo number_format($price,2,",","."); ?></p></td>
            <td><p>R$ <?php echo number_format($sub,2,",","."); ?></p></td>
            <td><a href="?acao=del&id=<?php echo $id; ?>">REMOVER</a></td>
        </tr>
    
    <?php
    endforeach;
        
        echo '<tr>
                <td></td>
                <td><i style="float:left;margin: 17px 10px 0 0;" class="fa fa-refresh"></i><input type="submit" id="up" value="Atualizar Quantidade" /></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>';
        ?>

        </tbody>    
    </form>
    </table>


    
    <!-- total produtos -->
    <table width="100%" cellpadding="10" cellspacing="10" border="0" bgcolor="#1E90FF">
        <tbody>
            <tr>
                <td width="50%"></td>
                <td width="20%"></td>
                <td width="30%"><p style="color:#fff;">Subtotal: R$ <?php echo number_format($subtotal,2,",","."); ?></p></td>
            </tr>
        </tbody>
    </table>

    <!--============================================== FINAL DO CARRINHO ===================================================-->
















    <!--============================================== CUPOM DE DESCONTO ===================================================-->

    <?php   
    if(isset($_POST["calcular_desconto"])):

    $codigo_desconto = $_POST["codigo_desconto"];

    $desconto = $con->prepare("SELECT * FROM desconto WHERE codigo='$codigo_desconto' ORDER BY id DESC LIMIT 1");
    $desconto->execute();
    $desc = $desconto->fetch(PDO::FETCH_ASSOC);
        
    $percentual = $desc["desconto"] / 100.0; // 8%
    $cdesconto = $subtotal - ($percentual * $subtotal);
    
    $_SESSION["cupom"] = $cdesconto;
    endif;
    ?>

    <table width="100%" cellpadding="10" cellspacing="10" border="0" bgcolor="#ffffff">
    <tbody>
    <tr>
    <td width="70%"><p style="float:right;">Informe o seu <b>Cupom</b> ou <b>Vale-Compra</b> para calcular seu desconto:</p></td>
    <td width="30%">
    <form id="cep_buyer" action="carrinho" method="POST">
    <fieldset><input type="text" name="codigo_desconto" placeholder="Código de Desconto" /></fieldset>
    <fieldset><input type="submit" name="calcular_desconto" style="background:#999;" value="CALCULAR"></fieldset>
    </form>
    </td>
    </tr>
    </tbody>
    </table>

    <table width="100%" cellpadding="10" cellspacing="10" border="0" bgcolor="#1E90FF">
        <tbody>
            <tr>
                <td width="50%"></td>
                <td width="20%"></td>
                <td width="30%"><p style="color:#fff;">Subtotal com desconto: R$ <?php if(!empty($_SESSION["cupom"])): echo number_format($_SESSION["cupom"],2,",","."); else: echo number_format($subtotal,2,",","."); endif; ?></p></td>
            </tr>
        </tbody>
    </table>

    <!--============================================== FIM CUPOM DE DESCONTO ===================================================-->

























    <!--============================================== CÁLCULO DO FRETE ===================================================-->

    <table width="100%" cellpadding="10" cellspacing="10" border="0" bgcolor="#ffffff">
        <tbody>
        <tr>
        <td width="70%"><p style="float:right;">Informe o <b>CEP</b> do endereço de entrega para calcular o <b>valor do frete</b>:</p></td>
        <td width="30%">
        <form id="cep_buyer" action="carrinho" method="POST">
        <input type="hidden" name="total" value="<?php if(!empty($_SESSION["cupom"])): echo number_format($_SESSION["cupom"],2,",","."); else: echo number_format($subtotal,2,",",".");; endif; ?>"/>
        <input type="hidden" name="weight_total" value="<?php echo $weight_tota; ?>"/>
        <fieldset><input type="text" name="cep_buyer" placeholder="Informe o CEP da entrega" /></fieldset>
        <fieldset><input type="submit" name="calcular_frete" value="CALCULAR FRETE"></fieldset>
        </form>
        </td>
        </tr>
        </tbody>
        </table>

    <?php
    $rioclaro = substr($_SESSION["cep"], 0, 3);

    if($rioclaro == '135'):
    ?>

        <table width="100%" cellpadding="10" cellspacing="10" border="0" bgcolor="#ffffff">
        <tbody>
        <tr>
        <td width="50%"><img src="http://www.lucasdecarvalho.com.br/img/comodo.png" style="width:auto;height:auto;float:right;margin:0 auto;" /><br><p style="float:right;text-align:right;margin-top:30px;margin-right:10px;">Você está em um site seguro</p></td>
        <td width="20%"></td>
        <td width="30%">

        <h2>O frete para Rio Claro é grátis!</h2>
        
        <br><br>

        </td>
        </tr>
        </tbody>
        </table>


    <?php
    elseif(!empty($_SESSION["cep"])):    
    ?>

        <table width="100%" cellpadding="10" cellspacing="10" border="0" bgcolor="#ffffff">
        <tbody>
        <tr>
        <td width="50%"><img src="http://www.lucasdecarvalho.com.br/img/comodo.png" style="width:auto;height:auto;float:right;margin:0 auto;" /><br><p style="float:right;text-align:right;margin-top:30px;margin-right:10px;">Você está em um site seguro</p></td>
        <td width="20%"></td>
        <td width="30%">

        <?php 
        foreach($dados->cServico as $linhas) {
        if($linhas->Erro == 0) {

            echo '<form name="payment" id="payment" action="carrinho" method="POST">';
            if($linhas->Codigo == '41211'):
            $valor1 = $linhas->Valor;
            $valor1 = str_replace(",",".",$valor1);
            $valor_total1 = $valor1 + str_replace(",",".",$_SESSION["total"]);
            echo '<input type="radio" name="paym" style="float:left;margin-right:10px;margin-top:3px;" ';

            if($_POST["paym"] == 'pac'): 
                echo 'checked';
            endif;

            echo' value="pac"/>';
            


            echo '<p><img src="img/selpac.png" style="width:60px;float:left;margin-right:10px;margin-bottom:-5px;"/> R$ '.$linhas->Valor .'</p>';
            //echo '<p>Total: R$ '.number_format($valor_total1,2,",",".");
            //if($_POST["paym"] == 'pac'): echo' <b>(Esta opção foi selecionada)<b></p>'; endif;
            echo '<br><hr><br>';
            endif;

            if($linhas->Codigo == '40096'):
            $valor2 = $linhas->Valor;
            $valor2 = str_replace(",",".",$valor2);
            $valor_total2 = $valor2 + str_replace(",",".",$_SESSION["total"]);
            echo '<input type="radio" name="paym" style="float:left;margin-right:10px;margin-top:3px;" ';

            if($_POST["paym"] == 'sedex'): 
                echo 'checked';
            endif;

            echo' value="sedex"/>';

            echo '<p><img src="img/selsedex.png" style="width:60px;float:left;margin-right:10px;margin-top:3px;"/> R$ '.$linhas->Valor .'</p>';
            //echo '<p>Total: R$ '.number_format($valor_total2,2,",",".");
            //if($_POST["paym"] == 'sedex'): echo' <b>(Esta opção foi selecionada)<b></p>'; endif;
            endif;
            //echo 'Aproximadamente '.$linhas->PrazoEntrega.' dia(s) para entrega.</p>';           

        }else{
            echo '<p>'.$linhas->MsgErro.'</p>';
        }
        }
        
        echo'<input type="hidden" name="subbmit" value=""/>';

        echo'</form>';
        ?>

        </td>
        </tr>
        </tbody>
        </table>

    <?php
    endif;
    ?>

    <!--============================================== FIM DO CÁLCULO DO FRETE ===================================================-->







    <!--============================================== VALOR FINAL ===================================================-->

    <table width="100%" cellpadding="10" cellspacing="10" border="0" bgcolor="#1E90FF">
        <tbody>
            <tr>
                <td width="50%"></td>
                <td width="20%"></td>
                <td width="30%"><h2 style="color:#fff;"><?php if($_POST["paym"] == 'pac'): echo 'Total R$ '.number_format($valor_total1,2,",","."); elseif($_POST["paym"] == 'sedex'): echo 'Total R$ '.number_format($valor_total2,2,",","."); elseif($rioclaro == '135'): echo 'Total R$ '.str_replace(".",",",$_SESSION["total"]); else: echo 'Selecione o frete acima.'; endif; ?></h2></td>
            </tr>
        </tbody>
    </table>


    <?php
    if(isset($_POST["paym"]) OR !empty($rioclaro)):
    ?>
    <!-- decisão -->
    <br />
    <table width="100%" cellpadding="10" cellspacing="10" border="0">
    <tbody>
    <tr>
    <td width="50%"></td>
    <td width="20%">
    <a id="voltar" href="<?php echo $url; ?>">VOLTAR ÀS COMPRAS</a>
    </td>
    <td width="30%">
    <form id="payment2" action="carrinho" method="POST">
    <input type="hidden" name="total_geral" value="<?php if($_POST["paym"] == 'pac'): echo number_format($valor_total1,2,"","."); elseif($_POST["paym"] == 'sedex'): echo number_format($valor_total2,2,"","."); elseif($rioclaro == '135'): echo number_format($_SESSION["total"],2,"","."); else: echo 'erro :('; endif; ?>"/>
    <input type="hidden" name="valor" value="<?php echo $total_geral; ?>">
    <input type="submit" name="finalizar" id="finalizar" value="FINALIZAR COMPRA">
    </form>
    </td>
    </tr>
    </tbody>
    </table>
    <br>
    <?php
    else:
    ?>
    <!-- decisão -->
    <br />
    <table width="100%" cellpadding="10" cellspacing="10" border="0">
    <tbody>
    <tr>
    <td width="50%"></td>
    <td width="20%">
    
    </td>
    <td width="30%">
    <a id="voltar" href="<?php echo $url; ?>">VOLTAR ÀS COMPRAS</a>
    </td>
    </tr>
    </tbody>
    </table>
    <br>
    <?php
    endif;
    ?>

    <!--============================================== FIM VALOR FINAL ===================================================-->





                








    <!--============================================== FINALIZAR ===================================================-->

    <?php
    if(isset($_POST["finalizar"])):
        $valor = $_POST["valor"];
    ?>

        <div id="foo">
            <div style="display:table;width:600px;height:450px;margin: 0 auto;position:relative;top:20px;background:#fff;z-index:12;">
                <form id="moip" action="https://www.moip.com.br/PagamentoMoIP.do" method="POST">
                <input type="hidden" name="id_carteira" value="wcomn">
                    <input type="hidden" name="nome" value="Loja Exemplo">
                    <input type="hidden" name="valor" value="<?php echo $_POST["total_geral"]; ?>">
                    <input type="hidden" name="id_transacao" value="">
                    <input type="hidden" name="descricao" value="<?php
                foreach($_SESSION['carrinho'] as $id => $qtd):
                $prods = $con->prepare("SELECT * FROM produtos WHERE id=$id");
                $prods->execute();
                $prod = $prods->fetch(PDO::FETCH_ASSOC);
                $name = $prod['name'];
                $motivos = $qtd .' '. $prod['name'].'<br />';
                ?>
                <p><?php echo $motivos; ?></p>
                <?php
                endforeach;
                ?>">
                <input type="hidden" name="frete" value="1">
                <input type="hidden" name="peso_compra" value="<?php echo intval($weight_total); ?>">
                <fieldset><input type="text" name="pagador_nome" placeholder="Nome completo:" value="<?php echo $pagador_nome; ?>"></fieldset>
                <fieldset style="width:49.5%;"><input type="text" name="pagador_email" placeholder="E-mail:" value="<?php echo $pagador_email; ?>"></fieldset>
                <fieldset style="width:49.5%;float:right;"><input type="text" name="pagador_telefone" placeholder="Telefone:" value="<?php echo $pagador_telefone; ?>"></fieldset>
                <fieldset style="width:49.5%;"><input type="text" name="pagador_logradouro" placeholder="Av./Rua:" value="<?php echo $pagador_logradouro; ?>"></fieldset>
                <fieldset style="width:49.5%;float:right;"><input type="text" name="pagador_numero" placeholder="Número:" value="<?php echo $pagador_numero; ?>"></fieldset>
                <fieldset style="width:49.5%;"><input type="text" name="pagador_complemento" placeholder="Complemento:" value="<?php echo $pagador_complemento; ?>"></fieldset>
                <fieldset style="width:49.5%;float:right;"><input type="text" name="pagador_bairro" placeholder="Bairro:" value="<?php echo $pagador_bairro; ?>"></fieldset>
                <fieldset style="width:49.5%;"><input type="text" name="pagador_cep" placeholder="CEP:" value="<?php echo $pagador_cep; ?>"></fieldset>
                <fieldset style="width:49.5%;float:right;"><input type="text" name="pagador_cidade" placeholder="Cidade:" value="<?php echo $pagador_cidade; ?>"></fieldset>
                <fieldset style="width:49.5%;"><input type="text" name="pagador_estado" placeholder="Estado:" value="<?php echo $pagador_estado; ?>"></fieldset>
                <input type="hidden" name="pagador_pais" value="Brasil">
                <fieldset style="width:49.5%;float:right;"><input type="text" name="pagador_cpf" placeholder="CPF:" value="<?php echo $pagador_cpf; ?>"></fieldset>
                <input type="hidden" name="url_retorno" value="<?php echo $url; ?>">
                <fieldset><input id="finalizar" type='submit' name='pedido' value="FORMA DE PAGAMENTO" />
                <p style="width:100%;float:left;margin-top:15px;color:#ccc;font-size:12px;">Ao clicar no botão acima, você será direcionado ao sistema de pagamentos seguro do Moip. <i class="fa fa-lock"></i></p></fieldset>
                </form>

                <?php
                if(isset($_POST['pedido'])):
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
                
                $to = 'contato@lucasdecarvalho.com.br';
                $subject = 'Aviso de pedido 3';
                $name = $_POST["name"];
                $email = $_POST["email"];
                $message = $_POST["text"];
                
                $text = '<p>Confira em sua conta Moip para mais detalhes sobre a efetivação do pagamento: www.moip.com.br</p>
                <hr>
                <p>IP: '.$the_ip.'</p>';
                
                $headers = "MIME-Version: 1.1\r\n";
                $headers .= "Content-type: text/html; charset=utf-8\r\n";
                $headers .= "From:" .$email. "\r\n"; // remetente
                $headers .= "Return-Path: $email \r\n"; // return-path
                
                mail($to,$subject,$text,$headers,$the_ip);
                
                endif;
                ?>
        
            </div>
            <a href="#" onclick="toggle_visibility('foo');"><div id="close" style="display:table;width:100%;height:100%;float:left;position:absolute;top:0;background:rgba(55,55,55,0.5);z-index:11;"></div></a>
        </div>
        




                    
    <?php
    endif;
    ?>

    <!--============================================== FIM FINALIZAR ===================================================-->

            


























<!--<table  id="foo" width="100%" cellpadding="20" cellspacing="20" border="0" bgcolor="#ffffff">
    <tbody>

        <tr>
            <td>
                <h2>Dados de Entrega e Forma Pagamento:</h2>

                <form id="moip" action="https://desenvolvedor.moip.com.br/sandbox/PagamentoMoIP.do" method="post">
                <input type="hidden" name="id_carteira" value="integracao@labs.moip.com.br">
                <input type="hidden" name="nome" value="Motivo do pagamento">
                <input type="hidden" name="valor" value="<?php echo number_format($total_geral,2,"",""); ?>">
                <input type="hidden" name="id_transacao" value="">
                <input type="hidden" name="descricao" value="Adicione aqui mais informações sobre o pagamento">
                <input type="hidden" name="frete" value="1">
                <input type="hidden" name="peso_compra" value="<?php echo '1'; ?>">
                <fieldset><label for="pagador_nome">Nome completo:</label><input type="text" name="pagador_nome"></fieldset>
                <fieldset><label for="pagador_email">Email:</label><input type="text" name="pagador_email"></fieldset>
                <fieldset><label for="pagador_telefone">Telefone:</label><input type="text" name="pagador_telefone"></fieldset>
                <fieldset><label for="pagador_logradouro">Av./Rua:</label><input type="text" name="pagador_logradouro"></fieldset>
                <fieldset><label for="pagador_numero">Número:</label><input type="text" name="pagador_numero"></fieldset>
                <fieldset><label for="pagador_complemento">Complemento:</label><input type="text" name="pagador_complemento"></fieldset>
                <fieldset><label for="pagador_bairro">Bairro:</label><input type="text" name="pagador_bairro"></fieldset>
                <fieldset><label for="pagador_cep">CEP:</label><input type="text" name="pagador_cep"></fieldset>
                <fieldset><label for="pagador_cidade">Cidade:</label><input type="text" name="pagador_cidade"></fieldset>
                <fieldset><label for="pagador_estado">Estado:</label><input type="text" name="pagador_estado"></fieldset>
                <input type="hidden" name="pagador_pais" value="Brasil">
                <fieldset><label for="pagador_cpf">CPF:</label><input type="text" name="pagador_cpf"></fieldset>
                <input type="hidden" name="url_retorno" value="http//www.lucasdecarvalho.com.br/">
                <fieldset><input id="finalizar" type='submit' name='submit' value="FINALIZAR COMPRA" /></fieldset>
                </form>
            </td>
        </tr>

    </tbody>

</table>-->


<?php
endif;
?>

</tbody>
</table>

</div>
</section>
<footer style="top:45px;">
    <div class="container">
        <?php include("footer.php"); ?>
    </div>
</footer>
</body>
</html>