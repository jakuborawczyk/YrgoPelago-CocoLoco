<?php

// Include the Connections
require_once '../src/database.php';
require_once '../src/functions.php';

// API URL for Transfer Code validation
$api_url = "https://www.yrgopelago.se/centralbank/transferCode";

// Get POST data
$room_id = isset($_POST['room_id']) ? (int)$_POST['room_id'] : null;
$guest_name = htmlspecialchars(trim($_POST['username'] ?? ''));
$check_in_date = htmlspecialchars(trim($_POST['start_date'] ?? ''));
$check_out_date = htmlspecialchars(trim($_POST['end_date'] ?? ''));
$transfer_code = htmlspecialchars(trim($_POST['transfer_code'] ?? ''));


// Check for empty fields
if (empty($room_id) || empty($guest_name) || empty($check_in_date) || empty($check_out_date) || empty($transfer_code) ) {
    die(json_encode(['status' => 'error', 'message' => 'All fields are required.']));
}

// Check room availability from the calendar
$stmt = $pdo->prepare("
    SELECT COUNT(*) 
    FROM calendar_availability 
    WHERE room_id = :room_id 
    AND date BETWEEN :check_in_date AND :check_out_date
");

$stmt->execute([
    ':room_id' => $room_id,
    ':check_in_date' => $check_in_date,
    ':check_out_date' => $check_out_date
]);

$isAvailable = $stmt->fetchColumn() > 0;
error_log('Room availability: ' . ($isAvailable ? 'Available' : 'Not Available'));

if (!$isAvailable) {
    die(json_encode(['status' => 'error', 'message' => 'Room is not available for the selected dates.']));
}

// Fetch room price from the rooms table
$stmt = $pdo->prepare("SELECT price_per_day FROM rooms WHERE room_id = :room_id");
$stmt->execute([':room_id' => $room_id]);
$room_price = $stmt->fetchColumn();

if (!$room_price) {
    die(json_encode(['status' => 'error', 'message' => 'Room not found.']));
}

// Calculate total cost based on number of nights
$check_in = new DateTime($check_in_date);// Convert the check-in and check-out dates to DateTime objects
$check_out = new DateTime($check_out_date);// Convert the check-in and check-out dates to DateTime objects
$numberOfNights = $check_in->diff($check_out)->days;// Calculate the number of nights
$totalCost = $numberOfNights * $room_price; // Total cost for the room


// Calculate the discount based on the number of nights
if ($numberOfNights >= 3) {
    $discount = 0.30; // 30% discount for bookings longer than 3 days
    $discountReason = 'Guest stayed for ' . $numberOfNights . ' nights, eligible for 30% long stay discount';
    $totalCost = $totalCost * (1 - $discount);
} else {
    $discount = 0;
    $discountReason = '';
}

$features = $_POST['features'] ?? [];
foreach ($features as $feature) {
    switch ($feature) {
        case "pool":
            $totalCost += 3;
            break;
        case "breakfast":
            $totalCost += 5;
            break;
        case "gym":
            $totalCost += 3;
            break;
    }
}


// Validate the transfer code
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
// Check if the transfer code is valid
if (!checkTransferCode($transfer_code, $totalCost, $api_url)) {
    die(json_encode(['status' => 'error', 'message' => 'Invalid or used transfer code.']));
}

// Proceed to insert the booking into the database
$stmt = $pdo->prepare("
    INSERT INTO bookings (room_id, guest_name, arrival_date, departure_date, transfer_code, discount, discount_reason, total_cost) 
    VALUES (:room_id, :guest_name, :check_in_date, :check_out_date, :transfer_code, :discount, :discount_reason, :total_cost)
");
if ($stmt->execute([
    ':room_id' => $room_id,
    ':guest_name' => $guest_name,
    ':check_in_date' => $check_in_date,
    ':check_out_date' => $check_out_date,
    ':transfer_code' => $transfer_code,
    ':discount' => $discount,
    ':discount_reason' => $discountReason,
    ':total_cost' => $totalCost
])) {
   
 // Return the booking details in JSON format
    $response = [
        "status" => "success",
        "booking_details" => [
            "hotel" => "Coco-Loco Resort",
            "arrival_date" => $check_in_date,
            "departure_date" => $check_out_date,
            "total_cost" => $totalCost,
            "stars" => "4",
            "discount" => $discountReason,
            "additional_info" => [
                "greeting" => "Thank you for choosing Coco-Loco Resort",
                "imageUrl" => "https://i.giphy.com/media/v1.Y2lkPTc5MGI3NjExZTNjMDBpbmRra3d2cHpqNW5wMjJwaWExYWZxbDF6bDF2aTBkcWtzdiZlcD12MV9pbnRlcm5hbF9naWZfYnlfaWQmY3Q9Zw/6R8hckRI78mlPJ3cjn/giphy.gif"
            ]
        ]
    ];

    header('Content-Type: application/json');
    echo json_encode($response, JSON_PRETTY_PRINT);
    exit;
} else {
    // Log the error if insertion fails
    error_log('Booking insertion failed: ' . print_r($stmt->errorInfo(), true));
    die(json_encode(['status' => 'error', 'message' => 'Booking failed.']));
}

?>