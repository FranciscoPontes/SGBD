<?php
    function back(){
        echo "<script type='text/javascript'>document.write(\"<a href='javascript:history.back()' class='backLink' title='Voltar atr&aacute;s'>Voltar atr&aacute;s</a>\");</script>
        <noscript>
        <a href='".$_SERVER['HTTP_REFERER']."‘ class='backLink' title='Voltar atr&aacute;s'>Voltar atr&aacute;s</a>
        </noscript>";
    }
       
    function executa_query($query){
        $link = mysqli_connect(DB_HOST,DB_USER,DB_PASSWORD,DB_NAME);
        if(!$result = mysqli_query($link,$query)){
            echo("Error: ".mysqli_error($link));
        }
        return $result;
    }
    
    function liga_basedados(){
        $link = mysqli_connect(DB_HOST,DB_USER,DB_PASSWORD,DB_NAME);
        return $link;
    }

    function guarda_variavel(){
        $link = mysqli_connect(DB_HOST,DB_USER,DB_PASSWORD,DB_NAME);
        $retorno = mysqli_real_escape_string($link,$verifica);
        return $retorno;
    }
    

?>
<!--
$clientsideval=0; se fizermos client side
-->