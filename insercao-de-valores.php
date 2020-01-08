<?php
require_once ("custom/php/common.php");
 
// Verifica se o utilizador fez login no wp e se tem permissão para mexer nos atributos
if (is_user_logged_in() && current_user_can('insert_values')) {        
    $liga = liga_basedados();

    // Quando o estado da execução não está definido
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
            
            <p>Objetos:</p>
            <?php

            while($array_object_type = mysqli_fetch_array($resultado_object_type)){
                $query_object = "SELECT * FROM object WHERE object.obj_type_id='{$array_object_type['id']}'";

                $resultado_object = executa_query($query_object);  
                ?> 
                <ul>
                    <li><?php echo $array_object_type['name']; ?></li>
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
            
            <p>Formulários customizados:</p>
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
        // session_start();
        // $_SESSION['obj_id'] = guarda_variavel($_REQUEST['obj']);

        $obj_id = guarda_variavel($_REQUEST['obj']);

        $query_object = "SELECT DISTINCT name, obj_type_id 
            FROM object 
            WHERE " . $obj_id . "=object.id";
        $resultado_query = executa_query($query_object);
        $array_object = mysqli_fetch_array($resultado_query);

        $obj_name  = guarda_variavel($array_object['name']);
        $obj_type_id  = guarda_variavel($array_object['obj_type_id']);
        ?> 
        
        
        <h3>Inserção de valores - <?php echo $obj_name;?></h3>
    
        <?php
         
        $query_novo_obj = "SELECT * 
            FROM attribute
            WHERE state='active' AND " . $obj_id . "=obj_id";
        $resultado_novo_obj = executa_query($query_novo_obj);

        $array_novo_obj = mysqli_fetch_array($resultado_novo_obj);
        
        // $object_id_relacao = guarda_variavel($resultado_novo_obj['rel_id']);
        // $object_nome_do_atributo = guarda_variavel($resultado_novo_obj['name']);
        // $object_tipo_de_valor = guarda_variavel($resultado_novo_obj['value_type']);
        // $object_nome_formulario = guarda_variavel($resultado_novo_obj['form_field_name']);
        // $object_tipo_formulario = guarda_variavel($resultado_novo_obj['form_field_type']);
        // $object_id_unidade = guarda_variavel($resultado_novo_obj['unit_type_id']);
        // $object_ordem_formulario = guarda_variavel($resultado_novo_obj['form_field_order']);
        // $object_tamanho_formulario = guarda_variavel($resultado_novo_obj['form_field_size']);
        // $object_obrigatorio = guarda_variavel($resultado_novo_obj['mandatory']);
        // $object_estado = guarda_variavel($resultado_novo_obj['state']);
        // $object_objeto_referenciado = guarda_variavel($resultado_novo_obj['obj_fk_id']);

        // switch($valor_a_executar) {
        //     case 0:
        //     break;
        // }

        ?> 
       <?php echo '<a href="insercao-de-valores?estado=validar&obj=' . $obj_id . '">Validar</a>';
    } elseif ($_REQUEST["estado"] == "validar") {
        ?> 
        <h3>Inserção de valores - <?php echo $obj_name;?> - Validar</h3>
        <p>Estamos prestes a inserir os dados abaixo na base de dados. 
        Confirma que os dados estão correctos e pretende submeter os mesmos?</p>
        <?php echo '<a href="insercao-de-valores?estado=inserir&obj=' . $obj_id . '">Submeter</a>';
    } elseif ($_REQUEST["estado"] == "inserir") {
        ?> 
        <h3>Inserção de valores - <?php echo $obj_name;?> - Inserção</h3>
        <p>Inseriu o(s) valor(es) com sucesso.</p>
        <p>Clique em <a href="insercao-de-valores">Voltar</a> para voltar ao início da inserção de valores e poder escolher outro objeto 
        ou em <?php echo '<a href="insercao-de-valores?estado=introducao&obj=' . $obj_id . '">Continuar a inserir valores neste objeto</a>';?> se quiser continuar a inserir valores</p> 
        <?php
    }
} else { ?>
    Não tem autorização para aceder a esta página.
    <?php
    }?>