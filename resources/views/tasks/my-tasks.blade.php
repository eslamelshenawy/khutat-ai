<x-layouts.app>
    <x-slot name="title">مهامي</x-slot>

<div class="container mx-auto px-4 py-8 max-w-7xl">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">مهامي</h1>
        <p class="text-gray-600 dark:text-gray-400 mt-2">جميع المهام المعينة لي</p>
    </div>

    @if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
        {{ session('success') }}
    </div>
    @endif

    <!-- Filter Tabs -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md mb-6">
        <div class="flex border-b border-gray-200 dark:border-gray-700">
            <a href="{{ route('tasks.my-tasks', ['status' => 'all']) }}"
               class="px-6 py-4 text-sm font-medium {{ $status === 'all' ? 'border-b-2 border-blue-600 text-blue-600' : 'text-gray-600 hover:text-gray-900' }}">
                الكل ({{ auth()->user()->assignedTasks->count() }})
            </a>
            <a href="{{ route('tasks.my-tasks', ['status' => 'pending']) }}"
               class="px-6 py-4 text-sm font-medium {{ $status === 'pending' ? 'border-b-2 border-blue-600 text-blue-600' : 'text-gray-600 hover:text-gray-900' }}">
                قيد الانتظار ({{ auth()->user()->assignedTasks->where('status', 'pending')->count() }})
            </a>
            <a href="{{ route('tasks.my-tasks', ['status' => 'in_progress']) }}"
               class="px-6 py-4 text-sm font-medium {{ $status === 'in_progress' ? 'border-b-2 border-blue-600 text-blue-600' : 'text-gray-600 hover:text-gray-900' }}">
                قيد التنفيذ ({{ auth()->user()->assignedTasks->where('status', 'in_progress')->count() }})
            </a>
            <a href="{{ route('tasks.my-tasks', ['status' => 'completed']) }}"
               class="px-6 py-4 text-sm font-medium {{ $status === 'completed' ? 'border-b-2 border-blue-600 text-blue-600' : 'text-gray-600 hover:text-gray-900' }}">
                مكتملة ({{ auth()->user()->assignedTasks->where('status', 'completed')->count() }})
            </a>
        </div>
    </div>

    <!-- Tasks List -->
    @if($tasks->count() > 0)
    <div class="space-y-4">
        @foreach($tasks as $task)
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 hover:shadow-lg transition
                    {{ $task->isOverdue() ? 'border-r-4 border-red-500' : '' }}">
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <!-- Task Header -->
                    <div class="flex items-center gap-3 mb-3">
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white">{{ $task->title }}</h3>

                        <!-- Priority Badge -->
                        @php
                            $priorityColors = [
                                'low' => 'bg-green-100 text-green-800',
                                'medium' => 'bg-yellow-100 text-yellow-800',
                                'high' => 'bg-orange-100 text-orange-800',
                                'urgent' => 'bg-red-100 text-red-800',
                            ];
                            $priorityLabels = [
                                'low' => 'منخفضة',
                                'medium' => 'متوسطة',
                                'high' => 'عالية',
                                'urgent' => 'عاجلة',
                            ];
                        @endphp
                        <span class="px-3 py-1 rounded-full text-xs font-bold {{ $priorityColors[$task->priority] ?? 'bg-gray-100 text-gray-800' }}">
                            {{ $priorityLabels[$task->priority] ?? $task->priority }}
                        </span>

                        <!-- Status Badge -->
                        @php
                            $statusColors = [
                                'pending' => 'bg-gray-100 text-gray-800',
                                'in_progress' => 'bg-blue-100 text-blue-800',
                                'completed' => 'bg-green-100 text-green-800',
                                'cancelled' => 'bg-red-100 text-red-800',
                            ];
                            $statusLabels = [
                                'pending' => 'قيد الانتظار',
                                'in_progress' => 'قيد التنفيذ',
                                'completed' => 'مكتملة',
                                'cancelled' => 'ملغاة',
                            ];
                        @endphp
                        <span class="px-3 py-1 rounded-full text-xs font-bold {{ $statusColors[$task->status] ?? 'bg-gray-100 text-gray-800' }}">
                            {{ $statusLabels[$task->status] ?? $task->status }}
                        </span>

                        @if($task->isOverdue())
                            <span class="px-3 py-1 rounded-full text-xs font-bold bg-red-100 text-red-800 flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                </svg>
                                متأخر
                            </span>
                        @endif
                    </div>

                    <!-- Task Description -->
                    @if($task->description)
                        <p class="text-gray-600 dark:text-gray-400 mb-4">{{ $task->description }}</p>
                    @endif

                    <!-- Task Info -->
                    <div class="flex flex-wrap items-center gap-4 text-sm text-gray-600 dark:text-gray-400 mb-4">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <a href="{{ route('business-plans.show', $task->businessPlan) }}"
                               class="text-blue-600 hover:text-blue-800 font-medium">
                                {{ $task->businessPlan->title }}
                            </a>
                        </div>

                        @if($task->assignedBy)
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                                <span>من: {{ $task->assignedBy->name }}</span>
                            </div>
                        @endif

                        @if($task->due_date)
                            <div class="flex items-center gap-2 {{ $task->isOverdue() ? 'text-red-600 font-bold' : '' }}">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                <span>الموعد: {{ $task->due_date->format('Y-m-d H:i') }}</span>
                            </div>
                        @endif

                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span>{{ $task->created_at->diffForHumans() }}</span>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    @if($task->status !== 'completed' && $task->status !== 'cancelled')
                    <div class="flex gap-3">
                        @if($task->status === 'pending')
                            <form action="{{ route('tasks.update-status', [$task->businessPlan, $task]) }}" method="POST">
                                @csrf
                                <input type="hidden" name="status" value="in_progress">
                                <button type="submit"
                                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm font-medium flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    بدء العمل
                                </button>
                            </form>
                        @endif

                        @if($task->status === 'in_progress')
                            <form action="{{ route('tasks.update-status', [$task->businessPlan, $task]) }}" method="POST">
                                @csrf
                                <input type="hidden" name="status" value="completed">
                                <button type="submit"
                                        class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition text-sm font-medium flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    إكمال المهمة
                                </button>
                            </form>
                        @endif

                        <a href="{{ route('tasks.index', $task->businessPlan) }}"
                           class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition text-sm font-medium">
                            عرض التفاصيل
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Pagination -->
    <div class="mt-8">
        {{ $tasks->links() }}
    </div>
    @else
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-12 text-center">
        <svg class="w-20 h-20 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
        </svg>
        <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">لا توجد مهام</h3>
        <p class="text-gray-600 dark:text-gray-400">
            @if($status === 'all')
                لم يتم تعيين أي مهام لك بعد
            @else
                لا توجد مهام {{ $statusLabels[$status] ?? '' }}
            @endif
        </p>
    </div>
    @endif
</div>

</x-layouts.app>
