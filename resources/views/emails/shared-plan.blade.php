<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تمت مشاركة خطة عمل معك</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            direction: rtl;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 30px;
            text-align: center;
            color: white;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .content {
            padding: 30px;
        }
        .plan-info {
            background-color: #f8f9fa;
            border-radius: 6px;
            padding: 20px;
            margin: 20px 0;
        }
        .plan-info h2 {
            margin: 0 0 10px 0;
            color: #333;
            font-size: 20px;
        }
        .plan-info p {
            margin: 5px 0;
            color: #666;
        }
        .message-box {
            background-color: #e3f2fd;
            border-right: 4px solid #2196F3;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .message-box p {
            margin: 0;
            color: #1976D2;
        }
        .button {
            display: inline-block;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-decoration: none;
            padding: 15px 40px;
            border-radius: 6px;
            margin: 20px 0;
            font-weight: bold;
            text-align: center;
        }
        .button:hover {
            opacity: 0.9;
        }
        .footer {
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
            color: #666;
            font-size: 14px;
        }
        .divider {
            height: 1px;
            background-color: #e0e0e0;
            margin: 20px 0;
        }
        .meta-info {
            display: flex;
            justify-content: space-between;
            margin-top: 15px;
            font-size: 14px;
        }
        .meta-item {
            color: #666;
        }
        .icon {
            display: inline-block;
            margin-left: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>📊 تمت مشاركة خطة عمل معك</h1>
        </div>

        <!-- Content -->
        <div class="content">
            <p>مرحباً،</p>
            <p>{{ $sender->name }} شارك معك خطة عمل على منصة معالج خطط الأعمال.</p>

            <!-- Plan Info -->
            <div class="plan-info">
                <h2>{{ $plan->title }}</h2>
                @if($plan->company_name)
                    <p><strong>اسم الشركة:</strong> {{ $plan->company_name }}</p>
                @endif
                @if($plan->description)
                    <p><strong>الوصف:</strong> {{ $plan->description }}</p>
                @endif

                <div class="meta-info">
                    @if($plan->project_type)
                        <div class="meta-item">
                            <span class="icon">🏷️</span> {{ $plan->project_type }}
                        </div>
                    @endif
                    @if($plan->industry_type)
                        <div class="meta-item">
                            <span class="icon">💼</span> {{ $plan->industry_type }}
                        </div>
                    @endif
                </div>
            </div>

            <!-- Custom Message -->
            @if($message)
                <div class="message-box">
                    <p><strong>رسالة من {{ $sender->name }}:</strong></p>
                    <p>{{ $message }}</p>
                </div>
            @endif

            <div class="divider"></div>

            <!-- CTA Button -->
            <div style="text-align: center;">
                <a href="{{ $share->getShareUrl() }}" class="button">
                    عرض خطة العمل
                </a>
            </div>

            <!-- Share Info -->
            <div style="margin-top: 30px; padding: 15px; background-color: #fff3e0; border-radius: 6px;">
                <p style="margin: 0; font-size: 14px; color: #e65100;">
                    <strong>ℹ️ معلومات المشاركة:</strong>
                </p>
                <ul style="margin: 10px 0; padding-right: 20px; color: #666; font-size: 14px;">
                    <li>نوع المشاركة: {{ $share->type === 'public' ? 'عامة' : 'خاصة (محمية بكلمة مرور)' }}</li>
                    <li>الصلاحيات: {{ $share->permission === 'view' ? 'عرض فقط' : ($share->permission === 'comment' ? 'عرض + تعليق' : 'عرض + تعليق + تحرير') }}</li>
                    @if($share->expires_at)
                        <li>صلاحية الرابط: حتى {{ $share->expires_at->format('Y/m/d') }}</li>
                    @else
                        <li>صلاحية الرابط: بدون تاريخ انتهاء</li>
                    @endif
                </ul>
            </div>

            @if($share->type === 'private')
                <div style="margin-top: 15px; padding: 15px; background-color: #ffebee; border-radius: 6px;">
                    <p style="margin: 0; font-size: 14px; color: #c62828;">
                        <strong>🔒 ملاحظة:</strong> هذه مشاركة خاصة تتطلب كلمة مرور. يرجى التواصل مع {{ $sender->name }} للحصول على كلمة المرور.
                    </p>
                </div>
            @endif

            <div class="divider"></div>

            <p style="font-size: 14px; color: #666;">
                إذا كان لديك أي استفسارات، يمكنك التواصل مع {{ $sender->name }} مباشرة.
            </p>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>هذه رسالة آلية من منصة معالج خطط الأعمال</p>
            <p>© {{ date('Y') }} جميع الحقوق محفوظة</p>
            <p style="margin-top: 10px;">
                <a href="https://start.al-investor.com" style="color: #667eea; text-decoration: none;">زيارة الموقع</a>
            </p>
        </div>
    </div>
</body>
</html>
