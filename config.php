<?php



if($_SERVER['USER'] == 'work') {
	$sv_config = [
		'dbname' => 'ceps',
		'user'   => 'root',
		'passwd' => '153356',
		'mysqlsv'=> '127.0.0.1'
	];
}else{
	$sv_config = [
		'dbname' => 'coletor_nat',
		'user'   => 'root',
		'passwd' => '2019maconhaOk@@',
		'mysqlsv'=> '127.0.0.1',
	];	
}
$sv_config['key_proxyrotator'] = 'FGKb6TcnwUgXP47LkmS9A8NdVhr5syYx';

