<?php
    function back(){
        echo "<script type='text/javascript'>document.write(\"<a href='javascript:history.back()' class='backLink' title='Voltar atr&aacute;s'>Voltar atr&aacute;s</a>\");</script>
        <noscript>
        <a href='".$_SERVER['HTTP_REFERER']."‘ class='backLink' title='Voltar atr&aacute;s'>Voltar atr&aacute;s</a>
        </noscript>";
    }

    function liga_basedados(){ // Função para ligação à base de dados
        $link = mysqli_connect(DB_HOST,DB_USER,DB_PASSWORD,DB_NAME);
        if (!$link){
            die("Erro na ligação".mysqli_error());
        }
        return $link;
    }

    function executa_query($query){ // Executa a query que recebe e dá mensagem de erro caso a query tenha algum erro
        $link = liga_basedados();
        if(!$result = mysqli_query($link,$query)){
            echo("Error: ".mysqli_error($link));
        }
        return $result;
    }
    
    function guarda_variavel($verifica){ // Passagem do parametro e posteriormente guarda no retorno
        $link = liga_basedados();
        $retorno = mysqli_real_escape_string($link,$verifica); // Retira os caracteres especiais, da variavel verifica para uso na posterior query
        return $retorno;
    }
    

?>
<!--
$clientsideval=0; se fizermos client side
-->