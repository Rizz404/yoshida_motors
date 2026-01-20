<?php

namespace Database\Factories;

use App\Models\AppraisalRequest;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AppraisalPhoto>
 */
class AppraisalPhotoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // 1. Pilih angka acak antara 1 sampai 6 (sesuai jumlah file Kakak)
        $fileNumber = fake()->numberBetween(1, 6);

        return [
            'appraisal_request_id' => AppraisalRequest::factory(),

            // Pilih kategori label secara acak
            'category_name' => fake()->randomElement([
                'Tampak Depan',
                'Tampak Samping',
                'Tampak Belakang',
                'Interior',
                'Mesin'
            ]),

            // 2. Arahkan ke file JFIF yang sudah ada di folder public
            // Pastikan file-nya ada di folder: storage/app/public/appraisal_photos/
            'image_path' => "appraisal_photos/cars-{$fileNumber}.jfif",
        ];
    }
}
