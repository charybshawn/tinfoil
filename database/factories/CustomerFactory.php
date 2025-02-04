<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\CustomerGroup;
use Illuminate\Database\Eloquent\Factories\Factory;

class CustomerFactory extends Factory
{
    protected $model = Customer::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->company(),
            'email' => $this->faker->unique()->companyEmail(),
            'secondary_emails' => $this->faker->boolean(30) ? 
                [$this->faker->safeEmail(), $this->faker->safeEmail()] : 
                [],
            'phone' => $this->faker->phoneNumber(),
            'street_address' => $this->faker->streetAddress(),
            'city' => $this->faker->city(),
            'prov' => $this->faker->stateAbbr(),
            'country' => $this->faker->country(),
            'postal_code' => $this->faker->postcode(),
            'notes' => $this->faker->optional()->paragraph(),
            'status' => 'active',
            'group_id' => CustomerGroup::factory(),
        ];
    }

    /**
     * Configure the model factory.
     */
    public function configure()
    {
        return $this->afterMaking(function (Customer $customer) {
            //
        })->afterCreating(function (Customer $customer) {
            //
        });
    }
} 