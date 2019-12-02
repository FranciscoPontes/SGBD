
<link rel="stylesheet" type="text/css" href="/custom/css/ag.css">


<?php
include 'common.php';
 
require_once("custom/php/common.php");
 
//verifica se o utilizador fez login no wp e se tem permissão para mexer nos objetos
if (is_user_logged_in() && current_user_can('manage_objects')) {        
 
$liga =liga_basedados();
 
// Quando o estado da execução não está definido
if ($_REQUEST["estado_execucao"] == "") {    
 
        //utiliza a query_object para por o código da query da SQL
        $query_object = "SELECT * FROM object  ";
 
        //utiliza a função executa_query definida em common.php e executa o SQL na base de dados
        $result_object = executa_query($query_object);  
 
        //verifica se há objetos e se não houver dá mensagem de erro
        if (mysqli_num_rows($result_object) == 0) {    
            echo "Não há objetos ";        
        } else {
            ?>
            <table class="mytable">
                <thead>
                    <tr>
                        <th>tipo de objeto</th>
                        <th>id</th>
                        <th>nome do objeto</th>
                        <th>estado</th>
                        <th>ação</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                //guarda na query o SQL dos objetos, já ordenados alfabeticamente
                $query_object_obj_type = "SELECT id, name FROM obj_type ORDER by name";
                //utiliza a função executa_query que está no common.php e executa o SQL na base de dados
                $result_object_obj_type = executa_query($query_object_obj_type);        
 
                //cria um array com os valores da query $result_object_obj_type
                while ($array_object_obj_type = mysqli_fetch_array($result_object_obj_type)) {
 
                    //definicao da query a ser executada posteriormente
                    $query_object_obj_type = "SELECT object.tipo_de_objeto, object.id, object.nome_do_objeto, object.estado, object.acao                          
                                        FROM object
                                        WHERE object.obj_type_id = " . $array_object_obj_type["id"] . " " .
                                        "ORDER BY object.nome_do_objeto";            
                   
                    //utiliza a função executa_query existente no ficheiro common.php e executa a query na base de dados
                    $result_object_obj_type = executa_query($query_object_obj_type);      
                   
                    //utiliza a função do mysql para saber o número de linhas para cada obj_type
                    $lines_object_obj_type = mysqli_num_rows($result_object_obj_type);                    
 
                    if ($lines_object_obj_type > 0) {
                        ?>
 
                        <!--definição numero colunas e linhas-->
                        <tr colspan="1" rowspan="<?php echo $lines_object_obj_type?>">
                        <?php
                       
                        //criação de um array com os valores da query guardados na variável $result_object_obj_type
                        while ($array_object_obj_type = mysqli_fetch_array($result_object_obj_type)) {              
                            ?>
                            <td >
                            <?php
                                //escreve os dados para cada posição do array
                                echo $array_object_obj_type["tipo_de_objeto"];
                                ?>
                            </td>
                            <td> <?php
                                echo $array_object_obj_type["id"];
                                ?>
                            </td>
                            <td> <?php
                                echo $array_object_obj_type["nome_do_objeto"];
                                ?>
                            </td>
                            <td> <?php
                                echo $array_object_obj_type["estado"];
                                ?>
                            </td>
                            <td> <?php
                                echo $array_object_obj_type["acao"];
                                ?>
                            </td>
                        </tr>
                            <?php
                        }
                    }
                }
                ?>
                </tbody>
            </table>
            <?php
        }
        //Gestão de objetos-Introdução
        ?>
        <h3><strong>Gestão de Objetos - <span>Introdução</span></strong></h3>
 
        <!--criação do formulário de inserção de objetos-->
        <form name="gestao_de_objetos"  method="post">
        <p>
            <label><b>Nome:</b></label>
            <input type="text" name="nome_do_objeto">
        </p>
        <p>
        <!-- Francisco Pontes -->
            <label><b>Tipo:</b></label>
            <?php
            // guarda na variavel a query
            $query_seleciona_tipos="SELECT id, name FROM obj_type";
            //executa a query e guarda o retorno na variavel
            $resultado_seleciona_tipos=executa_query($query_seleciona_tipos);
            //ciclo para percorrer todos os objetos e indicar os vários tipos de objeto
            while ($array_seleciona_tipos = mysqli_fetch_array($resultado_seleciona_tipos)) {
                $id=$array_seleciona_tipos["id"];
                $nome=$array_seleciona_tipos["nome_do_objeto"];
            }
            ?>
            <td>
                <input class="radio" type="radio" name="tipo_de_objeto"  value="<?php echo $id; ?>"> <?php echo $nome_do_objeto; ?>
            </td>
           
        </p>
        <p> <!--aqui-->
            <label><b>Estado:</b></label>
            <div>
                <label><b>Ativo</b></label>
                <input type="radio" name="estado" value="ativo"><!-- estado ativo-->
 
                <label><b>Inativo</b></label>
                <input type="radio" name="estado" value="inativo">
            </div>
        </p>
            <br>
            <input type= "hidden" name= "estado_execucao" value= "inserir">
            <input class= "button" type= "submit" value= "Inserir objeto">
            <br><br>
        </form>
        <?php
    }
 
    // Gestão de objetos-Inserção
    elseif ($_REQUEST["estado_execucao"] == "inserir") {              
        ?>
        <h3><b>Gestão de objetos - inserção</b></h3>
        <?php
        //          Francisco Pontes
        //usa a funcao guarda_variavel para guardar nas variáveis os inputs sem carateres especiais
        $object_nome_do_objeto = guarda_variavel($_REQUEST['nome_do_objeto']);
        $object_tipo_de_objeto = guarda_variavel($_REQUEST['tipo_de_objeto']);
        $object_estado = guarda_variavel($_REQUEST['estado']);
        if (empty($object_nome_do_objeto)) {
            ?>
            <p>É necessário indicar um nome para o objeto.<p>
            <?php
            // faz verificação, para ver se o object_nome_do_objeto não está vazio
            back();                    
 
            }elseif (empty($object_tipo_de_objeto)) {
            ?>
            <p>É necessário indicar o tipo de objeto.<p>
            <?php
             
            // faz verificação, para ver se o object_tipo_de_objeto não está vazio
            back();                    
            }elseif (empty($object_estado)) {
            ?>
            <p>É necessário indicar o estado do objeto.<p>
            <?php
            // faz a verificação, para ver se o object_estado não está vazio
            back();    
              
                      
        }
        
        else {
            //              Francisco Pontes
            //define a query para inserir valores
            $query_inserir = "INSERT INTO `object` (`tipo_de_objeto`, `id`, `nome_do_objeto`, `estado`, `acao`, `obj_type_id`) VALUES ('$object_tipo_de_objeto',NULL,'$object_nome_do_objeto','$object_estado','[editar][desativar]','1')"; 
            //executa a query
            $result_insert = executa_query($query_inserir);
 
            if ($result_insert) {
            mysqli_query($liga,'COMMIT');
            ?>
            <p>Inserção de dados feita com sucesso!
            Clique  em <a href="gestao-de-objetos">continuar</a> para avançar.
            <br/>
            <?php
            }
        }
    }
} else {
    ?>
    Não tem autorização para aceder a esta página.
    <?php
}
?>
