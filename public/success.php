
<?php
$response = json_decode($_GET['response'], true);

if ($response['status'] === 'success') {
    ?>
    <h1><?= $response['booking_details']['additional_info']['greeting'] ?></h1>
    <a href="<?= $response['booking_details']['additional_info']['imageUrl'] ?>" target="_blank">
        <img src="<?= $response['booking_details']['additional_info']['imageUrl'] ?>" alt="Image">
    </a>
    <pre><?= json_encode($response, JSON_PRETTY_PRINT) ?></pre>
    <?php
} else {
    echo 'Error: Invalid response.';
}
?>
<link rel="stylesheet" href="../src/success.css">


