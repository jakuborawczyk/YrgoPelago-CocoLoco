<?php

declare(strict_types=1);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Coco-Loco Resort</title>
    <link rel="stylesheet" href="../src/bookingstyle.css">
</head>

<body>
    <div class="booking-form">
        <h1>Coco-Loco Resort Booking</h1>

        <form id="bookingForm" method="POST" action="booking.php">
            <div class="form-group">
                <label for="room_id">Select Room:</label>
                <select name="room_id" id="room_id">
                    <option value="1">Economy Room ($3)</option>
                    <option value="2">Standard Room ($5)</option>
                    <option value="3">Luxury Room ($8)</option>
                </select>
            </div>

            <div class="form-group">
                <label>Select Features:</label>
                <div class="features-group">
                    <input type="checkbox" id="pool-access" name="features[]" value="pool" data-price="3">
                    <label for="pool-access">Pool Access ($3)</label>

                    <input type="checkbox" id="breakfast" name="features[]" value="breakfast" data-price="5">
                    <label for="breakfast">Breakfast ($5)</label>

                    <input type="checkbox" id="gym-access" name="features[]" value="gym" data-price="3">
                    <label for="gym-access">Gym Access ($3)</label>
                </div>
            </div>

            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required>
            </div>

            <div class="form-group">
                <label for="transfer_code">Transfer Code:</label>
                <input type="text" id="transfer_code" name="transfer_code" required>
            </div>

            <div class="form-group">
                <label for="start_date">Check-in Date:</label>
                <input type="date" id="start_date" name="start_date"
                    value="2025-01-10" min="2025-01-10" max="2025-01-31" required>
            </div>

            <div class="form-group">
                <label for="end_date">Check-out Date:</label>
                <input type="date" id="end_date" name="end_date"
                    value="2025-01-11" min="2025-01-10" max="2025-01-31" required>
            </div>

            <div class="summary" id="booking-summary">
                <h3>Booking Summary</h3>
                <p>Room Cost: <span id="room-cost">$3</span> per night</p>
                <p>Nights: <span id="nights-count">1</span></p>
                <p>Features Cost: <span id="features-cost">$0</span></p>
                <p class="discount" id="discount-text" style="display: none;">
                    30% Discount Applied: -$<span id="discount-amount">0</span>
                </p>
                <p class="total-cost">Total Cost: $<span id="total-cost">3</span></p>
            </div>

            <button type="submit">Book Room</button>
        </form>

        <div id="response"></div>
    </div>


    <div class="calendar-container">
    <div class="room-selector">
        <label for="calendar-room">View availability for:</label>
        <select id="calendar-room" onchange="updateCalendar()">
            <option value="1">Economy Room</option>
            <option value="2">Standard Room</option>
            <option value="3">Luxury Room</option>
        </select>
    </div>
    <div class="calendar" id="availability-calendar"></div>


<?php 


require_once '../src/database.php';

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
        if ($date >= $booking['arrival_date'] && $date <= $booking['departure_date']) {
            return true;
        }
    }
    return false;
}

$room_id = isset($_GET['room_id']) ? $_GET['room_id'] : 1;
$bookedDates = getBookedDates($pdo, $room_id);

$startDate = new DateTime('2025-01-10');
$endDate = new DateTime('2025-01-31');

echo "<div class='calendar-header'>";
echo "<div>Sun</div><div>Mon</div><div>Tue</div><div>Wed</div><div>Thu</div><div>Fri</div><div>Sat</div>";
echo "</div>";

$currentDate = clone $startDate;
$firstDay = $currentDate->format('w');

echo "<div class='calendar-grid'>";
// Add empty cells for days before start date
for ($i = 0; $i < $firstDay; $i++) {
    echo "<div class='calendar-day empty'></div>";
}

while ($currentDate <= $endDate) {
    $dateStr = $currentDate->format('Y-m-d');
    $isBooked = isDateBooked($dateStr, $bookedDates);
    $class = $isBooked ? 'booked' : 'available';
    
    echo "<div class='calendar-day {$class}' data-date='{$dateStr}'>";
    echo $currentDate->format('j');
    echo "</div>";
    
    $currentDate->modify('+1 day');
}
echo "</div>";


?>




    </div>
    <div class="calendar-legend">
        <div class="legend-item">
            <span class="legend-color available"></span>
            <span>Available</span>
        </div>
        <div class="legend-item">
            <span class="legend-color booked"></span>
            <span>Booked</span>
        </div>
    </div>
</div>

    <script src="../src/script.js"></script>
</body>

</html>