<?php
session_start();

if ((!isset($_SESSION['email']) == true) && (!isset($_SESSION['senha']) == true)) {
    session_unset();
    echo "<script>
        alert('Esta página só pode ser acessada por usuário logado');
        window.location.href = 'php/index.php';
        </script>";
}
$logado = $_SESSION['email'];

// Configurações do banco de dados
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "planifynow";

// Cria a conexão
$conn = new mysqli($servername, $username, $password, $dbname);

// Verifica a conexão
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Obtém a hora atual do sistema
date_default_timezone_set('America/Sao_Paulo');
$current_time = date('H:i:s');

// Obtém o ID do usuário logado
$sql_user = "SELECT id_usuario FROM usuario WHERE email = ?";
$stmt_user = $conn->prepare($sql_user);
$stmt_user->bind_param("s", $logado);
$stmt_user->execute();
$result_user = $stmt_user->get_result();
$user_id = $result_user->fetch_assoc()['id_usuario'];
$stmt_user->close();

// Consulta SQL para buscar eventos com hora de início igual à hora atual e que não tenham terminado
$sql = "SELECT e.nome, TIME_FORMAT(e.hr_inicio, '%H:%i') as hr_inicio, TIME_FORMAT(e.hr_termino, '%H:%i') as hr_termino, p.status as prioridade
        FROM evento e
        INNER JOIN prioridade p ON e.id_prioridade = p.id_prioridade
        WHERE e.id_usuario = ?
        AND CURDATE() = DATE(e.data)
        AND TIME_FORMAT(e.hr_inicio, '%H:%i:%s') <= ?
        AND TIME_FORMAT(e.hr_termino, '%H:%i:%s') >= ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iss", $user_id, $current_time, $current_time);
$stmt->execute();
$result = $stmt->get_result();

$notificacoes = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $notificacoes[] = $row;
    }
}

$stmt->close();
$conn->close();
?>

<!doctype html>
<html lang="en" data-bs-theme="auto">
<head><script src="../assets/js/color-modes.js"></script>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="Mark Otto, Jacob Thornton, and Bootstrap contributors">
    <meta name="generator" content="Hugo 0.122.0">
    <title>Planify Now</title>
    <link rel="canonical" href="https://getbootstrap.com/docs/5.3/examples/navbar-static/">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="css/agenda.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@docsearch/css@3">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900&display=swap" rel="stylesheet">
    <link href="../assets/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        .bd-placeholder-img {
            font-size: 1.125rem;
            text-anchor: middle;
            -webkit-user-select: none;
            -moz-user-select: none;
            user-select: none;
        }
        @media (min-width: 768px) {
            .bd-placeholder-img-lg {
                font-size: 3.5rem;
            }
        }
        .b-example-divider {
            width: 100%;
            height: 3rem;
            background-color: rgba(0, 0, 0, .1);
            border: solid rgba(0, 0, 0, .15);
            border-width: 1px 0;
            box-shadow: inset 0 .5em 1.5em rgba(0, 0, 0, .1), inset 0 .125em .5em rgba(0, 0, 0, .15);
        }
        .b-example-vr {
            flex-shrink: 0;
            width: 1.5rem;
            height: 100vh;
        }
        .bi {
            vertical-align: -.125em;
            fill: currentColor;
        }
        .nav-scroller {
            position: relative;
            z-index: 2;
            height: 2.75rem;
            overflow-y: hidden;
        }
        .nav-scroller .nav {
            display: flex;
            flex-wrap: nowrap;
            padding-bottom: 1rem;
            margin-top: -1px;
            overflow-x: auto;
            text-align: center;
            white-space: nowrap;
            -webkit-overflow-scrolling: touch;
        }
        .btn-bd-primary {
            --bd-violet-bg: #712cf9;
            --bd-violet-rgb: 112.520718, 44.062154, 249.437846;
            --bs-btn-font-weight: 600;
            --bs-btn-color: var(--bs-white);
            --bs-btn-bg: var(--bd-violet-bg);
            --bs-btn-border-color: var(--bd-violet-bg);
            --bs-btn-hover-color: var(--bs-white);
            --bs-btn-hover-bg: #6528e0;
            --bs-btn-hover-border-color: #6528e0;
            --bs-btn-focus-shadow-rgb: var(--bd-violet-rgb);
            --bs-btn-active-color: var(--bs-btn-hover-color);
            --bs-btn-active-bg: #5a23c8;
            --bs-btn-active-border-color: #5a23c8;
        }
        .bd-mode-toggle {
            z-index: 1500;
        }
        .bd-mode-toggle .dropdown-menu .active .bi {
            display: block !important;
        }
        .notification-item {
            border-left-width: 4px;
            border-left-style: solid;
            padding-left: 10px;
        }
        .dropdown {
            border: none;
        }
    </style>
    <link href="navbar-static.css" rel="stylesheet">
