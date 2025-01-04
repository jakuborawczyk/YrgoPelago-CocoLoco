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