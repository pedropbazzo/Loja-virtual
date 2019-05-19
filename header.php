<nav>
	<div class="container">
		<a href="<?php echo $url; ?>/carrinho">MEUS PEDIDOS</a>
		<a href="<?php echo $url; ?>/comocomprar">COMO COMPRAR</a>
		<a href="<?php echo $url; ?>/seguranca#privacidade">SEGURANÇA</a>
		<a href="<?php echo $url; ?>/contato">FALE CONOSCO</a>
	</div>
</nav>

<div class="container">
	<div id="logo">
	    <a href="<?php echo $url; ?>"><img src="<?php echo $url; ?>/img/logo.png"/></a>
	</div>
	<div id="search">
		<p style="text-align:center;">Dúvidas? (12) 3456-7890 | contato@loja.com.br</p>
		<form name="search" action="search" method="POST">
		    <input type="text" name="wordpass" placeholder="O QUE VOCÊ PROCURA?"/>
		    <input type="submit" name="find" value="BUSCAR"/>
		</form>
	</div>
	<div id="cart">
		<a href="<?php echo $url; ?>/carrinho">
		<p><i class='fa fa-shopping-cart'></i> MEU CARRINHO<br/>
		<?php
		if(count($_SESSION['carrinho']) == 0):
			echo'<span style="margin-top:-2.5px;color:#000;font-size:14px;">Está vazio.</span></p>';
		else:
			echo'<span style="margin-top:-4px;color:#000;font-size:14px;">R$ '.number_format($total,2,",",".").'</span></p>'; 
		endif;
		?>
		</a>
	</div>
</div>
