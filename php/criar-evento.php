<?php
session_start(); // Inicia uma nova sessão ou resume uma sessão existente

if ((!isset($_SESSION['email']) == true) and (!isset($_SESSION['senha']) == true)) {
    session_unset(); // Remove todas as variáveis de sessão
    echo "<script>
            alert('Esta página só pode ser acessada por usuário logado');
            window.location.href = 'php/index.php';
            </script>";
}

$logado = $_SESSION['email'];

include "conexao.php";

$nome = "";
$descricao = "";
$hr_inicio = "";
$hr_termino = "";
$data = "";
$prioridade = "";

$errorMessage = "";
$successMessage = "";

// Verifica se o formulário foi submetido via POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if (isset($_SESSION['email']) && !empty($_SESSION['email'])) {
        $email = $_SESSION['email'];

        $sql_usuario = "SELECT id_usuario FROM usuario WHERE email = ?";
        $stmt_usuario = $conexao->prepare($sql_usuario);
        $stmt_usuario->bind_param("s", $email);
        $stmt_usuario->execute();
        $result_usuario = $stmt_usuario->get_result();

        if ($result_usuario->num_rows > 0) {
            $row_usuario = $result_usuario->fetch_assoc();
            $id_usuario = $row_usuario['id_usuario'];
        } else {
            $errorMessage = "Usuário não encontrado";
        }
    } else {
        $errorMessage = "Email do usuário não encontrado na sessão";
    }

    // Pega os dados do formulário
    $nome = $_POST["nome"];
    $descricao = $_POST["descricao"];
    $hr_inicio = $_POST["hr_inicio"];
    $hr_termino = $_POST["hr_termino"];
    $data = $_POST["data"];

    // Isso aqui verifica se $_POST["prioridade"] está definido, se não tiver, define como vazio
    $prioridade_value = isset($_POST["prioridade"]) && !empty($_POST["prioridade"]) ? $_POST["prioridade"] : "";

    function validateDate($date, $format = 'd/m/Y')
    {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) === $date;
    }

    // Verifica se a data foi inserida no formato correto (dd/mm/yyyy)
    if (!validateDate($data)) {
        $errorMessage = "Formato de data inválido. Use o formato dd/mm/yyyy.";
    } elseif (empty($prioridade_value)) {
        $errorMessage = "Preencha todos os campos, incluindo a prioridade";
    } elseif (strtotime($hr_inicio) > strtotime($hr_termino)) {
        $errorMessage = "A hora de início deve ser menor do que a hora de término";
    } else {
        // Converte a data para o formato do MySQL
        $data_mysql = DateTime::createFromFormat('d/m/Y', $data)->format('Y-m-d');

        $sql_evento = "INSERT INTO evento (nome, descricao, hr_inicio, hr_termino, data, id_prioridade, id_usuario)
        VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt_evento = $conexao->prepare($sql_evento);
        if (!$stmt_evento) {
            $errorMessage = "Erro na preparação da declaração SQL: " . $conexao->error;
        } else {
            $stmt_evento->bind_param("sssssii", $nome, $descricao, $hr_inicio, $hr_termino, $data_mysql, $prioridade_value, $id_usuario);
            $result_evento = $stmt_evento->execute();

            if ($result_evento) {
                $successMessage = "Evento adicionado com sucesso";
                header("location: ../agenda.php");
                exit;
            } else {
                $errorMessage = "Erro ao adicionar evento";
            }
        }
    }

    if (empty($nome) || empty($descricao) || empty($hr_inicio) || empty($hr_termino) || empty($data)) {
        $errorMessage = "Preencha todos os campos";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Planify Now</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/criar-evento.css">
</head>
<body>
    <div class="container my-5">
        <h2>Criar Evento</h2>

        <?php
            if ( !empty($errorMessage) ) {
                echo "
                <div class='alert alert-warning alert-dismissible fade show' role='alert'>
                    <strong>$errorMessage</strong>
                    <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                </div>
                ";
            }
        ?>

        <form method="POST">
            <input type="hidden" name="id_usuario" value="<?php echo $id_usuario; ?>">

            <div class="row mb-3">
                <label class="col-sm-3 col-form-label">Nome</label>
                <div class="col-sm-6">
                    <input type="text" class="form-control" name="nome" value="<?php echo $nome; ?>">
                </div>
            </div>

            <div class="row mb-3">
                <label class="col-sm-3 col-form-label">Descrição</label>
                <div class="col-sm-6">
                    <input type="text" class="form-control" name="descricao" value="<?php echo $descricao; ?>">
                </div>
            </div>
            
            <div class="row mb-3">
                <label class="col-sm-3 col-form-label">Hora de Início<br><h3 class="cinza">Exemplo: 09:30</h3></label>
                <div class="col-sm-6">
                    <input type="text" class="form-control" name="hr_inicio" value="<?php echo $hr_inicio; ?> ">
                </div>
            </div>

            <div class="row mb-3">
                <label class="col-sm-3 col-form-label">Hora de Término<br><h3 class="cinza">Exemplo: 11:30</h3></label>
                <div class="col-sm-6">
                    <input type="text" class="form-control" name="hr_termino" value="<?php echo $hr_termino; ?>">
                </div>
            </div>

            <div class="row mb-3">
                <label class="col-sm-3 col-form-label">Data<br><h3 class="cinza">Exemplo: 12/05/2024</h3></label>
                <div class="col-sm-6">
                    <input type="text" class="form-control" name="data" value="<?php echo $data; ?>">
                </div>
            </div>

            <div class="row mb-3">
                <label class="col-sm-3 col-form-label">Prioridade</label>
                <div class="col-sm-6" style="margin-top:10px">
                    <input type="radio" class="opcoes" name="prioridade" id="alta" value="1"> <label for="alta" style="font-size:17px;margin-left:10px">Alta</label><br>
                    <input type="radio" class="opcoes" name="prioridade" id="media" value="2"> <label for="media" style="font-size:17px;margin-left:10px">Média</label><br>
                    <input type="radio" class="opcoes" name="prioridade" id="baixa" value="3"> <label for="baixa" style="font-size:17px;margin-left:10px">Baixa</label><br>
                </div>
            </div>

            <?php
                if ( !empty($successMessage) ) {
                    echo "
                    <div class='row mb-3'>
                        <div class='offset-sm-3 col-sm-6'>
                            <div class='alert alert-success alert-dismissible fade show' role='alert'>
                                <strong>$successMessage</strong>
                                <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                            </div>
                        </div>
                    </div>
                    ";
                }
            ?>

            <div class="row mb-3">
                <div class="offset-sm-3 col-sm-3 d-grid">
                    <button type="submit" class="btn btn-primary">Adicionar</button>
                </div>
                <div class="col-sm-3 d-grid">
                    <a class="btn btn-outline-primary" href="../agenda.php" role="button">Voltar</a>
                </div>
            </div>
        </form>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>