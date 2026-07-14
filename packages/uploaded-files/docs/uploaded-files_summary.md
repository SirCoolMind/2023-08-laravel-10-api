# Uploaded Files Package Summary

The `uploaded-files` package provides a robust solution for managing file uploads, processing, and secure retrieval within the Laravel application. 

## Key Features
- **File Uploading**: Allows storing single or multiple files associated with an Eloquent model via polymorphic relations.
- **Image Processing**: Integrates with Intervention Image to resize and compress image files (JPEG, PNG, WebP) upon upload.
- **Secure Downloads**: Provides signed URLs to download files securely and prevents unauthorized access.
- **File Synchronization**: Offers mechanisms to sync files based on the requested input.
- **Automatic Deletion**: Ensures that physical files on the disk are removed when the corresponding model record is deleted.
