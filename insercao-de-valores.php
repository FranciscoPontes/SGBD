<?php
require_once ("custom/php/common.php");
 
// Verifica se o utilizador fez login no wp e se tem permissão para mexer nos atributos
if (is_user_logged_in() && current_user_can('insert_values')) {        
    $liga = liga_basedados();

    // Quando o estado da execução não está definido
    if ($_REQUEST["estado_execucao"] == "") {    
    
        $query_object = "SELECT id, name FROM object";
        $query_object_type = "SELECT id, name FROM obj_type";

        $resultado_object = executa_query($query_object);  
        $resultado_object_type = executa_query($query_object_type);  

        if (mysqli_num_rows($resultado_object) == 0) {    
            echo "Não há propriedades especificadas";        
        } else if (mysqli_num_rows($resultado_object_type) == 0) {  
            echo "Não há propriedades especificadas"; 
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
                                        </a>' ?>]</li>
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
                                        </a>' ?>]</li>
            <?php } ?>
            </ul>
            <?php
        }
    }
} else { ?>
    Não tem autorização para aceder a esta página.
    <?php
    }?>