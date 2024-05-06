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
<link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">

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
    </style>

    
    <!-- Custom styles for this template -->
    <link href="navbar-static.css" rel="stylesheet">

  </head>
  <body>

<nav class="navbar navbar-expand-md mb-4">
  <div class="container-fluid">
    <a class="navbar-brand" href="php/landing-page.php">
      Planify Now
    </a>
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

          // Verificar se a chave 'id_usuario' está definida na variável $_SESSION
          if(isset($_SESSION['email'])) {
            // Definir a variável $id_usuario com o valor de $_SESSION['id_usuario']
            $email = $_SESSION['email'];

            // Ajustando a consulta SQL com a preparação da instrução
            if (!empty($_GET['search'])) {
                $data = "%" . $_GET['search'] . "%"; // Adicionando caracteres curinga para pesquisa com LIKE
                $sql = "SELECT e.*, p.status AS prioridade, DATE_FORMAT(e.data, '%d/%m/%Y') AS data_formatada
                FROM evento e
                INNER JOIN prioridade p ON e.id_prioridade = p.id_prioridade
                INNER JOIN usuario u ON e.id_usuario = u.id_usuario
                WHERE u.email = ?
                AND (e.nome LIKE ? OR e.descricao LIKE ?)
                ORDER BY e.data";
                
            } else {
                $sql = "SELECT e.*, p.status AS prioridade, DATE_FORMAT(e.data, '%d/%m/%Y') AS data_formatada
                FROM evento e
                INNER JOIN prioridade p ON e.id_prioridade = p.id_prioridade
                INNER JOIN usuario u ON e.id_usuario = u.id_usuario
                WHERE u.email = ?
                ORDER BY e.data";
            }

            // Preparar a instrução SQL
            $stmt = $conexao->prepare($sql);

            // Verificar se a preparação da instrução foi bem sucedida
            if ($stmt) {
                if (!empty($_GET['search'])) {
                    // Vincular o valor do ID do usuário e os valores de pesquisa à instrução preparada
                    $stmt->bind_param("sss", $email, $data, $data);
                } else {
                    // Vincular o valor do ID do usuário à instrução preparada
                    $stmt->bind_param("s", $email);
                }

                // Executar a instrução preparada
                $stmt->execute();

                // Obter o resultado da consulta
                $result = $stmt->get_result();

                // Verificar se a consulta retornou resultados
                if ($result) {
                    // Processar os resultados da consulta
                    while ($row = $result->fetch_assoc()) {
                        // Definindo estilo CSS com base na prioridade
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
                                $prioridade_style = ''; // Tratamento para outros casos
                        }
                      
                        // Exibindo os dados na tabela HTML
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
                    // Lidar com o caso de consulta sem resultados
                }

                // Fechar a instrução preparada
                $stmt->close();
            } else {
                // Lidar com o caso de falha na preparação da instrução SQL
            }
          } else {
            // Lidar com o caso em que $_SESSION['id_usuario'] não está definida
          }
        ?>

      </tbody>
    </table>
  </div>

</main>

<script src="../assets/dist/js/bootstrap.bundle.min.js"></script>

<script>
  var search = document.getElementById('pesquisar');

  search.addEventListener("keydown", function(event) {
    if (event.key === "Enter")
    {
      searchData();
    }
  });

  function searchData()
  {
    window.location = 'agenda.php?search='+search.value;
  }
</script>

    </body>
</html>
