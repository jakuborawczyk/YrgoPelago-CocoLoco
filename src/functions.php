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