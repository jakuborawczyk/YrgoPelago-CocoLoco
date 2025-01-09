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