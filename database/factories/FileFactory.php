<?php

namespace Pageworks\LaravelFileManager\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Pageworks\LaravelFileManager\Models\File;

class FileFactory extends Factory
{

    protected $model = File::class;

    public function definition()
    {
        return [
            'file_path' => $this->faker->name(),
            'tuskey' => $this->faker->creditCardNumber(),
        ];
    }
}