</head>
<body>

<nav class="navbar navbar-expand-md mb-4">
    <div class="container-fluid">
        <a class="navbar-brand" href="php/landing-page.php">Planify Now</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarCollapse">
            <ul class="navbar-nav me-auto mb-2 mb-md-0">
                <li class="nav-item">
                    <a class="nav-link active" aria-current="page" href="php/landing-page.php">Início</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="agenda.php">Eventos</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="calendario.html">Calendário</a>
                </li>
            </ul>

            <div class="dropdown" id="navdropdown">
                <button type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-bell"></i>

                    <!-- Bolinha pra ficar colorido no sininho de notificação !-->

                    <?php if (!empty($notificacoes)) : ?>
                        <span class="notification-badge"></span>
                    <?php endif; ?>
                </button>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton">
                    <?php if (empty($notificacoes)) : ?>
                        <li class="notification-item" style="border-left-color: gray;">
                            <a class="dropdown-item" href="#">
                                <strong>Sem notificações no momento</strong>
                            </a>
                        </li>
                    <?php else : ?>
                        <?php foreach ($notificacoes as $notificacao) : 
                            $linha_cor = '';
                            switch ($notificacao['prioridade']) {
                                case 'Alta':
                                    $linha_cor = 'border-left-color: #ff0000;';
                                    break;
                                case 'Média':
                                    $linha_cor = 'border-left-color: #FFFF00;';
                                    break;
                                case 'Baixa':
                                    $linha_cor = 'border-left-color: #00ff00;';
                                    break;
                                default:
                                    $linha_cor = 'border-left-color: gray;';
                            }
                        ?>
                            <li class="notification-item" style="<?php echo $linha_cor; ?>">
                                <a class="dropdown-item" href="#">
                                    <strong>Evento: <?php echo $notificacao['nome']; ?></strong><br>
                                    <span>Ocorre agora</span><br>
                                    <small>Início: <?php echo date("H:i", strtotime($notificacao['hr_inicio'])); ?> - Término: <?php echo date("H:i", strtotime($notificacao['hr_termino'])); ?></small>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </ul>
            </div>

            <a href="php/logout.php" class="tirar-sublinhado">
                <i class="fa-solid fa-right-from-bracket"></i>
                <button type="submit" class="logout">Sair</button>
            </a>
        </div>
    </div>
</nav>

