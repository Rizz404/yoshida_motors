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
        // 1. Pilih angka acak antara 1 sampai 6
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

            // 2. Instruct for image
            // NOTE FOR USER: Please create directory storage/app/public/seed_images/
            // and place some dummy images named car1.jpg, car2.jpg, ... car6.jpg inside it.
            // When symlinked, they will be accessible under public/storage/seed_images/carX.jpg
            'image_path' => "seed_images/car{$fileNumber}.jpg",
        ];
    }
}
