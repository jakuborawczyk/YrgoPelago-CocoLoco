<?php 
function validateTransferCode($transfer_code) {
    $url = 'https://www.yrgopelago.se/centralbank/deposit'; // Endpoint do walidacji
    $data = [
        'transferCode' => $transfer_code,
    ];

    $options = [
        'http' => [
            'header'  => "Content-type: application/json\r\n",
            'method'  => 'POST',
            'content' => json_encode($data),
        ]
    ];

    $context  = stream_context_create($options);
    $response = file_get_contents($url, false, $context);

    if ($response === FALSE) {
        return false; // Błąd połączenia
    }

    $result = json_decode($response, true);
    
    // Sprawdź odpowiedź z serwera
    return isset($result['valid']) && $result['valid'] === true;
}
