<?php

require_once("custom/php/common.php");
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
                $query_object = "SELECT DISTINCT object.id,object.name,obj_fk_id FROM object,attribute WHERE object.obj_type_id=".$array_object_type["id"]."
                AND object.id=attribute.obj_fk_id AND attribute.obj_id!=attribute.obj_fk_id";
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

        // para utilizar no caso obj_ref no estado execucao
        $_SESSION["id_objeto"]=$_REQUEST['obj'];

        echo "<form name='pesquisa_dinamica'>";
        $sql_busca_atributos="SELECT id,name,value_type FROM attribute WHERE obj_id=".$object_id;
        $query_busca_atributos=executa_query($sql_busca_atributos);

        // nl2br para \n ser visivel no browser
        echo nl2br("Atributos do objeto escolhido:\n");
        while ($array_busca_atributos=mysqli_fetch_array($query_busca_atributos)){
            echo nl2br("<input type='checkbox' name='atributos_escolhidos_objeto[]' value=".$array_busca_atributos['id'].">".$array_busca_atributos["name"]."\n");
            
            // diferentes resultados para diferentes tipos de objetos (enum,text,etc)
            switch($array_busca_atributos["value_type"]){
                case "enum":

                    //  sql para ir buscar os valores permitidos do atributo
                    $sql_busca_valores_permitidos="SELECT id,value FROM attr_allowed_value WHERE attribute_id=".$array_busca_atributos['id'];
                    $query_busca_valores_permitidos=executa_query($sql_busca_valores_permitidos);
                    echo nl2br("\nOperador:<select name=operador_".$array_busca_atributos["id"]."><option>  ");                
                    operadores('enum');
                    echo nl2br("</select>");
                    // option para dar a opcao de escolha vazia
                    echo"Valor:<select name=valor_".$array_busca_atributos["id"]."><option> ";
                    while ($array_busca_valores_permitidos=mysqli_fetch_array($query_busca_valores_permitidos)){
                        
                        echo "<option value=".$array_busca_valores_permitidos['value'].">".$array_busca_valores_permitidos["value"];
                        
                    }
                    echo nl2br("</select>\n\n");
                break;

                case "double":

                    //  sql para ir buscar os valores permitidos do atributo
                    $sql_busca_valores_permitidos="SELECT id,value FROM attr_allowed_value WHERE attribute_id=".$array_busca_atributos['id'];
                    $query_busca_valores_permitidos=executa_query($sql_busca_valores_permitidos);
                    echo nl2br("\nOperador:<select name=operador_".$array_busca_atributos["id"]."><option>  ");                
                    operadores('double');
                    echo nl2br("</select>");
                    echo nl2br("Valor(insira numero real):<input type='text' name=valor_".$array_busca_atributos["id"].">\n\n");
                break;

                case "int":

                    //  sql para ir buscar os valores permitidos do atributo
                    $sql_busca_valores_permitidos="SELECT id,value FROM attr_allowed_value WHERE attribute_id=".$array_busca_atributos['id'];
                    $query_busca_valores_permitidos=executa_query($sql_busca_valores_permitidos);
                    echo nl2br("\nOperador:<select name=operador_".$array_busca_atributos["id"]."><option>  ");                
                    operadores('int');
                    echo nl2br("</select>");
                    echo nl2br("Valor(insira numero inteiro):<input type='text' name=valor_".$array_busca_atributos["id"].">\n\n");
                break;

                case "bool":

                    //  sql para ir buscar os valores permitidos do atributo
                    $sql_busca_valores_permitidos="SELECT id,value FROM attr_allowed_value WHERE attribute_id=".$array_busca_atributos['id'];
                    $query_busca_valores_permitidos=executa_query($sql_busca_valores_permitidos);
                    echo nl2br("\nOperador:<select name=operador_".$array_busca_atributos["id"]."><option>  ");                
                    operadores('bool');
                    echo nl2br("</select>");
                    echo"Valor:<select name=valor_".$array_busca_atributos["id"].">";
                    echo"<option> ";
                    echo "<option value=true>true";
                    echo "<option value=false>false";
                    echo nl2br("</select>\n\n");
                break;

                case "text":

                    //  sql para ir buscar os valores permitidos do atributo
                    $sql_busca_valores_permitidos="SELECT id,value FROM attr_allowed_value WHERE attribute_id=".$array_busca_atributos['id'];
                    $query_busca_valores_permitidos=executa_query($sql_busca_valores_permitidos);
                    echo nl2br("\nOperador:<select name=operador_".$array_busca_atributos["id"]."><option>  ");                
                    operadores('text');
                    echo nl2br("</select>");
                    echo nl2br("Valor(insira texto):<input type='text' name=valor_".$array_busca_atributos["id"].">\n\n");
                break;

                case "obj_ref":
        
                    //  sql para ir buscar os valores permitidos do atributo
                    $sql_busca_valores_permitidos="SELECT id,value FROM attr_allowed_value WHERE attribute_id=".$array_busca_atributos['id'];
                    $query_busca_valores_permitidos=executa_query($sql_busca_valores_permitidos);
                    echo nl2br("\nOperador:<select name=operador_".$array_busca_atributos["id"]."><option>  ");                
                    operadores('obj_ref');
                    echo nl2br("</select>");
                    echo nl2br("Id do objeto selecionado:".$object_id."\n\n");
                break;
                    
            }
        }

        // selecionar id de objetos que têm um atributo com value_type='obj_ref' e obj_fk_id igual ao do 
        //  objeto escolhido
        $sql_objetos="SELECT DISTINCT object.id,object.name FROM object,attribute WHERE object.id=attribute.obj_id AND value_type='obj_ref'
        AND obj_fk_id=".$object_id;
        $query_objetos=executa_query($sql_objetos);
        if (mysqli_num_rows($query_objetos)>0){
            echo nl2br("\nAtributos de outros objetos que referenciam este:\n");
            while($array_query_objetos=mysqli_fetch_array($query_objetos)){
                // buscar atributos do objeto
                $sql_mais_atributos="SELECT id,name,value_type FROM attribute WHERE attribute.obj_id=".$array_query_objetos['id'];
                $query_mais_atributos=executa_query($sql_mais_atributos);
                echo nl2br("\n".$array_query_objetos["name"].":\n");
                // escrever atributos
                while($array_mais_atributos=mysqli_fetch_array($query_mais_atributos)){
                    echo nl2br("<input type='checkbox' name='atributos_escolhidos_objeto[]' value=".$array_mais_atributos['id'].">".$array_mais_atributos["name"]."\n");
                    
                    switch($array_mais_atributos['value_type']){
                        case "enum":
    
                            //  sql para ir buscar os valores permitidos do atributo
                            $sql_busca_valores_permitidos="SELECT id,value FROM attr_allowed_value WHERE attribute_id=".$array_mais_atributos['id'];
                            $query_busca_valores_permitidos=executa_query($sql_busca_valores_permitidos);
                            echo nl2br("\nOperador:<select name=operador_".$array_mais_atributos["id"]."><option>  ");                
                            operadores('enum');
                            echo nl2br("</select>");
                            echo"Valor:<select name=valor_".$array_mais_atributos["id"].">";
                            echo"<option> ";
                            while ($array_busca_valores_permitidos=mysqli_fetch_array($query_busca_valores_permitidos)){
                                
                                echo "<option value=".$array_busca_valores_permitidos['value'].">".$array_busca_valores_permitidos["value"];
                                
                            }
                            echo nl2br("</select>\n\n");
                        break;
        
                        case "double":
        
                            //  sql para ir buscar os valores permitidos do atributo
                            $sql_busca_valores_permitidos="SELECT id,value FROM attr_allowed_value WHERE attribute_id=".$array_mais_atributos['id'];
                            $query_busca_valores_permitidos=executa_query($sql_busca_valores_permitidos);
                            echo nl2br("\nOperador:<select name=operador_".$array_mais_atributos["id"]."><option>  ");                
                            operadores('double');
                            echo nl2br("</select>");
                            echo nl2br("Valor(insira numero real):<input type='text' name=valor_".$array_mais_atributos["id"].">\n\n");
                        break;
        
                        case "int":
        
                            //  sql para ir buscar os valores permitidos do atributo
                            $sql_busca_valores_permitidos="SELECT id,value FROM attr_allowed_value WHERE attribute_id=".$array_mais_atributos['id'];
                            $query_busca_valores_permitidos=executa_query($sql_busca_valores_permitidos);
                            echo nl2br("\nOperador:<select name=operador_".$array_mais_atributos["id"]."><option>  ");                
                            operadores('int');
                            echo nl2br("</select>");
                            echo nl2br("Valor(insira numero inteiro):<input type='text' name=valor_".$array_mais_atributos["id"].">\n\n");
                        break;
        
                        case "bool":
        
                            //  sql para ir buscar os valores permitidos do atributo
                            $sql_busca_valores_permitidos="SELECT id,value FROM attr_allowed_value WHERE attribute_id=".$array_mais_atributos['id'];
                            $query_busca_valores_permitidos=executa_query($sql_busca_valores_permitidos);
                            echo nl2br("\nOperador:<select name=operador_".$array_mais_atributos["id"]."><option>  ");                
                            operadores('bool');
                            echo nl2br("</select>");
                            echo"Valor:<select name=valor_".$array_mais_atributos["id"].">";
                            echo"<option> ";
                            echo "<option value=true>true";
                            echo "<option value=false>false";
                            echo nl2br("</select>\n\n");
                        break;
        
                        case "text":
        
                            //  sql para ir buscar os valores permitidos do atributo
                            $sql_busca_valores_permitidos="SELECT id,value FROM attr_allowed_value WHERE attribute_id=".$array_mais_atributos['id'];
                            $query_busca_valores_permitidos=executa_query($sql_busca_valores_permitidos);
                            echo nl2br("\nOperador:<select name=operador_".$array_mais_atributos["id"]."><option>  ");                
                            operadores('text');
                            echo nl2br("</select>");
                            echo nl2br("Valor(insira texto):<input type='text' name=valor_".$array_mais_atributos["id"].">\n\n");
                        break;

                        case "obj_ref":
        
                            //  sql para ir buscar os valores permitidos do atributo
                            $sql_busca_valores_permitidos="SELECT id,value FROM attr_allowed_value WHERE attribute_id=".$array_mais_atributos['id'];
                            $query_busca_valores_permitidos=executa_query($sql_busca_valores_permitidos);
                            echo nl2br("\nOperador:<select name=operador_".$array_mais_atributos["id"]."><option>  ");                
                            operadores('obj_ref');
                            echo nl2br("</select>");
                            echo nl2br("Id do objeto selecionado:".$object_id."\n\n");
                        break;
                    }

                }           
                
            }
        }  
        echo"<input type='hidden' name='estado' value='execucao'>
        <input type='submit' value='Pesquisar'>";
        echo "</form>";
    }
    elseif ($_REQUEST["estado"] == "execucao"){

        // se o utilizador nao selecionou nenhuma checkbox
        if (empty($_REQUEST["atributos_escolhidos_objeto"])){
            echo nl2br("\nTem de escolher pelo menos 1 atributo!\n");
            back();
            return;
        }

        $frase_pesquisa="Pesquisou com as seguintes definições:\n";
        $sql_pesquisa_final="";

        // percorre todos os atributos escolhidos pelo utilizador
        foreach ($_REQUEST["atributos_escolhidos_objeto"] as $id_atributo){

            // sql para buscar informacao dos atributos escolhidos
            $sql_atributos_escolhidos="SELECT * FROM attribute WHERE id=".$id_atributo;
            $query_atributos_escolhidos=executa_query($sql_atributos_escolhidos);

            while ($array_atributos_escolhidos=mysqli_fetch_array($query_atributos_escolhidos)){
                
                $frase_pesquisa.=" ".$array_atributos_escolhidos["name"];

                // sql para buscar informacao dos atributos escolhidos, ainda por finalizar
                $sql_pesquisa="SELECT * FROM attr_allowed_value WHERE attribute_id=".$array_atributos_escolhidos["id"];

                // verificar agora as escolhas de operador e valor para cada atributo escolhido
                       
                // se n usou nenhum filtro no atributo
                if (empty($_REQUEST["operador_".$array_atributos_escolhidos["id"]]) and empty($_REQUEST["valor_".$array_atributos_escolhidos["id"]])){
                    // para uma melhor visibilidade das escolhas na frase gerada
                    $frase_pesquisa.=";";
                }

                // caso em que selecionou um operador mas não um valor e value type nao é obj_ref
                if (!empty($_REQUEST["operador_".$array_atributos_escolhidos["id"]]) and empty($_REQUEST["valor_".$array_atributos_escolhidos["id"]]) and $array_atributos_escolhidos["value_type"]!='obj_ref'){
                    echo nl2br("\nFaltou inserir um valor!");
                    back();
                    return;
                }

                // caso em que o utilizador seleciona um valor mas nao um operador
                if (empty($_REQUEST["operador_".$array_atributos_escolhidos["id"]]) and !empty($_REQUEST["valor_".$array_atributos_escolhidos["id"]])){
                    echo nl2br("\nFaltou inserir um operador!");
                    back();
                    return;
                }

                // operador selecionado
                if (!empty($_REQUEST["operador_".$array_atributos_escolhidos["id"]])){
                    if ($_REQUEST["operador_".$array_atributos_escolhidos["id"]]=="Maior"){ $opera=">";}
                    if ($_REQUEST["operador_".$array_atributos_escolhidos["id"]]=="Menor"){ $opera="<";}
                    if ($_REQUEST["operador_".$array_atributos_escolhidos["id"]]=="Igual"){ $opera="=";}
                    else {$opera="!=";}

                    $frase_pesquisa.=$opera;
                    $sql_pesquisa.=" AND value".$opera;
                }

                // caso em que selecionou operador e atributo é do tipo obj_ref
                if ($array_atributos_escolhidos["value_type"]=='obj_ref'){
                    $frase_pesquisa.=$_SESSION["id_objeto"]."; ";
                    $sql_pesquisa.=$_SESSION["id_objeto"];
                }

                // selecionou operador e valor, e atributo n é do tipo obj_ref
                elseif (!empty($_REQUEST["valor_".$array_atributos_escolhidos["id"]])){
                    $frase_pesquisa.=$_REQUEST["valor_".$array_atributos_escolhidos["id"]]."; ";
                    $sql_pesquisa.="'".$_REQUEST["valor_".$array_atributos_escolhidos["id"]]."'";
                }


                if ($sql_pesquisa_final==""){$sql_pesquisa_final.=$sql_pesquisa;}
                else {$sql_pesquisa_final.=" UNION ".$sql_pesquisa;}
            }
        }

        $query_pesquisa_final=executa_query($sql_pesquisa_final);
        
        // variavel tabela contem toda a tabela em formato string, que depois é feito echo
        $tabela="<table><caption>".$frase_pesquisa."</caption>";

        $atributos_ids=[];
        while ($array_pesquisa_final=mysqli_fetch_array($query_pesquisa_final)){   
            
            // adiciona ao array ids de atributos distintos, que foram selecionados pela pesquisa do utilizador
            // podiam ser varios com o mesmo id caso o atributo tenha varios valores
            if (!in_array($array_pesquisa_final["attribute_id"],$atributos_ids)){
                $atributos_ids[]=$array_pesquisa_final["attribute_id"];
            }
        }

        // segunda linha
        $tabela.="<tr>";
        $nomes_atributos=[];
        $ids=[];
        foreach ($atributos_ids as $atributos_ids){

            // sql para ir buscar informacao dos atributos que tenham valores permitidos resultantes
            // na pesquisa
            $sql_atributos="SELECT * FROM attribute WHERE id=".$atributos_ids;
            $query_atributos=executa_query($sql_atributos);

            // para cada atributo escrever o form field name e guarda em arrays name e id  
            while($array_atributos=mysqli_fetch_array($query_atributos)){
                $tabela.="<th>".$array_atributos["form_field_name"]."</th>";
                $nomes_atributos[]=$array_atributos["name"];
                $ids[]=$array_atributos["id"];
            }
        }
        $tabela.="</tr>";

        // terceira linha
        $tabela.="<tr>";
        // escrever nomes dos atributos
        foreach ($nomes_atributos as $nome){
            $tabela.="<td>".$nome."</td>";
        }
        $tabela.="</tr>";
  
        $tabela.="<tr>";

        // para cada id vai escrever os valores permitidos resultantes da pesquisa
        // garantindo assim que cada atributo fica com os seus valores permitidos na linha abaixo
        // da tabela
        foreach ($ids as $id){
            $tabela.="<td>";
            $escreve="";
            $query_escreve=executa_query($sql_pesquisa_final);
            while ($array_query_escreve=mysqli_fetch_array($query_escreve)){
                if ($array_query_escreve["attribute_id"]==$id){
                    $escreve.=$array_query_escreve["value"]." ";
                }
            }
            $tabela.=$escreve."</td>";
        }
        $tabela.="</tr>"; 

        $tabela.="</table>";
        echo $tabela;
        echo "<a href='gera_excel.php'>Exportar para excel</a>";
        
    }
}
else {
    echo"Não tem autorização para aceder a esta página.";  
} 
?>