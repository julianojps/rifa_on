<link rel="icon" href="img/rifa.png">

<?php
$servername = "localhost";
$database = "rifa_db";
$username = "root";
$password = "";
$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

// ----------------------
// LÓGICA DO SORTEIO
// ----------------------
$duracao_sorteio = 30000; // 3 horas
$agora = time();
$numero_sorteado = "Nenhum ainda";
$proximo_sorteio = $agora + $duracao_sorteio;

// Verifica o último sorteio
$sql = "SELECT numero_sorteado, data_sorteio FROM sorteios ORDER BY id DESC LIMIT 1";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $ultimo_sorteio = strtotime($row['data_sorteio']);
    $numero_sorteado = $row['numero_sorteado'];
    $proximo_sorteio = $ultimo_sorteio + $duracao_sorteio;

    // Se passou do tempo, fazer novo sorteio
    if ($agora >= $proximo_sorteio) {
        $numero_sorteado = rand(1, 50);
        $data_sorteio = date("Y-m-d H:i:s", $agora);
        $conn->query("INSERT INTO sorteios (numero_sorteado, data_sorteio) VALUES ($numero_sorteado, '$data_sorteio')");
        $proximo_sorteio = $agora + $duracao_sorteio;
    }
} else {
    // Primeiro sorteio
    $numero_sorteado = rand(1, 50);
    $data_sorteio = date("Y-m-d H:i:s", $agora);
    $conn->query("INSERT INTO sorteios (numero_sorteado, data_sorteio) VALUES ($numero_sorteado, '$data_sorteio')");
    $proximo_sorteio = $agora + $duracao_sorteio;
}

