<?php

namespace Rahuldhole\Betterstack\Seeders;

use Faker\Factory;

class UserSeeder
{
    public function run($pdo)
    {
        $faker = Factory::create();

        $stmt = $pdo->prepare(
            "INSERT INTO users (name, email, city, phone, created_at)
             VALUES (?, ?, ?, ?, NOW())"
        );

        $count = 0;
        for ($i = 0; $i < 1000; $i++) {
            $stmt->execute([
                $faker->name(),
                $faker->unique()->safeEmail(),
                $faker->city(),
                $faker->phoneNumber()
            ]);
            $count++;
        }
        return $count;
    }
}