<?php 
require_once("custom/php/common.php");
 
// faz verificação, para ver se o utilizador está logado e se ter permissão para alterar objetos
if (is_user_logged_in() && current_user_can('manage_allowed_values')) {        
 
$liga =liga_basedados();

// Quando o estado da execução não está definido
if ($_REQUEST["estado_execucao"] == "") {    
    // código SQL em formato string para obter atributos cujo tipo de valor seja enum
    $query_atributos="SELECT * FROM attribute WHERE tipo_de_valor='enum'";
    $result_query_atributos=executa_query($query_atributos);
    
    $lines_atributo = mysqli_num_rows($result_query_atributos);

    if ($lines_atributo==0){
        echo 'Não há atributos especificados cujo tipo de valor seja enum. Especificar primeiro novo(s) atributo(s) e depois voltar a esta opção.';
    }
    else{?>
    <table>
        <tr>
            <th>objeto</th>
            <th>id</th>
            <th>atributo</th>
            <th>id</th>
            <th>valores permitidos</th>
            <th>estado</th>
            <th>ação</th>
        </tr>
        <?php
            
            // código SQL em formato string para obter o nome do objeto,nome do atributo e id, atributos do tipo enum
            $query_objeto_e_atributo="SELECT object.name,attribute.id,attribute.nome_do_atributo FROM object,attribute
                    WHERE attribute.objeto_id=object.id AND attribute.tipo_de_valor='enum'";

            $result_objeto_e_atributo=executa_query($query_objeto_e_atributo);

            while($array_objeto_e_atributo=mysqli_fetch_array($result_objeto_e_atributo)){
                
                $query_valores_permitidos="SELECT *
                    FROM  attr_allowed_value WHERE attribute_id=".$array_objeto_e_atributo["id"]." "."ORDER BY value";

                $result_valores_permitidos=executa_query($query_valores_permitidos);
                $numero_valores_permitidos=mysqli_num_rows($result_valores_permitidos);
                
                // se houverem tuplos
                if ($numero_valores_permitidos>0){
                    ?>
                    <tr>
                        <td colspan="1" rowspan="<?php echo $numero_valores_permitidos;?>">
                            <?php echo $array_objeto_e_atributo["name"]; ?>
                        </td>
                        <td colspan="1" rowspan="<?php echo $numero_valores_permitidos;?>">
                            <?php echo $array_objeto_e_atributo["id"]; ?>
                        </td>
                        <td colspan="1" rowspan="<?php echo $numero_valores_permitidos;?>">
                            <?php echo '<a href=gestao-de-valores-permitidos?estado=introducao&atributo='.$array_objeto_e_atributo["id"].'>['.$array_objeto_e_atributo["nome_do_atributo"].']</a>'; ?>
                        </td>
                    <?php
                    $variavel=1;
                    while ($array_valores_permitidos=mysqli_fetch_array($result_valores_permitidos)){
                        if ($variavel==1){
                            ?>
                            <td> <?php
                            echo $array_valores_permitidos["id"];
                            ?>
                            </td>
                            <td> <?php
                                echo $array_valores_permitidos["value"];
                                ?>
                            </td>
                            <td> <?php
                                echo $array_valores_permitidos["state"];
                                ?>
                            </td>
                            <td> <?php if ($array_valores_permitidos["state"]=='active'){
                                echo '[editar][desativar]';
                                }  
                                ?>
                            </td></tr><?php
                            $variavel=2; 
                        }
                        else{
                            ?>
                            <tr>
                                <td> <?php
                                echo $array_valores_permitidos["id"];
                                ?>
                                </td>
                                <td> <?php
                                    echo $array_valores_permitidos["value"];
                                    ?>
                                </td>
                                <td> <?php
                                    echo $array_valores_permitidos["state"];
                                    ?>
                                </td>
                                <td> <?php if ($array_valores_permitidos["state"]=='active'){
                                    echo '[editar][desativar]';
                                    }  
                                    ?>
                                </td>
                            </tr><?php
                        }
                    }
                    $variavel=1;
                }
                else{
                    ?>
                    <tr>
                        <td colspan="1" rowspan="1">
                            <?php echo $array_objeto_e_atributo["name"]; ?>
                        </td>
                        <td colspan="1" rowspan="1">
                            <?php echo $array_objeto_e_atributo["id"]; ?>
                        </td>
                        <td colspan="1" rowspan="1">
                            <?php echo '<a href=gestao-de-valores-permitidos?estado=introducao&atributo='.$array_objeto_e_atributo["id"].'>['.$array_objeto_e_atributo["nome_do_atributo"].']</a>'; ?>
                        </td>
                        <td colspan="4"> <?php
                            echo "Não há valores permitidos definidos";
                            ?>
                        </td>
                    </tr><?php
                }                
            }
        ?>
    </table>          
                  
    <?php
        }
    }
}

else{
    ?>
    Não tem autorização para aceder a esta página
    <?php
}
?>