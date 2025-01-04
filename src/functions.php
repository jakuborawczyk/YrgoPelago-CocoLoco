<?php
function getFeatureCost($featureName) {
    $featureCosts = [
        "Pool Access" => 8,
        "Breakfast" => 10,
        "Gym Access" => 5,
        // Add more features and their costs here
    ];

    return $featureCosts[$featureName] ?? 0; // Return 0 if feature not found
}