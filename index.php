<?php

require_once('libTotalVoice.php');

$api = new libTotalVoice('63bc3b5bddefb8a04fac29347fd665b6');

if (isset($_GET['cell'])) {
	$cell = $_GET['cell'];
} else {
	$cell = '48996447783';
}

if (isset($_GET['msg'])) {
	$msg = $_GET['msg'];
} else {
	$msg = ';P';
}

$return = $api->sendSMS($cell, $msg);
echo '<h1>Dados de envio</h1>';
echo "<p><strong>Para:</strong> {$cell}</p>";
echo "<p><strong>Mensagem:</strong> {$msg}</p>";
echo '<hr>';
echo '<h1>Retorno do envio</h1>';
echo "<p><strong>Status:</strong> {$return['status']}</p>";
echo "<p><strong>Resposta:</strong> {$return['response']}</p>";
echo '<hr>';
$valorSaldo = $api->getSaldo();
echo '<h1>Saldo</h1>';
echo "<p><strong>Saldo:</strong> R$ ".number_format($valorSaldo, 2, ',', '.')."</p>";
