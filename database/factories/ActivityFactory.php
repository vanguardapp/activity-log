<?php

namespace Vanguard\UserActivity\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Vanguard\UserActivity\Activity;
use Vanguard\User;

class ActivityFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Activity::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => function () {
                return User::factory()->create()->id;
            },
            'description' => substr($this->faker->paragraph, 0, 255),
            'ip_address' => $this->faker->ipv4,
            'user_agent' => $this->faker->userAgent
        ];
    }
}
