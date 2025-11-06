<?php
// upload_image.php
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $imageUrl = $_POST['image_url'] ?? '';
    
    if (empty($imageUrl)) {
        echo json_encode(['status' => 'error', 'message' => 'No image URL provided']);
        exit;
    }
    
    // Validate URL
    if (!filter_var($imageUrl, FILTER_VALIDATE_URL)) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid URL']);
        exit;
    }
    
    // Get image content
    $imageData = @file_get_contents($imageUrl);
    if ($imageData === false) {
        echo json_encode(['status' => 'error', 'message' => 'Could not download image from URL']);
        exit;
    }
    
    // Create images directory if it doesn't exist
    $uploadDir = 'C:/xampp/htdocs/movie_booking_app/image/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    
    // Extract filename from URL
    $parsedUrl = parse_url($imageUrl);
    $pathInfo = pathinfo($parsedUrl['path']);
    $filename = $pathInfo['filename'] . '.' . ($pathInfo['extension'] ?? 'jpg');
    
    // Sanitize filename
    $filename = preg_replace('/[^a-zA-Z0-9\._-]/', '_', $filename);
    
    // Save image
    $filePath = $uploadDir . $filename;
    if (file_put_contents($filePath, $imageData)) {
        // Return relative path for web use
        $relativePath = 'image/' . $filename;
        echo json_encode([
            'status' => 'success', 
            'message' => 'Image uploaded successfully!',
            'path' => $relativePath
        ]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to save image']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
?>