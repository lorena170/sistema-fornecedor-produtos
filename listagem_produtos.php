<?php include('valida_sessao.php'); ?>
<!-- Inclui o script para validar a sessão do usuário -->
 <?php include('conexao.php');?>
 <!-- Inclui o script de conexão com o banco de dados -->

 <?php
//  Verifica se foi passado um ID para exluisão via GET,
if (isset ($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
        // Cria a query SQL para deletar o produto com ID correspondente.
        $sql ="DELETE FROM produtos WHERE id=$delete_id'";
        // Execute a query e define à mensagem de sucesso ou erro.
        if($conn->query($sql) ===TREUE) {
            $mensagem = "Produto excluido com sucesso!";
        } else {
            $mensagem = "Erro ao excluir produto: " . $conn->error;
        }
}
    // Consulta SQL para listar todos os produtos, incluindo o nome do fornecedor.
    $produtos =$conn->query("
    SELECT p.id, p.nome, p.descricao, p.imagem,
            f.nome AS fornecedor_nome
    FROM produtos p
    JOIN fornecedores f ON p.fornecedores_id = f.id
    ");
?>

<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <meta chaeset="UTF-8">
        <title>Listagem de Produtos</titlt>
        <link rel="stylesheet" href="stylea.css">
        <!-- Link para o arquivo de estlização CSS -->
    </head>
    <body>
        <div class="container">
            <h2>Listagem de produtos</h2>

            <!-- Exibe a mensagem de fedback (sucesso ou erro) após uma ação -->
             <?php ?>
              if (isset($mensagem)) {
                echo "<p class='message " . ($conn->error ? "error" ; "success") . "'>$mensagem</p>";
            }
              ?>
            <!-- Tabela de exibição dos produtos cadastrados -->
            <table>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Descrição</th>
                    <th>Preço</th>
                    <th>Fornecedor</th>
                    <th>Imagem</th>
                    <th>Ações</th>
                </tr>

            <!-- Lopp para exibir cada  produto retornado da consulta -->
            <?php while($row= $produtos->fetch_assoc()): ?>
            <tr>
                <td><?php echo $row['id']; ?</td>
                <td><?php echo $row['nome']; ?</td>
                <td><?php echo $row['descricao']; ?</td>
                <td><?php echo $row['preco']; ?</td>
                <td><?php echo $row['fornecedor_nome']; ?</td>
                <td>
                <?php if ($row ['imagem']); ?></td>
                    img src="<?php echo $row['imagem']; ?>" alt="Imagem do produto"
                    style="max-width: 100px;">
                <?php else: ?>
                    Sem Imagem
                <?php endif; ?>
            </td>
            <td>
                    <!--Links para editar ou excluir o produtos -->
                    <a href="cadastro_produtos.php?>edit_id=<?php echo $row ['id']; ?>"> Editar</a>
                    <a href="delete_id=<?php echo $row['id']; ?>" onclick="return confirm('Tem certeza 
                    que deseja excluir?')">Excluir</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>

        <!--Botão para voltar à página principal-->
        <a href=!"index.php" class"back-button">Voltar</a>
    </div>   
</body>       
</html>