<x-layouts.app>
    <x-slot name="title">المهام - {{ $businessPlan->title }}</x-slot>

<div class="container mx-auto px-4 py-8 max-w-7xl">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">إدارة المهام</h1>
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

    <!-- Add Task Form -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 mb-8">
        <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6">إضافة مهمة جديدة</h2>

        <form action="{{ route('tasks.store', $businessPlan) }}" method="POST">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Title -->
                <div class="md:col-span-2">
                    <label class="block text-gray-700 dark:text-gray-300 font-bold mb-2">عنوان المهمة *</label>
                    <input type="text" name="title" value="{{ old('title') }}" required
                           class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-2 dark:bg-gray-700 dark:text-white">
                    @error('title')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Description -->
                <div class="md:col-span-2">
                    <label class="block text-gray-700 dark:text-gray-300 font-bold mb-2">الوصف</label>
                    <textarea name="description" rows="3"
                              class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-2 dark:bg-gray-700 dark:text-white">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Assigned To -->
                <div>
                    <label class="block text-gray-700 dark:text-gray-300 font-bold mb-2">تعيين إلى</label>
                    <select name="assigned_to" class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-2 dark:bg-gray-700 dark:text-white">
                        <option value="">-- اختر عضو --</option>
                        @foreach($teamMembers as $member)
                            <option value="{{ $member->id }}" {{ old('assigned_to') == $member->id ? 'selected' : '' }}>
                                {{ $member->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('assigned_to')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Priority -->
                <div>
                    <label class="block text-gray-700 dark:text-gray-300 font-bold mb-2">الأولوية *</label>
                    <select name="priority" required class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-2 dark:bg-gray-700 dark:text-white">
                        <option value="low" {{ old('priority') == 'low' ? 'selected' : '' }}>منخفضة</option>
                        <option value="medium" {{ old('priority', 'medium') == 'medium' ? 'selected' : '' }}>متوسطة</option>
                        <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>عالية</option>
                        <option value="urgent" {{ old('priority') == 'urgent' ? 'selected' : '' }}>عاجلة</option>
                    </select>
                    @error('priority')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Due Date -->
                <div>
                    <label class="block text-gray-700 dark:text-gray-300 font-bold mb-2">تاريخ الاستحقاق</label>
                    <input type="datetime-local" name="due_date" value="{{ old('due_date') }}"
                           class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-2 dark:bg-gray-700 dark:text-white">
                    @error('due_date')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mt-6">
                <button type="submit" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition font-bold">
                    إنشاء المهمة
                </button>
            </div>
        </form>
    </div>

    <!-- Tasks List -->
    @if($tasks->count() > 0)
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
        <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6">قائمة المهام</h2>

        <div class="space-y-4">
            @foreach($tasks as $task)
            <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-5 hover:shadow-md transition
                        {{ $task->isOverdue() ? 'bg-red-50 dark:bg-red-900/20 border-red-300' : '' }}">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <div class="flex items-center gap-3 mb-2">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ $task->title }}</h3>

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
                                <span class="px-3 py-1 rounded-full text-xs font-bold bg-red-100 text-red-800">
                                    ⚠ متأخر
                                </span>
                            @endif
                        </div>

                        @if($task->description)
                            <p class="text-gray-600 dark:text-gray-400 mb-3">{{ $task->description }}</p>
                        @endif

                        <div class="flex items-center gap-4 text-sm text-gray-600 dark:text-gray-400">
                            @if($task->assignedTo)
                                <div class="flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                    <span>{{ $task->assignedTo->name }}</span>
                                </div>
                            @endif

                            @if($task->due_date)
                                <div class="flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    <span>{{ $task->due_date->format('Y-m-d H:i') }}</span>
                                </div>
                            @endif

                            <div class="flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span>{{ $task->created_at->diffForHumans() }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center gap-2">
                        @if($task->status !== 'completed')
                            <form action="{{ route('tasks.update-status', [$businessPlan, $task]) }}" method="POST" class="inline">
                                @csrf
                                <input type="hidden" name="status" value="{{ $task->status === 'pending' ? 'in_progress' : 'completed' }}">
                                <button type="submit" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                    {{ $task->status === 'pending' ? 'بدء' : 'إكمال' }}
                                </button>
                            </form>
                        @endif

                        <form action="{{ route('tasks.destroy', [$businessPlan, $task]) }}" method="POST"
                              onsubmit="return confirm('هل أنت متأكد من حذف هذه المهمة؟')" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-800 text-sm font-medium">
                                حذف
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-6">
            {{ $tasks->links() }}
        </div>

        <!-- Summary Stats -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-8">
            @php
                $allTasks = $businessPlan->tasks;
                $pending = $allTasks->where('status', 'pending')->count();
                $inProgress = $allTasks->where('status', 'in_progress')->count();
                $completed = $allTasks->where('status', 'completed')->count();
                $overdue = $allTasks->filter(fn($t) => $t->isOverdue())->count();
            @endphp

            <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-4">
                <p class="text-sm text-gray-600 dark:text-gray-400">قيد الانتظار</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ $pending }}</p>
            </div>

            <div class="bg-blue-50 dark:bg-blue-900 rounded-lg p-4">
                <p class="text-sm text-blue-600 dark:text-blue-300">قيد التنفيذ</p>
                <p class="text-2xl font-bold text-blue-900 dark:text-blue-100 mt-1">{{ $inProgress }}</p>
            </div>

            <div class="bg-green-50 dark:bg-green-900 rounded-lg p-4">
                <p class="text-sm text-green-600 dark:text-green-300">مكتملة</p>
                <p class="text-2xl font-bold text-green-900 dark:text-green-100 mt-1">{{ $completed }}</p>
            </div>

            <div class="bg-red-50 dark:bg-red-900 rounded-lg p-4">
                <p class="text-sm text-red-600 dark:text-red-300">متأخرة</p>
                <p class="text-2xl font-bold text-red-900 dark:text-red-100 mt-1">{{ $overdue }}</p>
            </div>
        </div>
    </div>
    @else
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-12 text-center">
        <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
        </svg>
        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">لا توجد مهام بعد</h3>
        <p class="text-gray-600 dark:text-gray-400">استخدم النموذج أعلاه لإضافة مهمة جديدة</p>
    </div>
    @endif
</div>

</x-layouts.app>
