<?php
require __DIR__ . '/../../fullstackphp/fsphp.php';
fullStackPHPClassName("05.05 - Explorando estilos de busca");

require __DIR__ . "/../source/autoload.php";

use Source\Database\Connect;

/*
 * [ fetch ] http://php.net/manual/pt_BR/pdostatement.fetch.php
 */
fullStackPHPClassSession("fetch", __LINE__);

$connect = Connect::getInstance();
$read = $connect->query("SELECT * FROM users LIMIT 3");

if(!$read->rowCount()) {
    echo "<p class='trigger'>Não obteve resultados!</p>";
} else {
    // var_dump($read->fetch());
    // fetch -> Traz um resultado apenas e o nextRowSet para poder percorrer todos eles com while por ex.

    while($user = $read->fetch()) {
        var_dump($user);
    }
}

/*
 * [ fetch all ] http://php.net/manual/pt_BR/pdostatement.fetchall.php
 */
fullStackPHPClassSession("fetch all", __LINE__);

// fetch -> abrir com while, fetchAll -> abrir com foreach por ser uma array

$read = $connect->query("SELECT * FROM users LIMIT 3,2");

// while ($user = $read->fetchAll()) {
//     var_dump($user);
// }

foreach ($read->fetchAll() as $user) {
    var_dump($user);
}

/*
 * [ fetch save ] Realziar um fetch diretamente em um PDOStatement resulta em um clear no buffer da consulta. Você
 * pode armazenar esse resultado em uma variável para manipilar e exibir posteriormente.
 */
fullStackPHPClassSession("fetch save", __LINE__);


/*
 * [ fetch modes ] http://php.net/manual/pt_BR/pdostatement.fetch.php
 */
fullStackPHPClassSession("fetch styles", __LINE__);


