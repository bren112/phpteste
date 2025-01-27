<?php
$config = require_once '../config.php';

$accesstoken = $config['accesstoken'];

// Verificar se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Coletar os dados do formulário
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $cpf = $_POST['cpf'];

    // Gerar um UUID único para o cabeçalho X-Idempotency-Key
    $idempotency_key = uniqid('idempotency_', true);

    // Iniciar o cURL
    $curl = curl_init();

    // Dados do pagamento no formato JSON
    $data = [
        "description" => "Point product for card payments via Bluetooth.",
        "external_reference" => "MP0001",
        "payer" => [
            "email" => $email,
            "identification" => [
                "type" => "CPF",
                "number" => $cpf
            ],
            "first_name" => $nome
        ],
        "payment_method_id" => "pix",
        "transaction_amount" => 0.1
    ];

    // Configurar cURL
    curl_setopt_array($curl, [
        CURLOPT_URL => 'https://api.mercadopago.com/v1/payments',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => json_encode($data), // Converter o array para JSON
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $accesstoken,
            'X-Idempotency-Key: ' . $idempotency_key // Adicionar o cabeçalho X-Idempotency-Key
        ],
    ]);

    // Executar a requisição e capturar a resposta
    $response = curl_exec($curl);

    // Verificar erros no cURL
    if (curl_errno($curl)) {
        echo "Erro no cURL: " . curl_error($curl);
        curl_close($curl);
        exit;
    }

    // Fechar o cURL
    curl_close($curl);

    // Decodificar a resposta JSON
    $obj = json_decode($response);

    // Verificar o código HTTP e a resposta da API
    $http_code = json_decode($response)->status ?? null;
    echo "Código HTTP: $http_code<br>";
    echo "Resposta da API: $response<br>";

    // Verificar se o pagamento foi gerado com sucesso
    if (isset($obj->id) && isset($obj->point_of_interaction)) {
        // Capturar o link externo de pagamento
        $ticket_url = $obj->point_of_interaction->transaction_data->ticket_url ?? null;

        // Exibir o link de pagamento (iframe)
        if ($ticket_url) {
            echo "<iframe id='paymentIframe' src='{$ticket_url}' class='payment-iframe'></iframe><br/>";
        } else {
            echo "Link externo não disponível.<br/>";
        }
    } else {
        echo "Erro: Não foi possível gerar o pagamento PIX. Verifique a resposta da API.";
    }
} else {
    // Exibir o formulário com o botão para iniciar o pagamento
    echo '<div class="form-container">
            <h2>Pagamento via PIX</h2>
            <form method="POST" action="">
                <label for="nome">Nome:</label>
                <input type="text" id="nome" name="nome" required placeholder="Seu nome completo"><br><br>

                <label for="email">E-mail:</label>
                <input type="email" id="email" name="email" required placeholder="Seu e-mail"><br><br>

                <label for="cpf">CPF:</label>
                <input type="text" id="cpf" name="cpf" required placeholder="Seu CPF"><br><br>

                <input type="submit" value="Realizar Pagamento">
            </form>
        </div>';
}
?>

<!-- Adicionar o botão "Já paguei" e a função para atualizar o iframe -->
<script>
    function updateIframe() {
        // Obter o iframe
        var iframe = document.getElementById('paymentIframe');
        
        // Recarregar o conteúdo do iframe
        iframe.contentWindow.location.replace(iframe.src);
    }
</script>

<?php
    if (isset($ticket_url)) {
        echo "<button onclick='updateIframe()' class='payment-btn'>Já Paguei</button>";
    }
?>

<!-- Estilos CSS -->
<style>
/* Estilo geral */
body {
    font-family: 'Arial', sans-serif;
    background-color: #f0f0f0;
    margin: 0;
    padding: 0;
    text-align: center;
    color: #333;
}

/* Container do formulário */
.form-container {
    width: 100%;
    max-width: 600px;
    margin: 50px auto;
    padding: 20px;
    background-color: #fff;
    border-radius: 10px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
}

h2 {
    color: #444;
    font-size: 1.8em;
    margin-bottom: 30px;
}

/* Estilos do formulário */
form {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 15px;
}

form label {
    font-size: 1.2em;
    color: #555;
    margin-bottom: 5px;
}

form input[type="text"],
form input[type="email"],
form input[type="submit"] {
    width: 100%;
    max-width: 450px;
    padding: 12px;
    font-size: 1em;
    border-radius: 8px;
    border: 1px solid #ccc;
    box-sizing: border-box;
}

form input[type="text"]:focus,
form input[type="email"]:focus {
    border-color: #00aaff;
    outline: none;
}

form input[type="submit"] {
    background-color: #007bff;
    color: white;
    font-weight: bold;
    border: none;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

form input[type="submit"]:hover {
    background-color: #0056b3;
}

/* Estilo do iframe */
.payment-iframe {
    width: 100%;
    max-width: 800px;
    height: 450px;
    border: 2px solid #007bff;
    border-radius: 8px;
    margin-top: 20px;
}

/* Botão "Já paguei" */
.payment-btn {
    padding: 12px 30px;
    font-size: 1.2em;
    background-color: #28a745;
    border: none;
    border-radius: 8px;
    color: white;
    cursor: pointer;
    margin-top: 20px;
    transition: background-color 0.3s ease;
}

.payment-btn:hover {
    background-color: #218838;
}
</style>
