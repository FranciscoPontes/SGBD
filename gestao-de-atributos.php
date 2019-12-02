<?php
echo "<h1>Hello</h1>";
include 'common.php';
 
require_once("custom/php/common.php");
 
//verifica se o utilizador fez login no wp e se tem permissão para mexer nos objetos
if (is_user_logged_in() && current_user_can('manage attributes')) {        
    $liga =liga_basedados(); 


// Quando o estado da execução não está definido
if ($_REQUEST["estado_execucao"] == "") {    
 
        //utiliza a query_attribute para por o código da query da SQL
        $query_attribute = "SELECT * FROM attribute";
 
        //utiliza a função executa_query definida em common.php e executa o SQL na base de dados
        $resultado_attribute = executa_query($query_attribute);  
 
        //verifica se há objetos e se não houver dá mensagem de erro
        if (mysqli_num_rows($resultado_attribute) == 0) {    
            echo "Não há propriedades especificadas";        
        } else {
            ?>
            <table class="mytable">
                <thead>
                    <tr>
                        <th>objeto</th>
                        <th>id</th>
                        <th>nome do atributo</th>
                        <th>tipo de valor</th>
                        <th>nome do campo no formulário</th>
                        <th>tipo do campo no formulário</th>
                        <th>tipo de unidade</th>
                        <th>ordem do campo no formulário</th>
                        <th>tamanho do campo no formulário</th>
                        <th>obrigatório</th>
                        <th>estado</th>
                        <th>ação</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                //guarda na query o SQL dos objetos, já ordenados alfabeticamente
                $query_attribute = "SELECT id, name FROM obj_type ORDER by name";
                //utiliza a função executa_query que está no common.php e executa o SQL na base de dados
                $result_attribute = executa_query($query_attribute_obj_type);        
 
                //cria um array com os valores da query $result_attribute_obj_type
                while ($array_attribute_obj_type = mysqli_fetch_array($result_attribute_obj_type)) {
 
                    //definicao da query a ser executada posteriormente
                    $query_attribute_obj_type = "SELECT attribute.                          
                                        FROM attribute, attr_unit_type,
                                        WHERE attribute.obj_type_id = " . $array_attribute_obj_type["id"] . " " .
                                        "ORDER BY attribute.nome_do_objeto";            
                   
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
        <!-- onsubmit="return adicionar_objeto()" -->
        <form name="gestao_de_objetos"  method="post">
        <p>
            <label><b>Nome:</b></label>
            <input type="text" name="nome_do_objeto">
        </p>
        <p>
            <label><b>Tipo:</b></label>
            <div>
                <label><b>Propriedade</b></label>
                <input type="radio" name="tipo_de_objeto" value="propriedade">
 
                <label><b>Canal de venda</b></label>
                <input type="radio" name="tipo_de_objeto" value="canal de venda">
            </div>
           
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
} else {
    ?>
    Não tem autorização para aceder a esta página.
    <?php
}
?>