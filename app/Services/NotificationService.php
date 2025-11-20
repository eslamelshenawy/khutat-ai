<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Collection;

class NotificationService
{
    /**
     * Create a new notification
     */
    public function create(
        User $user,
        string $type,
        string $title,
        string $message,
        ?string $actionUrl = null,
        ?string $actionText = null,
        string $priority = 'medium',
        ?array $data = null
    ): Notification {
        return Notification::create([
            'user_id' => $user->id,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'action_url' => $actionUrl,
            'action_text' => $actionText,
            'icon' => $this->getIconForType($type),
            'priority' => $priority,
            'data' => $data,
        ]);
    }

    /**
     * Send business plan created notification
     */
    public function notifyBusinessPlanCreated(User $user, $businessPlan): Notification
    {
        return $this->create(
            user: $user,
            type: 'plan_created',
            title: 'تم إنشاء خطة عمل جديدة',
            message: "تم إنشاء خطة العمل '{$businessPlan->title}' بنجاح.",
            actionUrl: route('business-plans.show', $businessPlan),
            actionText: 'عرض الخطة',
            priority: 'low'
        );
    }

    /**
     * Send business plan completed notification
     */
    public function notifyBusinessPlanCompleted(User $user, $businessPlan): Notification
    {
        return $this->create(
            user: $user,
            type: 'plan_completed',
            title: 'خطة العمل مكتملة',
            message: "تهانينا! خطة العمل '{$businessPlan->title}' أصبحت مكتملة بنسبة 100%.",
            actionUrl: route('business-plans.show', $businessPlan),
            actionText: 'عرض الخطة',
            priority: 'high'
        );
    }

    /**
     * Send AI recommendation notification
     */
    public function notifyAiRecommendation(User $user, $businessPlan, int $count): Notification
    {
        return $this->create(
            user: $user,
            type: 'ai_recommendation',
            title: 'توصيات جديدة من الذكاء الاصطناعي',
            message: "تم إنشاء {$count} توصية لتحسين خطة العمل '{$businessPlan->title}'.",
            actionUrl: route('business-plans.show', $businessPlan),
            actionText: 'عرض التوصيات',
            priority: 'medium'
        );
    }

    /**
     * Send AI analysis complete notification
     */
    public function notifyAiAnalysisComplete(User $user, $businessPlan, ?int $score): Notification
    {
        $message = $score
            ? "تم تحليل خطة العمل '{$businessPlan->title}'. النتيجة: {$score}/100"
            : "تم تحليل خطة العمل '{$businessPlan->title}' بنجاح.";

        return $this->create(
            user: $user,
            type: 'ai_analysis',
            title: 'اكتمل التحليل بالذكاء الاصطناعي',
            message: $message,
            actionUrl: route('business-plans.show', $businessPlan),
            actionText: 'عرض التحليل',
            priority: 'medium'
        );
    }

    /**
     * Send chapter updated notification
     */
    public function notifyChapterUpdated(User $user, $chapter): Notification
    {
        return $this->create(
            user: $user,
            type: 'chapter_updated',
            title: 'تم تحديث فصل',
            message: "تم تحديث الفصل '{$chapter->title}' في خطة العمل.",
            actionUrl: route('business-plans.show', $chapter->businessPlan),
            actionText: 'عرض الفصل',
            priority: 'low'
        );
    }

    /**
     * Send export ready notification
     */
    public function notifyExportReady(User $user, $businessPlan, string $format): Notification
    {
        $formatName = match($format) {
            'pdf' => 'PDF',
            'word', 'docx' => 'Word',
            'excel', 'xlsx' => 'Excel',
            default => $format,
        };

        return $this->create(
            user: $user,
            type: 'export_ready',
            title: 'ملف التصدير جاهز',
            message: "تم تصدير خطة العمل '{$businessPlan->title}' إلى صيغة {$formatName}.",
            actionUrl: route('business-plans.export', ['businessPlan' => $businessPlan, 'format' => $format]),
            actionText: 'تحميل الملف',
            priority: 'medium'
        );
    }

    /**
     * Get unread notifications for a user
     */
    public function getUnread(User $user, ?int $limit = null): Collection
    {
        $query = $user->notifications()
            ->unread()
            ->latest();

        if ($limit) {
            $query->limit($limit);
        }

        return $query->get();
    }

    /**
     * Get all notifications for a user
     */
    public function getAll(User $user, int $perPage = 20)
    {
        return $user->notifications()
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Mark notification as read
     */
    public function markAsRead(Notification $notification): bool
    {
        return $notification->markAsRead();
    }

    /**
     * Mark all notifications as read for a user
     */
    public function markAllAsRead(User $user): int
    {
        return $user->notifications()
            ->unread()
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
    }

    /**
     * Delete notification
     */
    public function delete(Notification $notification): bool
    {
        return $notification->delete();
    }

    /**
     * Delete all read notifications for a user
     */
    public function deleteAllRead(User $user): int
    {
        return $user->notifications()
            ->read()
            ->delete();
    }

    /**
     * Get unread count for a user
     */
    public function getUnreadCount(User $user): int
    {
        return $user->notifications()
            ->unread()
            ->count();
    }

    /**
     * Get icon based on notification type
     */
    protected function getIconForType(string $type): string
    {
        return match($type) {
            'plan_created' => 'document-plus',
            'plan_completed' => 'check-circle',
            'plan_updated' => 'pencil',
            'ai_recommendation' => 'light-bulb',
            'ai_analysis' => 'chart-bar',
            'chapter_updated' => 'document-text',
            'export_ready' => 'download',
            'success' => 'check-circle',
            'warning' => 'exclamation-triangle',
            'error' => 'x-circle',
            default => 'bell',
        };
    }

    /**
     * Send system notification
     */
    public function sendSystemNotification(
        User $user,
        string $title,
        string $message,
        string $type = 'info',
        string $priority = 'low'
    ): Notification {
        return $this->create(
            user: $user,
            type: $type,
            title: $title,
            message: $message,
            priority: $priority
        );
    }
}
