<?php
/**
 * Generate QR Code for Parking Space
 * This file generates QR codes using the QR Server API
 * No library installation needed - uses Google's QR Code API
 */

function generateQRCode($text, $space_id, $size = 300) {
    $encoded_text = urlencode($text);
    $qr_url = "https://api.qrserver.com/v1/create-qr-code/?size={$size}x{$size}&data={$encoded_text}";
    return $qr_url;
}

function downloadQRCode($text, $filename) {
    $encoded_text = urlencode($text);
    $size = 300;
    $qr_url = "https://api.qrserver.com/v1/create-qr-code/?size={$size}x{$size}&data={$encoded_text}";
    
    // Create qr_codes directory if it doesn't exist
    $qr_dir = __DIR__ . '/../qr_codes/';
    if (!is_dir($qr_dir)) {
        mkdir($qr_dir, 0755, true);
    }
    
    // Download and save QR code image
    $image_data = @file_get_contents($qr_url);
    if ($image_data !== false) {
        $filepath = $qr_dir . $filename . '.png';
        file_put_contents($filepath, $image_data);
        return $filepath;
    }
    return false;
}

?>
