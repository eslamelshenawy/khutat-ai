<x-layouts.app>
    <x-slot name="title">البيانات المالية - {{ $businessPlan->title }}</x-slot>

<div class="container mx-auto px-4 py-8 max-w-7xl">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">البيانات المالية</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-2">{{ $businessPlan->title }}</p>
            </div>
            <a href="{{ route('business-plans.show', $businessPlan) }}" class="bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 px-4 py-2 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition">
                ← العودة للخطة
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
        {{ session('success') }}
    </div>
    @endif

    <!-- Add Financial Data Form -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 mb-8">
        <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6">إضافة/تحديث بيانات مالية</h2>

        <form action="{{ route('financial.store', $businessPlan) }}" method="POST">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Year -->
                <div>
                    <label class="block text-gray-700 dark:text-gray-300 font-bold mb-2">السنة *</label>
                    <input type="number" name="year" min="2020" max="2050" value="{{ old('year', date('Y')) }}" required
                           class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-2 dark:bg-gray-700 dark:text-white">
                </div>

                <!-- Revenue -->
                <div>
                    <label class="block text-gray-700 dark:text-gray-300 font-bold mb-2">الإيرادات *</label>
                    <input type="number" step="0.01" name="revenue" value="{{ old('revenue', 0) }}" required
                           class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-2 dark:bg-gray-700 dark:text-white">
                </div>

                <!-- Cost of Goods Sold -->
                <div>
                    <label class="block text-gray-700 dark:text-gray-300 font-bold mb-2">تكلفة البضاعة المباعة *</label>
                    <input type="number" step="0.01" name="cost_of_goods_sold" value="{{ old('cost_of_goods_sold', 0) }}" required
                           class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-2 dark:bg-gray-700 dark:text-white">
                </div>

                <!-- Operating Expenses -->
                <div>
                    <label class="block text-gray-700 dark:text-gray-300 font-bold mb-2">المصروفات التشغيلية *</label>
                    <input type="number" step="0.01" name="operating_expenses" value="{{ old('operating_expenses', 0) }}" required
                           class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-2 dark:bg-gray-700 dark:text-white">
                </div>

                <!-- Cash Inflow -->
                <div>
                    <label class="block text-gray-700 dark:text-gray-300 font-bold mb-2">التدفق النقدي الداخل</label>
                    <input type="number" step="0.01" name="cash_inflow" value="{{ old('cash_inflow', 0) }}"
                           class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-2 dark:bg-gray-700 dark:text-white">
                </div>

                <!-- Cash Outflow -->
                <div>
                    <label class="block text-gray-700 dark:text-gray-300 font-bold mb-2">التدفق النقدي الخارج</label>
                    <input type="number" step="0.01" name="cash_outflow" value="{{ old('cash_outflow', 0) }}"
                           class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-2 dark:bg-gray-700 dark:text-white">
                </div>

                <!-- Assets -->
                <div>
                    <label class="block text-gray-700 dark:text-gray-300 font-bold mb-2">الأصول</label>
                    <input type="number" step="0.01" name="assets" value="{{ old('assets', 0) }}"
                           class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-2 dark:bg-gray-700 dark:text-white">
                </div>

                <!-- Liabilities -->
                <div>
                    <label class="block text-gray-700 dark:text-gray-300 font-bold mb-2">الخصوم</label>
                    <input type="number" step="0.01" name="liabilities" value="{{ old('liabilities', 0) }}"
                           class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-2 dark:bg-gray-700 dark:text-white">
                </div>
            </div>

            <div class="mt-6">
                <button type="submit" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition font-bold">
                    حفظ البيانات المالية
                </button>
            </div>
        </form>
    </div>

    <!-- Charts Section -->
    @if($financialData->count() > 0)
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <!-- Revenue Chart -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">تطور الإيرادات</h3>
            <canvas id="revenueChart" height="250"></canvas>
        </div>

        <!-- Profit Margin Chart -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">هامش الربح (%)</h3>
            <canvas id="profitMarginChart" height="250"></canvas>
        </div>

        <!-- Net Income Chart -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">صافي الربح</h3>
            <canvas id="netIncomeChart" height="250"></canvas>
        </div>

        <!-- Cash Flow Chart -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">التدفق النقدي الصافي</h3>
            <canvas id="cashFlowChart" height="250"></canvas>
        </div>
    </div>

    <!-- Financial Data Tables -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
        <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6">البيانات المالية المحفوظة</h2>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b dark:border-gray-700">
                        <th class="text-right py-3 px-4 text-gray-700 dark:text-gray-300">السنة</th>
                        <th class="text-right py-3 px-4 text-gray-700 dark:text-gray-300">الإيرادات</th>
                        <th class="text-right py-3 px-4 text-gray-700 dark:text-gray-300">صافي الربح</th>
                        <th class="text-right py-3 px-4 text-gray-700 dark:text-gray-300">هامش الربح</th>
                        <th class="text-right py-3 px-4 text-gray-700 dark:text-gray-300">ROI</th>
                        <th class="text-right py-3 px-4 text-gray-700 dark:text-gray-300">إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($financialData as $data)
                    <tr class="border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700">
                        <td class="py-3 px-4 text-gray-900 dark:text-white font-bold">{{ $data->year }}</td>
                        <td class="py-3 px-4 text-gray-900 dark:text-white">{{ number_format($data->revenue, 2) }}</td>
                        <td class="py-3 px-4 text-gray-900 dark:text-white">{{ number_format($data->net_income, 2) }}</td>
                        <td class="py-3 px-4 text-gray-900 dark:text-white">{{ $data->profit_margin }}%</td>
                        <td class="py-3 px-4 text-gray-900 dark:text-white">{{ $data->roi }}%</td>
                        <td class="py-3 px-4">
                            <form action="{{ route('financial.destroy', [$businessPlan, $data]) }}" method="POST"
                                  onsubmit="return confirm('هل أنت متأكد من حذف هذه البيانات؟')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800 text-sm">حذف</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-8">
            <div class="bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900 dark:to-blue-800 rounded-lg p-6">
                <p class="text-sm text-blue-600 dark:text-blue-300 font-medium">متوسط الإيرادات السنوية</p>
                <p class="text-3xl font-bold text-blue-900 dark:text-blue-100 mt-2">{{ number_format($financialData->avg('revenue'), 0) }}</p>
            </div>

            <div class="bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900 dark:to-green-800 rounded-lg p-6">
                <p class="text-sm text-green-600 dark:text-green-300 font-medium">متوسط صافي الربح</p>
                <p class="text-3xl font-bold text-green-900 dark:text-green-100 mt-2">{{ number_format($financialData->avg('net_income'), 0) }}</p>
            </div>

            <div class="bg-gradient-to-br from-purple-50 to-purple-100 dark:from-purple-900 dark:to-purple-800 rounded-lg p-6">
                <p class="text-sm text-purple-600 dark:text-purple-300 font-medium">عدد السنوات</p>
                <p class="text-3xl font-bold text-purple-900 dark:text-purple-100 mt-2">{{ $financialData->count() }}</p>
            </div>
        </div>
    </div>
    @else
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-12 text-center">
        <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
        </svg>
        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">لا توجد بيانات مالية بعد</h3>
        <p class="text-gray-600 dark:text-gray-400">استخدم النموذج أعلاه لإضافة بيانات مالية لخطتك</p>
    </div>
    @endif
