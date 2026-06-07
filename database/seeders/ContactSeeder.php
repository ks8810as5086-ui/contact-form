<?php

namespace Database\Seeders;

use App\Models\Contact;
use App\Models\Tag;
use Illuminate\Database\Seeder;

class ContactSeeder extends Seeder
{
    public function run(): void
    {
        Contact::factory()->count(20)->create()->each(function ($contact) {

            $tags = Tag::inRandomOrder()->limit(rand(1, 3))->get();

            $contact->tags()->attach($tags);
        });
    }
}
