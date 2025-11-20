<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="utf-8">
    <title>{{ $plan->title }}</title>
    <style>
        @page {
            margin: 2cm;
        }
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            direction: rtl;
            text-align: right;
            color: #333;
            line-height: 1.6;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #1F4788;
            padding-bottom: 20px;
        }
        .title {
            font-size: 28px;
            font-weight: bold;
            color: #1F4788;
            margin-bottom: 10px;
        }
        .company-name {
            font-size: 22px;
            font-weight: bold;
            color: #333;
            margin-bottom: 15px;
        }
        .details {
            background: #f5f5f5;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
        }
        .details-row {
            margin: 8px 0;
        }
        .label {
            font-weight: bold;
            color: #1F4788;
            display: inline-block;
            width: 150px;
        }
        .value {
            display: inline-block;
        }
        .section {
            margin: 30px 0;
        }
        .section-title {
            font-size: 20px;
            font-weight: bold;
            color: #1F4788;
            border-bottom: 2px solid #1F4788;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }
        .section-content {
            text-align: justify;
            line-height: 1.8;
        }
        .chapter {
            page-break-before: always;
            margin: 30px 0;
        }
        .chapter-title {
            font-size: 22px;
            font-weight: bold;
            color: #1F4788;
            margin-bottom: 20px;
            border-bottom: 2px solid #1F4788;
            padding-bottom: 10px;
        }
        .chapter-content {
            text-align: justify;
            line-height: 1.8;
            margin-bottom: 20px;
        }
        .ai-content {
            background: #f9f9f9;
            border-right: 4px solid #4CAF50;
            padding: 15px;
            margin: 15px 0;
        }
        .ai-label {
            font-size: 12px;
            font-style: italic;
            color: #666;
            margin-bottom: 10px;
        }
        .footer {
            text-align: center;
            margin-top: 50px;
            padding-top: 20px;
            border-top: 2px solid #1F4788;
            font-size: 12px;
            color: #666;
        }
        .page-number:after {
            content: counter(page);
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <div class="title">{{ $plan->title }}</div>
        <div class="company-name">{{ $plan->company_name }}</div>
    </div>

    <!-- Details -->
    <div class="details">
        <div class="details-row">
            <span class="label">نوع المشروع:</span>
            <span class="value">
                @switch($plan->project_type)
                    @case('new_business') مشروع جديد @break
                    @case('existing_expansion') توسع مشروع قائم @break
                    @case('franchise') فرنشايز @break
                    @case('startup') شركة ناشئة @break
                    @default {{ $plan->project_type }}
                @endswitch
            </span>
        </div>
        <div class="details-row">
            <span class="label">نوع الصناعة:</span>
            <span class="value">{{ $plan->industry_type }}</span>
        </div>
        <div class="details-row">
            <span class="label">الحالة:</span>
            <span class="value">
                @switch($plan->status)
                    @case('draft') مسودة @break
                    @case('in_progress') قيد التنفيذ @break
                    @case('review') مراجعة @break
                    @case('completed') مكتمل @break
                    @case('archived') مؤرشف @break
                    @default {{ $plan->status }}
                @endswitch
            </span>
        </div>
        <div class="details-row">
            <span class="label">نسبة الإكمال:</span>
            <span class="value">{{ $plan->completion_percentage }}%</span>
        </div>
        <div class="details-row">
            <span class="label">تاريخ الإنشاء:</span>
            <span class="value">{{ $plan->created_at->format('Y-m-d') }}</span>
        </div>
    </div>

    <!-- Vision & Mission -->
    @if($plan->vision)
    <div class="section">
        <div class="section-title">الرؤية</div>
        <div class="section-content">{{ $plan->vision }}</div>
    </div>
    @endif

    @if($plan->mission)
    <div class="section">
        <div class="section-title">الرسالة</div>
        <div class="section-content">{{ $plan->mission }}</div>
    </div>
    @endif

    <!-- Chapters -->
    @foreach($plan->chapters as $chapter)
    <div class="chapter">
        <div class="chapter-title">{{ $chapter->title }}</div>

        @if($chapter->content)
        <div class="chapter-content">
            {!! nl2br(e($chapter->content)) !!}
        </div>
        @endif

        @if($chapter->ai_generated_content)
        <div class="ai-content">
            <div class="ai-label">محتوى تم توليده بالذكاء الاصطناعي:</div>
            {!! nl2br(e($chapter->ai_generated_content)) !!}
        </div>
        @endif
    </div>
    @endforeach

    <!-- Footer -->
    <div class="footer">
        <p>تم إنشاء هذه الخطة بواسطة <strong>Business Plan Wizard</strong></p>
        <p>تاريخ الإنشاء: {{ now()->format('Y-m-d H:i') }}</p>
    </div>
</body>
</html>
