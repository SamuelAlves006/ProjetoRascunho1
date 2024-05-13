<?php
    include 'conexao.php';

    // Obtenha o primeiro e último dia do mês atual
    $firstDayOfMonth = date('Y-m-01');
    $lastDayOfMonth = date('Y-m-t');

    // Consulta para obter os eventos agendados para o mês atual
    $sql = "SELECT * FROM evento WHERE data BETWEEN '$firstDayOfMonth' AND '$lastDayOfMonth'";

    $result = $conexao->query($sql);

    $events = array();

    // Construa um array de eventos
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $events[] = array(
                'data' => $row['data'],
                'descricao' => $row['descricao']
            );
        }
    }



    // Retorne os eventos como JSON
    echo json_encode($events);

    $conexao->close();
?>
