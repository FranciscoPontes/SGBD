<?php
    require_once("custom/php/common.php");

    // verifica se utilizador tem permissao
    if (is_user_logged_in() && current_user_can('values_import')) {

        $liga =liga_basedados();

        if ($_REQUEST["estado"] == ""){
            echo "<h3><b>Importação de valores - escolher objeto</b></h3>";

            $query_object = "SELECT id, name FROM object";
            $query_object_type = "SELECT id, name FROM obj_type";

            $resultado_object = executa_query($query_object);  
            $resultado_object_type = executa_query($query_object_type);  

            if (mysqli_num_rows($resultado_object) == 0) {    
                echo "Não há objetos na base de dados";        
            } else if (mysqli_num_rows($resultado_object_type) == 0) {  
                echo "Não há tipos de valores na base de dados"; 
            } else {
                $query_object_type = "SELECT * FROM obj_type";
                $resultado_object_type = executa_query($query_object_type);                 
                echo"<p>Objetos:</p>";

                // para cada tipo de objeto escreve o nome dos objetos em forma de lista
                while($array_object_type = mysqli_fetch_array($resultado_object_type)){
                    $query_object = "SELECT * FROM object WHERE object.obj_type_id=".$array_object_type["id"];
                    $resultado_object = executa_query($query_object);  
                    echo"<ul>
                        <li>".$array_object_type["name"]."</li>
                        <ul>";
                        while($array_object = mysqli_fetch_array($resultado_object)){
                            echo"<li><a href=importacao-de-valores?estado=introducao&obj=".$array_object["id"].">["
                                            .$array_object["name"]."]</a></li>";
                        } 
                        echo"</ul>
                    </ul>";                       
                }
                
                echo"<p>Formulários customizados:</p>";

                $query_forms = "SELECT * FROM custom_form";
                $resultado_forms = executa_query($query_forms);  

                // escreve o nome dos formularios em forma de lista
                echo"<ul>"; 
                    while($array_forms = mysqli_fetch_array($resultado_forms)){
                    
                    echo"<li><a href=importacao-de-valores?estado=introducao&form=".$array_forms["id"].">["
                                                .$array_forms["name"]."]</a></li>";                                           
                } 
                echo"</ul>";
            }
        }

        elseif ($_REQUEST["estado"] == "introducao"){
            echo"<table><tr>";
            
            // se for objeto
            if (!empty($_REQUEST["obj"])){
                $sql_atributos="SELECT * FROM attribute WHERE obj_id=".$_REQUEST["obj"];
            }
            // se for form
            else{
                $sql_atributos="SELECT attribute.id as id,attribute.value_type as value_type, attribute.form_field_name as form_field_name 
                    FROM attribute,custom_form_has_attribute WHERE custom_form_has_attribute.custom_form_id=".$_REQUEST["form"]." AND
                    attribute.id=custom_form_has_attribute.attribute_id";
            }
            
            $query_atributos=executa_query($sql_atributos);

            while ($array_atributos=mysqli_fetch_array($query_atributos)){

                // se o value type for enum escrever o form field name o numero de vezes
                // equivalente ao numero de valores permitidos
                if ($array_atributos["value_type"]=='enum'){
                    $sql_valores_permitidos="SELECT * FROM attr_allowed_value WHERE attribute_id=".$array_atributos["id"];
                    $query_valores_permitidos=executa_query($sql_valores_permitidos);
                    $numero_valores=mysqli_num_rows($query_valores_permitidos);
                    for($i=0;$i<$numero_valores;$i++){
                        echo"<td>".$array_atributos["form_field_name"]."</td>";
                    }
                }
                // caso contrario escreve apenas uma vez
                else{
                    echo"<td>".$array_atributos["form_field_name"]."</td>";
                }
            }
            echo"</tr>";

            // segunda linha
            echo"<tr>";

            $query_atributos2=executa_query($sql_atributos);

            while ($array_atributos=mysqli_fetch_array($query_atributos2)){
                // se o value type do atributo for enum escreve os valores permitidos
                if ($array_atributos["value_type"]=='enum'){
                    $sql_valores_permitidos="SELECT * FROM attr_allowed_value WHERE attribute_id=".$array_atributos["id"];
                    $query_valores_permitidos=executa_query($sql_valores_permitidos);
                    while($array_valores_permitidos=mysqli_fetch_array($query_valores_permitidos)){
                        echo"<td>".$array_valores_permitidos["value"]."</td>";
                    }
                }
                // caso contrario uma celula vazia
                else{
                    echo"<td></td>";
                }
            }
            echo"</tr>";
            echo "</table>";
            echo"Deverá copiar estas linhas para um ficheiro excel e introduzir os valores a importar, sendo que no caso dos atributos enum, 
            deverá constar um 0 quando esse valor permitido não se aplique à instância em causa e um 1 quando esse valor se aplica.";
        
            echo"<form><p><input type= 'hidden' name= 'estado' value= 'insercao'>
            <input class= 'button' type= 'submit' value= 'Upload ficheiro excel'></p></form>";
        }

    }
    else{
        echo"Não tem autorização para aceder a esta página";
    }
?>