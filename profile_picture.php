<?php

@include '../config/config.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

function generateProfilePicture($name, $width = 200, $height = 200) {
   
    $colors = [
        '#FF6B6B', '#4ECDC4', '#45B7D1', '#96CEB4', '#FFEAA7',
        '#DDA0DD', '#98D8C8', '#F7DC6F', '#BB8FCE', '#85C1E9'
    ];
    
    
    $initials = getInitials($name);
    
    // Generate consistent color based on name
    $colorIndex = ord($name[0]) % count($colors);
    $backgroundColor = $colors[$colorIndex];
    
    // Create image
    $image = imagecreate($width, $height);
    
    // Convert hex to RGB
    list($r, $g, $b) = sscanf($backgroundColor, "#%02x%02x%02x");
    $bgColor = imagecolorallocate($image, $r, $g, $b);
    $textColor = imagecolorallocate($image, 255, 255, 255);
    
    // Fill background
    imagefill($image, 0, 0, $bgColor);
    
    // Calculate font size and position
    $fontSize = $width / 3;
    $fontPath = __DIR__ . '/fonts/arial.ttf';
    
    // Use built-in font if custom font not available
    if (!file_exists($fontPath)) {
        // Use built-in font (smaller size)
        $fontSize = 5; // Built-in font size
        $textWidth = imagefontwidth($fontSize) * strlen($initials);
        $textHeight = imagefontheight($fontSize);
        $x = ($width - $textWidth) / 2;
        $y = ($height - $textHeight) / 2;
        imagestring($image, $fontSize, $x, $y, $initials, $textColor);
    } else {
        // Use custom font
        $bbox = imagettfbbox($fontSize, 0, $fontPath, $initials);
        $textWidth = $bbox[2] - $bbox[0];
        $textHeight = $bbox[1] - $bbox[7];
        $x = ($width - $textWidth) / 2;
        $y = ($height + $textHeight) / 2;
        imagettftext($image, $fontSize, 0, $x, $y, $textColor, $fontPath, $initials);
    }
    
    return $image;
}

function getInitials($name) {
    $words = explode(' ', $name);
    $initials = '';
    
    foreach ($words as $word) {
        if (!empty(trim($word))) {
            $initials .= strtoupper($word[0]);
        }
    }
    
    return substr($initials, 0, 2);
}

function saveProfilePicture($userId, $userName) {
    global $conn;
    
    // Generate the image
    $image = generateProfilePicture($userName, 200, 200);
    
    // Create directory if it doesn't exist
    $dir = '../profile_pictures/';
    if (!file_exists($dir)) {
        mkdir($dir, 0777, true);
    }
    
    // Save image file
    $filename = 'profile_' . $userId . '.png';
    $filepath = $dir . $filename;
    imagepng($image, $filepath);
    
    // Clean up memory
    imagedestroy($image);
    
    // Update database
    $initials = getInitials($userName);
    $profileUrl = '../profile_pictures/' . $filename;
    
    $stmt = $conn->prepare("UPDATE students SET profile_picture_url = ?, profile_initials = ? WHERE id = ?");
    $stmt->bind_param("ssi", $profileUrl, $initials, $userId);
    $stmt->execute();
    
    return $profileUrl;
}

function getProfilePicture($userId, $userName) {
    global $conn;
    
   
    $stmt = $conn->prepare("SELECT profile_picture_url FROM students WHERE id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if (!empty($row['profile_picture_url']) && file_exists($row['profile_picture_url'])) {
            return $row['profile_picture_url'];
        }
    }
    
    // Generate new profile picture if doesn't exist
    return saveProfilePicture($userId, $userName);
}

// API endpoint to serve profile pictures
if (isset($_GET['user_id'])) {
    header('Content-Type: image/png');
    
    $userId = $_GET['user_id'];
    
    // Get user name from database
    $stmt = $conn->prepare("SELECT name FROM students WHERE id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $userName = $row['name'];
        
        $image = generateProfilePicture($userName);
        imagepng($image);
        imagedestroy($image);
    }
    exit;
}
?>