<?php

require_once("custom/php/common.php");
 
// verifica se o utilizador fez login no wp e se tem permissão para mexer nos objetos
if (is_user_logged_in() && current_user_can('dynamic_search')) {        
 
$liga =liga_basedados();
 
// quando o estado da execução não está definido
if ($_REQUEST["estado_execucao"] == ""){
    ?>
    <h3><b>Pesquisa Dinâmica - escolher objeto</b></h3>
    <?php
    }

}
else {
    ?>
    Não tem autorização para aceder a esta página.
    <?php
}
?>