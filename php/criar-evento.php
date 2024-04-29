<?php
    session_start();//Inicia uma nova sessão ou resume uma sessão existente

    if((!isset ($_SESSION['email']) == true) and (!isset ($_SESSION['senha']) == true))
    {
        session_unset();//remove todas as variáveis de sessão
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
        // Verifica se o email do usuário está presente na sessão
        if(isset($_SESSION['email']) && !empty($_SESSION['email'])) {
            $email = $_SESSION['email'];

            // Consulta para obter o ID do usuário com base no email
            $sql_usuario = "SELECT id_usuario FROM usuario WHERE email = ?";
            $stmt_usuario = $conexao->prepare($sql_usuario);
            $stmt_usuario->bind_param("s", $email);
            $stmt_usuario->execute();
            $result_usuario = $stmt_usuario->get_result();

            // Verifica se o usuário foi encontrado
            if ($result_usuario->num_rows > 0) {
                $row_usuario = $result_usuario->fetch_assoc();
                $id_usuario = $row_usuario['id_usuario']; // Obtém o ID do usuário
            } else {
                // Trate o caso em que o usuário não foi encontrado
                $errorMessage = "Usuário não encontrado";
            }
        } else {
            // Trate o caso em que o email não está presente na sessão
            $errorMessage = "Email do usuário não encontrado na sessão";
        }

        // Pega os dados do formulário
        $nome = $_POST["nome"];
        $descricao = $_POST["descricao"];
        $hr_inicio = $_POST["hr_inicio"];
        $hr_termino = $_POST["hr_termino"];
        $data = $_POST["data"];

        // Verifica se $_POST["prioridade"] está definido, se não, define como vazio
        $prioridade_value = isset($_POST["prioridade"]) && !empty($_POST["prioridade"]) ? $_POST["prioridade"] : "";

        do {

            if (empty($nome) || empty($descricao) || empty($hr_inicio) || empty($hr_termino) || empty($data) || empty($prioridade_value)) {
                $errorMessage = "Preencha todos os campos";
                break;
            }

            // Verifica se a prioridade selecionada corresponde a um ID de prioridade na tabela
            $sql_prioridade = "SELECT status FROM prioridade WHERE id_prioridade = ?";
            $stmt_prioridade = $conexao->prepare($sql_prioridade);
            $stmt_prioridade->bind_param("i", $prioridade_value);
            $stmt_prioridade->execute();
            $result_prioridade = $stmt_prioridade->get_result();

            if ($result_prioridade->num_rows > 0) {
                $row_prioridade = $result_prioridade->fetch_assoc();
                $prioridade = $row_prioridade['status']; // Obtém o nome da prioridade correspondente ao ID
            } else {
                $errorMessage = "Prioridade selecionada não encontrada";
            }

            // Se a prioridade foi encontrada, prosseguir com a inserção do evento
            if (!empty($prioridade) && isset($id_usuario)) {
                $sql_evento = "INSERT INTO evento (nome, descricao, hr_inicio, hr_termino, data, id_prioridade, id_usuario)
                            VALUES (?, ?, ?, ?, ?, ?, ?)";
                $stmt_evento = $conexao->prepare($sql_evento);
                $stmt_evento->bind_param("sssssii", $nome, $descricao, $hr_inicio, $hr_termino, $data, $prioridade_value, $id_usuario);
                $result_evento = $stmt_evento->execute();

                if ($result_evento) {
                    $successMessage = "Evento adicionado com sucesso";
                    header("location: ../agenda.php");
                    exit;
                } else {
                    $errorMessage = "Erro ao adicionar evento";
                }
            }
        } while (false);
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
                <label class="col-sm-3 col-form-label">Hora de Início</label>
                <div class="col-sm-6">
                    <input type="text" class="form-control" name="hr_inicio" value="<?php echo $hr_inicio; ?> ">
                </div>
            </div>

            <div class="row mb-3">
                <label class="col-sm-3 col-form-label">Hora de Término</label>
                <div class="col-sm-6">
                    <input type="text" class="form-control" name="hr_termino" value="<?php echo $hr_termino; ?>">
                </div>
            </div>

            <div class="row mb-3">
                <label class="col-sm-3 col-form-label">Data</label>
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