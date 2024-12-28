<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hotel Booking</title>
    <link rel="stylesheet" href="../src/styles.css">
</head>
<body>
    <h1>Hotel Booking System</h1>
    <form id="bookingForm" method="POST" action="booking.php">
        <label for="room_id">Select Room:</label>
        <select name="room_id" id="room_id">
            <option value="1">Luxury Room</option>
            <option value="2">Standard Room</option>
            <option value="3">Economy Room</option>
        </select><br>

        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required><br>

        <label for="transfer_code">Transfer Code:</label>
        <input type="text" id="transfer_code" name="transfer_code" required><br>

        <label for="start_date">Start Date:</label>
        <input type="date" id="start_date" name="start_date" value="2025-01-10" min="2025-01-10" max="2025-01-31" required><br>

        <label for="end_date">End Date:</label>
        <input type="date" id="end_date" name="end_date" value="2025-01-11" min="2025-01-10" max="2025-01-31" required><br>

        <input type="submit" value="Book Room">
    </form>

    <div id="response"></div>

    <script>
        // Handle form submission via AJAX to avoid page reload
        document.getElementById('bookingForm').addEventListener('submit', function(event) {
            event.preventDefault();

            const formData = new FormData(this);
            fetch('booking.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                const responseDiv = document.getElementById('response');
                if (data.status === 'success') {
                    responseDiv.innerHTML = `<p>Booking Success! Total Cost: ${data.booking_details.total_cost}</p>`;
                    responseDiv.innerHTML += `<p>Features:</p><ul>`;
                    data.booking_details.features.forEach(feature => {
                        responseDiv.innerHTML += `<li>${feature.name} - Cost: ${feature.cost}</li>`;
                    });
                    responseDiv.innerHTML += `</ul>`;
                } else {
                    responseDiv.innerHTML = `<p>Error: ${data.message}</p>`;
                }
            })
            .catch(error => {
                document.getElementById('response').innerHTML = `<p>Error: Unable to process the booking</p>`;
            });
        });
    </script>
</body>
</html>
