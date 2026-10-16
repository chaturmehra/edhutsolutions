<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\CorporateImage;
use Illuminate\Database\Seeder;

class ClientSeeder extends Seeder
{
    public function run(): void
    {
        $clients = [
            ['Accenture', 'assets/images/clients/accenture.png'],
            ['Atlassian', 'assets/images/clients/atlassian.png'],
            ['AECOM', 'assets/images/clients/aecom.jpg'],
            ['Batelco', 'assets/images/clients/batelco.png'],
        ];

        foreach ($clients as $index => [$name, $logo]) {
            Client::query()->updateOrCreate(['name' => $name], [
                'logo' => $logo,
                'sort_order' => $index + 1,
                'status' => 'active',
            ]);
        }

        $photos = [
            ['EdHut classroom training session', 'assets/images/gallery/training-room.jpg'],
            ['Professionals in an EdHut workshop', 'assets/images/gallery/team-session.jpg'],
        ];

        foreach ($photos as $index => [$title, $image]) {
            CorporateImage::query()->updateOrCreate(['image' => $image], [
                'title' => $title,
                'alt_text' => $title,
                'sort_order' => $index + 1,
                'status' => 'active',
            ]);
        }
    }
}
