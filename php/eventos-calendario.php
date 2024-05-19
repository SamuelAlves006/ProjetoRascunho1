<?php
    session_start(); // Inicia uma nova sessão ou resume uma sessão existente

    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "planifynow";

    // Conecta-se ao banco de dados
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Verifica a conexão
    if ($conn->connect_error) {
        die("Erro na conexão com o banco de dados: " . $conn->connect_error);
    }

    if (!isset($_SESSION['email'])) {
        die("Erro: Email do usuário não definido na sessão.");
    }

    $logado = $_SESSION['email'];

    $sql = "SELECT evento.*, prioridade.status AS prioridade_status
    FROM evento 
    INNER JOIN usuario ON evento.id_usuario = usuario.id_usuario
    INNER JOIN prioridade ON evento.id_prioridade = prioridade.id_prioridade
    WHERE (MONTH(evento.data) = MONTH(NOW()) OR 
            MONTH(evento.data) > MONTH(NOW()) OR 
            (YEAR(evento.data) = YEAR(NOW()) AND MONTH(evento.data) < MONTH(NOW()))) 
    AND usuario.email = ?";

    // Prepara a declaração SQL
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("Erro na preparação da consulta: " . $conn->error);
    }

    // Vincula os parâmetros
    $stmt->bind_param("s", $logado);

    // Executa a consulta
    $result = $stmt->execute();
    if (!$result) {
        die("Erro na execução da consulta: " . $stmt->error);
    }

    // Obtém o resultado da consulta
    $result = $stmt->get_result();

    $events = array();
    while ($row = $result->fetch_assoc()) {
        $events[] = $row;
    }

    // Retorne os eventos como JSON
    header('Content-Type: application/json');
    echo json_encode($events);

    // Fecha a conexão com o banco de dados
    $stmt->close();
    $conn->close();

?>
