# Uploaded Files Package Details

This document provides a detailed technical overview of the `uploaded-files` package components.

## 1. Models
### `UploadedFile`
Located at `src/app/Models/UploadedFile.php`.
This is the core model that represents a file in the system. It handles the business logic for storing, updating, deleting, and syncing files.
- **Relations**: Uses polymorphic relation (`morphTo`) via the `source()` method to attach a file to any model.
- **Key Methods**:
  - `store()`: Accepts single or multiple files, processes them, handles image resizing/compression (if applicable), and stores them on the `public` disk.
  - `syncFiles()`: Synchronizes a collection of uploaded files against incoming requests to delete or retain files appropriately.
  - `deleteFile()`: Deletes the file record from the database and its corresponding physical file from the storage.
  - `retrievePath()`: Generates a 1-hour valid signed URL for secure file downloading.
  - Internal helpers: `handleFileUpload`, `resizeImage`, `validateResizeTarget`, `makeUrlSafe` for safe filenames.

## 2. Controllers
### `UploadedFileController`
Located at `src/app/Http/Controllers/UploadedFileController.php`.
Responsible for serving files to the user securely.
- **Key Methods**:
  - `download(Request $request, $id, $filename = null)`: Validates the request signature (preventing unauthorized downloads), fetches the file by ID, verifies its existence on the disk, and returns the file as a download response.

## 3. Routes
Located at `src/routes/api.php`.
- **`GET files/download/{id}/{filename?}`**: A named route (`files.download`) mapped to `UploadedFileController@download` for handling signed URL download requests.

## 4. Migrations
Located at `src/database/migrations`.
- **Create Table**: Creates the `uploaded_files` table to store metadata such as file paths, types, sizes, names, and polymorphic relationships.
- **Add Safe Filename**: Adds `safe_filename` to ensure filenames are URL safe during downloads.

## 5. Jobs
*No jobs are currently defined in this package.*
