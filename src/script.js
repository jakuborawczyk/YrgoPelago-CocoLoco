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

// Update calendar when room is changed
function updateCalendar() {
    const roomId = document.getElementById('calendar-room').value;
    fetch(`book.php?room_id=${roomId}`)
        .then(response => response.text())
        .then(html => {
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            const newCalendar = doc.querySelector('.calendar');
            const calendarContainer = document.querySelector('.calendar-container .calendar');
            calendarContainer.innerHTML = newCalendar.innerHTML;
        })
        .catch(error => console.error('Error updating calendar:', error));
}