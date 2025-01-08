<?php

declare(strict_types=1);

?>
<!-- index.html -->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Coco-Loco Resort</title>
    <link rel="stylesheet" href="../src/styles.css">
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

    <script>
        const roomPrices = {
            '1': 3, // Economy Room
            '2': 5, // Standard Room
            '3': 8 // Luxury Room
        };

        const featurePrices = {
            'Pool Access ($3)': 3,
            'Breakfast ($5)': 5,
            'Gym Access ($3)': 3
        };

        function calculateTotalCost() {
            const roomId = document.getElementById('room_id').value;
            const startDate = new Date(document.getElementById('start_date').value);
            const endDate = new Date(document.getElementById('end_date').value);

            // Calculate features cost using data-price attribute
            const selectedFeatures = document.querySelectorAll('input[name="features[]"]:checked');
            const featuresCost = Array.from(selectedFeatures).reduce((sum, feature) => {
                return sum + parseFloat(feature.dataset.price);
            }, 0);

            // Calculate nights
            const nights = Math.max(1, Math.floor((endDate - startDate) / (1000 * 60 * 60 * 24)));
            document.getElementById('nights-count').textContent = nights;

            // Calculate room cost
            const roomCost = roomPrices[roomId] * nights;
            document.getElementById('room-cost').textContent = '$' + roomPrices[roomId];

            // Update features cost display
            document.getElementById('features-cost').textContent = '$' + featuresCost.toFixed(2);

            // Calculate total before discount
            let totalCost = roomCost + featuresCost;

            // Apply discount if applicable
            const discountText = document.getElementById('discount-text');
            if (nights >= 3) {
                const discountAmount = totalCost * 0.3;
                totalCost = totalCost * 0.7;
                document.getElementById('discount-amount').textContent = discountAmount.toFixed(2);
                discountText.style.display = 'block';
            } else {
                discountText.style.display = 'none';
            }

            document.getElementById('total-cost').textContent = totalCost.toFixed(2);
        }

        // Add event listeners
        document.getElementById('room_id').addEventListener('change', calculateTotalCost);
        document.getElementById('start_date').addEventListener('change', calculateTotalCost);
        document.getElementById('end_date').addEventListener('change', calculateTotalCost);
        document.querySelectorAll('input[name="features[]"]').forEach(checkbox => {
            checkbox.addEventListener('change', calculateTotalCost);
        });

        // Form submission
        document.getElementById('bookingForm').addEventListener('submit', async function(e) {
    e.preventDefault();

    try {
        const formData = new FormData(this);
        const response = await fetch('booking.php', {
            method: 'POST',
            body: formData
        });

        const result = await response.json();

        if (result.status === 'success') {
            window.location.href = 'success.php?response=' + encodeURIComponent(JSON.stringify(result));
        } else {
            alert(result.message || 'Booking failed.');
        }
    } catch (error) {
        alert('Error submitting booking.');
        console.error('Error:', error);
    }
});

        // Initialize calculation
        calculateTotalCost();
    </script>
</body>

</html>