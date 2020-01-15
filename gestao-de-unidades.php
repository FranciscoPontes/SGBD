
<?php 
require_once("custom/php/common.php");
 
// faz verificação, para ver se o utilizador está logado e se ter permissão para alterar objetos
if (is_user_logged_in() && current_user_can('manage_unit_types')) {        
 
$liga =liga_basedados();

// Se o estado de execução não estiver definido
if ($_REQUEST["estado_execucao"] == "") {    
 
    //  Utiliza a $query_object para colocar o código na query SQL, guarda na variável
    $query_object = "SELECT * FROM  attr_unit_type  ORDER BY name" ;

    // Usa a função executa_query definida no common e executa o SQL na base de dados, coloca o valor em $result_object, executa a query e guarda na variável
    $result_object = executa_query($query_object);  

    // Faz verificação para ver se existem objetos, caso não haja dá "Não  há tipos de unidades"
    if (mysqli_num_rows($result_object) == 0) {    
        echo "Não há tipos de unidades";        
    } else {
        ?>
                <!-- Criação da tabela -->
        <table class="mytable"> 
            <thead>
                <tr>
                <!-- Parametros da tabela -->
                    <th scope="coluna">Id</th> 
                    <th scope="coluna">Name</th>  
                </tr>

            </thead>
            <tbody>
            <?php
   
        ?>
        <?php
            while ($array_attr_unit_type = mysqli_fetch_assoc($result_object)) { 
                ?>
                <tr> <!-- Faz impressão dos valores -->
                    <td> <?php echo $array_attr_unit_type['id']; ?></td>
                    <td> <?php echo $array_attr_unit_type['name']; ?></td>
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
                <label  id=" nomes" for="name">Nome:</label>
                <input id="castanhas" type="text" name="name" placeholder="Insira a unidade">

                <input type="hidden" name="estado_execucao" value="inserir"> <!--altera o estado de execução no if abaixo-->
                        <!--Botão Submit-->
                
                <button   id= "botaounid" type="submit" name="insere_unidade">Submit</button>
            </form>
        <?php
        } else {
           
            // Inserções - Coloca da base de dados o valor da unidade que o utilizador introduziu 
            if($_REQUEST["estado_execucao"] == "inserir") {
        ?>
                <h3>Gestão de unidades - inserção</h3>
                <?php
                    //  Pedido do valor unidade, que depois é passado a função guarda_variavel e coloca em $unidade 
                    // Verifica se tem caracteres especiais
                    $unidade= guarda_variavel($_REQUEST['name']);

                    // Caso o valor em $unidade seja vazio, não avança e dá a mensagem Insira o nome de uma unidade
                    if (empty($unidade)) {
                        ?>
                        <p id="alerta">Insira o nome de uma unidade!</p>
                        <?php
                        back(); // Faz voltar atrás
                    } else {
                        // Insere na tabela attr_unit_type na coluna unidade os valores da unidade que são passados pelo utilizador.p.s-não precisa passar o id pois este está AI
                        $query_insert_unit_type = "INSERT INTO `attr_unit_type` (`name`) VALUES ('$unidade')";

                        // Passa o valor de $query_insert_unit_type(valores passados pelo insert) para a função executa_query e atribui ao $result_insert_unit_type
                        $result_insert_unit_type = executa_query($query_insert_unit_type);

                        ?>
                        <p id="feito">Inseriu os dados de novo tipo de unidade com sucesso.</p>
                        <p id="feito">
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