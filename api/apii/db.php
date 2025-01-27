<?php
// Carregar configuração com o access token
$config = require_once '/config.php';
$accesstoken = $config['accesstoken'];

// Iniciar o cURL para buscar as transações de pagamento
$curl = curl_init();

// Configurar a requisição
curl_setopt_array($curl, [
    CURLOPT_URL => 'https://api.mercadopago.com/v1/payments/search',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'GET',
    CURLOPT_HTTPHEADER => [
        'Authorization: Bearer ' . $accesstoken
    ],
]);

// Executar a requisição e obter os dados
$response = curl_exec($curl);

// Verificar se houve erro na requisição
if (curl_errno($curl)) {
    echo "Erro no cURL: " . curl_error($curl);
    curl_close($curl);
    exit;
}

// Fechar o cURL
curl_close($curl);

// Decodificar a resposta JSON
$obj = json_decode($response);

// Verificar se as transações foram recuperadas com sucesso
if (isset($obj->results) && !empty($obj->results)) {
    echo '<table border="1">';
    echo '<thead>';
    echo '<tr>';
    echo '<th>ID</th>';
    echo '<th>Data</th>';
    echo '<th>Valor</th>';
    echo '<th>Status</th>';
    echo '<th>Meio de Pagamento</th>';
    echo '<th>Nome do Comprador</th>';
    echo '<th>E-mail do Comprador</th>';
    echo '</tr>';
    echo '</thead>';
    echo '<tbody>';

    // Exibir os dados das transações em linhas
    foreach ($obj->results as $payment) {
        echo '<tr>';
        echo '<td>' . $payment->id . '</td>';
        echo '<td>' . date('Y-m-d H:i:s', strtotime($payment->date_approved)) . '</td>';
        echo '<td>' . $payment->transaction_amount . '</td>';
        echo '<td>' . $payment->status . '</td>';
        echo '<td>' . $payment->payment_method_id . '</td>';

        // Verificar e exibir o nome completo do comprador (caso disponível)
        if (isset($payment->payer->first_name) && isset($payment->payer->last_name)) {
            echo '<td>' . $payment->payer->first_name . ' ' . $payment->payer->last_name . '</td>';
        } else {
            echo '<td>Nome não disponível</td>';
        }

        // Verificar e exibir o e-mail do comprador (caso disponível)
        if (isset($payment->payer->email) && !empty($payment->payer->email)) {
            echo '<td>' . $payment->payer->email . '</td>';
        } else {
            echo '<td>E-mail não disponível</td>';
        }
    
        echo '</tr>';
    }

    echo '</tbody>';
    echo '</table>';
} else {
    echo "Nenhuma transação encontrada.";
}
?>
