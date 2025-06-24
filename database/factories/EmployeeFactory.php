<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\Tenants\Employee;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Company>
 */
class EmployeeFactory extends Factory
{
    /** ✨ EZ A FONTOS SOR ✨ */
    protected $model = Employee::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name,
            'position' => $this->faker->jobTitle,
            'email' => $this->faker->unique()->safeEmail,
            'active' => $this->faker->boolean(90),
            'company_id' => Company::inRandomOrder()->first()->id,
        ];
    }
}
