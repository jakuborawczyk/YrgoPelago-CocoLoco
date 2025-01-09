<?php

// Include the Connections
require_once '../src/database.php';
require_once '../src/functions.php';

// API URL for Transfer Code validation
$api_url = "https://www.yrgopelago.se/centralbank/transferCode";
$deposit_url = "https://www.yrgopelago.se/centralbank/deposit";
$user = "Jakub"; // User for the deposit

// Get POST data
$room_id = isset($_POST['room_id']) ? (int)$_POST['room_id'] : null;
$guest_name = htmlspecialchars(trim($_POST['username'] ?? ''));
$check_in_date = htmlspecialchars(trim($_POST['start_date'] ?? ''));
$check_out_date = htmlspecialchars(trim($_POST['end_date'] ?? ''));
$transfer_code = htmlspecialchars(trim($_POST['transfer_code'] ?? ''));

// Check for empty fields
if (empty($room_id) || empty($guest_name) || empty($check_in_date) || empty($check_out_date) || empty($transfer_code)) {
    die(json_encode(['status' => 'error', 'message' => 'All fields are required.']));
}

// Check room availability from the calendar
$stmt = $pdo->prepare("
    SELECT COUNT(*) 
    FROM bookings 
    WHERE room_id = :room_id 
    AND (
        (arrival_date <= :check_in_date AND departure_date > :check_in_date) OR
        (arrival_date < :check_out_date AND departure_date >= :check_out_date) OR
        (arrival_date >= :check_in_date AND departure_date <= :check_out_date)
    )
");

$stmt->execute([
    ':room_id' => $room_id,
    ':check_in_date' => $check_in_date,
    ':check_out_date' => $check_out_date
]);

$isAvailable = $stmt->fetchColumn() == 0;
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
$check_in = new DateTime($check_in_date);
$check_out = new DateTime($check_out_date);
$numberOfNights = $check_in->diff($check_out)->days;
$totalCost = $numberOfNights * $room_price;

// Calculate the discount based on the number of nights
if ($numberOfNights >= 3) {
    $discount = 0.30; // 30% discount for bookings longer than 3 days
    $discountReason = 'Guest stayed for ' . $numberOfNights . ' nights, eligible for 30% long stay discount';
    $totalCost = $totalCost * (1 - $discount);
} else {
    $discount = 0;
    $discountReason = '';
}

// Check if features are set and iterate over them
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

// Check if the transfer code is valid
if (!checkTransferCode($transfer_code, $totalCost, $api_url)) {
    die(json_encode(['status' => 'error', 'message' => 'Invalid or used transfer code.']));
}

// Consume the transfer code
if (!consumeTransferCode($transfer_code, $numberOfNights, $deposit_url, $user)) {
    die(json_encode(['status' => 'error', 'message' => 'Failed to consume transfer code.']));
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