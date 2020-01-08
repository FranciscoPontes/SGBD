<?php

require_once("custom/php/common.php");
echo"3";
// verifica se o utilizador fez login no wp e se tem permissão para mexer nos objetos
if (is_user_logged_in() && current_user_can('dynamic_search')) {        
 
$liga =liga_basedados();

// quando o estado da execução não está definido
    if ($_REQUEST["estado"] == ""){
        echo "<h3><b>Pesquisa Dinâmica - escolher objeto</b></h3>";

        // sql para verificar se existem objetos e tipos de objetos na base de dados
        $query_object = "SELECT DISTINCT id FROM object";
        $query_object_type = "SELECT id,name FROM obj_type";

        $resultado_object = executa_query($query_object);  
        $resultado_object_type = executa_query($query_object_type);  

        if (mysqli_num_rows($resultado_object) == 0) {    
            echo "Não há objetos na base de dados";        
        } else if (mysqli_num_rows($resultado_object_type) == 0) {  
            echo "Não há tipos de objetos na base de dados"; 
        } else {
            
            echo"<p>Objetos:</p>";

            // percorre o array que contem os tipos de objetos
            while($array_object_type = mysqli_fetch_array($resultado_object_type)){

                // sql para selecionar id e nome dos objetos e o obj_fk_id da tabela attribute
                $query_object = "SELECT object.id,object.name,obj_fk_id FROM object,attribute WHERE object.obj_type_id=".$array_object_type["id"]."
                AND object.id=attribute.obj_fk_id";
                $resultado_object = executa_query($query_object);  
                echo"<ul>";
                    echo"<li>".$array_object_type['name']."</li>";
                    echo"<ul>";

                    // percorre o array dos objetos com o atual tipo de objeto do array_object_type
                    while($array_object = mysqli_fetch_array($resultado_object)){

                        // verificar se obj_fk_id é diferente de null
                        if (!empty($array_object['obj_fk_id'])){
                            echo"<li><a href=pesquisa-dinamica?estado=escolha&obj=".$array_object["id"].">[".$array_object["name"]."]</a></li>";
                        }
                    } 
                    echo"</ul>";
                echo"</ul>";
                     
            }
        }
    }
    elseif ($_REQUEST["estado"] == "escolha"){
        $object_id=guarda_variavel($_REQUEST['obj']);
        echo "<form action='pesquisa_dinamica'>";
        $sql_busca_atributos="SELECT id,name,value_type FROM attribute WHERE obj_id=".$object_id;
        $query_busca_atributos=executa_query($sql_busca_atributos);

        // nl2br para \n ser visivel no browser
        echo nl2br("Atributos do objeto escolhido:\n");
        while ($array_busca_atributos=mysqli_fetch_array($query_busca_atributos)){
            echo nl2br("<input type='checkbox' value=".$array_busca_atributos['id'].">".$array_busca_atributos["name"]."\n");
        }

        // selecionar id de objetos que têm um atributo com value_type='obj_ref' e obj_fk_id igual ao do 
        //  objeto escolhido
        $sql_objetos="SELECT DISTINCT object.id FROM object,attribute WHERE object.id=attribute.obj_id AND value_type='obj_ref'
        AND obj_fk_id=".$object_id;
        $query_objetos=executa_query($sql_objetos);
        if (mysqli_num_rows($query_objetos)>0){
            echo nl2br("Atributos de outros objetos que referenciam este:\n");
            while($array_query_objetos=mysqli_fetch_array($query_objetos)){
                // buscar atributos do objeto
                $sql_mais_atributos="SELECT id,name FROM attribute WHERE attribute.obj_id=".$array_query_objetos['id'];
                $query_mais_atributos=executa_query($sql_mais_atributos);

                // escrever atributos
                while($array_mais_atributos=mysqli_fetch_array($query_mais_atributos)){
                    echo nl2br("<input type='checkbox' value=".$array_mais_atributos['id'].">".$array_mais_atributos["name"]."\n");
                }
            }
        }
        echo "</form>";
    }
}
else {
    echo"Não tem autorização para aceder a esta página.";  
}
?>