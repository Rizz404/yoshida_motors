<?php

namespace Database\Seeders;

use App\Models\AppraisalPhoto;
use App\Models\AppraisalRequest;
use App\Models\User;
use Illuminate\Database\Seeder;

class AppraisalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (User::count() === 0) {
            User::factory(5)->create();
        }

        $users = User::all();


        AppraisalRequest::factory(20)
            ->recycle($users)
            ->create()
            ->each(function ($request) {
                // 👇 Perhatikan tanda backslash "\" di depan rand()
                AppraisalPhoto::factory(fake()->numberBetween(1, 3))->create([
                    'appraisal_request_id' => $request->id
                ]);
            });
    }
}
