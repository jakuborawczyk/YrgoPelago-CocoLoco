<?php

declare(strict_types=1);

require 'db_connection.php';

$room_id = isset($_POST['room_id']) ? (int)$_POST['room_id'] : null;
$guest_name = htmlspecialchars(trim($_POST['guest_name'] ?? ''));
$check_in_date = htmlspecialchars(trim($_POST['check_in_date'] ?? ''));
$check_out_date = htmlspecialchars(trim($_POST['check_out_date'] ?? ''));
$transfer_code = htmlspecialchars(trim($_POST['transfer_code'] ?? ''));

// Walidacja danych
if (empty($room_id) || empty($guest_name) || empty($check_in_date) || empty($check_out_date) || empty($transfer_code)) {
    die('Wszystkie pola są wymagane.');
}

// Sprawdzenie dostępności pokoju i ceny za noc
$stmt = $pdo->prepare("SELECT price_per_day FROM rooms WHERE room_id = :room_id");
$stmt->execute([':room_id' => $room_id]);
$room_price = $stmt->fetchColumn();

if (!$room_price) {
    die('Pokój nie został znaleziony.');
}

// Obliczanie liczby nocy i całkowitych kosztów
$numberOfNights = (strtotime($check_out_date) - strtotime($check_in_date)) / (60 * 60 * 24);
$totalCost = $numberOfNights * (float)$room_price;

// Walidacja kodu transferu (tutaj powinna być logika do sprawdzenia kodu)
if ($transfer_code !== "valid-code") { // Zastąp to rzeczywistą walidacją
    die('Nieprawidłowy kod transferu.');
}

// Zapisanie rezerwacji w bazie danych
$stmt = $pdo->prepare("
    INSERT INTO bookings (room_id, guest_name, arrival_date, departure_date, transfer_code) 
    VALUES (:room_id, :guest_name, :arrival_date, :departure_date, :transfer_code)
");
if ($stmt->execute([
    ':room_id' => $room_id,
    ':guest_name' => $guest_name,
    ':arrival_date' => $check_in_date,
    ':departure_date' => $check_out_date,
    ':transfer_code' => $transfer_code
])) {
    // Przygotowanie odpowiedzi JSON dla udanej rezerwacji
    $response = [
        "island" => "Coconut Paradise",
        "hotel" => "Coco-Loco Resort",
        "arrival_date" => $check_in_date,
        "departure_date" => $check_out_date,
        "total_cost" => "$" . number_format($totalCost, 2),
        "stars" => "4", // Przykładowa ocena gwiazdkowa
        "features" => [
            [
                "name" => "sauna",
                "cost" => 20
            ]
        ],
        "additional_info" => [
            "greeting" => "Dziękujemy za wybór Coco-Loco Resort",
            "imageUrl" => "https://giphy.com/gifs/adultswim-liBsVeLILcyaY"
        ]
    ];

    header('Content-Type: application/json');
    echo json_encode($response);
} else {
    die('Rezerwacja nie powiodła się.');
}
?>
