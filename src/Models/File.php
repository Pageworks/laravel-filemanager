<?php

namespace Pageworks\LaravelFileManager\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    use HasFactory;

    /**
     * Polymorphic relationship to a "user" table.
     *
     * @return void
     */
    public function owner(){
        return $this->morphTo();
    }
}
