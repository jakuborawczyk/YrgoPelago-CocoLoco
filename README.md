# YRGOPELAGO Assignment Coco-Loco Resort
Yrgopelago

This project was created for the PHP course in the Web Developer program at YRGO Higher Vocational School, Gothenburg. The goal was to build a hotel website where classmates could book rooms using transfer codes generated via an API. The project uses PHP, SQLite, JavaScript, and CSS and is licensed under MIT.

Features
The hotel website allows users to:

Book Rooms: Choose from three room types and optional features.
View Availability: A calendar shows available dates.
Generate Transfer Codes: Users can create transfer codes on the site or via the Yrgopelago Central Bank API.
Secure Payment: Bookings are validated with the API, ensuring transfer codes match the total cost.
How It Works
Room Selection: Users select room and features. Prices are calculated per day and per booking.
Transfer Code Generation: A form on the site (or directly via the API) creates transfer codes.
Validation:
Dates are checked for availability.
The transfer code and cost are verified via the API.
If valid, the booking is saved in the database, and a receipt is generated.
Completion: Successful bookings deposit the transfer code and redirect users to a confirmation page with a downloadable receipt.
Visit the live site here: "https://jakuborawczyk.se/Coco-Loco/YrgoPelago-CocoLoco/public/index.php".

### Feedback - Anna
- index.php 24-27,36,45,54,84 - Avoid using inline styles. Instead, move styling to your CSS file for better maintainability and consistency.
- index.php 21 & 23 - Ensure consistent naming conventions for classes to improve readability and reduce potential confusion.
- Booking.php 14 - Consider applying trim() to sanitize input data and handle cases where extra spaces might cause unexpected behavior.
- booking.php 25-36 - Avoid abbreviations like stmt. Use more descriptive variable names for better readability and maintainability.
- There's an inconsistency in your use of declare(strict_types=1);. For example, it is present in database.php (required in booking.php) but not in functions.php. Ensure consistency across all files if you're aiming for strict typing.
- styles.css - Consider breaking down the styles into smaller, more modular files or sections to improve organization and scalability.
- database.php 9 - Consider removing unnecessary commented-out code

- Great work overall! Very clean and easy to understand code and the website looks amazing 🫶🏼
