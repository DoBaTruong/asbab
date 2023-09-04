<?php

namespace Database\Factories;

use App\Models\Model;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Order;
use App\Models\User;

class OrderFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Order::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $userID = random_int(1, 10);
        $user = User::find($userID);
        return [
            'name' => $user->name,
            'phone' => $user->phone,
            'address' => $user->address,
            'mail' => $user->email,
            'user_id' => $userID,
            'code' => substr(md5(microtime()),rand(0,26),6),
            'status' => 3,
            'coupon_id' => random_int(1,2),
            'ship_id' => random_int(11,15),
            'fee_ship' => random_int(20,100),
            'amount' => random_int(3000,15000),
            'paymethod' => random_int(0,3),
            'created_at' => $this->faker->dateTimeBetween('2020-01-01', '2020-12-30'),
            'updated_at' => $this->faker->dateTimeBetween('2020-01-01', '2020-12-30'),
        ];
    }
}
