<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Chốt chặn ở tầng DB cho `targeting_mode`.
 *
 * Cột này KHÔNG có form, không validation, không API, không lệnh console nào ghi — cách
 * duy nhất bật segment là gõ tay một câu UPDATE. Chốt ở PHP (Campaign::cheDoNhamMuc) chỉ
 * bắt được khi app ĐỌC; câu UPDATE gõ sai vẫn nằm im trong DB tới lần gửi sau. CHECK bắt
 * ngay lúc GHI, tại đúng nơi người vận hành đang gõ.
 *
 * COLLATE utf8mb4_bin là cố ý: collation mặc định của bảng (utf8mb4_unicode_ci) KHÔNG phân
 * biệt hoa thường, nên `IN ('legacy','segment')` trần vẫn nhận 'Segment' và 'SEGMENT'.
 * cheDoNhamMuc() hiểu đúng những giá trị đó, nhưng ba chỗ trong preview.blade.php vẫn so
 * chuỗi trần `=== 'segment'` — lưu 'Segment' là màn hình hiện giao diện legacy (accordion
 * Locations, tuỳ chọn "All subscribers") trong khi đường gửi chạy segment. Bắt cột chỉ chứa
 * đúng hai chuỗi chuẩn thì khe đó không mở ra được.
 *
 * MariaDB 10.11 (prod) thi hành CHECK thật. Giới hạn đã đo: so sánh VARCHAR bỏ qua khoảng
 * trắng ĐUÔI, nên 'segment ' vẫn lọt — cheDoNhamMuc() trim() nên vô hại. ' segment' (đầu),
 * 'Segment', 'segments', '' đều bị chặn.
 */
return new class extends Migration
{
    private const TEN = 'sendportal_campaigns_targeting_mode_check';

    public function up(): void
    {
        // Thêm CHECK lên bảng đang có dòng vi phạm thì ALTER hỏng giữa chừng với thông báo
        // của MariaDB, không nói được dòng nào. Đếm trước bằng CHÍNH biểu thức sẽ dùng
        // (nhị phân, không phải so ci) rồi dừng với câu người sửa đọc được.
        $xau = DB::table('sendportal_campaigns')
            ->whereRaw("targeting_mode COLLATE utf8mb4_bin NOT IN ('legacy','segment')")
            ->count();

        if ($xau > 0) {
            $mau = DB::table('sendportal_campaigns')
                ->whereRaw("targeting_mode COLLATE utf8mb4_bin NOT IN ('legacy','segment')")
                ->limit(10)
                ->pluck('targeting_mode', 'id')
                ->all();

            throw new RuntimeException(
                'Không thêm được CHECK: ' . $xau . ' campaign có targeting_mode ngoài '
                . '("legacy","segment"). Sửa dữ liệu trước. Ví dụ (id => giá trị): '
                . json_encode($mau, JSON_UNESCAPED_UNICODE)
            );
        }

        DB::statement(
            'ALTER TABLE sendportal_campaigns ADD CONSTRAINT ' . self::TEN
            . " CHECK (targeting_mode COLLATE utf8mb4_bin IN ('legacy','segment'))"
        );
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE sendportal_campaigns DROP CONSTRAINT ' . self::TEN);
    }
};
