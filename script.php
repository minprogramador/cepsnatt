<?php

error_reporting(0);
ini_set('error_reporting', 0);
	
setlocale(LC_ALL, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');
date_default_timezone_set('America/Sao_Paulo');

use Nette\Database\Context;
	
require(dirname(__FILE__).'/vendor/autoload.php');
require(__DIR__. "/config.php");


$user     = $sv_config['user'];
$password = $sv_config['passwd'];
$mysqlsv  = $sv_config['mysqlsv'];
$mysqlbd  = $sv_config['dbname'];
$dsn      = "mysql:host={$mysqlsv};dbname={$mysqlbd}";
	
$database = new Nette\Database\Connection($dsn, $user, $password);

function curl($url,$post,$cookies,$header=true,$referer,$follow=false,$proxy=null) {
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

function corta($str, $left, $right) {
    $str = substr ( stristr ( $str, $left ), strlen ( $left ) );
    @$leftLen = strlen ( stristr ( $str, $right ) );
    $leftLen = $leftLen ? - ($leftLen) : strlen ( $str );
    $str = substr ( $str, 0, $leftLen );
    return $str;
}

function parserTable($table) {
	if($table == '<table></table>') {
		return [];
	}
    libxml_use_internal_errors(true);
    $dom = new DOMDocument;
    $dom->loadHTML($table);

    $dom->preserveWhiteSpace = false;   
    $tables = $dom->getElementsByTagName('table');   
    $rows = $tables->item(0)->getElementsByTagName('tr');   
    $cols = $rows->item(0)->getElementsByTagName('th');   
    $row_headers = NULL;

    foreach ($cols as $node) {
        $row_headers[] = $node->nodeValue;
    }   

    $table = array();
    $rows = $tables->item(0)->getElementsByTagName('tr');   
    foreach ($rows as $row)   
    {
        $cols = $row->getElementsByTagName('td');   
        $row = array();
        $i=0;
        foreach ($cols as $node) {
            if($row_headers==NULL){
                $row[] = $node->nodeValue;
            }
            else{
                $row[$row_headers[$i]] = $node->nodeValue;
            }
            $i++;
        }   
        $table[] = $row;
    }
    
    $res = $table;
    $resPr = array_shift($res);
    $dadosOk = [];

    foreach($res as $r) {
        $doc         = $r[0];
        $nome        = $r[1];
        $logradouro  = $r[2];
        $numero      = $r[3];
        $complemento = $r[4];
        $bairro      = $r[5];
        $cep         = $r[6];
        $rr = [
            'doc' => $doc,
            'nome' => $nome,
            'logradouro' => $logradouro,
            'numero' => $numero,
            'complemento' => $complemento,
            'bairro' => $bairro,
            'cep' => $cep
        ];
        array_push($dadosOk, $rr);
    }
    return $dadosOk;
}

function getPaginas($dados) {
    $paginacao = corta($dados, 'paginacao">', '</div>');
    $paginas = [];
    if(stristr($paginacao, '">pr&oacute;xima')) {

        $paginacao = explode("</a>", $paginacao);
        $paginacao = array_filter($paginacao);
        $ultimo = array_pop($paginacao);
        foreach($paginacao as $pg) {
            $pgs = $pg;
            $pgs = explode('">', $pgs);
            $pgs = $pgs[1];
            $paginas[] = $pgs;
        }
    } else {
        if(stristr($dados, 'class="atual')) {
            return '1';
		} elseif(stristr($dados, 'hum Resultado Encontrado!!!')) {
			return '1';
		} elseif(stristr($dados, 'FORAM ENCONTRADOS 0 REGISTRO')) {
			return 1;
		} elseif(stristr($dados, '403 Forbidden</title>')) {
			//sleep(5);
			return 1;
		} else {
        	echo "debug 133;\n\n";
			//echo $dados;
			//die;
        	print_r($paginacao);
        	die;
        }
    }
    $ultPg = array_pop($paginas);

    return $ultPg;
}

function run($cep) {

    $url   = 'https://www.natt.com.br/sistema/consultas/cep/Resposta0501.php';
    $post  = 'e_cep='.$cep.'&e_numero_inicio=&e_numero_fim=';
    $dados = curl($url, $post, null, true, null);

    $paginas = getPaginas($dados);

    $dados = corta($dados, 'id="cTable">', '</table>');
    $dados = "<table>$dados</table>";
    $table = utf8_decode($dados);

    $result = [];

    $resone = parserTable($table);
    foreach($resone as $res) {
        array_push($result, $res);
    }

    $totalPaginas = (int) $paginas;

    if($totalPaginas > 1) {
        for ($x = 2; $x <= $totalPaginas; $x++) {
            $p = $x;
            $url = 'https://www.natt.com.br/sistema/consultas/cep/Resposta0501.php';
            $post = 'e_cep='.$cep.'&e_numero_inicio=&e_numero_fim=';
            $post = 'e_pag='.$p.'&e_cep='.$cep.'&e_numero_inicio=0&e_numero_fim=999999999999999';
            $dados = curl($url, $post, null, true, null);

            $dados = corta($dados, 'id="cTable">', '</table>');
            $dados = "<table>$dados</table>";
            $table = utf8_decode($dados);
            $res = parserTable($table);
            foreach($res as $re) {
                array_push($result, $re);
            }
        }
    }
    return $result;
}

while(true) {
	$result = $database->query('select cep from ceps where `status`=1 limit 1;');
	$count_ativos   = 0;
	$count_inativos = 0;
	$count_total    = $result->getRowCount();

	foreach ($result as $row) {
		$cep = $row['cep'];

		$payload = [];
		$payload['inicio'] = date("Y-m-d H:i:s");
		$payload['status']  = 2;

		$database->query('UPDATE ceps SET', $payload , 'WHERE cep = ?', $cep);

		$coleta = run($cep);

		foreach($coleta as $col) {
			$doc = str_replace(['.', '/', '-', ' ', ','], '', $col['doc']);

			$database->query('INSERT INTO enderecos ?', [
								 'doc' => $doc,
								 'nome' => $col['nome'],
								 'logradouro'=> $col['logradouro'],
								 'numero' => $col['numero'],
								 'complemento'=> $col['complemento'],
								 'bairro'=> $col['bairro'],
								 'cep'=> $col['cep'],
								 'data'=> date("Y-m-d H:i:s"),
								 'status'=> 1
							]);

		}

		unset($payload);
		$payload = [];
		$payload['total'] = count($coleta);
		$payload['salvos'] = count($coleta);
		$payload['fim'] = date("Y-m-d H:i:s");
		$payload['status']  = 3;

		$upfinal = $database->query('UPDATE ceps SET', $payload , 'WHERE cep = ?', $cep);
		echo "\n#Fim coleta: cep {$cep} - total ".count($coleta)." \n\n";
	}
}

// $cep = null;

// if(isset($argv)) {
//     foreach($argv as $v) {
//         if(stristr($v, 'cep=')) {
//             $cep = str_replace(['cep=', '-', ' '], '', $v);
//         }
//     }
// }

// if(isset($cep)) {
//     $coleta = run($cep);
//     echo json_encode($coleta);
// }
