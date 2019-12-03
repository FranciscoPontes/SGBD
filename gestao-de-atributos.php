<link rel="stylesheet" type="text/css" href="/custom/css/ag.css">

<?php
include 'common.php';
 
require_once ("custom/php/common.php");
 
// verifica se o utilizador fez login no wp e se tem permissão para mexer nos atributos
if (is_user_logged_in() && current_user_can('manage_attributes')) {        
    $liga = liga_basedados();

    // Quando o estado da execução não está definido
    if ($_REQUEST["estado_execucao"] == "") {    
    
        // utiliza a query_attribute para por o código da query da SQL
        $query_attribute = "SELECT * FROM `attribute`";

        // utiliza a função executa_query definida em common.php e executa o SQL na base de dados
        $resultado_attribute = executa_query($query_attribute);  

        // verifica se há atributos e se não houver dá mensagem de erro
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

                    //definicao da query a ser executada posteriormente
                    $query_attributes = "SELECT DISTINCT attribute.nome_do_atributo, object.nome_do_objeto, attribute.id, attribute.tipo_de_valor, attribute.nome_campo_formulario, attribute.tipo_campo_formulario, attr_unit_type.unidade, attribute.ordem_campo_formulario, attribute.tamanho_campo_formulario, attribute.obrigatorio, attribute.estado, attribute.acao
                    FROM `attribute`, `object`, `attr_unit_type`
                    WHERE attribute.objeto_id = object.id AND attr_unit_type.id = attribute.tipo_unidade_id
                    ORDER BY object.nome_do_objeto";            
                
                    //utiliza a função executa_query existente no ficheiro common.php e executa a query na base de dados
                    $result_attributes = executa_query($query_attributes);     

                    //utiliza a função do mysql para saber o número de linhas para cada obj_type
                    $lines_attributes = mysqli_num_rows($result_attributes); 
                    echo $lines_attributes;                   

                    ?>
                        <!--definição numero colunas e linhas-->
                        <tr colspan="1" rowspan="<?php echo $lines_attributes?>">
                        <?php
                    
                        //criação de um array com os valores da query guardados na variável $result_object_obj_type
                        while ($array_attributes = mysqli_fetch_array($result_attributes)) {              
                            ?>
                                <td><?php
                                    echo $array_attributes['nome_do_objeto']; ?></td>
                                <td><?php
                                    echo $array_attributes['id']; ?></td>
                                <td><?php
                                    echo $array_attributes['nome_do_atributo']; ?></td>
                                <td><?php
                                    echo $array_attributes['tipo_de_valor']; ?></td>
                                <td><?php
                                    echo $array_attributes['nome_campo_formulario']; ?></td>
                                <td><?php
                                    echo $array_attributes['tipo_campo_formulario']; ?></td>
                                <td><?php
                                    echo $array_attributes['unidade']; ?></td>
                                <td><?php
                                    echo $array_attributes['ordem_campo_formulario']; ?></td>
                                <td><?php
                                    echo $array_attributes['tamanho_campo_formulario']; ?></td>
                                 <?php
                                    if ($array_attributes['obrigatorio'] == 1) {?>
                                        <td>sim</td>
                                    <?php 
                                    } else { ?>
                                        <td>não</td>
                                        <?php 
                                    } ?>
                                <td><?php
                                    echo $array_attributes['estado']; ?></td>
                                <td><?php
                                    echo $array_attributes['acao']; ?></td>
                        </tr>
                            <?php
                        }?>
                </tbody>
            </table>
            <?php
        }
        //Gestão de atributos-Introdução
        ?>
        <h3><strong>Gestão de Atributos - <span>Introdução</span></strong></h3>

        <?php
    }
} else { ?>
Não tem autorização para aceder a esta página.
<?php
}?>