<?php
namespace Database\Factories;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use Illuminate\Support\Str;

class UserFactory extends Factory {
    protected $model = User::class;

    public function definition() {
        return [
            'name' => \$this->faker->name,
            'email' => \$this->faker->unique()->safeEmail,
            'password' => bcrypt('password'),
            'locale' => 'en',
            'active' => true,
        ];
    }
}
