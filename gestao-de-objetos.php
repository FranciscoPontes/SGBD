
<?php

require_once("custom/php/common.php");
 
// verifica se o utilizador fez login no wp e se tem permissão para mexer nos objetos
if (is_user_logged_in() && current_user_can('manage_objects')) {        
$liga =liga_basedados();
 
// quando o estado da execução não está definido
if ($_REQUEST["estado_execucao"] == "") {    
 
        // código SQL em formato string para obter os tuplos da tabela object
        $query_object = "SELECT * FROM object  ";
 
        // utiliza a função executa_query definida em common.php e executa o SQL na base de dados
        $result_object = executa_query($query_object);  
 
        // verifica se há objetos e se não houver dá mensagem de erro
        if (mysqli_num_rows($result_object) == 0) {    
            echo "Não há objetos ";        
        } else {
            echo"<table class='mytable2'>
                <tr>
                    <th scope='coluna'>tipo de objeto</th>
                    <th scope='coluna'>id</th>
                    <th scope='coluna'>nome do objeto</th>
                    <th scope='coluna'>estado</th>
                    <th scope='coluna'>ação</th>
                </tr>";
            
            // código SQL em formato string para obter id e nome da tabela obj_type, ordenados pelo nome
            $query_tipos_objeto="SELECT id,name from obj_type ORDER BY name";

            $result_tipos_objeto = executa_query($query_tipos_objeto);        
            
            // ciclo que percorre o array associativo
            while ($array_tipos_objeto = mysqli_fetch_array($result_tipos_objeto)) {


                // código SQL em formato string para obter id,nome e estado da tabela objeto 
                $query_objeto = "SELECT id, name,state                         
                                    FROM object WHERE obj_type_id=" . $array_tipos_objeto["id"] . " ".
                                    "ORDER BY name";         
                
                $result_objeto = executa_query($query_objeto);      
                
                //utiliza a função do mysql para saber o número de linhas para cada obj_type
                $lines_objeto = mysqli_num_rows($result_objeto);                    

                if ($lines_objeto > 0) {
                    
                    //colspan define o numero de colunas que irá ocupar(tamanho na horizontal) 
                     //   e rowspan define o numero de linhas que ira ocupar(tamanho na vertical)
                    echo"<tr>
                        <td scope= 'row' colspan='1' rowspan='$lines_objeto'>
                            ".$array_tipos_objeto["name"]."
                        </td>";   

                    // variavel para garantir o funcionamento correto dos <tr>, que na primeira iteração do while abaixo não são necessários
                    $variavel=1;
                    //criação de um array com os valores da query guardados na variável $result_objeto
                    while ($array_objeto = mysqli_fetch_array($result_objeto)) {              
                        if ($variavel==2){echo"<tr>";}
                        echo"<td>".$array_objeto["id"]."</td>
                        <td>".$array_objeto["name"]."</td>
                        <td>".$array_objeto["state"]."</td>
                        <td>";
                        if ($array_objeto["state"]=='active'){
                            echo '[editar][desativar]';
                        }  
                        echo"</td></tr>";
                        $variavel=2;  
                    }
                    $variavel=1;
                }
            }
            echo"</table>";
        }

        //Gestão de objetos-Introdução
        
        echo"<h3><strong>Gestão de Objetos - <span>Introdução</span></strong></h3>";
 
        //criação do formulário de inserção de objetos
        echo"<form name='gestao_de_objetos'>
        <p>
            <label><b>Nome:</b></label>
            <input type='text' name='nome_do_objeto'>
        </p>
        <p>
        <label><b>Tipo:</b></label>";
        // código SQL em formato string para obter tuplos de nome diferente e o seu id, da tabela obj_type
        $query_seleciona_tipos="SELECT distinct name,id from obj_type";

        $resultado_seleciona_tipos=executa_query($query_seleciona_tipos);

        // ciclo que percorre o array associativo
        while ($array_seleciona_tipos = mysqli_fetch_array($resultado_seleciona_tipos)) {
            $id=$array_seleciona_tipos["id"];
            $tipo=$array_seleciona_tipos["name"];         
        
            echo"<input type='radio' name='tipo_de_objeto'  value='$id'> $tipo";

        }
        echo"</p>
        <p> 
            <label><b>Estado:</b></label>
            <div>
                <label><b>Ativo</b></label>
                <input type='radio' name='estado' value='active'> 
 
                <label><b>Inativo</b></label>
                <input type='radio' name='estado' value='inactive'>
            </div>
        </p>
            <br>
            <input type= 'hidden' name= 'estado_execucao' value= 'inserir'>
            <p id='botao'>
            <input class= 'button' type= 'submit' value= 'Inserir objeto'>
            </p>
            <br><br>
        </form>";
    }
  
     // Gestão de objetos-Inserção
     elseif ($_REQUEST["estado_execucao"] == "inserir") {              
        echo"<h3><b>Gestão de objetos - inserção</b></h3>";

        //usa a funcao guarda_variavel para guardar nas variáveis os inputs sem carateres especiais
        $object_nome_do_objeto = guarda_variavel($_REQUEST['nome_do_objeto']);
        $object_estado = guarda_variavel($_REQUEST['estado']);
        //variavel para guardar o obj_type_id do objeto que corresponderá a um certo tipo
        $object_obj_type_id = guarda_variavel($_REQUEST['tipo_de_objeto']);
        if (empty($object_nome_do_objeto)) {             
            echo"<p>É necessário indicar um nome para o objeto.<p>";
            if (empty($object_obj_type_id)) {
                echo"<p>É necessário indicar o tipo de objeto.<p>";
            }
            if (empty($object_estado)) {
                echo"<p>É necessário indicar o estado do objeto.<p>";
            }
            back();
            return;                    
  
        }
        if (empty($object_obj_type_id)) {
            echo"<p>É necessário indicar o tipo de objeto.<p>";
            if (empty($object_estado)) {
                echo"<p>É necessário indicar o estado do objeto.<p>";
            }
            back();
            return;                     
        }
        if (empty($object_estado)) {
             echo"<p>É necessário indicar o estado do objeto.<p>";
             back();
             return;                  
        }
   
        // código SQL em formato string para inserir novos objetos
        $query_inserir = "INSERT INTO `object` (`id`, `name`, `state`, `obj_type_id`) VALUES (NULL,'$object_nome_do_objeto','$object_estado','$object_obj_type_id')"; 

        $result_insert = executa_query($query_inserir);

        if ($result_insert) {
        mysqli_query($liga,'COMMIT');
        echo"<p>Inserção de dados feita com sucesso!
        Clique  em <a href='gestao-de-objetos'>continuar</a> para avançar.
        <br/>";
        }
    }
} 
else {
    echo"Não tem autorização para aceder a esta página.";
}
?>
