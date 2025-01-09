<?php
function getFeatureCost($featureName) {
    $featureCosts = [
        "Pool Access" => 3,
        "Breakfast" => 5,
        "Gym Access" => 3, 
        // Add more features and their costs underneath
    ];

    return $featureCosts[$featureName] ?? 0; // Return 0 if feature not found
}

function getBookedDates($pdo, $room_id) {
    $stmt = $pdo->prepare("
        SELECT arrival_date, departure_date 
        FROM bookings 
        WHERE room_id = :room_id
    ");
    $stmt->execute([':room_id' => $room_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function isDateBooked($date, $bookedDates) {
    foreach ($bookedDates as $booking) {
        if ($date >= $booking['arrival_date'] && $date < $booking['departure_date']) {
            return true;
        }
    }
    return false;
}


function checkTransferCode($transfer_code, $total_cost, $api_url) {
    $postData = json_encode([
        'transferCode' => $transfer_code,
        'totalcost' => $total_cost
    ]);

    // Initialize cURL session to the API
    $ch = curl_init($api_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

    // Execute the request and get the response
    $response = curl_exec($ch);

    // Log the request
    error_log('API Request Data: ' . $postData);

    if ($response === false) {
        error_log('cURL Error: ' . curl_error($ch));
        curl_close($ch);
        return false; // Error in cURL request
    }

    curl_close($ch);

    // Decode the JSON response
    $responseData = json_decode($response, true);

    // Log the response for debugging
    error_log('API Response: ' . $response);

    if ($responseData && isset($responseData['status']) && $responseData['status'] === 'success') {
        return true;  // Transfer code is valid
    } else {
        error_log('Transfer code validation failed: ' . print_r($responseData, true));
        return false;  // Invalid or used transfer code
    }
}

function consumeTransferCode($transfer_code, $numberOfNights, $deposit_url, $user) {
    $postData = json_encode([
        'user' => $user,
        'transferCode' => $transfer_code,
        'numberOfDays' => $numberOfNights
    ]);

    // Initialize cURL session to the API
    $ch = curl_init($deposit_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

    // Execute the request and get the response
    $response = curl_exec($ch);

    // Log the request
    error_log('API Deposit Request Data: ' . $postData);

    if ($response === false) {
        error_log('cURL Error: ' . curl_error($ch));
        curl_close($ch);
        return false; // Error in cURL request
    }

    curl_close($ch);

    // Decode the JSON response
    $responseData = json_decode($response, true);

    // Log the response for debugging
    error_log('API Deposit Response: ' . $response);

    if ($responseData && isset($responseData['status']) && $responseData['status'] === 'success') {
        return true;  // Transfer code consumed successfully
    } else {
        error_log('Transfer code consumption failed: ' . print_r($responseData, true));
        return false;  // Failed to consume transfer code
    }
}