
<?php
require_once("custom/php/common.php");

        // Verifico se o utilizador esta logado e se tem acesso
if (is_user_logged_in() && current_user_can('manage_custom_forms')) {

    // Realizo a ligação com a base de dados
    $liga =  liga_basedados();

      // estado_execução == "" -estado inicial-
    if ($_REQUEST["estado_execucao"] == "") {
        ?>
        <h3>Gestão de Formulários Customizados</h3>
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
                    <th>Nome do Formulário</th>
                    <th>Id</th>
                    <th>Atributo</th>
                    <th>Tipo de Valor</th>
                    <th>Nome do Campo no Formulário</th>
                    <th>Tipo do Campo no Formulário</th>
                    <th>Tipo de Unidade</th>
                    <th>Ordem do Campo no Formulário</th>
                    <th>Tamanho do Campo no Formulário</th>
                    <th>Obrigatório</th>
                    <th>Estado</th>
                    <th>Acção</th>
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
                        <td colspan="1" rowspan="<?php echo $num_rows_attribute; ?>"> <!-- Passa ao  rowspan o valor do $num_rows_attribute visto acima -->

                            <!-- Após  o nome gestao-de-formularios-custmizados são passados o estado= editar_form e é passado o id enquanto que o name é colocado na tabela-->
                            <?php 
                            echo '
                                        <a href="gestao-de-formularios-customizados?estado=editar_form&id=' . $array_formularios_customizados['id'] . '">
                                            ' . $array_formularios_customizados['name'] . ' 
                                        </a>';
                            ?>
                        </td>
                        <?php
                        // Preencher o resto da tabela com os valores da tabela atributo
                        while ($array_attribute = mysqli_fetch_assoc($resultado_attribute)) {
                        ?>
                        <!-- Valores para preencher a tabela, valores vêm de cima ($array_attribute) -->
                        <td><?php echo $array_attribute['id']; ?></td>
                        <td><?php echo $array_attribute['name']; ?></td>
                        <td><?php echo $array_attribute['value_type']; ?></td>
                        <td><?php echo $array_attribute['form_field_name']; ?></td>
                        <td><?php echo $array_attribute['form_field_type']; ?></td>
                        <?php

                       
                        if ($array_attribute['unit_type_id'] != null) { // Faz a verificação para ver se há unidades
                            
                            // A query vai selecionar as unidades(name) da tabela attr_unit_type e e comparar o id com o id que está no array
                            $query_tipo_unidade = "SELECT DISTINCT name
                                                        FROM attr_unit_type
                                                        WHERE id='{$array_attribute['unit_type_id']}'";

                            $resultado_tipo_unidade = executa_query($query_tipo_unidade); // Passa $query_tipo_unidade como parametro de executa_query e coloca o resultado na variavel $resultado_tipo_unidade

                            // VER MELHOR
                            $array_tipo_unidade = mysqli_fetch_assoc($resultado_tipo_unidade);
                            ?>
                            <td><?php echo $array_tipo_unidade['name'];?></td>
                            <?php
                        // Se não existem unidades coloca o (-)
                        } else { 
                            ?>
                            <td> - </td>
                            <?php
                        }
                        ?>
                        <td><?php echo $array_attribute['form_field_order']; ?></td>
                        <?php
                        // Se o array for diferente de null, ou seja, não estiver vazio vai imprimir o valor do form_field_size
                        if ($array_attribute['form_field_size'] != null) { 
                            ?>
                            <td><?php echo $array_attribute['form_field_size'];?></td>
                            <?php
                        // Se o array estiver vazio coloca o (-) 
                        } else { 
                            ?>
                            <td> - </td>
                            <?php
                        }

                        // Se for obrigatório coloca 'SIM', se não for obrigatório coloca 'NÃO'
                        if ($array_attribute['mandatory'] == 1) {
                            ?>
                            <td>SIM</td>
                            <?php
                        } else { 
                            ?>
                            <td>NÃO</td>
                            <?php
                        }

                        if ($array_attribute['state'] == "active") { // Se  o estado for ativo escreve o que está abaixo 
                            ?>
                            <td>Activo</td> 
                            <td>[editar]<br>[desactivar]</td>
                            <?php
                        } else { // Se o estado for inativo escreve o que está abaixo
                            ?>
                            <td>Inactivo</td>
                            <td>[editar]<br>[activar]</td>
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
    }       
} else { // Se o utilizador não tiver autorização escreve a mensagem abaixo
    //
    ?>
     Não tem autorização para aceder a esta página.
<?php
}