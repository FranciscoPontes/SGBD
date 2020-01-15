<?php
require_once ("custom/php/common.php");
 
if (is_user_logged_in() && current_user_can('insert_values')) {        
    $liga = liga_basedados();

    // Quando o estado não está definido
    if ($_REQUEST["estado"] == "") {    
    
        $query_object = "SELECT id, name FROM object";
        $query_object_type = "SELECT id, name FROM obj_type";

        $resultado_object = executa_query($query_object);  
        $resultado_object_type = executa_query($query_object_type);  

        if (mysqli_num_rows($resultado_object) == 0) {    
            echo "Não há valores especificados";        
        } else if (mysqli_num_rows($resultado_object_type) == 0) {  
            echo "Não há valores especificados"; 
        } else {
            ?>
            
            <?php
            $query_object_type = "SELECT * FROM obj_type";
    
            $resultado_object_type = executa_query($query_object_type); 
            ?>
            
            <h2>Objetos:</h2>
            <?php

            while($array_object_type = mysqli_fetch_array($resultado_object_type)){
                $query_object = "SELECT * FROM object WHERE object.obj_type_id='{$array_object_type['id']}'";

                $resultado_object = executa_query($query_object);  
                ?> 
                <ul>
                    <li><h3><?php echo $array_object_type['name']; ?></h3></li>
                    <ul><?php 
                     while($array_object = mysqli_fetch_array($resultado_object)){?>
                        <li>[<?php echo '<a href="insercao-de-valores?estado=introducao&obj=' . $array_object['id'] . '">
                                            ' . $array_object['name'] . ' 
                                        </a>'; ?>]</li>
                    <?php } ?>
                    </ul>
                </ul>
            <?php
                     
            }?>
            
            <h2>Formulários customizados:</h2>
            <?php
            $query_forms = "SELECT * FROM custom_form";

            $resultado_forms = executa_query($query_forms);  
            ?> 
            
            <ul><?php 
                while($array_forms = mysqli_fetch_array($resultado_forms)){?>
                
                <li>[<?php echo '<a href="insercao-de-valores?estado=introducao&form=' . $array_forms['id'] . '">
                                            ' . $array_forms['name'] . ' 
                                        </a>'; ?>]</li>
            <?php } ?>
            </ul>
            <?php
        }
    } elseif ($_REQUEST["estado"] == "introducao") {
        if($_REQUEST["obj"]) {
            $_SESSION["obj_id"] = guarda_variavel($_REQUEST['obj']);

            $query_object = "SELECT DISTINCT name, obj_type_id 
                FROM object 
                WHERE object.id = '{$_SESSION["obj_id"]}'";
            $resultado_query = executa_query($query_object);
            $array_object = mysqli_fetch_array($resultado_query);
    
            $_SESSION["obj_name"] = guarda_variavel($array_object['name']);
            $_SESSION["obj_type_id"] = guarda_variavel($array_object['obj_type_id']);
            ?> 
            
            
            <h3>Inserção de valores - <?php echo $_SESSION["obj_name"];?></h3>
            <form method="post"
                  action=<?php echo "?estado=validar&obj={$_SESSION["obj_id"]}";?>
                  name=<?php echo "obj_type_{$_SESSION["obj_type_id"]}_obj_{$_SESSION["obj_id"]}";?>>
            <?php
             
            $query_attribute = "SELECT attribute.*, attr_unit_type.name AS unit
                FROM attribute 
                LEFT JOIN attr_unit_type ON (attribute.unit_type_id = attr_unit_type.id),object
                WHERE attribute.obj_id = object.id
                AND attribute.obj_id = {$_SESSION["obj_id"]} 
                AND attribute.state = 'active'";

            $resultado_attribute = executa_query($query_attribute);
            
            $lines_attribute = mysqli_num_rows($resultado_attribute);
    
            if (!$lines_attribute) {
                echo "<p>Não existem atributos para este formulário.</p>";
                back();
                return;
            }
            else {
                for($i = 0 ; $i < $lines_attribute ; $i++) {
                    $array_resultado_attribute = mysqli_fetch_assoc($resultado_attribute);

                    $id_attribute = $array_resultado_attribute['id'];

                    switch($array_resultado_attribute['value_type']) {
                        case "text":
                            echo "{$array_resultado_attribute['name']}";

                            switch($array_resultado_attribute['form_field_type'])
                            {
                                case "text":
                                    ?><p>Text</p>
                                    <input type="text" name=<?php echo $array_resultado_attribute['id']; ?> autocomplete="off">
                                        <?php echo $array_resultado_attribute['unit']; ?> <br><br> <?php
                                break;

                                case "textbox":
                                    ?><p>Textbox</p>
                                    <input type="textbox" name=<?php echo $array_resultado_attribute['id']; ?> autocomplete="off">
                                        <?php echo $array_resultado_attribute['unit']; ?> <br><br><?php
                                break;

                                default:
                                ?>
                                    <p>Não tem atributos deste tipo.</p>
                                <?php
                                break;
                            }
                            break;

                            //Caso o atributo seja do tipo 'bool'
                        case "bool":
                            ?>
                            <input type="radio" name=<?php echo $array_resultado_attribute["id"]; ?> autocomplete="off" >
                                <?php echo "{$array_resultado_attribute["name"]} {$array_resultado_attribute["unit"]}"; ?><br><br>
                            
                            <?php
                        break;

                        // Caso o atributo seja do tipo 'Int' ou 'Double'
                        case "int":
                        case "double":
                            echo "{$array_resultado_attribute["name"]}";
                            ?>
                            <input type="text" name=<?php echo $array_resultado_attribute["id"]; ?> autocomplete="off">
                                <?php echo $array_resultado_attribute["unit"]; ?>
                            
                            <?php
                        break;

                        //Caso o atributo seja do tipo 'enum': várias opções - preciso adquirir primeiro
                        case "enum":
                            //String com query para adquirir odas as opções disponíveis para o "ENUM" em questão.
                            //
                            $opcoes = "SELECT attr_allowed_value.id,
                                              attr_allowed_value.value
                                        FROM attribute,
                                             attr_allowed_value 
                                          WHERE attr_allowed_value.attribute_id = attribute.id
                                            AND attr_allowed_value.attribute_id ='{$array_resultado_attribute["id"]}'"; //atributo clickado
                            //execução
                            $query_opc = executa_query($opcoes);

                            //Busca o número de opções - conta rows (linhas)
                            $nr_opcoes = mysqli_num_rows($query_opc);

                            //Mostra o nome do atributo //label
                            echo "{$array_resultado_attribute["name"]}.':'";

                            //switch para os ti
                            switch($array_resultado_attribute["form_field_type"])
                            {
                                //Para o caso do campo de seleção seja do tipo 'radio button'
                                case "radio":
                                    //Percorre todas as opções
                                    for($j=0; $j<$nr_opcoes; $j++)
                                    {
                                        //*Fetch a result row as an associative array*
                                        $opcao_atual = mysqli_fetch_assoc($query_opc);
                                        ?>
                                        <input type="radio"
                                               name=<?php echo $array_resultado_attribute["id"]; ?> autocomplete="off"
                                               value="<?php echo $opcao_atual["value"]; ?>">
                                            <?php echo "{$opcao_atual["value"]} {$array_resultado_attribute["unit"]}"; ?>
                                        
                                    <?php
                                    }
                                    ?>
                                    <?php
                                    break;
                                //Para o caso do campo de seleção seja do tipo 'checkbox'
                                case "checkbox":
                                    //Percorre todas as opções
                                    for($j = 0; $j < $nr_opcoes; $j++)
                                    {
                                        //*Fetch a result row as an associative array*
                                        $opcao_atual = mysqli_fetch_assoc($query_opc);
                                        ?>
                                        <input type="checkbox"
                                               name=<?php echo $array_resultado_attribute["id"]; ?> autocomplete="off"
                                               value="<?php echo $opcao_atual["value"]; ?>" >
                                            <?php echo "{$opcao_atual["value"]} {$array_resultado_attribute["unit"]}"; ?> <?php
                                    }
                                    break;

                                //Para o caso do campo de seleção seja do tipo 'selectbox'
                                case "selectbox":
                                    // Percorre todas as opções
                                    ?>
                                    <select name=<?php echo $array_resultado_attribute["id"]; ?> > <?php
                                        for($j = 0; $j < $nr_opcoes; $j++)
                                        {
                                            //*Fetch a result row as an associative array*
                                            $opcao_atual = mysqli_fetch_assoc($query_opc);
                                        ?>
                                            <option value="<?php echo $opcao_atual["value"]; ?>" >
                                                <?php echo "{$opcao_atual["value"]} {$array_resultado_attribute["unit"]}"; ?>
                                        
                                    <?php
                                        }
                                    ?>
                                    </select>
                                    
                                    <?php
                                    break;

                                //Caso não seja nenhuma das opções anteriores
                                default:
                                    ?>
                                    <div class="alert">
                                        <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span>
                                        Nenhuma opção enum.
                                    </div>
                                    <?php
                                    break;
                            }
                            break;

                        //Caso o atributo seja do tipo 'obj_ref': referência a um objecto
                        case "obj_ref":
                            //Busca todas as opções de instâncias
                            $opcoes = "SELECT obj_inst.id AS obj_inst_id, 
                                                  obj_inst.object_name
                                            FROM attribute,
                                                 obj_inst
                                              WHERE attribute.obj_fk_id = obj_inst.object_id
                                                AND attribute.obj_id = '{$_SESSION["obj_id"]}'";
                            //execução
                            $query_opc = executa_query($opcoes);

                            //Busca o número de opções
                            $nr_opcoes = mysqli_num_rows($query_opc);

                            //Mostra o nome da attributos
                            echo "{$array_resultado_attribute["name"]}";

                            //Mostra todas as opções numa 'selectbox'
                            ?>
                            <select name=<?php echo $array_resultado_attribute["id"];?>>
                            <?php
                                for($j = 0; $j < $nr_opcoes; $j++)
                                {
                                    $opcao_atual = mysqli_fetch_assoc($query_opc);
                                    ?>
                                    <option value="<?php echo $opcao_atual["obj_inst_id"].','.$opcao_atual["object_name"]; ?>">
                                        <?php echo "{$opcao_atual["object_name"]}"; ?> 
                            <?php
                                }
                            ?>
                            </select>
                            
                            <?php
                            break;
                        //Caso contrário
                        default:
                            ?>
                            <div class="alert">
                                <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span>
                                Nenhuma opção enum.
                            </div>
                            <?php
                            break;
                    }
                }
            }
        }

        back();
        echo '<a href="insercao-de-valores?estado=validar&obj=' . $_SESSION["obj_id"] . '">Validar</a>';
    } elseif ($_REQUEST["estado"] == "validar") {
        ?> 
        <h3>Inserção de valores - <?php echo $_SESSION["obj_name"];?> - Validar</h3>
        <p>Estamos prestes a inserir os dados abaixo na base de dados. 
        Confirma que os dados estão correctos e pretende submeter os mesmos?</p>
        <?php echo '<a href="insercao-de-valores?estado=inserir&obj=' . $_SESSION["obj_id"] . '">Submeter</a>';
    } elseif ($_REQUEST["estado"] == "inserir") {
        ?> 
        <h3>Inserção de valores - <?php echo $_SESSION["obj_name"];?> - Inserção</h3>
        <p>Inseriu o(s) valor(es) com sucesso.</p>
        <p>Clique em <a href="insercao-de-valores">Voltar</a> para voltar ao início da inserção de valores e poder escolher outro objeto 
        ou em <?php echo '<a href="insercao-de-valores?estado=introducao&obj=' . $_SESSION["obj_id"] . '">Continuar a inserir valores neste objeto</a>';?> se quiser continuar a inserir valores</p> 
        <?php
    }
} else { ?>
    Não tem autorização para aceder a esta página.
    <?php
    }?>