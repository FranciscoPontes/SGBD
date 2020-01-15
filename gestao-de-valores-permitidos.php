<?php 
require_once("custom/php/common.php");

// faz verificação, para ver se o utilizador está logado e se ter permissão para alterar objetos
if (is_user_logged_in() && current_user_can('manage_allowed_values')) {        
$liga =liga_basedados();
// Quando o estado da execução não está definido
if ($_REQUEST["estado"] == "") {    
    // código SQL em formato string para obter atributos cujo tipo de valor seja enum
    $query_atributos="SELECT * FROM attribute WHERE value_type='enum'";
    $result_query_atributos=executa_query($query_atributos);
    
    $lines_atributo = mysqli_num_rows($result_query_atributos);

    if ($lines_atributo==0){
        echo 'Não há atributos especificados cujo tipo de valor seja enum. Especificar primeiro novo(s) atributo(s) e depois voltar a esta opção.';
    }
    else{
        echo"<table>
            <tr>
                <th>objeto</th>
                <th>id</th>
                <th>atributo</th>
                <th>id</th>
                <th>valores permitidos</th>
                <th>estado</th>
                <th>ação</th>
            </tr>";
            
            // código SQL em formato string para obter o nome do objeto,nome do atributo e id, atributos do tipo enum
            $query_objeto="SELECT DISTINCT object.name,object.id FROM object,attribute
                    WHERE attribute.obj_id=object.id AND attribute.value_type='enum'";

            $result_objeto=executa_query($query_objeto);
            
            // ciclo que percorre o array associativo que contem o nome dos objetos
            while($array_objeto=mysqli_fetch_array($result_objeto)){
                
                // código para definir o tamanho em linhas de cada objeto, guardado na variável $size_rowspan

                $size_rowspan=0;
                $query_atributo_tamanho="SELECT id,name FROM attribute
                    WHERE obj_id=".$array_objeto["id"]." AND value_type='enum'";               
                $result_atributo_tamanho=executa_query($query_atributo_tamanho);
                while($array_atributo_tamanho=mysqli_fetch_array($result_atributo_tamanho)){
                    $query_valores_permitidos_tamanho="SELECT *
                    FROM  attr_allowed_value WHERE attribute_id=".$array_atributo_tamanho["id"]." "."ORDER BY value";

                    $result_valores_permitidos_tamanho=executa_query($query_valores_permitidos_tamanho);
                    $numero_valores_permitidos_tamanho=mysqli_num_rows($result_valores_permitidos_tamanho);
                    if ($numero_valores_permitidos_tamanho==0){
                        $size_rowspan=$size_rowspan+1;
                    }
                    else{
                        $size_rowspan=$size_rowspan+$numero_valores_permitidos_tamanho;
                    }
                }
                echo"<tr>";    
                    //colspan define o numero de colunas que irá ocupar(tamanho na horizontal) 
                            //e rowspan define o numero de linhas que ira ocupar(tamanho na vertical)
                    echo"<td colspan='1' rowspan='$size_rowspan'>
                        ".$array_objeto["name"]."
                    </td>";
                $var=1;

                $query_atributo="SELECT id,name FROM attribute
                    WHERE obj_id=".$array_objeto["id"]." AND value_type='enum'";
                
                $result_atributo=executa_query($query_atributo);
                
                // ciclo que percorre o array associativo que contem os atributos
                while($array_atributo=mysqli_fetch_array($result_atributo)){
                    $query_valores_permitidos="SELECT *
                    FROM  attr_allowed_value WHERE attribute_id=".$array_atributo["id"]." "."ORDER BY value";

                    $result_valores_permitidos=executa_query($query_valores_permitidos);
                    $numero_valores_permitidos=mysqli_num_rows($result_valores_permitidos);
                    // se houverem tuplos
                    if ($numero_valores_permitidos>0){

                            //colspan define o numero de colunas que irá ocupar(tamanho na horizontal) 
                            //e rowspan define o numero de linhas que ira ocupar(tamanho na vertical)
                            if ($var==2){echo"<tr>";}
                            echo"<td colspan='1' rowspan='$numero_valores_permitidos'>
                                ".$array_atributo["id"]."
                            </td>
                            <td colspan='1' rowspan='$numero_valores_permitidos'>
                                <a href=gestao-de-valores-permitidos?estado=introducao&atributo=".$array_atributo["id"].">[".$array_atributo["name"]."]</a>
                            </td>";
                            $var=2;
                            $variavel=1;

                            // ciclo que percorre o array associativo
                            while ($array_valores_permitidos=mysqli_fetch_array($result_valores_permitidos)){
                                if ($variavel==2){echo"<tr>";}
                                    echo"<td>".$array_valores_permitidos["id"]."</td>
                                    <td> ".$array_valores_permitidos["value"]." </td>
                                    <td> ".$array_valores_permitidos["state"]." </td>
                                    <td>"; if ($array_valores_permitidos["state"]=='active'){
                                        echo"[editar][desativar]";
                                        }  
                                    echo"</td> </tr>"; 
                                $variavel=2;
                            }
                            $variavel=1;
                    }

                    // n tem valores permitidos                      
                    elseif ($numero_valores_permitidos==0){
                            //colspan define o numero de colunas que irá ocupar(tamanho na horizontal) 
                            //e rowspan define o numero de linhas que ira ocupar(tamanho na vertical)
                            if ($var==2){echo"<tr>";}
                            echo"<td colspan='1' rowspan='1'>
                                    ".$array_atributo["id"]."
                                </td>
                                <td colspan='1' rowspan='1'>
                                    <a href=gestao-de-valores-permitidos?estado=introducao&atributo=".$array_atributo["id"].">[".$array_atributo["name"]."]</a>
                                </td>
                                <td colspan='4'> 
                                    Não há valores permitidos definidos
                                </td> </tr>";
                            $var=2;
                    }                     
                }
            $var=1;                   
            }
    echo"</table>";                           
        }
    }
    elseif ($_REQUEST["estado"] == "introducao"){

        // guarda na variável de sessão 
        $_SESSION["attribute_id"] = guarda_variavel($_REQUEST["atributo"]);
        echo"<h3><b>Gestão de valores permitidos - introdução</b></h3>";
    
        //Formulario
        echo"<form name='gestao-de-valores-permitidos'>
            <p><label><b>Valor:</b></label>
                <input type='text' name='valor'>
            </p>
            <p>
                <input type= 'hidden' name= 'estado' value= 'inserir'>
                <input type= 'submit' value= 'Inserir valor permitido'>
            </p>
        </form>";
    }
    elseif ($_REQUEST["estado"] == "inserir"){
        $attribute_value = guarda_variavel($_REQUEST["valor"]);
        $attribute_id= $_SESSION["attribute_id"];

        if (empty($attribute_value)){
            echo"<p>É necessário inserir um valor do tipo texto!</p>";
            back();
            return;
        }
        echo"<h3><b>Gestão de valores permitidos - inserção</b></h3>";
            // código SQL em formato string para inserir novos valores permitidos
            $query_insere = "INSERT INTO `attr_allowed_value` (`id`, `attribute_id`, `value`, `state`) VALUES (NULL,'$attribute_id','$attribute_value','active')"; 

            $result_insere = executa_query($query_insere);
 
            if ($result_insere) {
                mysqli_query($liga,'COMMIT');
                echo"<p>Inserção de dados feita com sucesso!
                Clique  em <a href='gestao-de-valores-permitidos'>continuar</a> para avançar.";
            }       
    }
}

else{
    echo "Não tem autorização para aceder a esta página";
}
?>