
<?php
include 'common.php';
 
require_once("custom/php/common.php");
 
// faz verificação, para ver se o utilizador está logado e se ter permissão para alterar objetos
if (is_user_logged_in() && current_user_can('manage_unit_types')) {        
 
$liga =liga_basedados();

// Se o estado de execução não estiver definido
if ($_REQUEST["estado_execucao"] == "") {    
 
    //  utiliza a $query_object para colocar o código na query SQL
    $query_object = "SELECT * FROM  attr_unit_type  ORDER BY unidade" ;

    // usa a função executa_query definida no common e executa o SQL na base de dados, coloca o valor em $result_object
    $result_object = executa_query($query_object);  

    // faz verificação para ver se existem objetos, caso não haja dá "Não  há tipos de unidades"
    if (mysqli_num_rows($result_object) == 0) {    
        echo "Não há tipos de unidades";        
    } else {
        ?>
                <!--criação da tabela-->
        <table class="mytable"> 
            <thead>
                <tr>
                <!-- parametros da tabela-->
                    <th>Id</th> 
                    <th>Unidade</th>  
                </tr>

            </thead>
            <tbody>
            <?php
   
        ?>
        <?php
            while ($array_attr_unit_type = mysqli_fetch_array($result_object)) { 
                ?>
                <tr> <!-- Faz impressão dos valores -->
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

                        <!--Introdução de valores-->
            <h3>Gestão de unidades - Introdução</h3>

            <form class="form-inline" method="POST" name="gestao_unidades">
                <label for="unidade">Nome:</label>
                <input type="text" name="unidade" placeholder="Insira a unidade">

                <input type="hidden" name="estado_execucao" value="inserir">
                        <!--Botão Submit-->
                <button type="submit" name="insere_unidade">Submit</button>
            </form>
        <?php
        } else {
           
            // Inserções - Coloca da base de dados o valor da unidade que o utilizador introduziu 
            if($_REQUEST["estado_execucao"] == "inserir") {
        ?>
                <h3>Gestão de unidades - Inserção</h3>
                <?php
                    //  Pedido do valor unidade, que depois é passado a função guarda_variavel e coloca em $unidade
                    $unidade= guarda_variavel($_REQUEST['unidade']);

                    // Caso o valor em $unidade seja vazio, não avança e dá a mensagem Insira o nome de uma unidade
                    if (empty($unidade)) {
                        ?>
                        <p>Insira o nome de uma unidade</p>
                        <?php
                        back(); // Faz voltar atrás
                    } else {
                        // Insere na tabela attr_unit_type na coluna unidade os valores da unidade que são passados pelo utilizador.p.s-não precisa passar o id pois este está AI
                        $query_insert_unit_type = "INSERT INTO `attr_unit_type` (`unidade`) VALUES ('$unidade')";

                        // Passa o valor de $query_insert_unit_type(valores passados pelo insert) para a função executa_query e atribui ao $result_insert_unit_type
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