// ----------------------
// BUSCAR GANHADORES
// ----------------------
$ganhadores = [];
$sql_ganhadores = "SELECT dados_usuario FROM tbl_pagamento WHERE numero = '$numero_sorteado'";
$result_ganhadores = $conn->query($sql_ganhadores);
if ($result_ganhadores->num_rows > 0) {
    while ($row = $result_ganhadores->fetch_assoc()) {
        if (!empty($row["dados_usuario"]) && $row["dados_usuario"] != "0") {
            $ganhadores[] = $row["dados_usuario"];
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Rifa R$1,00</title>
    </head>

</html>

<?php
// Criar conexão
$conn = new mysqli($servername, $username, $password, $database);

// Verificar conexão
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

// Buscar números cadastrados no banco de dados

$conn = new mysqli($servername, $username, $password, $database);
$QUERY = "SELECT numero FROM tbl_pagamento";
$executa_query = mysqli_query($conn, $QUERY);
$conta_linhas = mysqli_num_rows($executa_query);
?><?php
$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

// Definição do tempo de sorteio (3 horas = 10800 segundos)
$duracao_sorteio = 30000; // 3 horas
// Buscar o último sorteio
$sql = "SELECT numero_sorteado, data_sorteio FROM sorteios ORDER BY id DESC LIMIT 1";
$result = $conn->query($sql);
$agora = time();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $ultimo_sorteio = strtotime($row['data_sorteio']);
    $numero_sorteado = $row['numero_sorteado'];
    $proximo_sorteio = $ultimo_sorteio + $duracao_sorteio;
} else {
    // Caso não haja sorteio anterior, definir um horário de referência
    $proximo_sorteio = $agora + $duracao_sorteio;
    $numero_sorteado = "Nenhum ainda";
}

// Se já passou do tempo, fazer um novo sorteio
if ($agora >= $proximo_sorteio) {
    $numero_sorteado = rand(1, 50);
    $data_sorteio = date("Y-m-d H:i:s", $agora);
    $sql = "INSERT INTO sorteios (numero_sorteado, data_sorteio) VALUES ($numero_sorteado, '$data_sorteio')";
    $conn->query($sql);

    // Atualizar o próximo sorteio
    $proximo_sorteio = $agora + $duracao_sorteio;
}
// Buscar os dados dos ganhadores na tbl_pagamento
$sql_ganhadores = "SELECT dados_usuario, numero, telefone, email, dados_pix, opcao_pix, hora_aposta FROM tbl_pagamento WHERE numero = '$numero_sorteado'";
$result_ganhadores = $conn->query($sql_ganhadores);

// Verifica se houve erro na query
if (!$result_ganhadores) {
    die("Erro na consulta SQL: " . $conn->error);
}

if ($result_ganhadores->num_rows > 0) {
    while ($row = $result_ganhadores->fetch_assoc()) {
        $dados_usuario = $row['dados_usuario'];
        $numero = $row['numero'];
        $telefone = $row['telefone'];
        $email = $row['email'];
        $data_sorteio = $row['hora_aposta'];
        $chave_pix = $row['dados_pix'];
        $opcoes_chave_pix = $row['opcao_pix']; // Ajuste aqui (o nome do campo na query é 'opcoes')
      //  $data_sorteio = date("Y-m-d H:i:s"); // Data do sorteio atual

        // Verifica se o ganhador já está na tbl_ganhadores para evitar duplicação
        $sql_verifica = "SELECT id FROM tbl_ganhadores WHERE dados_usuario = '$dados_usuario' AND numero = '$numero'";
        $resultado_verifica = $conn->query($sql_verifica);

        // Verifica se a consulta de verificação falhou
        if (!$resultado_verifica) {
            die("Erro na consulta de verificação: " . $conn->error);
        }

       // if ($resultado_verifica->num_rows == 0) {
            // Se não existir, insere o ganhador na tbl_ganhadores
            $sql_inserir = "INSERT INTO tbl_ganhadores (dados_usuario, numero, telefone, email, chave_pix, opcoes_chave_pix, data_sorteio) 
                            VALUES ('$dados_usuario', '$numero', '$telefone', '$email', '$chave_pix', '$opcoes_chave_pix', '$data_sorteio')";

            if (!$conn->query($sql_inserir)) {
                die("Erro ao inserir ganhador: " . $conn->error);
          //  }
        }
    }
   
} 

$conn->close();
?><!DOCTYPE html>
<html lang="pt-BR">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Rifa R$1,00</title>
        <link rel="icon" href="img/rifa.p">
        <link rel="stylesheet" href="css/pag.css">
        <link rel="stylesheet" href="css/imagem.css">
    </head>
    <body>

        <div class="container" >  <img src="img/trevo.png" id="bloco1" class="footer">


            <div class="numeros" id="numeros"></div>
            <body>

                <div style="max-width: 600px; margin: -15px auto; padding: -20px; background: #f4f4f4; border-radius: -20px; text-align: center;">

                    <h3 style="color: #555;">Último Número Sorteado:<?php echo $numero_sorteado; ?></h3>

                    <h3 style="color: #555;">Ganhadores:</h3>
                    <ul style="list-style: none; padding: 0;">
                        <?php
                        if (!empty($ganhadores)) {
                            foreach (array_unique($ganhadores) as $ganhador) {
                                echo "<li style='font-size: 18px; padding: 5px; color: #5cb85c; font-weight: bold;'>$ganhador</li>";
                            }
                        } else {
                            echo "<li style='color: #d9534f;'>Nenhum ganhador encontrado.</li>";
                        }
                        ?>
                    </ul>
                </div>
            </body>

            <h3 >Próximo Sorteio: <span id="contador"></span></h3>
            <form>
                <div class="info-pagamento">
                    <strong>Rifa Selecionada:</strong> <span id="numeroSelecionado">Nenhum</span>
                    <p><p><p><p><strong align="center">Valor acumulado:</strong> <span id="numeroSelecionado">
                            R$ <?php
                            if ($conta_linhas == 0) {
                                echo "0";
                            } else {
                                echo $conta_linhas . ",00";
                            }
                            ?>      
                            </head>
                            <body>   <br>
                                <?php
                                $conexao = new mysqli($servername, $username, $password, $database);
                                $sql = "SELECT numero,dados_usuario FROM tbl_pagamento ORDER BY numero asc"; // Substitua pelo nome real da sua tabela
                                $result = $conexao->query($sql);
                                          $sql = "SELECT dados_usuario, telefone FROM tbl_ganhadores  ORDER BY id DESC LIMIT 10 "; // Substitua pelo nome real da sua tabela
                                $result2 = $conexao->query($sql);
                                ?>
                               <div class="select-container">
    <div>
      <style>  #numerosEscolhidos {
        width: 50px; /* Ajuste a largura conforme necessário */
        height:40px; /* Ajuste a altura conforme necessário */
        font-size: 14px; /* Tamanho da fonte */
        padding: 5px; /* Espaçamento interno */
    }</style>
       <select id="numerosEscolhidos" >
    <option value="">Números já escolhidos</option>
    <?php
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "<option>" . $row["dados_usuario"] . " escolheu o número " . $row["numero"] . "</option>";
        }
    } else {
        echo "<option value=''>Nenhum número escolhido</option>";
    }
    ?>
</select>
    </div>
   <img src="img/regras.png" id="openPdf" width="60" class="footer" alt="Regras">
    <div>
   
        <select id="numerosEscolhidos">
            <option value="">Últimos 10 Ganhadores</option>
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result2->fetch_assoc()) {
                    echo "<option>" . $row["dados_usuario"] . " que tem como telefone de contato  " . $row["telefone"] . "</option>";
                }
            } else {
                echo "<option value=''>Nenhum ganhador encontrado</option>";
            }
            ?>
        </select>
    </div>
