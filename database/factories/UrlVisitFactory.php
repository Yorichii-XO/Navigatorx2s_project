<?php

namespace Database\Factories;

use App\Models\UrlVisit;
use Illuminate\Database\Eloquent\Factories\Factory;

class UrlVisitFactory extends Factory
{
    protected $model = UrlVisit::class;

    public function definition()
    {
        return [
            'url' => $this->faker->url,
            'screenshot' => $this->faker->text,
            'visit_time' => $this->faker->dateTime(),
            'duration' => $this->faker->numberBetween(1, 60), // duration in minutes
        ];
    }
}
