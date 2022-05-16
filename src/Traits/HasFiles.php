<?php

namespace Pageworks\LaravelFileManager\Traits;

use Pageworks\LaravelFileManager\Models\File;

trait HasFiles {
    public function files(){
        return $this->morphMany(File::class, 'owner');
    }
}