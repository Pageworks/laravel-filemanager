<?php

namespace Pageworks\LaravelFileManager\Interfaces;

use Pageworks\LaravelFileManager\FilePath;
use Pageworks\LaravelFileManager\Models\File;

interface FileRepositoryInterface {

    public function listItemsInDir(FilePath $dir_path);
    public function makeDir(FilePath $dir_path, string $name);
    public function rename(FilePath $dir_path, string $name);
    public function downloadFile(FilePath $file_path);
    public function deleteFile(FilePath $file_path);
    public function addModel(FilePath $file_path);
    public function removeModel(File $file);

}