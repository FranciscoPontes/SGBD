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
    
        <?php
         
        $query_attribute = "SELECT * 
            FROM attribute
            WHERE state='active' AND attribute.obj_id = '{$_SESSION["obj_id"]}'";
        $resultado_attribute = executa_query($query_attribute);
        
        $lines_attribute = mysqli_num_rows($resultado_attribute);

        if (!$lines_attribute) {
            echo "<p>Não existem atributos para este formulário.</p>";
            back();
            return;
        }

        while($lines_attribute) {
           
        }

        ?>
    <?php
        back();

        ?> 
       <?php echo '<a href="insercao-de-valores?estado=validar&obj=' . $_SESSION["obj_id"] . '">Validar</a>';
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