</div>
                                <form id="formPagamento" method="POST" action="pagamento_cadstro_rifa.php">

                                    <div class="form-container">
                                        <!-- Linha com Campo 3, 2 e 1 -->
                                        <div class="form-row">
                                            <div class="form-group">
                                                <label for="campo3">Nome</label>
                                                <input type="text" id="campo3" name="nome" required="Campo Obrigatório">
                                            </div>
                                            <div class="form-group">
                                                <label for="campo2">Telefone</label>
                                                <input type="text" id="campo2" name="telefone">
                                            </div>
                                            <div class="form-group">
                                                <label for="campo1">Email</label>
                                                <input type="text" id="campo1" name="email">
                                            </div>
                                        </div>

                                        <!-- Linha com Campo 4 e Select -->
                                        <div class="form-row">
                                            <div class="form-group">
                                                <label for="campo4">Favor informar Chave Pix</label>
                                                <input type="text" id="campo4" name="chave_pix">
                                            </div>

                                            <div class="form-group">
                                                <label for="opcoes">Opções de Chave Pix</label>
                                                <select id="opcoes" name="opcoes">
                                                    <option value="CPF">CPF</option>
                                                    <option value="Telefone">Telefone</option>
                                                    <option value="Aleatória">Chave Aleatória</option>
                                                    <option value="Email">Email</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <input hidden="numeroSelecionado">
                                    <button type="button" class="btn-confirmar" name="numeroSelecionado" id="confirmarPagamento" onclick="enviarConteudo()">Confirmar Pagamento</button>
                                </form>
                              

                                <p class="alerta" id="alerta"></p>
                                <p class="alerta" id="numeroSelecionado"></p>
                                </div>
                                </div>
                                <script src="js/pag2.js"></script>
                            </body>
                            </html>

                            <script>
                                       function enviarConteudo() {
    // Coletar os dados do formulário
    let nome = document.getElementById('campo3').value;
    let telefone = document.getElementById('campo2').value;
    let email = document.getElementById('campo1').value;
    let chavePix = document.getElementById('campo4').value;
    let opcoes = document.getElementById('opcoes').value;
    let conteudo = document.getElementById('numeroSelecionado').innerText;

    // Verificar se todos os campos estão preenchidos
    if (nome === "" || telefone === "" || email === "" || chavePix === "" || opcoes === "") {
        alert("Por favor, preencha todos os campos obrigatórios.");
        return; // Impede o envio se algum campo estiver vazio
    }

    // Verificar se o número foi selecionado
    if (conteudo === "" || conteudo === "Nenhum") {
        alert("Por favor, selecione um número da rifa antes de confirmar o pagamento.");
        return; // Impede o envio se o número não for escolhido
    }

    // Enviar os dados para o PHP via fetch
    fetch('pagamento_cadstro_rifa.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'nome=' + encodeURIComponent(nome) +
                '&telefone=' + encodeURIComponent(telefone) +
                '&email=' + encodeURIComponent(email) +
                '&chave_pix=' + encodeURIComponent(chavePix) +
                '&opcoes=' + encodeURIComponent(opcoes) +
                '&conteudo=' + encodeURIComponent(conteudo)
    })
    .then(response => response.text())
    .then(data => {
        alert('' + data);
        // Após o envio dos dados, podemos submeter o formulário
        document.getElementById('formPagamento').submit(); // Se precisar submeter o formulário após o envio via fetch
    })
    .catch(error => {
        console.error('Erro ao enviar dados:', error);
    });
}

                            </script>

                            <!DOCTYPE html>
                            <html lang="pt-BR">
                                <head>
                                    <meta charset="UTF-8">
                                    <meta name="viewport" content="width=device-width, initial-scale=1.0">

                                    <script>
                                        let proximoSorteio = <?php echo $proximo_sorteio; ?> * 1000; // Converter para milissegundos
                                        let agora = new Date().getTime();

                                        function atualizarContador() {
                                            let tempoRestante = proximoSorteio - new Date().getTime();

                                            if (tempoRestante <= 0) {
                                                location.reload();
                                                return;
                                            }

                                            let horas = Math.floor(tempoRestante / (1000 * 60 * 60));
                                            let minutos = Math.floor((tempoRestante % (1000 * 60 * 60)) / (1000 * 60));
                                            let segundos = Math.floor((tempoRestante % (1000 * 60)) / 1000);

                                            document.getElementById('contador').innerText =
                                                    (horas < 10 ? "0" : "") + horas + ":" +
                                                    (minutos < 10 ? "0" : "") + minutos + ":" +
                                                    (segundos < 10 ? "0" : "") + segundos;

                                            setTimeout(atualizarContador, 1000);
                                        }

                                        atualizarContador();
                                    </script>
                                    </body>
                            </html>
                            <!DOCTYPE html>
                            <html lang="pt-br">
                                <head>
                                    <meta charset="UTF-8">
                                    <title>Formulário com campos lado a lado</title>
                                    <style>
                                        body {
                                            font-family: Arial, sans-serif;
                                            background-color: #f0f0f0;
                                            padding: 30px;
                                        }

                                        .form-container {
                                            background: #fff;
                                            padding: 8px;
                                            border-radius: 10px;
                                            max-width: 1000px;
                                            margin: auto;
                                            box-shadow: 0 0 10px rgba(0,0,0,0.1);
                                        }

                                        .form-row {
                                            display: flex;
                                            gap: 20px;
                                            flex-wrap: wrap;
                                            margin-top: 20px;
                                        }

                                        .form-group {
                                            flex: 1;
                                            min-width: 150px;
                                            display: flex;
                                            flex-direction: column;
                                        }

                                        label {
                                            font-weight: bold;
                                            margin-bottom: 5px;
                                        }

                                        input[type="text"], select {
                                            padding: 7px;
                                            font-size: 16px;
                                            border: 1px solid #ccc;
                                            border-radius: 6px;
                                        }
                                        .container-escolhidos {
                                            display: flex;
                                            align-items: center; /* Alinha os itens verticalmente */
                                            gap: 10px; /* Espaço entre o select e a imagem */
                                        }

                                        .container-escolhidos select {
                                            flex: 1; /* Faz o select ocupar mais espaço */
                                        }

                                        .container-escolhidos img {
                                            width: 41px; /* Ajuste conforme necessário */
                                            height: auto;
                                            cursor: pointer; /* Para indicar que pode ser clicado */
                                        }
                                    .select-container {
    display: flex;
    justify-content: space-between; /* Distribui os selects com espaço entre eles */
    gap: 15px; /* Dá um espaçamento entre os selects */
    align-items: center; /* Alinha os selects verticalmente */
    flex-wrap: wrap; /* Se a tela for pequena, eles quebram para a próxima linha */
}

