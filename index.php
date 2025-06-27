<?php
require_once("util/Conexao.php");
$con = Conexao::getConexao();

$msgErro = '';
$erros = [];

$nome_perito = '';
$tipo = '';
$local = '';
$data = '';
$status = '';
$evidencias = '';
$observacao = '';

$sql = "SELECT * FROM laudos";
$stm = $con->prepare($sql);
$stm->execute();
$laudos = $stm->fetchAll();

if (isset($_POST["nome_perito"])) {
    $nome_perito = trim($_POST["nome_perito"]);
    $tipo = $_POST["tipo"];
    $local = trim($_POST["local"]);
    $data = trim($_POST["data"]);
    $status = $_POST["status"];
    $evidencias = trim($_POST["evidencias"]);
    $observacao = trim($_POST["observacao"]);

    if (!$nome_perito) array_push($erros, "Informe o nome do perito.");
    if (!$tipo) array_push($erros, "Selecione o tipo de perícia.");
    if (!$local) array_push($erros, "Informe o local da perícia.");
    if (!$data) array_push($erros, "Informe a data.");
    if (!$status) array_push($erros, "Selecione o status.");
    if (!$evidencias) array_push($erros, "Descreva as evidências.");

    if (strlen($nome_perito) < 3)
        array_push($erros, "Nome do perito muito curto.");
    if (!strtotime($data))
        array_push($erros, "Data inválida.");

    if (count($erros) == 0) {
        $sql = "INSERT INTO laudos (nome_perito, tipo_pericia, local_pericia, data, status, evidencias, observacao)
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stm = $con->prepare($sql);
        $stm->execute([$nome_perito, $tipo, $local, $data, $status, $evidencias, $observacao]);

        header("Location: index.php");
        exit();
    } else {
        $msgErro = implode("<br>", $erros);
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Registro de Perícias Criminais</title>
    <style>
        body {
            background-color: #f4f4f4;
            font-family: sans-serif;
            text-align: center;
            padding: 20px;
        }
        form, table {
            margin: auto;
            width: 90%;
            max-width: 800px;
            margin-top: 20px;
        }
        input, select, textarea {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #aaa;
            border-radius: 5px;
        }
        button {
            padding: 10px 20px;
            background-color: teal;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        table {
            border-collapse: collapse;
            margin-top: 30px;
        }
        th, td {
            border: 1px solid #444;
            padding: 8px;
        }
        th {
            background-color: #ddd;
        }
        #divErro {
            color: red;
            font-weight: bold;
            margin-top: 20px;
        }
    </style>
</head>
<body>

<h1>Registro de Perícias Criminais</h1>
<h4>Cadastro e listagem de laudos</h4>

<div id="divErro"><?= $msgErro ?></div>

<form action="" method="POST">
    <input type="text" name="nome_perito" placeholder="Nome do perito" value="<?= $nome_perito ?>" />
    
    <select name="tipo">
        <option value="">Tipo de perícia</option>
        <option value="AM" <?= $tipo == 'AM' ? 'selected' : '' ?>>Ambiental</option>
        <option value="HM" <?= $tipo == 'HM' ? 'selected' : '' ?>>Homicídio</option>
        <option value="CB" <?= $tipo == 'CB' ? 'selected' : '' ?>>Cibernética</option>
        <option value="BT" <?= $tipo == 'BT' ? 'selected' : '' ?>>Balística</option>
    </select>

    <input type="text" name="local" placeholder="Local da perícia" value="<?= $local ?>" />
    <input type="date" name="data" value="<?= $data ?>" />

    <select name="status">
        <option value="">Status do laudo</option>
        <option value="EA" <?= $status == 'EA' ? 'selected' : '' ?>>Em andamento</option>
        <option value="F" <?= $status == 'F' ? 'selected' : '' ?>>Finalizado</option>
        <option value="P" <?= $status == 'P' ? 'selected' : '' ?>>Pendente</option>
    </select>

    <textarea name="evidencias" placeholder="Evidências encontradas"><?= $evidencias ?></textarea>
    <textarea name="observacao" placeholder="Observações (opcional)"><?= $observacao ?></textarea>

    <button type="submit">Gravar Laudo</button>
</form>

<h2>Laudos Registrados</h2>
<table>
    <tr>
        <th>ID</th>
        <th>Perito</th>
        <th>Tipo</th>
        <th>Local</th>
        <th>Data</th>
        <th>Status</th>
        <th>Evidências</th>
        <th>Observações</th>
        <th>Ações</th>
    </tr>

    <?php foreach ($laudos as $l): ?>
        <tr>
            <td><?= $l["id"] ?></td>
            <td><?= htmlspecialchars($l["nome_perito"]) ?></td>
            <td><?= $l["tipo_pericia"] ?></td>
            <td><?= $l["local_pericia"] ?></td>
            <td><?= $l["data"] ?></td>
            <td><?= $l["status"] ?></td>
            <td><?= nl2br(htmlspecialchars($l["evidencias"])) ?></td>
            <td><?= nl2br(htmlspecialchars($l["observacao"])) ?></td>
            <td><a href="excluir.php?id=<?= $l["id"] ?>" onclick="return confirm('Confirma a exclusão?');">Excluir</a></td>
        </tr>
    <?php endforeach ?>
</table>

</body>
</html>
