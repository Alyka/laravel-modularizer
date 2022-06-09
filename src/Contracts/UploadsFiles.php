<?php

namespace Modularizer\Contracts;

use Illuminate\Http\UploadedFile;

interface UploadsFiles
{
    /**
     * Get the directory to upload the file to
     * -- relative to storage root directory.
     *
     * @return string
     */
    public function getPath();

    /**
     * Get the visibility scope of the file.
     *
     * @return string
     */
    public function getVisibility();

    /**
     * Get the storage disk to store the file to.
     *
     * @return string
     */
    public function getDisk();

    /**
     * Upload the given file to a storage disk.
     *
     * @param UploadedFile|string $source
     * @return array paths to the uploaded files
     */
    public function upload($source) : array;

    /**
     * Delete the given files from a storage disk.
     *
     * @param array $filePath
     * @return void
     */
    public function delete($filePath);
}
