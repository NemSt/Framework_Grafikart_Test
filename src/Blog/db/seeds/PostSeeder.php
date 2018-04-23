<?php

use Phinx\Seed\AbstractSeed;

class PostSeeder extends AbstractSeed
{
    /**
     * Run Method.
     *
     * Write your database seeder using this method.
     *
     * More information on writing seeders is available here:
     * http://docs.phinx.org/en/latest/seeding.html
     */
    public function run()
    {
        // Seeding des catégories
        $data = [];
        $faker = \Faker\Factory::create('fr_CA');
        for ($i = 0; $i < 5; ++$i) {
            $data[] = [
                'name' => $faker->catchPhrase,
                'slug' => $faker->slug,
            ];
        }
        $this->table('categories')->insert($data)->save();

        // Seeding des articles
        $data = [];
        //$faker = \Faker\Factory::create('fr_CA');
        for ($i = 0; $i < 100; ++$i) { //pour avoir 100 faux articles
            $date = $faker->unixTime('now'); //pour que ce soit la même date
            $data[] = [
                'name' => $faker->catchPhrase,
                'slug' => $faker->slug,
                'category_id' => rand(1, 5),
                'content' => $faker->text(3000),
                'created_at' => date('Y-m-d', $date), //pour mettre la date au format sql
                'updated_at' => date('Y-m-d', $date)
            ];
        }
        $this->table('posts')->insert($data)->save();
    }
}
