<?php
//VEPUF
// Inicia o cURL
$ch = curl_init();


// Define a URL original (do formulário de login)
curl_setopt($ch, CURLOPT_URL, 'http://oficial.stellarwars.net/index.php?page=login');

// Habilita o protocolo POST
curl_setopt ($ch, CURLOPT_POST, 1);

// Define os parâmetros que serão enviados (usuário e senha por exemplo)
curl_setopt ($ch, CURLOPT_POSTFIELDS, 'uni=1&username=vepuf&password=joan18957');

// Imita o comportamento patrão dos navegadores: manipular cookies
curl_setopt ($ch, CURLOPT_COOKIEJAR, 'cookie.txt');
curl_setopt ($ch, CURLOPT_COOKIEFILE, 'cookie.txt');

// Define o tipo de transferência (Padrão: 1)
curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 0);

curl_setopt ($ch, CURLOPT_FOLLOWLOCATION, 0);

// Executa a requisição
$store = curl_exec ($ch);

// Define uma nova URL para ser chamada (após o login)
curl_setopt($ch, CURLOPT_URL, 'http://oficial.stellarwars.net/game.php?page=buildings');

// Executa a segunda requisição
$content = curl_exec ($ch);

curl_setopt($ch, CURLOPT_URL, 'http://oficial.stellarwars.net/game.php?page=&mode=&cp=528');
$content2 = curl_exec ($ch);
curl_setopt($ch, CURLOPT_URL, 'http://oficial.stellarwars.net/game.php?page=&mode=&cp=554');
$content3 = curl_exec ($ch);
curl_setopt($ch, CURLOPT_URL, 'http://oficial.stellarwars.net/game.php?page=&mode=&cp=559');
$content4 = curl_exec ($ch);
curl_setopt($ch, CURLOPT_URL, 'http://oficial.stellarwars.net/game.php?page=&mode=&cp=560');
$content5 = curl_exec ($ch);
curl_setopt($ch, CURLOPT_URL, 'http://oficial.stellarwars.net/game.php?page=&mode=&cp=561');
$content6 = curl_exec ($ch);
curl_setopt($ch, CURLOPT_URL, 'http://oficial.stellarwars.net/game.php?page=&mode=&cp=562');
$content7 = curl_exec ($ch);
curl_setopt($ch, CURLOPT_URL, 'http://oficial.stellarwars.net/game.php?page=&mode=&cp=563');
$content8 = curl_exec ($ch);
curl_setopt($ch, CURLOPT_URL, 'http://oficial.stellarwars.net/game.php?page=&mode=&cp=564');
$content9 = curl_exec ($ch);
curl_setopt($ch, CURLOPT_URL, 'http://oficial.stellarwars.net/game.php?page=&mode=&cp=565');
$content10 = curl_exec ($ch);
curl_setopt($ch, CURLOPT_URL, 'http://oficial.stellarwars.net/game.php?page=&mode=&cp=498');
$content11 = curl_exec ($ch);

// Encerra o cURL
curl_close ($ch);

?>