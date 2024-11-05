<?php
// inclui o arquivo que valida a sessao do usuario
include('valida_sessao.php');

// inclui o arquivo de conexao com o banco de dados
include('conexao.php');

// funçao para redimensionar e salvar as imagens
function redimensionarESalvarImagem($arquivo, $largura = 80, $autura = 80) {
    $diretorio_destino = "img/";
    $nome_arquivo = uniqid() . '_' . basename($arquivo['name']);
    $caminho_completo = $diretorio_destino . $nome_arquivo;
    $tipo_arquivo = strtolower(pathinfo($caminho_completo, PATHINFO_EXTENSION));

    // verifica se e uma imagem valida
    $check = getimagesize($arquivo["tmp+name"]);
if ($check === false) {
    return "o arquivo nao e uma imagem valida"
}

// verifica o tamanho di arquivo (limite de 5MB)
if ($arquivo['size'] > 5000000) {
    return "o arquivo e muito grande, o matanho maximo permitido é 5MB"
}
// permite apenas alguns formatos de arquivo
if($tipo_arquivo != "jpg" && $tipo_arquivo != "png" && $tipo_arquivo "gif" && $tipo_arquivo != "jpeg") {
    return "apenas arquivos JPG, JPEG, PNG e GIF sao permitidos." ; 
}

// cria unma nova imagem a partir do arquivo enviado
if ($tipo_arquivo == "jpg" || $tipo_arquivo == "jpeg") {
    $imagem_original = imagecreatefromjpeg($arquivo["tmp_name"]);
} elseif ($tipo_arquivo == "png") {
    $imagem_original = imagecreatefrompng($arquivo["tmp_name"]);

} elseif ($tipo_arquivo == "gif") {
    $imagem_original = imagecreatefromgif($arquivo["tmp_name"]);
}

// obtem as dimençoes originais da imagem
$largura_original = imagesx($imagem_original);
$altura_original = imagesy($imagem_original);

// calcula as novas dimençoes mantendo proporçao
$ratio = min($largura / $largura_original, $altura / $altura_original);
$nova_largura = $largura_original * $ratio;
$nova_autura = $altura_original * $ratio;

// cria uma nova imagem com as dimençoes calculadas
$nova_imagem = imagecreatetruecolor($nova_autura, $nova_largura);

// redimensiona a imagem original para a nova imagem
imagecopysampled($nova_imagem, $imagem_original, 0, 0, 0, 0, $nova_largura, $largura_original, $altura_original);

// salva nova imagem
if ($tipo_arquivo == "jpg" || $tipo_arquivo == "jpeg") {
    imagejpeg($nova_imagem, $caminho_completo, 90);
} elseif ($tipo_arquivo == "png") {
    imagejpng($nova_imagem, $caminho_completo);

} elseif ($tipo_arquivo == "gif") {
    imagegif($nova_imagem, $caminho_completo);
}

// libera a memoria
imagedestroy($imagem_original);
imagedestroy($nova_imagem);
return $caminho_completo;

}

// verifica se o formulari foi enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST ['id'];
    $nome = $_POST ['nome'];
    $email = $_POST ['email'];
    $telefone = $_POST ['telefone'];
    // processa o upload da imagem
    $imagem = "";
    if (isset($files['imagem']) && $_FILES['imagem']['error'] == 0) {
        $resultado_upload = redimensionarESalvarImagem($_FILES['imagem']);
        if(strpos($resultado_upload, 'img/') == 0) {
            
            $imagem = $resultado_upload;
        } else {
            $mensagem_erro = $resultado_upload;
        }
    }
    // prepara a query sql para a inserçao ou atualizaçao
    if ($id) {
        // se ID existe, e uma atualizaçao
        $sql = "UPDATE fornecedores SET nome= '$nome'. email='$email', telefone='$telefone'";
        if($imagem) {
            $sql .=", imagem = '$imagem'";
                }
            $sql .= "WHERE id='$id'";
            $mensagem = "fornecedor atualizado com sucesso!";
    } else { 
        // se nao ha ID, e uma nova inserçao
        $sql = "INSERT INTO fornecedores (nome, email, telefone, imagem) VALUES ('$nome', '$email', '$telefone', '$imagem'";
        $mensagem = "fornececor cadastrado com sucesso!";
    }
    // executa a query e verifica se houve erro
    if ($conn->query($sql) !== TRUE) {
        $mensagem = "erro: " . $conn->error;
    }
}

