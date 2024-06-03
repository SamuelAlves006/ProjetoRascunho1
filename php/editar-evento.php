<?php
$servidor = 'localhost';
$usuario = 'root';
$senha = '';
$dbname = 'planifynow';

// Criar conexão
$conexao = new mysqli($servidor, $usuario, $senha, $dbname);

$id_evento = "";
$nome = "";
$descricao = "";
$hr_inicio = "";
$hr_termino = "";
$data = "";
$prioridade = "";
$prioridade_nome = "";

$errorMessage = "";
$successMessage = "";

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    // GET method: Mostrar os dados do evento

    if (!isset($_GET["id_evento"])) {
        header("location: ../agenda.php");
        exit;
    }

    $id_evento = $_GET["id_evento"];

    $sql = "SELECT evento.*, DATE_FORMAT(data, '%d/%m/%Y') AS data_formatada, prioridade.status AS prioridade_nome FROM evento 
            INNER JOIN prioridade ON evento.id_prioridade = prioridade.id_prioridade 
            WHERE id_evento = $id_evento";
    $result = $conexao->query($sql);
    $row = $result->fetch_assoc();

    if (!$row) {
        header("location: ../agenda.php");
        exit;
    }

    $nome = $row["nome"];
    $descricao = $row["descricao"];
    $hr_inicio = date("H:i", strtotime($row["hr_inicio"]));
    $hr_termino = date("H:i", strtotime($row["hr_termino"]));
    $data = $row["data_formatada"];
    $prioridade_nome = $row["prioridade_nome"];
    
} else {
    // POST method: Atualizar os dados do evento

    $id_evento = $_POST["id_evento"];
    $nome = $_POST["nome"];
    $descricao = $_POST["descricao"];
    $hr_inicio = $_POST["hr_inicio"];
    $hr_termino = $_POST["hr_termino"];
    $data = $_POST["data"];
    $prioridade = isset($_POST["prioridade"]) ? $_POST["prioridade"] : "";

    // Verificar se todos os campos foram preenchidos
    if (empty($id_evento) || empty($nome) || empty($descricao) || empty($hr_inicio) || empty($hr_termino) || empty($data) || empty($prioridade)) {
        $errorMessage = "Preencha todos os campos";
    } elseif ($hr_inicio >= $hr_termino) {
        $errorMessage = "A hora de início deve ser menor do que a hora de término";
    } elseif (!validarFormatoData($data)) {
        $errorMessage = "Formato de data inválido. Use o formato dd/mm/yyyy.";
    } else {

        $sql_prioridade = "SELECT COUNT(*) AS total FROM prioridade WHERE id_prioridade = ?";
        $stmt = $conexao->prepare($sql_prioridade);
        $stmt->bind_param("i", $prioridade);
        $stmt->execute();
        $result_prioridade = $stmt->get_result();
        $row_prioridade = $result_prioridade->fetch_assoc();

        if ($row_prioridade["total"] == 1) {

            $data_mysql = date("Y-m-d", strtotime(str_replace('/', '-', $data)));

            // Verifica se já existe um evento com o mesmo horário no mesmo dia, excluindo o evento atual
            $sql_verifica = "SELECT * FROM evento WHERE data = ? AND id_usuario = (SELECT id_usuario FROM evento WHERE id_evento = ?) AND id_evento != ? AND (hr_inicio = ? OR hr_termino = ?)";
            $stmt_verifica = $conexao->prepare($sql_verifica);
            $stmt_verifica->bind_param("siiss", $data_mysql, $id_evento, $id_evento, $hr_inicio, $hr_termino);
            $stmt_verifica->execute();
            $result_verifica = $stmt_verifica->get_result();

            if ($result_verifica->num_rows > 0) {
                $errorMessage = "Já existe um evento para esse horário.";
            } else {
                $sql_update = "UPDATE evento 
                        SET nome = ?, descricao = ?, hr_inicio = ?, hr_termino = ?, data = ?, id_prioridade = ? 
                        WHERE id_evento = ?";
                $stmt = $conexao->prepare($sql_update);
                $stmt->bind_param("sssssii", $nome, $descricao, $hr_inicio, $hr_termino, $data_mysql, $prioridade, $id_evento);

                if ($stmt->execute()) {
                    $successMessage = "Evento atualizado com sucesso";
                    header("location: ../agenda.php");
                    exit;
                } else {
                    $errorMessage = "Erro ao atualizar o evento: " . $conexao->error;
                }
            }
        } else {
            $errorMessage = "Prioridade selecionada inválida. Por favor, selecione uma prioridade válida.";
        }
    }
}

function validarFormatoData($date)
{
    $dateObj = DateTime::createFromFormat('d/m/Y', $date);
    return $dateObj && $dateObj->format('d/m/Y') === $date;
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
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/criar-evento.css">
</head>
<body>
    <div class="container my-5">
        <h2>Editar Evento</h2>

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

        <form method="post">
            <input type="hidden" name="id_evento" value="<?php echo $id_evento; ?>">
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
                    <input type="text" class="form-control" name="hr_inicio" value="<?php echo $hr_inicio; ?>">
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
                    <input type="radio" class="opcoes" name="prioridade" id="alta" value="1" <?php echo ($prioridade_nome == 'Alta') ? 'checked' : ''; ?>> <label for="alta" style="font-size:17px;margin-left:10px">Alta</label><br>
                    <input type="radio" class="opcoes" name="prioridade" id="media" value="2" <?php echo ($prioridade_nome == 'Média') ? 'checked' : ''; ?>> <label for="media" style="font-size:17px;margin-left:10px">Média</label><br>
                    <input type="radio" class="opcoes" name="prioridade" id="baixa" value="3" <?php echo ($prioridade_nome == 'Baixa') ? 'checked' : ''; ?>> <label for="baixa" style="font-size:17px;margin-left:10px">Baixa</label><br>
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
                    <button type="submit" class="btn btn-primary">Atualizar</button>
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
