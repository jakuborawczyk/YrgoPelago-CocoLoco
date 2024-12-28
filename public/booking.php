<?php
// Database connection
$db_path = '../hotel_database.sqlite3';  // Update with your actual database path
$db = new PDO("sqlite:$db_path");  // Connect to the database

// Ensure POST data exists (form submission)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Fetch user input from the form
    $room_id = $_POST['room_id']; // Room ID (1, 2, or 3)
    $username = $_POST['username']; // User's name
    $transfer_code = $_POST['transfer_code']; // Transfer Code
    $start_date = $_POST['start_date']; // Start date (YYYY-MM-DD)
    $end_date = $_POST['end_date']; // End date (YYYY-MM-DD)

    // Validate required fields
    if (empty($room_id) || empty($username) || empty($start_date) || empty($end_date) || empty($transfer_code)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'All fields are required.'
        ]);
        exit;
    }

    // Convert dates into timestamps for comparison
    $start_timestamp = strtotime($start_date);
    $end_timestamp = strtotime($end_date);
    $days = ($end_timestamp - $start_timestamp) / (60 * 60 * 24); // Calculate the number of days

    // Validate the booking dates (Jan 10 to Jan 31)
    if ($start_timestamp < strtotime('2025-01-10') || $end_timestamp > strtotime('2025-01-31') || $start_timestamp > $end_timestamp) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Invalid booking dates. Please select dates between January 10 and January 31.'
        ]);
        exit;
    }

    // Check room availability for the selected dates (using bookings table)
    $stmt = $db->prepare('
        SELECT COUNT(*) 
        FROM bookings 
        WHERE room_id = ? 
        AND (start_date < ? AND end_date > ?)
    ');
    $stmt->execute([$room_id, $end_date, $start_date]);
    $isBooked = $stmt->fetchColumn() > 0;

    if ($isBooked) {
        echo json_encode([
            'status' => 'error',
            'message' => 'The selected room is not available for these dates.'
        ]);
        exit;
    }

    // Check if the transfer code is valid (this example assumes an external API validation)
    $transfer_api_url = 'https://www.yrgopelago.se/centralbank/transferCode'; // Ensure this URL is correct
    $total_cost = $days * 100; // Assume the cost per day is 100 for the selected room (adjust as necessary)
    $transfer_code_valid = checkTransferCode($transfer_code, $total_cost, $transfer_api_url);

    if (!$transfer_code_valid) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Invalid or used transfer code.'
        ]);
        exit;
    }

    // Apply discount if the stay is longer than 2 days
    $discount = 0;
    if ($days > 2) {
        $discount = 0.1; // 10% discount for stays longer than 2 days
    }

    // Calculate total cost after discount
    $total_cost_after_discount = $total_cost - ($total_cost * $discount);

    // Insert the booking into the database
    $stmt = $db->prepare('INSERT INTO bookings (room_id, guest_name, arrival_date, transfer_code) VALUES (?, ?, ?, ?)');
    $stmt->execute([$room_id, $username, $start_date, $transfer_code]);
    $booking_id = $db->lastInsertId(); // Get the last inserted booking ID

    // JSON response with booking details
    echo json_encode([
        'status' => 'success',
        'message' => 'Booking successfully made.',
        'booking_details' => [
            'island' => 'Main island',
            'hotel' => 'My Luxury Hotel',
            'arrival_date' => $start_date,
            'departure_date' => $end_date,
            'total_cost' => $total_cost_after_discount,
            'stars' => 5,
            'features' => [
                ['name' => 'pool', 'cost' => 10],
                ['name' => 'sauna', 'cost' => 5],
                ['name' => 'spa', 'cost' => 15]
            ],
            'additional_info' => [
                'greeting' => 'Thank you for choosing My Luxury Hotel!',
                'imageUrl' => 'https://example.com/hotel_image.jpg'
            ]
        ]
    ]);
}

// Function to check if the transfer code is valid
function checkTransferCode($transfer_code, $total_cost, $api_url) {
    $postData = json_encode([
        'transferCode' => $transfer_code,
        'totalcost' => $total_cost
    ]);

    $ch = curl_init($api_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

    $response = curl_exec($ch);
    curl_close($ch);

    $responseData = json_decode($response, true);
    return isset($responseData['status']) && $responseData['status'] === 'success';
}
?>
