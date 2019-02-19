<?php


//natt 25/01/2017
error_reporting(E_ALL);

function curl($url,$post,$cookies,$header=true,$referer,$follow=false,$proxy=null)
{
	$ch = @curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_HEADER, $header);
	if ($cookies) curl_setopt($ch, CURLOPT_COOKIE, $cookies);
	curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 5.1; rv:2.0.1) Gecko/20100101 Firefox/4.0.1');
	curl_setopt($ch, CURLOPT_REFERER,$referer);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	@curl_setopt($ch, CURLOPT_FOLLOWLOCATION, $follow);
	curl_setopt($ch, CURLOPT_REFERER,$url); 
	if ($post){curl_setopt($ch, CURLOPT_POST, 1);curl_setopt($ch, CURLOPT_POSTFIELDS, $post);}
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE); 
	curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($ch, CURLOPT_TIMEOUT, 30); 
	curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, 30);

	curl_setopt($ch, CURLOPT_PROXY, "18.231.75.72:3128");
	#curl_setopt($ch, CURLOPT_PROXYUSERPWD, "beave1939:cLkDmXiT");

	$page = curl_exec( $ch);
	curl_close($ch); 
	return $page;
}

function getCookies($get)
{
	preg_match_all('/Set-Cookie: (.*);/U',$get,$temp);
	$cookie = $temp[1];
	$cookies = implode('; ',$cookie);
	return $cookies;
}

function corta($str, $left, $right) 
{
	$str = substr ( stristr ( $str, $left ), strlen ( $left ) );
	@$leftLen = strlen ( stristr ( $str, $right ) );
	$leftLen = $leftLen ? - ($leftLen) : strlen ( $str );
	$str = substr ( $str, 0, $leftLen );
	return $str;
}

function clean($res)
{
	$res = str_replace('css/reset.css','nx/css/reset.css',$res);
	$res = str_replace('css/novo.css','nx/css/novo.css',$res);

	$res = str_replace('<img src="../img/bsi.jpg">','<br/>',$res);
	$res = str_replace('<img src="../img/logo_nat_brasil.gif">','',$res);
	$res = str_replace('<input type="submit" class="btn firebrick" value="Altera Senha" onClick="Opcao(\'pass\')" />','',$res);
	$cort = corta($res,'<div style="margin-top: 180px">','</aside>');
	$res  = str_replace($cort,'',$res);

	$res = str_replace('painel.php?acao=','Natt.php?pg=',$res);

	$res = str_replace('cpf/Resposta0101.php','?res=cpf',$res);

	$res = str_replace('<b>Erro na Linha: #116 ::</b> Undefined index: Nattlogin<br><small>/var/www/html/sistema/_app/Models/LoginNatt.class.php</small><span class="ajax_close"></span></p>','',$res);
	$res = str_replace('<p class="trigger 8"><b>Erro na Linha: #138 ::</b> Undefined index: Nattlogin<br><small>/var/www/html/sistema/_app/Models/LoginNatt.class.php</small><span class="ajax_close"></span></p>','',$res);
	$res = str_replace('Erro na Linha: #113 ::</b> Undefined variable: Nattlogin',null,$res);
	$res = str_replace('/var/www/html/sistema/consultas/?res=cpf','',$res);
	$res = str_replace('../../css/estilo.css','nx/css/estilo.css',$res);

	$res = str_replace('Erro na Linha: #1035 ::','',$res);
	$res = str_replace('Erro na Linha: #1036 ::','',$res);
	$res = str_replace('Erro na Linha: #1045 ::','',$res);
	$res = str_replace('Undefined variable: Nattlogin','',$res);

	return $res;
}

function getCookieGlob()
{
	$res    = curl('http://www.natt.com.br/sistema/consultas/index.php',null,null,true,null);
	$cookie = getCookies($res);
	return $cookie;
}

$cookie = getCookieGlob();


