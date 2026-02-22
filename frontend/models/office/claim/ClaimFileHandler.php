<?php

namespace frontend\models\office\claim;
use Yii;
/**
 * Description of ClaimFileHandler
 *
 * @author user
 */
class ClaimFileHandler {

    private $lastError = null;

    // Handle file upload for new records
    public function handleFileUpload($fileData, $claimCode, $index, $isRequired = true) {
        if (!$fileData || !isset($fileData['new_file'])) {
            if ($isRequired) {
                $this->lastError = 'Receipt file is required for new claim details.';
                return false;
            }
            return null;
        }

        $newFile = $fileData['new_file'];
        if (!$newFile || $newFile->error !== UPLOAD_ERR_OK) {
            if ($isRequired) {
                $this->lastError = 'Receipt file is required for new claim details.';
                return false;
            }
            return null;
        }

        return $this->saveFile($newFile, $claimCode);
    }

    // Handle file update for existing records
    public function handleFileUpdate($claimDetail, $fileData, $claimCode, $index, $isExisting) {
        if (!$fileData) {
            if (!$isExisting) {
                $this->lastError = 'Receipt file is required for new claim details.';
                return false;
            }
            return true; // No file operation needed
        }

        $newFile = $fileData['new_file'] ?? null;
        $removeExisting = $fileData['remove_existing'] ?? false;

        if ($isExisting) {
            return $this->handleExistingRecordFile($claimDetail, $newFile, $removeExisting, $claimCode);
        } else {
            return $this->handleNewRecordFile($claimDetail, $newFile, $claimCode);
        }
    }

    // Handle file for existing record
    private function handleExistingRecordFile($claimDetail, $newFile, $removeExisting, $claimCode) {
        $originalFile = $claimDetail->receipt_file;

        // New file uploaded
        if ($newFile && $newFile->error === UPLOAD_ERR_OK) {
            $fileName = $this->saveFile($newFile, $claimCode);
            if ($fileName) {
                if ($originalFile) {
                    $this->deleteFile($originalFile);
                }
                $claimDetail->receipt_file = $fileName;
            } else {
                return false;
            }
        }
        // Remove existing file
        elseif ($removeExisting && !$newFile) {
            if ($originalFile) {
                $this->deleteFile($originalFile);
            }
            $claimDetail->receipt_file = null;
        }
        // Keep existing file (no changes needed)

        return true;
    }

    // Handle file for new record
    private function handleNewRecordFile($claimDetail, $newFile, $claimCode) {
        if ($newFile && $newFile->error === UPLOAD_ERR_OK) {
            $fileName = $this->saveFile($newFile, $claimCode);
            if ($fileName) {
                $claimDetail->receipt_file = $fileName;
                return true;
            } else {
                return false;
            }
        } else {
            $this->lastError = 'Receipt file is required for new claim details.';
            return false;
        }
    }

    // Save uploaded file
    private function saveFile($uploadedFile, $claimCode) {
        if (!$this->validateFile($uploadedFile)) {
            return false;
        }

        $uploadPath = Yii::getAlias('@frontend/uploads/claim/');
        if (!$this->ensureDirectoryExists($uploadPath)) {
            return false;
        }

        $fileName = $claimCode . '_' . uniqid() . '.' . $uploadedFile->extension;
        $filePath = $uploadPath . $fileName;

        if ($uploadedFile->saveAs($filePath)) {
            return $fileName;
        } else {
            $this->lastError = 'Failed to save uploaded file';
            return false;
        }
    }

    // Validate uploaded file
    private function validateFile($uploadedFile) {
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'pdf'];
        $allowedMimeTypes = ['image/jpeg', 'image/png', 'application/pdf'];

        if (!in_array(strtolower($uploadedFile->extension), $allowedExtensions) ||
                !in_array($uploadedFile->type, $allowedMimeTypes)) {
            $this->lastError = 'Invalid file type. Only JPG, PNG, and PDF files are allowed.';
            return false;
        }

        if ($uploadedFile->size > 5 * 1024 * 1024) {
            $this->lastError = 'File size too large. Maximum 5MB allowed.';
            return false;
        }

        return true;
    }

    // Ensure directory exists
    private function ensureDirectoryExists($path) {
        if (!is_dir($path)) {
            if (!mkdir($path, 0755, true)) {
                $this->lastError = 'Failed to create upload directory';
                return false;
            }
        }
        return true;
    }

    // Delete file
    public function deleteFile($fileName) {
        if (!$fileName)
            return;

        $uploadPath = Yii::getAlias('@frontend/uploads/claim/');
        $filePath = $uploadPath . $fileName;

        if (file_exists($filePath)) {
            if (!unlink($filePath)) {
                Yii::warning("Failed to delete file: " . $filePath, __METHOD__);
            }
        }
    }

    // Get last error message
    public function getLastError() {
        return $this->lastError;
    }
}
