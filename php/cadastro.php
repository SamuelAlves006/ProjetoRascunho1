<?php
    include("conexao.php");

    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $senha = $_POST['senha'];

    // Verificar se o usuário já existe no banco de dados
    $sql_verificar = "SELECT * FROM usuario WHERE email='$email'";
    $resultado = mysqli_query($conexao, $sql_verificar);
    if(mysqli_num_rows($resultado) > 0) {
        echo "<h2 style='color:#FF0000;text-align:center;font-family:Montserrat, sans-serif;font-weight:500;margin-top:60px'>Erro: Este email já está cadastrado!</h2>";
        echo "<h3 style='color:#FF0000;text-align:center;font-family:Montserrat, sans-serif;font-weight:500'>Por favor, utilize outro email.</h3>";
    } else {
        // Se não existir, realizar o cadastro
        $sql = "INSERT INTO usuario(nome, email, senha) 
                VALUES ('$nome', '$email', '$senha')";

        if (mysqli_query($conexao, $sql)) {
            echo "<h2 style='color:#00dc00;text-align:center;font-family:Montserrat, sans-serif;font-weight:500;margin-top:60px'>Usuário cadastrado com sucesso</h2>";
            echo "<h3 style='color:#00dc00;text-align:center;font-family:Montserrat, sans-serif;font-weight:500'>Volte para a tela de login!</h3>";
        } else {
            echo "<h2 style='color:#FF0000;text-align:center;font-family:Montserrat, sans-serif;font-weight:500;margin-top:60px'>Erro ao cadastrar usuário</h2>";
        }
    }

    mysqli_close($conexao);
?>