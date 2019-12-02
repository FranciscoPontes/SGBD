<?php echo "<h1>HELLO WORLD</h1>"; ?>


<?php
include 'common.php';
 
require_once("custom/php/common.php");
 
//verifica se o utilizador fez login no wp e se tem permissão para mexer nos objetos
if (is_user_logged_in() && current_user_can('manage_unit_types')) {        
 
$liga =liga_basedados();


// Quando o estado da execução não está definido
if ($_REQUEST["estado_execucao"] == "") {    
 
    //utiliza a query_object para por o código da query da SQL
    $query_object = "SELECT * FROM  attr_unit_type  ORDER BY unidade" ;

    //utiliza a função executa_query definida em common.php e executa o SQL na base de dados
    $result_object = executa_query($query_object);  

    //verifica se há objetos e se não houver dá mensagem de erro
    if (mysqli_num_rows($result_object) == 0) {    
        echo "Não há tipos de unidades";        
    } else {
        ?>
        <table class="mytable">
            <thead>
                <tr>
                    <th>Id</th>
                    <th>Unidade</th>
                </tr>

            </thead>
            <tbody>
            <?php

            //$query_valor = "SELECT id, unidade FROM attr_unit_type ORDER by unidade";
            //$result_valor = executa_query($query_valor); 

            //aqui query
        ?>
        <?php
            while ($array_attr_unit_type = mysqli_fetch_array($result_object)) {
                ?>
                <tr> <!-- Imprime os valores -->
                    <td> <?php echo $array_attr_unit_type['id']; ?></td>
                    <td> <?php echo $array_attr_unit_type['unidade']; ?></td>
                </tr>
                <?php
                }
                ?>
            </table>
            <?php
            }
            ?>
            <!-- Form para a introduçao de novos valores na Base de Dados -->
            <h3>Gestão de unidades - Introdução</h3>

            <form class="form-inline" method="POST" name="gest_unidades">
                <label for="nome">Nome:</label>
                <input type="text" name="unidade" placeholder="Insira a unidade">

                <input type="hidden" name="estado_execucao" value="inserir">
                <!-- botão de submissão-->
                <button type="submit" name="insere_unidade">Submit</button>
            </form>
        <?php
        } else {
            //Inserção: Insert na base de dados do valor introduzido
            if($_REQUEST["estado_execucao"] == "inserir") {
        ?>
                <h3>Gestão de unidades - Inserção</h3>
                <?php
                    //passa pela função de verifcação de segurança -- que recebe do Input
                    $unidade= executa_query($_REQUEST['unidade']);

                    //se estiver vazio não deixa avançar, retorna um aviso ao utilizador
                    if (empty($unidade)) {
                        ?>
                        <p>Insira o nome de uma unidade</p>
                        <?php
                        back();
                    } else {
                        //o id está no modo AUTO_INCREMENT, não é necessário
                        $query_insert_unit_type = "INSERT INTO `attr_unit_type` (name) VALUES ('$unidade')";

                        //corre a query (mysqli_query(connection,query,resultmode);)
                        //$result_insert_unit_type = mysqli_query($link, $query_insert_unit_type); //erro?
                        $result_insert_unit_type = executa_query($query_insert_unit_type);

                        ?>
                        <p>Inseriu os dados de novo tipo de unidade com sucesso.</p>
                        <p>
                            Clique em <a href="gestao-de-unidades"><strong>Continuar</strong></a> para avançar
                        </p>
                        <?php
                    }
            }
        }
    } else {
    ?>
        Não tem autorização para aceder a esta página
    <?php
    }
?>

                   
        