// verifica se foi solicitada a exclusao de um fornecedor
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];

    // verifica se o fornecedor tem produtos cadastrados
    $check_produtos = $conn->query("SELECT COUNT(*) as count FROM produtos WHERE fornecedor_id = 'delete_id'")->fetch_assoc();

    if ($check_produtos ['count'] > 0){
        $mensagem = "nao é possivel exlcluir este fornecedor por existem produtos cadastrados para ele";
    } else {
        $sql = "DELETE FROM fornecedores WHERE id= '$delete_id";
        if ($conn->query($sql) === TRUE) {
            $mensagem = "fornecedor excluido com sucesso!";
        } else {
            $mensagem = "Erro ao ecluir fornecedor: " . $conn->error;
        }
    }
}

// busca todos os fonecedores para listar na tabela
$fonecedores = $conn->query("SELECT * FROM fornecedores");

// se foi solicitada a adiçao de um fornecedor, busca os dados dele
$fornecedor = null;
if (isset($_GET['edit_id'])) {
    $edit_id = $_GET['edit_id'];
    $fornecedor = $conn->query("SELECT * FROM fornecedores WHERE id='$edit_id'")->fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="PT-BR">
<head>
    <meta charset="UTF-8">

    <title>cadastro fonrecedor</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<div class="container" style="width; 900px;"> 
    <h2>Cadastro de fornecedores</h2>

    <!-- formulario para cadasrro/ediçao de fornecedor -->
     <form method="post" action ="" enctype="multipart/form-data">

        <input type="hidden" name="id" value="<?php echo $fornecedor['id'] ?? ''; ?>">

        <label for="nome">Nome:</label>
        <input type="text" name="nome" value="<?php echo $fornecedor ['nome'] ?? ''; ?> "> required>

        <label for="email">Email:</label>
        <input type="email" name="email" value="<?php echo $fornecedor ['email'] ?? ''; ?> ">

        <label for="Telefone">Telefone:</label>
        <input type="text" name="telefone" value="<?php echo $fornecedor ['telefone'] ?? ''; ?> ">

        <label for="imagem">imagem:</label>
        <input type="file" name="imagem" accept="image/*"><?php if (isset($fornecedor['imagem']) && $fornecedor['imagem']): ?>
            <img src="<?php echo $fornecedor['imagem']; ?>" alt ="imagem a tual do fornecedor" class="update-image">
            <?php endif; ?>
            <br>
            <button type="submit"><?php echo $fornecedor ? 'atualizar' : 'cadastrar'; ?> </button>
     </form>

     <!-- exibe mensahems de sucesso ou erro -->

     <?php 
     if (isset($mensagem)) echo "<p class='message" . (strpos($mensagem, 'Erro') !== false ? "error" : "sucess") . "'>$mensagem</p>";

     if (isset($mensagem_erro)) echo "<p class='message error'>$mensagem_erro</p>";
     ?>

     <h2>Listagem de fornecedores</h2>
     <!-- tabela para listar os fornecedores cadastrados -->

     <table>
        <tr>
            <th>ID</th>
            <th>Nome</th>
            <th>Email</th>
            <th>Telefone</th>
            <th>Imagens</th>
            <th>Açoes</th>

        </tr>

        <?php while ($row = $fornecedores->fetch_assoc()): ?>

        <tr>
            <td><?php echo $row['id'] ?></td>
            <td><?php echo $row['nome'] ?></td>
            <td><?php echo $row['email'] ?></td>
            <td><?php echo $row['telefone'] ?></td>
            <td>
                <?php if ($row['imagem']):   ?>
                    <img src="<?php echo $row['imagem']; ?>" alt="imagem do fornecedor"
                    class="thumbnail">
                    <?php else: ?>
                        sem imagem
                    <?php endif; ?>
            </td>
            <td>
                <a href="?edit_id=<?php echo $row['id']; ?>"> Editar</a>

                <a href="?delete_id=<?php echo $row['id']; ?>" onclick="return confirm('tem certeza que deseja exclkuir?')" > Editar</a>
            </td>
        </tr>
        <?php endwhile; ?>
     </table>
     <div class="actions">
        <a href="index.php" class="back-button">Voltar</a>
     </div>

</div>
    
</body>
</html>