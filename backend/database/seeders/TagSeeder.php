<?php

namespace Database\Seeders;

use App\Models\Tag;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TagSeeder extends Seeder
{
    /** @var list<string> */
    protected array $names = [
        'Java',
        'Python',
        'Vue',
        'Laravel',
        'AI',
        'Machine Learning',
        'Deep Learning',
        'Database',
        'Algorithm',
        'Web',
        'Mobile',
        'Network',
        'Security',
        'Marketing',
        'Finance',
        'English',
        'IELTS',
        'Data Science',
        'Docker',
        'Kubernetes',
        'Blockchain',
        'Cloud',
        'DevOps',
        'Linux',
        'PHP',
        'JavaScript',
    ];

    public function run(): void
    {
        foreach ($this->names as $name) {
            Tag::firstOrCreate(
                ['slug' => Str::slug($name)],
                ['name' => $name]
            );
        }
    }
}
