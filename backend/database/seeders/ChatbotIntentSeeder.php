<?php

namespace Database\Seeders;

use App\Models\ChatbotIntent;
use Illuminate\Database\Seeder;

class ChatbotIntentSeeder extends Seeder
{
    public function run(): void
    {
        $intents = [
            [
                'intent_key' => 'greeting',
                'name' => 'Chào hỏi',
                'keywords' => ['xin chào', 'hello', 'hi', 'chào bạn'],
                'response_template' => 'Xin chào! Tôi là trợ lý ảo của Tri Thức Số. Tôi có thể giúp gì cho bạn?',
                'data_source' => null,
            ],
            [
                'intent_key' => 'find_document',
                'name' => 'Tìm tài liệu',
                'keywords' => ['tìm tài liệu', 'tìm sách về', 'tài liệu về', 'có sách', 'cách tìm kiếm'],
                'response_template' => 'Hiện có {{count}} tài liệu về {{topic}}:\n{{list}}',
                'data_source' => null,
            ],
            [
                'intent_key' => 'borrow_guide',
                'name' => 'Hướng dẫn mượn',
                'keywords' => ['làm sao mượn', 'làm sao để mượn', 'cách mượn', 'mượn sách', 'hướng dẫn mượn'],
                'response_template' => "Quy trình thư viện số:\n1) Đăng nhập\n2) Chọn tài liệu\n3) Nhấn tải/xem\n4) Tuân thủ quy định bản quyền.",
                'data_source' => null,
            ],
            [
                'intent_key' => 'opening_hours',
                'name' => 'Giờ mở cửa',
                'keywords' => ['giờ mở cửa', 'mấy giờ'],
                'response_template' => 'Giờ mở cửa thư viện: Thứ 2-6: 7h-21h, Thứ 7: 8h-17h, Chủ nhật nghỉ.',
                'data_source' => null,
            ],
            [
                'intent_key' => 'forgot_password',
                'name' => 'Quên mật khẩu',
                'keywords' => ['quên mật khẩu', 'mất mật khẩu'],
                'response_template' => 'Bạn dùng chức năng “Quên mật khẩu” trên trang đăng nhập và làm theo email hướng dẫn.',
                'data_source' => null,
            ],
            [
                'intent_key' => 'popular',
                'name' => 'Tài liệu phổ biến',
                'keywords' => ['tài liệu phổ biến', 'sách hot', 'phổ biến', 'nhiều người xem'],
                'response_template' => "Top tài liệu xem nhiều:\n{{popular_documents}}",
                'data_source' => 'documents.popular',
            ],
            [
                'intent_key' => 'register_guide',
                'name' => 'Đăng ký',
                'keywords' => ['đăng ký', 'tạo tài khoản'],
                'response_template' => 'Chọn Đăng ký, nhập họ tên, email và mật khẩu; mặc định là tài khoản sinh viên.',
                'data_source' => null,
            ],
            [
                'intent_key' => 'account_type',
                'name' => 'Loại tài khoản',
                'keywords' => ['sinh viên', 'giảng viên', 'khác nhau'],
                'response_template' => 'Sinh viên và giảng viên có quyền xem/tải khác nhau; chi tiết do quản trị cấu hình.',
                'data_source' => null,
            ],
            [
                'intent_key' => 'contact',
                'name' => 'Liên hệ',
                'keywords' => ['liên hệ', 'thủ thư', 'hotline', 'liên hệ thư viện'],
                'response_template' => 'Thông tin liên hệ thư viện: SĐT 0123 456 789, email support@tts.local, địa chỉ: Phòng Thư viện - Tri Thức Số.',
                'data_source' => null,
            ],
            [
                'intent_key' => 'categories',
                'name' => 'Danh mục',
                'keywords' => ['danh mục', 'thể loại'],
                'response_template' => "Danh mục chính:\n{{categories_list}}",
                'data_source' => null,
            ],
            [
                'intent_key' => 'new_documents',
                'name' => 'Tài liệu mới',
                'keywords' => ['mới nhất', 'cập nhật', 'tài liệu mới', 'mới'],
                'response_template' => "Tài liệu mới cập nhật:\n{{new_documents}}",
                'data_source' => null,
            ],
            [
                'intent_key' => 'thank_you',
                'name' => 'Cảm ơn',
                'keywords' => ['cảm ơn', 'thanks'],
                'response_template' => 'Rất vui được hỗ trợ bạn!',
                'data_source' => null,
            ],
            [
                'intent_key' => 'goodbye',
                'name' => 'Tạm biệt',
                'keywords' => ['tạm biệt', 'bye'],
                'response_template' => 'Chúc bạn học tập hiệu quả. Hẹn gặp lại!',
                'data_source' => null,
            ],
            [
                'intent_key' => 'about',
                'name' => 'Giới thiệu',
                'keywords' => ['giới thiệu', 'là gì', 'tri thức số'],
                'response_template' => 'Tri Thức Số (TTS-2026) là thư viện điện tử phục vụ sinh viên và giảng viên.',
                'data_source' => null,
            ],
            [
                'intent_key' => 'fallback',
                'name' => 'Fallback',
                'keywords' => [],
                'response_template' => 'Tôi chưa hiểu câu hỏi. Bạn có thể thử: 1) Tìm theo tên sách 2) Xem danh mục 3) Gõ “tài liệu phổ biến”.',
                'data_source' => null,
            ],
        ];

        foreach ($intents as $row) {
            ChatbotIntent::updateOrCreate(
                ['intent_key' => $row['intent_key']],
                [
                    'name' => $row['name'],
                    'keywords' => $row['keywords'],
                    'response_template' => $row['response_template'],
                    'data_source' => $row['data_source'],
                    'is_active' => true,
                ]
            );
        }
    }
}
