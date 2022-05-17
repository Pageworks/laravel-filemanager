<?php

namespace Pageworks\LaravelFileManager\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    use HasFactory;

    protected $fillable = [
        'file_name',
        'file_path',
        'dir_path',
        'size',
    ];

    // what factory class to use
    protected static function newFactory(){
        return \Pageworks\LaravelFileManager\Database\Factories\FileFactory::new();
    }

    // Polymorphic relationship to a "user" table.
    public function owner(){
        return $this->morphTo();
    }
}