</div>

@if($financialData->count() > 0)
<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<script>
    // Prepare data from Laravel
    const financialData = @json($financialData->values());
    const years = financialData.map(d => d.year);
    const revenues = financialData.map(d => parseFloat(d.revenue));
    const netIncomes = financialData.map(d => parseFloat(d.net_income));
    const profitMargins = financialData.map(d => d.profit_margin);
    const cashFlows = financialData.map(d => parseFloat(d.net_cash_flow));

    // Common chart options
    const commonOptions = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: true,
                position: 'top',
                rtl: true,
                labels: {
                    font: {
                        family: 'Tajawal, Arial, sans-serif'
                    }
                }
            }
        },
        scales: {
            x: {
                ticks: {
                    font: {
                        family: 'Tajawal, Arial, sans-serif'
                    }
                }
            },
            y: {
                ticks: {
                    font: {
                        family: 'Tajawal, Arial, sans-serif'
                    }
                }
            }
        }
    };

    // Revenue Chart
    new Chart(document.getElementById('revenueChart'), {
        type: 'line',
        data: {
            labels: years,
            datasets: [{
                label: 'الإيرادات',
                data: revenues,
                borderColor: 'rgb(59, 130, 246)',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            ...commonOptions,
            scales: {
                ...commonOptions.scales,
                y: {
                    ...commonOptions.scales.y,
                    beginAtZero: true
                }
            }
        }
    });

    // Profit Margin Chart
    new Chart(document.getElementById('profitMarginChart'), {
        type: 'bar',
        data: {
            labels: years,
            datasets: [{
                label: 'هامش الربح (%)',
                data: profitMargins,
                backgroundColor: 'rgba(34, 197, 94, 0.6)',
                borderColor: 'rgb(34, 197, 94)',
                borderWidth: 1
            }]
        },
        options: {
            ...commonOptions,
            scales: {
                ...commonOptions.scales,
                y: {
                    ...commonOptions.scales.y,
                    beginAtZero: true,
                    max: 100
                }
            }
        }
    });

    // Net Income Chart
    new Chart(document.getElementById('netIncomeChart'), {
        type: 'bar',
        data: {
            labels: years,
            datasets: [{
                label: 'صافي الربح',
                data: netIncomes,
                backgroundColor: netIncomes.map(val => val >= 0 ? 'rgba(34, 197, 94, 0.6)' : 'rgba(239, 68, 68, 0.6)'),
                borderColor: netIncomes.map(val => val >= 0 ? 'rgb(34, 197, 94)' : 'rgb(239, 68, 68)'),
                borderWidth: 1
            }]
        },
        options: commonOptions
    });

    // Cash Flow Chart
    new Chart(document.getElementById('cashFlowChart'), {
        type: 'line',
        data: {
            labels: years,
            datasets: [{
                label: 'التدفق النقدي الصافي',
                data: cashFlows,
                borderColor: 'rgb(168, 85, 247)',
                backgroundColor: 'rgba(168, 85, 247, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: commonOptions
    });
</script>
@endif

</x-layouts.app>
