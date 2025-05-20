<?php

    $host = 'br612.hostgator.com.br'; //nome do host
    $username = 'hubsap45_usrrifanet'; //nome do usuario
    $password = 'rifa@!42Z4'; //senha do banco
    $database = 'hubsap45_bd_2cb_rifanet'; //nome do banco

    $conexao = new mysqli($host, $username, $password, $database); //variavel que faz de fato a

    // if ($conexao->connect_error) {
    //     echo "Erro na conexão";
    // } else {
    //     echo "Conectado com sucesso";
    // }
?>