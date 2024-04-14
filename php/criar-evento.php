<?php
    include "conexao.php";

    $nome = "";
    $descricao = "";
    $hr_inicio = "";
    $hr_termino = "";
    $data = "";
    $prioridade = "";

    $errorMessage = "";
    $successMessage = "";

    if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
        // Pega os dados do formulário
        $nome = $_POST["nome"];
        $descricao = $_POST["descricao"];
        $hr_inicio = $_POST["hr_inicio"];
        $hr_termino = $_POST["hr_termino"];
        $data = $_POST["data"];
        $prioridade = $_POST["prioridade"];

        do {
            if ( empty($nome) || empty($descricao) || empty($hr_inicio) || empty($hr_termino) || empty($data) || empty($prioridade) ) {
                $errorMessage = "Preencha todos os campos";
                break;
            }

            // Adicionar novo evento ao banco de dados
            $sql = "INSERT INTO evento (nome, descricao, hr_inicio, hr_termino, data, prioridade)
                    VALUES ('$nome', '$descricao', '$hr_inicio', '$hr_termino', '$data', '$prioridade')";

            $result = $conexao->query($sql);

            if (!$result) {
                $errorMessage = "Invalid query: " . $conexao->error;
                break;
            }

            $nome = "";
            $descricao = "";
            $hr_inicio = "";
            $hr_termino = "";
            $data = "";
            $prioridade = "";

            $successMessage = "Evento adicionado com sucesso";

            header("location: ../agenda.php");
            exit;

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
                    <input type="text" class="form-control" name="hr_inicio" value="<?php echo $hr_inicio; ?>">
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
                <div class="col-sm-6">
                    <input type="text" class="form-control" name="prioridade" value="<?php echo $prioridade; ?>">
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