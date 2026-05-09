<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /** @var list<array{name:string, children:list<string>}> */
    protected array $tree = [
        ['name' => 'Công nghệ thông tin', 'children' => ['Lập trình Web', 'Trí tuệ nhân tạo']],
        ['name' => 'Kinh tế', 'children' => ['Tài chính', 'Marketing']],
        ['name' => 'Ngôn ngữ', 'children' => ['Tiếng Anh', 'Tiếng Hàn']],
        ['name' => 'Khoa học', 'children' => ['Vật lý', 'Hóa học']],
        ['name' => 'Văn học', 'children' => ['Tiểu thuyết', 'Thơ']],
        ['name' => 'Giáo dục', 'children' => ['Phương pháp dạy học', 'Đánh giá học tập']],
        ['name' => 'Y học', 'children' => ['Nội khoa', 'Ngoại khoa']],
        ['name' => 'Pháp luật', 'children' => ['Luật dân sự', 'Luật kinh doanh']],
    ];

    public function run(): void
    {
        foreach ($this->tree as $i => $node) {
            $parent = Category::create([
                'parent_id' => null,
                'name' => $node['name'],
                'slug' => Str::slug($node['name']).'-'.$i,
                'icon' => 'mdi-book-multiple',
                'description' => null,
                'sort_order' => $i,
            ]);

            foreach ($node['children'] as $j => $childName) {
                Category::create([
                    'parent_id' => $parent->id,
                    'name' => $childName,
                    'slug' => Str::slug($parent->name.'-'.$childName).'-'.$j,
                    'icon' => 'mdi-folder-outline',
                    'description' => null,
                    'sort_order' => $j,
                ]);
            }
        }
    }
}
