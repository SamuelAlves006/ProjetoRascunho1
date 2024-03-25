<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <?php 
        session_start();//Inicia uma nova sessão ou resume uma sessão existente



        if((!isset ($_SESSION['login']) == true) and (!isset ($_SESSION['senha']) == true))
        {
            session_unset();//remove todas as variáveis de sessão
            echo "<script>
                alert('Esta página só pode ser acessada por usuário logado');
                window.location.href = 'index.html';
                </script>";

        }
        $logado = $_SESSION['login'];
        ?>
    </head>
    <body>
        <div>Somente usuário logado</div><br>
        <a href="logout.php"><button>Logout</button></a></h3>
    </body>
</html>
