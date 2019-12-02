<?php
    function back(){
        echo "<script type='text/javascript'>document.write(\"<a href='javascript:history.back()' class='backLink' title='Voltar atr&aacute;s'>Voltar atr&aacute;s</a>\");</script>
        <noscript>
        <a href='".$_SERVER['HTTP_REFERER']."‘ class='backLink' title='Voltar atr&aacute;s'>Voltar atr&aacute;s</a>
        </noscript>";
    }

    function liga_basedados(){
        $link = mysqli_connect(DB_HOST,DB_USER,DB_PASSWORD,DB_NAME);
        if (!$link){
            die("Erro na ligação".mysqli_error());
        }
        return $link;
    }

    function executa_query($query){
        $link = liga_basedados();
        if(!$result = mysqli_query($link,$query)){
            echo("Error: ".mysqli_error($link));
        }
        return $result;
    }
    
    function guarda_variavel(){
        $link = liga_basedados();
        $retorno = mysqli_real_escape_string($link,$verifica);
        return $retorno;
    }
    

?>
<!--
$clientsideval=0; se fizermos client side
-->