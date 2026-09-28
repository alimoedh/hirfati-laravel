<?php

namespace App\Services;

class AutoReplyService
{
    private array $autoReplies = [
        'سعر'   => 'سعر الخدمة يختلف حسب نوع العمل، يمكنك إرسال تفاصيل المشكلة للتقييم',
        'موعد'  => 'يمكن تحديد الموعد المناسب لك بعد قبول الطلب',
        'شكراً' => 'العفو، بخدمتك دائماً',
        'سلام'  => 'وعليكم السلام ورحمة الله',
        'متأخر' => 'اعتذر عن التأخير، سأصل قريباً',
    ];

    public function getReply(string $message): ?string
    {
        foreach ($this->autoReplies as $keyword => $reply) {
            if (mb_strpos($message, $keyword) !== false) {
                return $reply;
            }
        }
        return null;
    }
}