if(isset($_GET['pg']))
{
	$pg = $_GET['pg'];
	if($pg == 'cpf')
	{
		if(strlen(http_build_query($_POST)) > 5)
		{
			$res = curl('https://www.natt.com.br/sistema/consultas/cpf/Resposta0101.php',http_build_query($_POST),$cookie,false,'http://www.natt.com.br/sistema/consultas/painel.php');

			if(stristr($res, 'timo Endere'))
			{
				#$control->saveConsulta();
				#$comp = new Sistema_Verificacao();
		        #$comp->setServico('Natt');
		        #$comp->Computa();
			}
			
			$res = corta($res,'<div class="article">
					<br>','</html>');
			
			$rem = explode('</table>',$res);
			$res = str_replace($rem[20],'',$res);
			$res = str_replace('../images/cancelar2.png','images/cancelar2.png',$res);

			$rem = explode('trigger',$res);
			$res = str_replace($rem[1],'',$res);
			$res = str_replace($rem[2],'',$res);
			$res = str_replace($rem[3],'',$res);
			$res = '<title>Consulta Achei - PF</title>
		<meta name="description" content="Consultado Basse Achei via CPF">
		<link rel="stylesheet" type="text/css" href="nx/css/estilo.css">
		 
		<script type="text/javascript">
			function consultar($valor)
				{
					document.form1.e_cnpj.value = $valor;
					document.form1.submit();
				}
			function consultarParente($valor)
				{
					document.form2.e_cpf.value = $valor;
					document.form2.submit();
				}
			function consultarTelefone($valor)
				{
					document.form3.e_telefone.value = $valor;
					document.form3.submit();
				}	
			function consultarNome($valor)
				{
					document.form4.e_nome.value = $valor;
					document.form4.submit();
				}	

			function consultarVizinho($valor, $valor2)
				{
					if ($valor == 1) {
						document.form2.e_cpf.value = $valor2;
						document.form2.submit();
					} else { 
						document.form1.e_cnpj.value = $valor2;
						document.form1.submit();
					}
				}

			function consultarBacen($valor)
				{
					document.form5.e_cpf_bacen.value = $valor;
					document.form5.submit();
				}
					
			function Opcao($valor)
				{
					switch ($valor) {
						case "nova":
							window.location.href="../index.php";
							break
					case "voltar":
							window.history.back();
							break
					case "imprimir":
							window.print();
							break
					default:
							alert($valor);
							break
					}
				}	
		</script>
		<script type="text/javascript">
		$(document).ready(function(){
		    	  $(".ajax-mapa").colorbox({iframe:true, width:"950px", height:"450px"});
		});
		</script><div class="article">'.$res;
		$menub = '<br><br>
		<center>
		<div align="center" style=" margin-bottom: 2%;
    margin-top: -3%; top: 0;
    width: 376px;">
			
			<input class="btn firebrick" value="     VOLTAR    " onclick="Opcao(\'voltar\')" type="submit">
			<input class="btn firebrick" value=" NOVA CONSULTA " onclick="Opcao(\'nova\')" type="submit">
			<input class="btn firebrick" value="    IMPRIMIR   " onclick="Opcao(\'imprimir\')" type="submit">
		</div>
		</center>';
			if(!stristr($res,'NOVA CONSULTA'))
			{
				$res = $res.$menub;
			}
			$res = str_replace('<p class="triggertriggertrigger<br><br>','',$res);
			#$res = str_replace('<br><br><div align=','',$res);
			#$res = str_replace('<p class="" center"=""','<p class="boxfm"',$res);
			$res = str_replace('../index.php','Natt.php',$res);
			die($res).'<br><br>';
		}
	}
	elseif($pg == 'cnpj')
	{
		$res = curl('https://www.natt.com.br/sistema/consultas/cpf/Resposta0102.php',http_build_query($_POST),$cookie,false,'https://www.natt.com.br/sistema/consultas/painel.php');

		$res = str_replace('<p class="trigger 8"></p>','',$res);
		$res = str_replace('Erro na Linha: #116 ::','',$res);
		$res = str_replace('Erro na Linha: #138 ::','',$res);
		$res = str_replace('Erro na Linha: #101 ::','',$res);
		$res = str_replace('Erro na Linha: #817 ::','',$res);
		$res = str_replace('Erro na Linha: #818 ::','',$res);
		$res = str_replace('Erro na Linha: #827 ::','',$res);
		$res = str_replace('Undefined index: Nattlogin','',$res);
		$res = str_replace('Undefined variable: Nattlogin','',$res);
		$res = str_replace('/var/www/html/sistema/consultas/cpf/Resposta0102.php','',$res);
		$res = str_replace('/var/www/html/sistema/_app/Models/LoginNatt.class.php','',$res);
		$res = str_replace('<p class="trigger 8"><b></b> <br><small></small><span class="ajax_close"></span></p>','',$res);
		$res = str_replace('../images/cancelar2.png','images/cancelar2.png',$res);
		$res = str_replace('<b><span>CLIENTE: </span></b>-','',$res);
		$res = str_replace('../../css/estilo.css','nx/css/estilo.css',$res);
		$res = str_replace('<span>CLIENTE:','',$res);
		$res = str_replace('</b>-','</b>',$res);
		$res = str_replace('http://www.natt.com.br/sistema/maps/mapa.php','?maps=true&',$res);
		$rem = corta($res,'<body>','</div> ');
		$res = str_replace($rem,'',$res);

		$res = str_replace('window.location.href="../index.php";','window.location.href="Natt.php";',$res);
		
		die($res);
	}
	elseif($pg == 'fone')
	{
		if(strlen(http_build_query($_POST)) < 5)
		{
			die('refaca sua busca..');
		}
		$res = curl('https://www.natt.com.br/sistema/consultas/telefone/Resposta0301.php',http_build_query($_POST),$cookie,false,'https://www.natt.com.br/sistema/consultas/painel.php');
		
		if(stristr($res,'os Anteriores deste Telefone'))
		{
			#$control->saveConsulta();
			#$comp = new Sistema_Verificacao();
		    #$comp->setServico('Natt');
		    #$comp->Computa();
		}

		$res = str_replace('<p class="trigger 8"></p>','',$res);
		$res = str_replace('Erro na Linha: #116 ::','',$res);
		$res = str_replace('Erro na Linha: #138 ::','',$res);
		$res = str_replace('Erro na Linha: #101 ::','',$res);
		$res = str_replace('Erro na Linha: #817 ::','',$res);
		$res = str_replace('Erro na Linha: #818 ::','',$res);
		$res = str_replace('Erro na Linha: #827 ::','',$res);
		$res = str_replace('Erro na Linha: #324 ::','',$res);
		$res = str_replace('Erro na Linha: #325 ::','',$res);
		$res = str_replace('Erro na Linha: #334 ::','',$res);
		$res = str_replace('/var/www/html/sistema/consultas/telefone/Resposta0301.php','',$res);
		$res = str_replace('Undefined index: Nattlogin','',$res);
		$res = str_replace('Undefined variable: Nattlogin','',$res);
		$res = str_replace('/var/www/html/sistema/consultas/cpf/Resposta0102.php','',$res);
		$res = str_replace('/var/www/html/sistema/_app/Models/LoginNatt.class.php','',$res);
		$res = str_replace('<p class="trigger 8"><b></b> <br><small></small><span class="ajax_close"></span></p>','',$res);
		$res = str_replace('../images/cancelar2.png','images/cancelar2.png',$res);
		$res = str_replace('<b><span>CLIENTE: </span></b>-','',$res);
		$res = str_replace('../../css/estilo.css','nx/css/estilo.css',$res);
		$res = str_replace('<span>CLIENTE:','',$res);
		$res = str_replace('</b>-','</b>',$res);
		$res = str_replace('http://www.natt.com.br/sistema/maps/mapa.php','?maps=true&',$res);
		
		$rem = corta($res,'<body>','</div>');
		$res = str_replace($rem,'',$res);

		$res = str_replace('window.location.href="../index.php";','window.location.href="Natt.php";',$res);
		
		die($res);
	}
	elseif($pg == 'nome')
	{
		if(isset($_POST['e_pag']))
		{
			$res = curl('https://www.natt.com.br/sistema/consultas/nome/Resposta0202.php',http_build_query($_POST),$cookie,false,'https://www.natt.com.br/sistema/consultas/nome/Resposta0201.php');
		}
		elseif(isset($_POST['e_cpf']))
		{
			$res = curl('https://www.natt.com.br/sistema/consultas/cpf/Resposta0101.php',http_build_query($_POST),$cookie,false,'https://www.natt.com.br/sistema/consultas/nome/Resposta0201.php');

			if(stristr($res, 'timo Endere'))
			{
				##$control->saveConsulta();
				#$comp = new Sistema_Verificacao();
		        #$comp->setServico('Natt2');
		        #$comp->Computa();
			}


			$res = corta($res,'<div class="article">
					<br>','</html>');
			
			$rem = explode('</table>',$res);
			$res = str_replace($rem[20],'',$res);
			$res = str_replace('../images/cancelar2.png','images/cancelar2.png',$res);

			$rem = explode('trigger',$res);
			$res = str_replace($rem[1],'',$res);
			$res = str_replace($rem[2],'',$res);
			$res = str_replace($rem[3],'',$res);
			$res = '<title>Consulta Achei - PF</title>
		<meta name="description" content="Consultado Basse Achei via CPF">
		<link rel="stylesheet" type="text/css" href="nx/css/estilo.css">
		 
		<script type="text/javascript">
			function consultar($valor)
				{
					document.form1.e_cnpj.value = $valor;
					document.form1.submit();
				}
			function consultarParente($valor)
				{
					document.form2.e_cpf.value = $valor;
					document.form2.submit();
				}
			function consultarTelefone($valor)
				{
					document.form3.e_telefone.value = $valor;
					document.form3.submit();
				}	
			function consultarNome($valor)
				{
					document.form4.e_nome.value = $valor;
					document.form4.submit();
				}	

			function consultarVizinho($valor, $valor2)
				{
					if ($valor == 1) {
						document.form2.e_cpf.value = $valor2;
						document.form2.submit();
					} else { 
						document.form1.e_cnpj.value = $valor2;
						document.form1.submit();
					}
				}

			function consultarBacen($valor)
				{
					document.form5.e_cpf_bacen.value = $valor;
					document.form5.submit();
				}
					
			function Opcao($valor)
				{
					switch ($valor) {
						case "nova":
							window.location.href="../index.php";
							break
					case "voltar":
							window.history.back();
							break
					case "imprimir":
							window.print();
							break
					default:
							alert($valor);
							break
					}
				}	
		</script>
		<script type="text/javascript">
		$(document).ready(function(){
		    	  $(".ajax-mapa").colorbox({iframe:true, width:"950px", height:"450px"});
		});
		</script><div class="article">'.$res;
		$menub = '<br><br>
		<center>
		<div align="center" style=" margin-bottom: 2%;
    margin-top: 0; top: 0;
    width: 376px;">
			
			<input class="btn firebrick" value="     VOLTAR    " onclick="Opcao(\'voltar\')" type="submit">
			<input class="btn firebrick" value=" NOVA CONSULTA " onclick="Opcao(\'nova\')" type="submit">
			<input class="btn firebrick" value="    IMPRIMIR   " onclick="Opcao(\'imprimir\')" type="submit">
		</div>
		</center>';
			if(!stristr($res,'NOVA CONSULTA'))
			{
				$res = $res.$menub;
			}
			$res = str_replace('<p class="triggertriggertrigger<br><br>','',$res);
			#$res = str_replace('<br><br><div align=','',$res);
			#$res = str_replace('<p class="" center"=""','<p class="boxfm"',$res);
			$res = str_replace('../index.php','Natt.php',$res);
			die($res).'<br><br>';

		}
		else
		{
			$res = curl('https://www.natt.com.br/sistema/consultas/nome/Resposta0201.php',http_build_query($_POST),$cookie,false,'https://www.natt.com.br/sistema/consultas/painel.php');
		}

		$res = str_replace('Erro na Linha: #116 ::','',$res);
		$res = str_replace('Erro na Linha: #138 ::','',$res);
		$res = str_replace('Erro na Linha: #94 ::','',$res);
		$res = str_replace('Erro na Linha: #202 ::','',$res);

		$res = str_replace("Use of undefined constant adjacentes - assumed 'adjacentes'",'',$res);
		$res = str_replace('/var/www/html/sistema/consultas/nome/','',$res);
		$res = str_replace('Undefined index: Nattlogin','',$res);
		$res = str_replace('Undefined variable: Nattlogin','',$res);
		$res = str_replace('/var/www/html/sistema/_app/Models/LoginNatt.class.php','',$res);
		$res = str_replace('/var/www/html/sistema/consultas/nome/Resposta0201.php','',$res);
		$res = str_replace('mysqli_next_result(): There is no next result set. Please, call mysqli_more_results()/mysqli::more_results() to check whether to call this function/method','',$res);

		$res = str_replace('<p class="trigger 8"><b></b> <br><small></small><span class="ajax_close"></span></p>','',$res);
		$res = str_replace('../images/cancelar2.png','nx/images/cancelar2.png',$res);
		$res = str_replace('<b><span>CLIENTE: </span></b>-','',$res);
		$res = str_replace('../../css/estilo.css','nx/css/estilo.css',$res);
		$res = str_replace('<span>CLIENTE:','',$res);
		$res = str_replace('</b>-','</b>',$res);
		$rem = corta($res,'<body>','</div>');
		$res = str_replace($rem,'',$res);
		$res = str_replace('../nome/Resposta0202.php','',$res);
		$res = str_replace('Resposta0202.php','',$res);
		$res = str_replace('../index.php','',$res);
		$res = str_replace('../cpf/Resposta0102.php','',$res);
		$res = str_replace('../cpf/Resposta0101.php','',$res);
		echo $res;
		die;
	}
	elseif($pg == 'end')
	{
		// echo "<pre>";
		// print_r($_POST);
		// die;
		if(isset($_POST['e_cep']))
		{
			$res = curl('https://www.natt.com.br/sistema/consultas/cep/Resposta0501.php',http_build_query($_POST),$cookie,false,'https://www.natt.com.br/sistema/consultas/painel.php');

		}
		elseif(isset($_POST['e_pag']))
		{
			$res = curl('https://www.natt.com.br/sistema/consultas/endereco/Resposta0402.php',http_build_query($_POST),$cookie,false,'https://www.natt.com.br/sistema/consultas/endereco/Resposta0403.php');
		}
		elseif(isset($_POST['e_total']))
		{
			$res = curl('https://www.natt.com.br/sistema/consultas/endereco/Resposta0403.php',http_build_query($_POST),$cookie,false,'https://www.natt.com.br/sistema/consultas/painel.php');		
		}
		elseif(isset($_POST['e_cidade']))
		{
			$res = curl('https://www.natt.com.br/sistema/consultas/endereco/Resposta0401.php',http_build_query($_POST),$cookie,false,'https://www.natt.com.br/sistema/consultas/painel.php');
		}
		else
		{
			$res = curl('https://www.natt.com.br/sistema/consultas/endereco/Resposta0401.php',http_build_query($_POST),$cookie,false,'https://www.natt.com.br/sistema/consultas/painel.php');		
		}

		$res = str_replace('Resposta0501.php','',$res);
		$res = str_replace('../cpf/Resposta0102.php','',$res);
		$res = str_replace('../cpf/Resposta0101.php','?pg=cpf',$res);
		$res = str_replace('Resposta0402.php','',$res);
		$res = str_replace('Resposta0403.php','',$res);
		$res = str_replace('Erro na Linha: #46 ::','',$res);
		$res = str_replace('Erro na Linha: #50 ::','',$res);
		$res = str_replace('Erro na Linha: #52 ::','',$res);
		$res = str_replace('/var/www/html/sistema/consultas/endereco/Resposta0403.php','',$res);
		$res = str_replace('Undefined index: e_pag','',$res);
		$res = str_replace('Undefined index: e_bairro','',$res);
		$res = str_replace('Undefined index: e_total','',$res);

		$res = str_replace('../../css/estilo.css','nx/css/estilo.css',$res);
		$rem = corta($res,'<body>','</div>');
		$res = str_replace($rem,'',$res);

		$res = str_replace('window.location.href="../index.php";','window.location.href="Natt.php";',$res);
		
		echo $res;
		die;
	}
	elseif($pg == 'bacen')
	{
		include('nx/bacen.html');
		die;

	}

}


if(isset($_GET['bacen']))
{
	if(strlen(http_build_query($_POST)) > 5)
	{
		$res = curl('https://www.natt.com.br/sistema/consultas/bacen/Resposta1001.php',http_build_query($_POST),$cookie,false,'https://www.natt.com.br/sistema/consultas/painel.php?acao=bacen');
		$res = str_replace('Resposta0501.php','',$res);
		$res = str_replace('../cpf/Resposta0102.php','',$res);
		$res = str_replace('../cpf/Resposta0101.php','?pg=cpf',$res);
		$res = str_replace('Resposta0402.php','',$res);
		$res = str_replace('Resposta0403.php','',$res);
		$res = str_replace('Erro na Linha: #116 ::','',$res);
		$res = str_replace('Erro na Linha: #138 ::','',$res);
		$res = str_replace('Erro na Linha: #66 ::','',$res);
		$res = str_replace('Erro na Linha: #187 ::','',$res);
		$res = str_replace('Erro na Linha: #188 ::','',$res);
		$res = str_replace('Erro na Linha: #197 ::','',$res);
		
		$res = str_replace('/var/www/html/sistema/consultas/bacen/Resposta1001.php','',$res);
		
		$res = str_replace('Undefined index: Nattlogin','',$res);
		$res = str_replace('/var/www/html/sistema/_app/Models/LoginNatt.class.php','',$res);

		$res = str_replace("Use of undefined constant adjacentes - assumed 'adjacentes'",'',$res);
		$res = str_replace('/var/www/html/sistema/consultas/nome/','',$res);
		$res = str_replace('Undefined index: Nattlogin','',$res);
		$res = str_replace('Undefined variable: Nattlogin','',$res);
		$res = str_replace('/var/www/html/sistema/_app/Models/LoginNatt.class.php','',$res);
		$res = str_replace('/var/www/html/sistema/consultas/nome/Resposta0201.php','',$res);
		$res = str_replace('mysqli_next_result(): There is no next result set. Please, call mysqli_more_results()/mysqli::more_results() to check whether to call this function/method','',$res);

		$res = str_replace('<p class="trigger 8"><b></b> <br><small></small><span class="ajax_close"></span></p>','',$res);
		$res = str_replace('../images/cancelar2.png','images/cancelar2.png',$res);
		$res = str_replace('<b><span>CLIENTE: </span></b>-','',$res);
		$res = str_replace('../../css/estilo.css','nx/css/estilo.css',$res);
		$res = str_replace('<span>CLIENTE:','',$res);
		$res = str_replace('</b>-','</b>',$res);
		$rem = corta($res,'<body>','</div>');
		$res = str_replace($rem,'',$res);
		$res = str_replace('../nome/Resposta0202.php','',$res);
		$res = str_replace('Resposta0202.php','',$res);
		$res = str_replace('../index.php','',$res);
		$res = str_replace('../cpf/Resposta0102.php','',$res);
		$res = str_replace('../cpf/Resposta0101.php','',$res);
		
		die($res);
	}
	else
	{
		include('nx/bacen.html');
		die;
	}
}
else
{
	include('nx/inicial.html');
	die;
}




if(isset($_SESSION['cookienatt']))
{
	$cookie = $_SESSION['cookienatt'];
	$res = curl('https://www.natt.com.br/sistema/consultas/painel.php',null,$cookie,false,'https://www.natt.com.br/sistema/consultas/index.php?exe=logoff');
	$res = clean($res);
}
else
{
	//loga
	$res    = curl('https://www.natt.com.br/sistema/consultas/index.php?exe=logoff',null,null,true,'https://www.natt.com.br/sistema/consultas/index.php?exe=logoff');
	$cookie = getCookies($res);
	$_SESSION['cookienatt'] = $cookie;
	$res = curl('https://www.natt.com.br/sistema/consultas/index.php?exe=logoff','user=2412&pass=wlar1958&NattLogin=Logar',$cookie,true,'https://www.natt.com.br/sistema/consultas/index.php?exe=logoff');

	if(stristr($res,'ocation: painel.php'))
	{
		$res = curl('https://www.natt.com.br/sistema/consultas/painel.php',null,$cookie,false,'https://www.natt.com.br/sistema/consultas/index.php?exe=logoff');
		$res = clean($res);
	}	
}



if(isset($_GET['pg']))
{
	$res = curl('https://www.natt.com.br/sistema/consultas/painel.php?acao='.$_GET['pg'],null,$cookie,false,'https://www.natt.com.br/sistema/consultas/index.php?exe=logoff');
	$res = clean($res);
}

if(isset($_GET['res']))
{
	$res = curl('https://www.natt.com.br/sistema/consultas/cpf/Resposta0101.php',http_build_query($_POST),null,false,'https://www.natt.com.br/sistema/consultas/painel.php');
	$cort = corta($res,'<body>','</span></p>');
	$res  = str_replace($cort,'',$res);
	$res = str_replace('../../css/estilo.css','nx/css/estilo.css',$res);

	$res = str_replace('Erro na Linha: #116 ::','',$res);
	$res = str_replace('Erro na Linha: #138 ::','',$res);
	$res = str_replace('Erro na Linha: #113 ::','',$res);
	$res = str_replace('Erro na Linha: #1035 ::','',$res);
	$res = str_replace('Erro na Linha: #1036 ::','',$res);
	$res = str_replace('Erro na Linha: #1045 ::','',$res);
	$res = str_replace('Undefined variable: Nattlogin','',$res);
	$res = str_replace('Undefined index: Nattlogin','',$res);
	$res = str_replace('/var/www/html/sistema/_app/Models/LoginNatt.class.php','',$res);
	$res = str_replace('/var/www/html/sistema/consultas/cpf/Resposta0101.php','',$res);
	$res = str_replace('<p></p>','',$res);
	$res = str_replace('<b></b> <br>','',$res);
	$res = str_replace('<small></small>','',$res);
	$res = str_replace('<span class="ajax_close"></span>','',$res);
	$res = str_replace('<br><small></small><span class="ajax_close"></span></p>-','',$res);

	die(($res));	
}

echo $res;
die;
