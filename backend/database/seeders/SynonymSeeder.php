<?php

namespace Database\Seeders;

use App\Models\Synonym;
use Illuminate\Database\Seeder;

class SynonymSeeder extends Seeder
{
    public function run(): void
    {
        $rows = [
            ['keyword' => 'ai', 'synonyms' => ['trí tuệ nhân tạo', 'machine learning', 'học máy', 'deep learning']],
            ['keyword' => 'lập trình', 'synonyms' => ['coding', 'programming', 'code']],
            ['keyword' => 'web', 'synonyms' => ['website', 'frontend', 'backend', 'fullstack']],
            ['keyword' => 'sách', 'synonyms' => ['tài liệu', 'giáo trình', 'ebook']],
            ['keyword' => 'database', 'synonyms' => ['cơ sở dữ liệu', 'sql', 'nosql']],
            ['keyword' => 'mobile', 'synonyms' => ['ứng dụng di động', 'android', 'ios']],
            ['keyword' => 'kinh tế', 'synonyms' => ['economics', 'tài chính', 'thị trường']],
            ['keyword' => 'anh văn', 'synonyms' => ['english', 'tiếng anh', 'ielts']],
            ['keyword' => 'toán', 'synonyms' => ['mathematics', 'đại số', 'giải tích']],
            ['keyword' => 'mạng', 'synonyms' => ['network', 'tcp/ip', 'bảo mật mạng']],
            ['keyword' => 'thiết kế', 'synonyms' => ['design', 'ui', 'ux']],
            ['keyword' => 'cloud', 'synonyms' => ['đám mây', 'aws', 'azure']],
            ['keyword' => 'docker', 'synonyms' => ['container', 'kubernetes']],
            ['keyword' => 'python', 'synonyms' => ['pandas', 'numpy', 'django']],
            ['keyword' => 'javascript', 'synonyms' => ['typescript', 'node', 'vue']],
        ];

        foreach ($rows as $row) {
            Synonym::updateOrCreate(['keyword' => $row['keyword']], ['synonyms' => $row['synonyms']]);
        }
    }
}
