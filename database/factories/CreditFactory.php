<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\User;

class CreditFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            // 'user_id' =>  User::factory(),
            'amount' =>  intval(mt_rand(10000,5000000)/10000) * 10000,
            'balance' =>  intval(mt_rand(3000000,5000000)/10000) * 10000,
            'status' => 0,
        ];
    }

    
}