.select-container select {
    flex: 1; /* Faz os selects ocuparem o mesmo espaço */
    min-width: 280px; /* Define um tamanho mínimo */
    padding: 10px;
    font-size: 16px;
    border: 1px solid #ccc;
    border-radius: 6px;
}
</style>
                                </head>
                                <body>
                                </body>
                            </html>

                            <!DOCTYPE html>
                            <html lang="pt-BR">
                                <head>
                                    <meta charset="UTF-8">
                                    <meta name="viewport" content="width=device-width, initial-scale=1.0">
                                    <title>Rifa R$1,00</title>
                                    <link rel="stylesheet" href="css/pag.css">
                                    <link rel="stylesheet" href="css/imagem.css">
                                    <style>
                                        .modal {
                                            display: none;
                                            position: fixed;
                                            z-index: 1;
                                            left: 0;
                                            top: 0;
                                            width: 100%;
                                            height: 100%;
                                            background-color: rgba(0, 0, 0, 0.5);
                                        }

                                        .modal-content {
                                            background-color: #fff;
                                            margin: 15% auto;
                                            padding: 20px;
                                            border-radius: 10px;
                                            width: 50%;
                                            text-align: center;
                                        }

                                        .close {
                                            color: #aaa;
                                            float: right;
                                            font-size: 28px;
                                            font-weight: bold;
                                            cursor: pointer;
                                        }
                                        .container2 {
                                            display: flex;
                                            justify-content: flex-end; /* Move a imagem para a direita */
                                        }

                                        img {
                                            max-width: 100%; /* Mantém a imagem responsiva */
                                            height: auto;
                                        }
                                    </style>
                                </head>
                                <body> 
                                    <script>
                                        var modal = document.getElementById("myModal");
                                        var btn = document.getElementById("openModal");
                                        var span = document.getElementsByClassName("close")[0];

                                        btn.onclick = function () {
                                            modal.style.display = "block";
                                        }

                                        span.onclick = function () {
                                            modal.style.display = "none";
                                        }

                                        window.onclick = function (event) {
                                            if (event.target == modal) {
                                                modal.style.display = "none";
                                            }
                                        }
                                    </script>
                                </body>
                            </html>
                            <script>
                                document.getElementById("openPdf").addEventListener("click", function () {
                                    window.open("img/regras.pdf", "_blank");
                                });
                            </script>
                            
                