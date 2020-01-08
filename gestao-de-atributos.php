<?php
require_once ("custom/php/common.php");
 
// Verifica se o utilizador fez login no wp e se tem permissão para mexer nos atributos
if (is_user_logged_in() && current_user_can('manage_attributes')) {        
    $liga = liga_basedados();

    // Quando o estado da execução não está definido
    if ($_REQUEST["estado_execucao"] == "") {    
    
        // Utiliza a query_attribute para por o código da query da SQL
        $query_attribute = "SELECT * FROM `attribute`";

        // Utiliza a função executa_query definida em common.php e executa o SQL na base de dados
        $resultado_attribute = executa_query($query_attribute);  

        // Verifica se há atributos e se não houver dá mensagem de erro
        if (mysqli_num_rows($resultado_attribute) == 0) {    
            echo "Não há atributos especificados";        
        } else {
            ?>
            <table class="mytable tabela-attr">
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
                
                $query_objeto="SELECT DISTINCT attribute.obj_id, object.name
                                FROM `attribute`, `object`
                                WHERE attribute.obj_id = object.id";

                $result_objeto = executa_query($query_objeto);  

                $lines_objeto = mysqli_num_rows($result_objeto);   

                // ciclo que percorre o array associativo
                while ($array_objeto = mysqli_fetch_array($result_objeto)) { 
                    
                    // Definição da query a ser executada posteriormente
                    $query_attributes = "SELECT DISTINCT attribute.name, attribute.obj_id, attribute.id, attribute.value_type, attribute.form_field_name, attribute.form_field_type, attribute.unit_type_id, attribute.form_field_order, attribute.form_field_size, attribute.mandatory, attribute.state
                    FROM `attribute`, `object`
                    WHERE attribute.obj_id = '{$array_objeto['obj_id']}'";            

                    // Utiliza a função executa_query existente no ficheiro common.php e executa a query na base de dados
                    $result_attributes = executa_query($query_attributes); 

                    // Utiliza a função do mysql para saber o número de linhas necessário em cada célula
                    $lines_attributes = mysqli_num_rows($result_attributes);?>

                    <!-- Definição número colunas e linhas-->
                    <tr>
                        <td  colspan="1" rowspan="<?php echo $lines_attributes;?>"><?php echo $array_objeto['name'];?>
                        </td>

                    <?php 
                    
                    // Criação de um array com os valores da query guardados na variável $result_attributes
                    while ($array_attributes = mysqli_fetch_array($result_attributes)) {              
                        ?>
                        <td class="attr-id"><?php
                            echo $array_attributes['id']; ?>
                            </td>
                        <td><?php
                            echo $array_attributes['name']; ?>
                            </td>
                        <td><?php
                            echo $array_attributes['value_type']; ?>
                            </td>
                        <td><?php
                            echo $array_attributes['form_field_name']; ?>
                            </td>
                        <td><?php
                            echo $array_attributes['form_field_type']; ?>
                            </td>
                        <td><?php
                            if ($array_attributes['unit_type_id'] != null) { // Faz a verificação para ver se há unidades
                                // A query vai selecionar as unidades(name) da tabela attr_unit_type e e comparar o id com o id que está no array
                                $query_tipo_unidade = "SELECT DISTINCT name
                                                            FROM attr_unit_type
                                                            WHERE id='{$array_attributes['unit_type_id']}'";

                                $resultado_tipo_unidade = executa_query($query_tipo_unidade); // Passa $query_tipo_unidade como parametro de executa_query e coloca o resultado na variavel $resultado_tipo_unidade

                                $array_tipo_unidade = mysqli_fetch_assoc($resultado_tipo_unidade);
                                echo $array_tipo_unidade['name'];
                            // Se não existem unidades coloca o (-)
                            } else { 
                                echo '-';
                            } ?>
                        </td>
                        <td><?php
                            echo $array_attributes['form_field_order']; ?>
                        </td>
                        <td><?php
                            echo $array_attributes['form_field_size']; ?>
                        </td>
                            <?php
                            if ($array_attributes['mandatory'] == 1) {?>
                                <td>sim</td>
                            <?php 
                            } else { ?>
                                <td>não</td>
                                <?php 
                            } ?>
                        <td><?php
                            if ($array_attributes['state'] == "active") { // Se o estado for ativo escreve o que está abaixo 
                                ?>Activo</td> 
                                <td>[editar][desactivar]</td>
                                <?php
                            } else { // Se o estado for inativo escreve o que está abaixo
                                ?>Inactivo</td>
                                <td>[editar][activar]</td>
                                <?php
                            }?>
                        </td>
                        </tr>
                        <?php
                    }?>
                    <?php
                }?>
                </tbody>
            </table>
            <?php
        }
        // Gestão de atributos-Introdução
        ?>
        <h3><strong>Gestão de Atributos - <span>Introdução</span></strong></h3>
             <!-- Criação do formulário de inserção de atributos-->
         <form name="gestao_de_atributos"  method="post">
            <p>
                <label><b>Nome do atributo:</b></label>
                <input type="text" name="nome_do_atributo">
            </p>
            <p>
                <label><b>Tipo de valor:</b></label>
                <?php
                $query_tipos_valor = "SELECT distinct value_type from attribute";
                // Executa a query e guarda o retorno na variavel
                $resultado_tipos_valor = executa_query($query_tipos_valor); 
                // Ciclo para percorrer todos os objetos e indicar os vários tipos de value_type
                while ($array_tipos_valor = mysqli_fetch_array($resultado_tipos_valor)) {
                    // value_type da tabela attribute
                    $tipo = $array_tipos_valor["value_type"];?>
                    <input type="radio" name="tipo_de_valor" value="<?php echo $tipo; ?>"> <?php echo $tipo;
                }
                ?>
            </p>
            <p>
                <label><b>Objeto a que irá pertencer este atributo:</b></label>
                <?php
                $query_objetos = "SELECT distinct name, id from object";
                // Executa a query e guarda o retorno na variavel
                $resultado_objetos = executa_query($query_objetos);
                ?> <select name="objeto"> <?php
                // Ciclo para percorrer todos os objetos e indicar os vários tipos de objeto
                while ($array_objetos = mysqli_fetch_array($resultado_objetos)) {
                    $id = $array_objetos["id"];
                    // name da tabela object
                    $objeto = $array_objetos["name"];?>
                    <option value="<?php echo $id;?>"> <?php echo $objeto;
                }
                ?></select>
            </p>
            <p>
                <label><b>Tipo do campo do formulário:</b></label>
                <?php
                $query_nome_formulario = "SELECT distinct form_field_type from attribute";
                // Executa a query e guarda o retorno na variavel
                $resultado_nome_formulario = executa_query($query_nome_formulario);
                // Ciclo para percorrer todos os nome_formulario e indicar as várias opções
                while ($array_nome_formulario = mysqli_fetch_array($resultado_nome_formulario)) {
                    // form_field_type da tabela attribute
                    $nome_formulario = $array_nome_formulario["form_field_type"];?>
                    <input type="radio" name="nome_formulario" value="<?php echo $nome_formulario; ?>"> <?php echo $nome_formulario;
                }
                ?>
            </p>
            <p>
                <label><b>Tipo de unidade:</b></label>
                <?php
                $query_unidades = "SELECT distinct name, id from attr_unit_type";
                // Executa a query e guarda o retorno na variavel
                $resultado_unidades = executa_query($query_unidades);
                ?> <select name="unidade"> 
                <option selected value="NULL"></option><?php
                // Ciclo para percorrer todos os unidades e indicar os vários tipos de unidades
                while ($array_unidades = mysqli_fetch_array($resultado_unidades)) {
                    $id = $array_unidades["id"];
                    // name da tabela attr_unit_type
                    $unidade = $array_unidades["name"];?>
                    <option value="<?php echo $id; ?>"> <?php echo $unidade;
                }
                ?></select>
            </p>
            <p>
                <label><b>Ordem do campo no formulário:</b></label>
                <input type="text" name="ordem_formulario">
            </p>
            <p>
                <label><b>Tamanho do campo no formulário:</b></label>
                <input type="text" name="tamanho_formulario">
            </p>
            <p>
                <label><b>Obrigatorio:</b></label>
                    <input type="radio" name="obrigatorio" value="sim"> <?php echo "Sim";?>
                    <input type="radio" name="obrigatorio" value="nao"> <?php echo "Não";?>
            </p>
            <p>
                <label><b>Objeto referenciado por este atributo:</b></label>
                <?php
                $query_objetos = "SELECT distinct name, id from object";
                // Executa a query e guarda o retorno na variavel
                $resultado_objetos = executa_query($query_objetos);
                ?> <select name="objeto_referenciado"> 
                <option selected value="NULL"></option><?php
                // Ciclo para percorrer todos os objetos e indicar os vários tipos de objeto
                while ($array_objetos = mysqli_fetch_array($resultado_objetos)) {
                    $id = $array_objetos["id"];
                    // name da tabela object
                    $objeto = $array_objetos["name"];?>
                    <option value="<?php echo $id;?>"> <?php echo $objeto;}
                ?></select>
            </p>

            <br>
             <input type= "hidden" name= "estado_execucao" value= "inserir">
             <input class= "button" type= "submit" value= "Inserir objeto">
             <br><br>
        </form>
    <?php
    } elseif ($_REQUEST["estado_execucao"] == "inserir") {              
        ?>
        <h3><b>Gestão de Atributos - Inserção</b></h3>
        <?php

        $erro = 0;
        // Usa a funcao guarda_variavel para guardar nas variáveis os inputs sem carateres especiais
        $object_nome_do_atributo = guarda_variavel($_REQUEST['nome_do_atributo']);
        $object_tipo_de_valor = guarda_variavel($_REQUEST['tipo_de_valor']);
        $object_objeto = guarda_variavel($_REQUEST['objeto']);
        $object_tipo_formulario = guarda_variavel($_REQUEST['nome_formulario']);
            $object_unidade = guarda_variavel($_REQUEST['unidade']);
        $object_ordem_formulario = guarda_variavel($_REQUEST['ordem_formulario']);
        $object_tamanho_formulario = guarda_variavel($_REQUEST['tamanho_formulario']);
        $object_obrigatorio = guarda_variavel($_REQUEST['obrigatorio']);
            $object_objeto_referenciado = guarda_variavel($_REQUEST['objeto_referenciado']);

        if (empty($object_nome_do_atributo)) {
            $erro = 1;
            ?>
            <p>É necessário indicar um nome para o atributo.<p>
            <?php
            back();                    
    
        } if (empty($object_tipo_de_valor)) {
            $erro = 1;
            ?>
            <p>É necessário indicar o tipo de valor.<p>
            <?php
            back();                    
        } if (empty($object_objeto)) {
            $erro = 1;
            ?>
            <p>É necessário indicar um objeto.<p>
            <?php
            back();   
        } if (empty($object_tipo_formulario)) {
            $erro = 1;
            ?>
            <p>É necessário indicar um nome para o campo do formulário.<p>
            <?php
            back();  
        } if (empty($object_ordem_formulario)) {
            $erro = 1;
            ?>
            <p>É necessário indicar uma ordem para o campo do formulário.<p>
            <?php
            back(); 
        } if (empty($object_tamanho_formulario)) {
            $erro = 1;
            ?>
            <p>É necessário indicar um tamanho para o campo do formulário.<p>
            <?php
            back();     
        } if (empty($object_obrigatorio)) {
            $erro = 1;
            ?>
            <p>É necessário indicar se é obrigatório ou não.<p>
            <?php
            back();       
        } if ($erro == 1) {
            if($object_obrigatorio == "sim") {
                $object_obrigatorio = 1;
            } else {
                $object_obrigatorio = 0;
            }
        
            // código SQL em formato string para inserir novos atributos
            $query_inserir = "INSERT INTO `attribute` (`id`, `name`, `value_type`, `obj_id`, `form_field_name`, `form_field_type`,
            `unit_type_id`, `form_field_order`, `form_field_size`, `mandatory`, `obj_fk_id`) 
            VALUES (NULL, '$object_nome_do_atributo', '$object_tipo_de_valor', '$object_objeto', 'tv-7-diagonal', '$object_tipo_formulario', " 
            . $object_unidade . ", '$object_ordem_formulario', '$object_tamanho_formulario', '$object_obrigatorio', " . $object_objeto_referenciado . ")"; 

            $result_insert = executa_query($query_inserir);
 
            if ($result_insert) {
            mysqli_query($liga,'COMMIT');
            ?>
            <p>Inseriu os dados de novo tipo de unidade com sucesso!
            Clique  em <a href="gestao-de-atributos">continuar</a> para avançar.
            <!-- $string = preg_replace('/[^a-z0-9_ ]/i', '', $string);) -->
            <br/>
            <?php
            }
        }
    }
        
} else { ?>
Não tem autorização para aceder a esta página.
<?php
}?>