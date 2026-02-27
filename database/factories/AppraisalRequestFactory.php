<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AppraisalRequest>
 */
class AppraisalRequestFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $brands = ['Honda', 'Toyota', 'Suzuki', 'Mitsubishi', 'Hyundai', 'Wuling'];
        $models = ['Civic Turbo', 'Jazz RS', 'Avanza Veloz', 'Xpander Cross', 'Creta', 'Almaz'];

        // Kita random statusnya
        $status = fake()->randomElement(['draft', 'submitted', 'under_review', 'completed']);

        return [
            // Kita ambil user acak nanti di Seeder biar lebih efisien
            'user_id' => User::factory(),

            'vehicle_brand' => fake()->randomElement($brands),
            'vehicle_model' => fake()->randomElement($models),
            'year_manufacture' => fake()->numberBetween(2015, 2025),
            'description' => fake()->paragraph(2),

            'status' => $status,

            // Logic: Kalau completed, harus ada harganya dong~
            'final_price' => $status === 'completed'
                ? fake()->numberBetween(150, 500) * 10000 // 150万円 - 500万円
                : null,

            'admin_note' => ($status === 'completed' || $status === 'under_review')
                ? fake()->sentence(10)
                : null,

            'price_valid_until' => $status === 'completed'
                ? fake()->dateTimeBetween('now', '+30 days')
                : null,
        ];
    }
}
