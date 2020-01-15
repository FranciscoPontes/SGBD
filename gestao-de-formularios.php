

<?php
require_once("custom/php/common.php");

        // Verifico se o utilizador esta logado e se tem acesso
if (is_user_logged_in() && current_user_can('manage_custom_forms')) {

    // Realizo a ligação com a base de dados
    $liga =  liga_basedados();

      // estado_execução == "" estado inicial
    if ($_REQUEST["estado_execucao"] == "") {
        ?>
        <h3 id="h3">Gestão de Formulários Customizados</h3>
        <?php
        
       
        $query_existem_formularios_customizados = "SELECT DISTINCT * FROM custom_form";  // Query utilizada para ver se há formulários
        $resultado_existem_formularios_customizados = executa_query($query_existem_formularios_customizados); // Execução da query na base de dados

        if (mysqli_num_rows($resultado_existem_formularios_customizados) == 0) { // Retorna o numero de linhas do resultado_existem_formularios_costumizados
            echo "Não existem formulários customizados";

        } else {
           
            // Esta query seleciona todos os name e id da custom_form e da custom_form_has_attribute, compara os id e ordena pelo name
            $query_formularios_customizados = "SELECT DISTINCT name, id
                                        FROM custom_form,  custom_form_has_attribute 
                                        WHERE custom_form.id = custom_form_has_attribute.custom_form_id
                                        ORDER by name ";

            $resultado_formularios_customizados = executa_query($query_formularios_customizados); // Execução da query ($query_formularios_customizados)
            ?>

            <!-- Criação da tabela -->
            <table class="mytable">
                <tr> 
                    <th scope="coluna"> Nome do Formulário</th>
                    <th scope="coluna"> Id</th>
                    <th scope="coluna">Atributo</th>
                    <th scope="coluna">Tipo de Valor</th>
                    <th scope="coluna">Nome do Campo no Formulário</th>
                    <th scope="coluna">Tipo do Campo no Formulário</th>
                    <th scope="coluna">Tipo de Unidade</th>
                    <th scope="coluna">Ordem do Campo no Formulário</th>
                    <th scope="coluna">Tamanho do Campo no Formulário</th>
                    <th scope="coluna">Obrigatório</th>
                    <th scope="coluna">Estado</th>
                    <th scope="coluna">Acção</th>
                </tr>

                <?php
                while ($array_formularios_customizados = mysqli_fetch_assoc($resultado_formularios_customizados)) { // O msqli_fetch_assoc serve para buscar uma linha de resultado como um array associativo 

                    // A query vai selecionar todos os atributos de attribut, para comparar os ids
                    $query_attribute = "SELECT DISTINCT attribute.* 
                                            FROM attribute, 
                                                 custom_form_has_attribute
                                              WHERE attribute.id = custom_form_has_attribute.attribute_id
                                              AND custom_form_has_attribute.custom_form_id = '{$array_formularios_customizados["id"]}' ";

                    $resultado_attribute = executa_query($query_attribute); // Executa a query ($query_attribute)

                    
                    $num_rows_attribute = mysqli_num_rows($resultado_attribute); // Conta as linhas para juntar na mesma 'parcela'
                    
                    ?>
                    <tbody>
                    <tr> <!-- colspan é o nr de colunas que uma parcela vai conter , rowspan = nr de linhas que uma celula vai ter-->
                        <td scope= "row" colspan="1" rowspan="<?php echo $num_rows_attribute; ?>"> <!-- Passa ao  rowspan o valor do $num_rows_attribute visto acima -->

                            <!-- Após  o nome gestao-de-formularios-custmizados são passados o estado= editar_form e é passado o id enquanto que o name é colocado na tabela-->
                            <?php 
                            echo '
                                        <a href="gestao-de-formularios?estado_execucao=editar_form&id=' . $array_formularios_customizados['id'] . '">
                                            ' . $array_formularios_customizados['name'] . ' 
                                        </a>'; // Concatenação
                                        
                            ?>

                            
                        </td>
                        <?php
                        // Preencher o resto da tabela com os valores da tabela atributo
                        while ($array_attribute = mysqli_fetch_assoc($resultado_attribute)) {
                        ?>
                        <!-- Valores para preencher a tabela, valores vêm de cima ($array_attribute) -->
                        <td scope="td"><?php echo $array_attribute['id']; ?></td>
                        <td scope="td"><?php echo $array_attribute['name']; ?></td>
                        <td scope="td"><?php echo $array_attribute['value_type']; ?></td>
                        <td scope="td"><?php echo $array_attribute['form_field_name']; ?></td>
                        <td scope="td"><?php echo $array_attribute['form_field_type']; ?></td>
                        <?php

                       
                        if ($array_attribute['unit_type_id'] != null) { // Faz a verificação para ver se há unidades
                            
                            // A query vai selecionar as unidades(name) da tabela attr_unit_type e e comparar o id com o id que está no array
                            $query_tipo_unidade = "SELECT DISTINCT name
                                                        FROM attr_unit_type
                                                        WHERE id='{$array_attribute['unit_type_id']}'";

                            $resultado_tipo_unidade = executa_query($query_tipo_unidade); // Passa $query_tipo_unidade como parametro de executa_query e coloca o resultado na variavel $resultado_tipo_unidade
                            

                            //  O msqli_fect_assoc busca uma linha de resultado como um array associativo
                            $array_tipo_unidade = mysqli_fetch_assoc($resultado_tipo_unidade);
                            ?>
                            <td scope="td"><?php echo $array_tipo_unidade['name'];?></td>
                            <?php
                        // Se não existem unidades coloca o (-)
                        } else { 
                            ?>
                            <td scope="td"> - </td>
                            <?php
                        }
                        // Query para a inserção da ordem
                        // faço comparação do custom_form_has_attribute.attribute_id='{$array_attribute['id']}' 
                        //para comparar o attribute id da tabela custom_form_has_attribute com o id do attributo do elemento atual do $array_attribute['id']}
                        // faço custom_form_has_attribute.custom_form_id= '{$array_formularios_customizados["id"]}'" para
                        // comparar o id do custom_form na tabela do custom_form_h_attribute com o id que está a ser percorrida no while

                        $ordem= "SELECT field_order
                        From custom_form_has_attribute, custom_form
                        where custom_form_has_attribute.attribute_id='{$array_attribute['id']}' 
                        and  custom_form_has_attribute.custom_form_id= '{$array_formularios_customizados["id"]}'";
                       
                
                        $resultado_ordem=executa_query($ordem); // Executa a query acima na BD
                        
                        //  O msqli_fect_assoc busca uma linha de resultado como um array associativo
                        $array_resultado=mysqli_fetch_assoc($resultado_ordem);

                        ?>
                        <td scope="td"><?php echo $array_resultado['field_order']; ?></td>
                        <?php
                        // Se o array for diferente de null, ou seja, não estiver vazio vai imprimir o valor do form_field_size
                        if ($array_attribute['form_field_size'] != null) { 
                            ?>
                            <td scope="td"><?php echo $array_attribute['form_field_size'];?></td>
                            <?php
                        // Se o array estiver vazio coloca o (-) 
                        } else { 
                            ?>
                            <td scope="td"> - </td>
                            <?php
                        }

                        // Se for obrigatório coloca 'SIM', se não for obrigatório coloca 'NÃO'
                        if ($array_attribute['mandatory'] == 1) {
                            ?>
                            <td scope="td">SIM</td>
                            <?php
                        } else { 
                            ?>
                            <td scope="td">NÃO</td>
                            <?php
                        }

                        if ($array_attribute['state'] == "active") { // Se  o estado for ativo escreve o que está abaixo 
                            ?>
                            <td scope="td">Activo</td> 
                            <td scope="td">[editar]
                            <br>[desactivar]</td>
                            <?php
                        } else { // Se o estado for inativo escreve o que está abaixo
                            ?>
                            <td scope="td">Inactivo</td>
                            <td scope="td">[editar]<br>[activar]</td>
                            <?php
                        }
                        ?>
                    </tr>
                    <?php
                        }
                }
                ?>
                </tbody>
            </table>
        <?php
        } 
        ?>
        <br>                <!-- RT-07-->
        <h3>Gestão de Formulários - Inserção</b></h3>
                    <!-- Inserção de um novo formulário-->
        <form name="registo_formulario" class="form-inline" method="POST">
                <p>
                    <legend class="name"> Insira um nome para o formulário:</legend>
                </p>
                <p id="amendoin">
                    <input  type="text" name="form_name" placeholder="Nome:">
                </p>
                <br>
                    <table class="mytable2"> <!--CSS diminuir tabela-->
                        <thead>
                        <tr> <!-- Tabela -->
                            <th scope="coluna">Objeto</th>
                            <th scope="coluna">Id</th>
                            <th scope="coluna">Atributo</th>
                            <th scope="coluna">Tipo de Valor</th>
                            <th scope="coluna">Nome do Campo no Formulário</th>
                            <th scope="coluna">Tipo do Campo no Formulário</th>
                            <th scope="coluna">Tipo de Unidade</th>
                            <th scope="coluna">Ordem do Campo no Formulário</th>
                            <th scope="coluna">Tamanho do Campo no Formulário</th>
                            <th scope="coluna">Obrigatório</th>
                            <th scope="coluna">Estado</th>
                            <th scope="coluna">Escolher</th>
                            <th scope="coluna">Ordem</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $query_objeto = "SELECT * FROM object"; // Query para obter todos os tuplos da tabela object
                        $resultado_objeto = executa_query($query_objeto); // Execução da query($query_objeto)

                        foreach ($resultado_objeto AS $objeto) { 
                        // Estrutura para percorrer os elementos de um array, 
                        //para cada iteração o valor do elemento atual do Array é atribuído ao valor $objeto.   

                            // Seleciona todos os atributos da tabela attribute se ids forem iguais
                            $query_atributo = "SELECT attribute.* 
                                               FROM attribute,object
                                               WHERE object.id = attribute.obj_id 
                                               AND object.id = '{$objeto['id']}'";
                            
                            $resultado_atributo = executa_query($query_atributo); 
                            // Execução da query($query_atributo)

                            $num_rows = mysqli_num_rows($resultado_atributo); 
                            // Saber/Contar o número de linhas do $resultado_atributo

                            if ($num_rows > 0) { // Se num_rows maior que zero
                                ?>
                                <tr> <!-- colspan é o nr de colunas que uma parcela vai conter , rowspan = nr de linhas que uma celula vai ter-->
                                <td scope= "row" class="nome" colspan="1" rowspan="<?php echo $num_rows; ?>">
                                    <?php echo $objeto['name']; ?>
                                </td>
                                <?php
                                foreach ($resultado_atributo as $atributo) {
                                    // Estrutura para percorrer os elementos de um array, 
                                    //para cada iteração o valor do elemento atual da Array é atribuído ao valor $atributo. 
                                    
                                    
                                    // Tabela que contém os valores dos atributos
                                    ?>
                                    <td scope="td"><?php echo $atributo['id'];?></td>
                                    <td scope="td"><?php echo $atributo['name'];?></td>
                                    <td scope="td"><?php echo $atributo['value_type'];?></td>
                                    <td scope="td"><?php echo $atributo['form_field_name'];?></td>
                                    <td scope="td"><?php echo $atributo['form_field_type'];?></td>
                                    <?php

                                    // Query para termos o nome das unidades, comparando os ids
                                    $query_da_unidade = "SELECT name 
                                                         FROM attr_unit_type 
                                                         WHERE attr_unit_type.id = '{$atributo['unit_type_id']}'";
                                    
                                    $resultado_da_unidade = executa_query($query_da_unidade); // Execução da query($query_da_unidade)

                                    //  O msqli_fect_assoc busca uma linha de resultado como um array associativo
                                    $unidade = mysqli_fetch_assoc($resultado_da_unidade);

                                    if ($atributo['unit_type_id'] != NULL) {
                                        ?>
                                        <td scope="td"><?php echo $unidade['name'];?></td>
                                        <?php
                                    } else {
                                        ?>
                                        <td scope="td">-</td>
                                        <?php
                                    }
                                    ?>
                                    <td scope="td">
                                        <!--Não há verificações logo não preciso do if-->
                                        <?php echo $atributo['form_field_order']; ?> 
                                    </td>
                                    <?php
                                    if ($atributo['form_field_size'] != null) {
                                        ?>
                                        <td scope="td">
                                            <?php echo $atributo['form_field_size']; ?>
                                        </td>
                                        <?php
                                    } else {
                                        ?>
                                        <td scope="td">-</td>
                                        <?php
                                    }

                                    if ($atributo['mandatory'] == 1) {
                                        ?>
                                        <td scope="td">SIM</td>
                                        <?php
                                    } else {
                                        ?>
                                        <td scope="td">NÃO</td>
                                        <?php
                                    }
                                    if ($atributo['state'] == "active") {
                                        ?>
                                        <td scope="td">Activo</td>
                                        <?php
                                    } else {
                                        ?>
                                        <td scope="td">Inactivo</td>
                                        <?php
                                    }
                                    ?>
                                    <td scope="td">
                                                  <!-- Faz a analise de todos os campos com [] -->
                                        <input  type="checkbox" name="check[]" value="<?php echo $atributo['id']; ?>">
                                    </td>
                                    <td>
                                        <input  scope="textbox" type="number" name="order_<?php echo $atributo['id']; ?>">
                                    </td>
                                    </tr>
                                    <?php
                                }
                            }
                        }
                        ?>
                        </tbody>
                    </table>
                  <p id="botao">              
                <input type="hidden" name="estado_execucao" value="inserir"><!-- Botão para o estado inserir-->
                <input class="button" type="submit" value="Criar Formulário">
                    </p>
        </form>
        <?php // Estado Fechado
        
    }
    elseif($_REQUEST['estado_execucao'] == "inserir")
    {
        // Verifica o nome do formulário
        $nome_formulario = guarda_variavel($_REQUEST['form_name']); // Verifica se tem caracteres especiais
       
        $check= $_REQUEST['check']; // Busca o array do check[] visto acima

        if(empty($nome_formulario)) // Se nome formulario for vazio da erro
        {
            ?>
            <p id="alerta" >Tem de escolher um nome para o formulário!</p>
            <?php
            back();
        }
        elseif(is_null($check)) // Se não tiver nenhum campo check da erro
        {
            ?>
            <p id="alerta" >Tem de escolher pelo menos um atributo!</p>
            <?php
            back();
        }
        else
        {

            $query_inserir_nome ="INSERT INTO custom_form (`name`) VALUES('$nome_formulario');"; // Query para inserir o nome do novo formulário
            
            $resultado_inserir_nome = mysqli_query($liga,$query_inserir_nome); // Execução da query($query_inserir_nome)
            
            $custom_form_id = mysqli_insert_id($liga); // ID do novo formulário(último inserido)

            foreach($check as $valor)  
            // Estrutura para percorrer os elementos de um array, 
            //para cada iteração o valor do elemento atual do Array é atribuído ao $valor.
            {

                $ordem_v = $_REQUEST['order_'.$valor]; // Recebe a ordem como input

                if(empty($ordem_v)) // caso o campo ordem esteja vazio
                {
                    
                    
                    $custom_form_id_v=guarda_variavel($custom_form_id); // Faz verificação para ver se tem caracteres especiais
                    $valor_v=guarda_variavel($valor); // Faz verificação para ver se tem caracteres especiais
                    
                    // Query para inserir id do novo formulario e atributo_id com os valores passados pelo ('$custom_form_id_v','$valor')
                    $query_inserir_custom_f_h_attribute ="INSERT INTO custom_form_has_attribute (custom_form_id,attribute_id) 
													      VALUES ('$custom_form_id_v','$valor_v')";

                    $resultado_ins_custom_form_has_attr = mysqli_query($liga, $query_inserir_custom_f_h_attribute); // Execução da query, executa uma consulta na base de dados.
                    
                }
                else // Se o ordem não for vazio
                {
                    $custom_form_id_v=guarda_variavel($custom_form_id); // Faz verificação para ver se tem caracteres especiais
                    
                    $valor_v=guarda_variavel($valor); // Faz verificação para ver se tem caracteres especiais
                    $ordem_v= guarda_variavel($ordem_v);// Faz verificação para ver se tem caracteres especiais
                    
                    // Query para inserir em custom_form_has_attribute nos atributos (`custom_form_id`, `attribute_id`, `field_order`) os valores ('$custom_form_id_v','$valor','$ordem')
                    $inserir_custom_f_h_attribute="INSERT INTO custom_form_has_attribute (`custom_form_id`, `attribute_id`, `field_order`) 
														 VALUES ('$custom_form_id_v','$valor_v','$ordem_v')";
                    $resultado_ins_custom_form_has_attr = mysqli_query($liga,$inserir_custom_f_h_attribute); // Execução da query, executa uma consulta na base de dados.
                   
                }
            }

            if($resultado_inserir_nome && $resultado_ins_custom_form_has_attr) // Se os dois se confirmarem, inserção feita com sucesso
            {
                ?>
                <p id="feito">Inseriu os dados para o novo formulário com sucesso.</p>
                <p id="feito">Clique em <a href="gestao-de-formularios">Continuar</a> para avancar.</p><br>
                <?php
                
            }
        }

        // -------RT-08-----
    }elseif ($_REQUEST['estado_execucao'] == "editar_form") {
       
        // Estado editar formulários
        
        ?>
        <h3><b>Gestão de formulários customizados - Editar</b></h3>
            <?php
            // Busca o id formulário ao URL 
            $custom_form_id = $_GET['id'];

            // Query para buscar o nome do formulario
            $query_nome_formulario = " SELECT name 
                                        FROM custom_form 
                                        WHERE id = '$custom_form_id'";

            // Execução da query
            $resultado_nome_formulario = executa_query($query_nome_formulario);

            // O msqli_fect_assoc busca uma linha de resultado como um array associativo
            $array_nome_formulario = mysqli_fetch_assoc($resultado_nome_formulario);
            ?>
            <form name="gestao-de-formularios-editar" method="POST">
                
                    <input type="hidden" name="estado_execucao" value="atualizar_formulario">
                    <p id="amendoas">
                        <label><b>Nome do Formulário:</b></label>
                        <input  type="text" name="form_name" value="<?php echo $array_nome_formulario['name']; ?>">
                    </p>

                    <table class="mytable">
                        <thead>
                        <tr>
                            <th scope="coluna">Objeto</th>
                            <th scope="coluna">ID</th>
                            <th scope="coluna">Atributo</th>
                            <th scope="coluna">Tipo de Valor</th>
                            <th scope="coluna">Nome do Campo no Formulário</th>
                            <th scope="coluna">Tipo do Campo no Formulário</th>
                            <th scope="coluna">Tipo de Unidade</th>
                            <th scope="coluna">Ordem do Campo no Formulário</th>
                            <th scope="coluna">Tamanho do Campo no Formulário</th>
                            <th scope="coluna">Obrigatorio</th>
                            <th scope="coluna">Estado</th>
                            <th scope="coluna">Escolher</th>
                            <th scope="coluna">Ordem</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php

                        // Query para buscar o id e o name da tabela object
                        $query_objeto = " SELECT id, name
                                       FROM object";

                        // Execução da query acima
                        $resultado_objeto = executa_query($query_objeto );

                        // Ciclo para percorrer linha a linha 
                        // O msqli_fect_assoc busca uma linha de resultado como um array associativo
                        while ($array_objeto = mysqli_fetch_assoc($resultado_objeto)) {
                            
                            // Query para selecionar todos os atributos da tabela attribute
                            $query_atributo = "SELECT * 
                                                FROM attribute
                                                WHERE obj_id ='{$array_objeto["id"]}'";

                            // Execução da query acima
                            $resultado_atributo = executa_query($query_atributo);
                            
                            // Vai contar o número de linhas em $resultado_atributo
                            $num_rows_atributo = mysqli_num_rows($resultado_atributo);
                            
                            if ($num_rows_atributo > 0) {
                            ?>
                            <tr>
                            <!-- colspan é o nr de colunas que uma parcela vai conter , rowspan = nr de linhas que uma celula vai ter-->
                            <td  scope= "row" colspan="1" rowspan="<?php echo $num_rows_atributo;?>">
                                 <?php echo $array_objeto['name']; ?>
                            
                           

                            <?php
                            // Ciclo para percorrer linha a linha 
                            // O msqli_fect_assoc busca uma linha de resultado como um array associativo
                            while ($array_atributo = mysqli_fetch_assoc($resultado_atributo)) {
                                ?>
                                <td> <?php echo $array_atributo['id']; ?> </td>
                                <td> <?php echo $array_atributo['name']; ?> </td>
                                <td> <?php echo $array_atributo['value_type']; ?> </td>
                                <td> <?php echo $array_atributo['form_field_name']; ?> </td>
                                <td> <?php echo $array_atributo['form_field_type']; ?> </td>
                                <?php

                                // Se o unit_type_id do $array_obj_attr não for nulo
                                if ($array_atributo['unit_type_id'] != null) {
                                  
                                    // Query para selecionar o name da tabela attr_unit_type
                                    $query_unit_types = " SELECT name
                                                          FROM attr_unit_type
                                                          WHERE " . $array_atributo['unit_type_id'] . " = id";
                                    
                                    // Execução da query acima
                                    $resultado_unit_types = executa_query($query_unit_types);

                                    // O msqli_fect_assoc busca uma linha de resultado como um array associativo
                                    $array_unit_types = mysqli_fetch_assoc($resultado_unit_types);
                                    ?>
                                    <td>
                                        <?php echo $array_unit_types['name']; ?>
                                    </td>
                                    <?php
                                } else {
                                    ?>
                                    <td>-</td>
                                    <?php
                                }
                                ?>
                                <td> <?php echo $array_atributo['form_field_order']; ?> </td>
                                <?php
                               
                                if ($array_atributo['form_field_size'] != null) {
                                    ?>
                                    <td> <?php echo $array_atributo['form_field_size']; ?> </td>
                                    <?php
                                } else {
                                    ?>
                                    <td>-</td>
                                    <?php
                                }
                                
                                // Se for obrigatório
                                if ($array_atributo['mandatory'] == 1) {
                                    ?>
                                    <td>SIM</td>
                                    <?php
                                } else {
                                    ?>
                                    <td>NÃO</td>
                                    <?php
                                }

                                // Se o estado for ativo
                                if ($array_atributo['state'] == "active") {
                                    ?>
                                    <td>Activo</td>
                                    <?php
                                } else {
                                    ?>
                                    <td>Inactivo</td>
                                    <?php
                                }

                                // Query para selecionar o attribute_id, custom_form_id e field_order da tabela custom_form_has_attribute
                                $query_form_has_attr = " SELECT attribute_id, custom_form_id, field_order
                                                        FROM custom_form_has_attribute 
                                                        WHERE custom_form_id = '{$custom_form_id}' 
                                                        AND attribute_id = '{$array_atributo['id']}'";

                                // Execução da query acima
                                $resultado_form_has_attr = executa_query($query_form_has_attr);

                                // O msqli_fect_assoc busca uma linha de resultado como um array associativo
                                $array_form_has_attr = mysqli_fetch_assoc($resultado_form_has_attr);

                                // Se os id são iguais
                                if ($array_atributo['id'] == $array_form_has_attr['attribute_id']) {
                                    ?>
                                    <td>
                                                <!-- Faz a analise de todos os campos com [] -->
                                        <input type="checkbox" name="check[]" value="<?php echo $array_atributo['id']; ?>" CHECKED>
                                    </td>
                                    <td>
                                        <input  scope="textbox" type="number" name="order_<?php echo $array_atributo['id']; ?>" value="<?php echo $array_form_has_attr['field_order']; ?>">
                                    </td>
                                    <?php
                                } else {
                                    ?>
                                    <td>
                                               
                                                <!-- Faz a analise de todos os campos com [] -->
                                        <input type="checkbox" name="check[]" value="<?php echo $array_atributo['id']; ?>">
                                    </td>
                                    <td>
                                        <input  scope="textbox" type="number" name="order_<?php echo $array_atributo['id']; ?>">
                                    </td>
                                    <?php
                                    
                                }
                                ?>
                                </tr>
                                <?php
                            }
                        }
                        }
                        ?>
                        </tbody>
                    </table>
                    <p id="botao">
                        <input  type="submit" value="Atualizar Formulario">
                    </p>
            
            </form>  <!--RT-09--> 
            <?php
        ?>
       <?php 
    }else if ($_REQUEST['estado_execucao'] == "atualizar_formulario") {
        
        // Uso get para ir buscar o id do formulário ao URL
        $custom_form_id = $_GET['id'];

        // Faço verificação para ver se tem caracteres especiais
        $nome_formulario = guarda_variavel($_REQUEST['form_name']);

        // O request serve para a cada iteração colocar no $select o valor que está no check
        $verifica_check = $_REQUEST['check'];

        // Se nome do formuario for empty, dá mensagem de erro
        if (empty($nome_formulario)) {
            ?>
            <p id="alerta">Tem de escolher o nome do formulário.</p>
            <?php
            back();
        } elseif (is_null($verifica_check)) { // Se o select for null, dá mensagem de erro
            ?>
            <p id="alerta">Tem de escolher pelo menos um atributo.</p>
            <?php
            back();
        } else {

            // Query para o update do nome do formulário cujo id é igual ao id do $custom_form_id 
            $novo_nome = " UPDATE `custom_form` SET `name` = '$nome_formulario' WHERE `id` = '$custom_form_id' ";

            // Execução da query acima na BD
            $resultado_update_nome = executa_query($novo_nome);

            //DELETE -  retirar o valor antigo, coloca o atualizado 
            $elimina_custom_form_has_attribute = "DELETE FROM `custom_form_has_attribute` WHERE `custom_form_id` = '$custom_form_id'";

            $resultado_eliminar = executa_query($elimina_custom_form_has_attribute);

            // Foreach serve para percorrer o array pelo indice, onde $select é o array e $index é o indice do mesmo
            // e $id_checks os dados associados ao indice
            foreach ($verifica_check as $id_checks) {
                $ordem = $_REQUEST['order_' . $id_checks];

                // Se ordem for emptyo então o valor da ordem fica null na BD 
                if (empty($ordem)) {

                    // Query Update, insere o custom_form_id e o id_checks na tabela custom_form_has_atribute
                    $update_custom_form_has_attr = " INSERT INTO custom_form_has_attribute (`custom_form_id`,`attribute_id`) 
                                                VALUES ('$custom_form_id', '$id_checks')";
                    // Execução da query acima 
                    $resultado_update_custom_form_has_attribute = executa_query($update_custom_form_has_attr);

                } // Se ordem não for empty, insere o valor da ordem no formulário 
                else {

                    // Query Update, insere o $custom_form_id, o $id_checks e a $ordem no na tabela custom_form_has_attribute
                    $update_custom_form_has_attr = " INSERT INTO custom_form_has_attribute (`custom_form_id`, `attribute_id`, `field_order`) 
                                                VALUES ('$custom_form_id', '$id_checks','$ordem')";

                    // Executa a query acima
                    $resultado_update_custom_form_has_attribute = executa_query($update_custom_form_has_attr);
                }
            }

            // Se os três se confirmarem então a transação foi feita com sucesso
            if ($resultado_update_nome && $resultado_update_custom_form_has_attribute && $resultado_eliminar) {
                ?>
                <p id="feito">Os dados do formulário foram atualizados com sucesso.
                Clique em <a href="gestao-de-formularios">Continuar</a> para avancar.</p>
                <?php
            }
        }
    }          
}       
     else { // Se o utilizador não tiver autorização escreve a mensagem abaixo
    ?>
     Não tem autorização para aceder a esta página.
<?php
}