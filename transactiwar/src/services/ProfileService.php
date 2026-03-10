<?php

require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../utils/validator.php';

class ProfileService {


    public static function getProfile($pdo, $username) {
        $user = User::findPublicByUsername($pdo, $username);
        if (!$user) {
            throw new Exception("User not found.");
        }
        return $user;
    }

    public static function updateProfile($pdo, $userId, $bio, $file) {

        $bio = trim($bio);
        
        // 1. Prevent Database Bloat / DoS via Bio
        if (strlen($bio) > 1000) {
            throw new Exception("Biography exceeds the maximum allowed length of 1000 characters.");
        }

        // Fetch current user to retain the existing image if no new one is uploaded (public fields only)
        $currentUser = User::findPublicById($pdo, $userId);
        $imagePath = $currentUser['profile_image_path'];

        // Handle Image Upload Securely
        if (isset($file) && $file['error'] !== UPLOAD_ERR_NO_FILE) {
            
            // Catch PHP-level file size errors (exceeds upload_max_filesize or post_max_size)
            if ($file['error'] === UPLOAD_ERR_INI_SIZE || $file['error'] === UPLOAD_ERR_FORM_SIZE) {
                throw new Exception("Image size must not exceed 2MB.");
            }

            // Catch any other PHP upload errors
            if ($file['error'] !== UPLOAD_ERR_OK) {
                throw new Exception("Image upload failed with error code: " . $file['error']);
            }

            // Your existing manual size check
            if ($file['size'] > 2097152) { // 2MB disk size limit
                throw new Exception("Image size must not exceed 2MB.");
            }

            $fileTmpPath = $file['tmp_name'];

            // 2. Prevent Decompression Bombs (Pixel Flooding)
            $imageInfo = @getimagesize($fileTmpPath);
            if ($imageInfo === false) {
                throw new Exception("Invalid image data. File may be corrupted.");
            }
            
            $width = $imageInfo[0];
            $height = $imageInfo[1];

            if ($width > 2000 || $height > 2000) {
                throw new Exception("Image dimensions are too large. Maximum allowed is 2000x2000 pixels.");
            }

            // 3. Verify Magic Bytes
            $finfo = new finfo(FILEINFO_MIME_TYPE);
            $trueMimeType = $finfo->file($fileTmpPath);
            
            $allowedTypes = [
                'image/jpeg' => '.jpg', 
                'image/png' => '.png', 
                'image/gif' => '.gif'
            ];
            
            if (!array_key_exists($trueMimeType, $allowedTypes)) {
                throw new Exception("Invalid image format. True MIME type not allowed.");
            }

            // 4. Generate a secure random filename
            $ext = $allowedTypes[$trueMimeType];
            $filename = bin2hex(random_bytes(16)) . $ext;
            
            $uploadDir = __DIR__ . '/../../public/uploads/profile_images/';
            
            if(!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            
            $destinationPath = $uploadDir . $filename;

            // 5. Strip EXIF metadata / Re-encode image using GD library
            $imageResource = null;
            if ($trueMimeType === 'image/jpeg') {
                $imageResource = @imagecreatefromjpeg($fileTmpPath);
            } elseif ($trueMimeType === 'image/png') {
                $imageResource = @imagecreatefrompng($fileTmpPath);
            } elseif ($trueMimeType === 'image/gif') {
                $imageResource = @imagecreatefromgif($fileTmpPath);
            }

            if (!$imageResource) {
                throw new Exception("Failed to process image. File may contain malicious payload.");
            }

            // Save the newly encoded, clean image
            $saveSuccess = false;
            if ($trueMimeType === 'image/jpeg') {
                $saveSuccess = imagejpeg($imageResource, $destinationPath, 90);
            } elseif ($trueMimeType === 'image/png') {
                $saveSuccess = imagepng($imageResource, $destinationPath);
            } elseif ($trueMimeType === 'image/gif') {
                $saveSuccess = imagegif($imageResource, $destinationPath);
            }

            imagedestroy($imageResource);

            if($saveSuccess) {
                $imagePath = '/uploads/profile_images/' . $filename;
                if (!empty($currentUser['profile_image_path'])) {
                    // Map the absolute path to the old image
                    $oldImageFullPath = __DIR__ . '/../../public' . $currentUser['profile_image_path'];
                    
                    // If it exists on the disk, delete it
                    if (file_exists($oldImageFullPath) && is_file($oldImageFullPath)) {
                        unlink($oldImageFullPath);
                    }
                }
            } else {
                throw new Exception("Failed to save the processed image.");
            }
        }

        User::updateProfile($pdo, $userId, $bio, $imagePath);
    }

}