<main>
    <div class="container my-5">
        <h4 style="padding:10px;font-weight:400">Lista de Eventos</h4>
        <div class="pesquisar">
            <div class="d-flex justify-content-between">
                <div class="d-flex">
                    <input class="form-control me-2" type="search" placeholder="Pesquisar um evento" aria-label="Search" id="pesquisar">
                    <button onclick="searchData()" class="btn btn-outline-success" type="submit">Buscar</button>
                </div>
                <div>
                    <a class="btn btn-primary" href="php/criar-evento.php" role="button">Adicionar Evento</a>
                </div>
            </div>
        </div>
        <br>
        <table class="table">
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Descrição</th>
                    <th>Hora de Início</th>
                    <th>Hora de Término</th>
                    <th>Data</th>
                    <th>Prioridade</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php
                include 'php/conexao.php';

                if (isset($_SESSION['email'])) {
                    $email = $_SESSION['email'];

                    if (!empty($_GET['search'])) {
                        $data = "%" . $_GET['search'] . "%";
                        $sql = "SELECT e.*, p.status AS prioridade, DATE_FORMAT(e.data, '%d/%m/%Y') AS data_formatada
                        FROM evento e
                        INNER JOIN prioridade p ON e.id_prioridade = p.id_prioridade
                        INNER JOIN usuario u ON e.id_usuario = u.id_usuario
                        WHERE u.email = ?
                        AND (e.nome LIKE ? OR e.descricao LIKE ?)
                        ORDER BY e.data, e.hr_inicio";
                    } else {
                        $sql = "SELECT e.*, p.status AS prioridade, DATE_FORMAT(e.data, '%d/%m/%Y') AS data_formatada
                        FROM evento e
                        INNER JOIN prioridade p ON e.id_prioridade = p.id_prioridade
                        INNER JOIN usuario u ON e.id_usuario = u.id_usuario
                        WHERE u.email = ?
                        ORDER BY e.data, e.hr_inicio";
                    }

                    $stmt = $conexao->prepare($sql);

                    if ($stmt) {
                        if (!empty($_GET['search'])) {
                            $stmt->bind_param("sss", $email, $data, $data);
                        } else {
                            $stmt->bind_param("s", $email);
                        }

                        $stmt->execute();

                        $result = $stmt->get_result();

                        if ($result) {
                            while ($row = $result->fetch_assoc()) {
                                $prioridade_style = '';
                                switch ($row['prioridade']) {
                                    case 'Alta':
                                        $prioridade_style = 'background-color: #ffa5a5; color: #640000; border-radius: 10px;';
                                        break;
                                    case 'Média':
                                        $prioridade_style = 'background-color: #ffff9a; color: #464500; border-radius: 10px;';
                                        break;
                                    case 'Baixa':
                                        $prioridade_style = 'background-color: #7dffba; color: #004720; border-radius: 10px;';
                                        break;
                                    default:
                                        $prioridade_style = '';
                                }
                                echo "<tr>
                                        <td style='max-width:200px'>{$row['nome']}</td>
                                        <td style='max-width:200px'>{$row['descricao']}</td>
                                        <td>" . date("H:i", strtotime($row['hr_inicio'])) . "</td>
                                        <td>" . date("H:i", strtotime($row['hr_termino'])) . "</td>
                                        <td>{$row['data_formatada']}</td>
                                        <td>
                                            <span style='{$prioridade_style} padding: 5px;'>{$row['prioridade']}</span>
                                        </td>
                                        <td>
                                            <a class='btn btn-primary btn-sm' href='php/editar-evento.php?id_evento={$row['id_evento']}'>
                                                <i class='fa-solid fa-pen-to-square'></i>
                                            </a>
                                            <a class='btn btn-danger btn-sm' href='php/excluir-evento.php?id_evento={$row['id_evento']}'>
                                                <i class='fa-solid fa-trash-can'></i>
                                            </a>
                                        </td>
                                    </tr>";
                            }
                        } else {
                            echo "Consulta sem resultados!";
                        }
                        $stmt->close();
                    } else {
                        echo "Erro na preparação da instrução SQL";
                    }
                } else {
                    session_unset();
                    echo "<script>
                        alert('Esta página só pode ser acessada por usuário logado');
                        window.location.href = 'php/index.php';
                        </script>";
                }
                ?>
            </tbody>
        </table>
    </div>
</main>

<script src="../assets/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

<script>
    var search = document.getElementById('pesquisar');

    search.addEventListener("keydown", function(event) {
        if (event.key === "Enter") {
            searchData();
        }
    });

    function searchData() {
        window.location = 'agenda.php?search=' + search.value;
    }
</script>
</body>
</html>
