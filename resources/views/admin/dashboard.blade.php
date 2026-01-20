<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Admin Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Pending Grading Alert -->
            @if(isset($pendingGradingCount) && $pendingGradingCount > 0)
                <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 mb-6" role="alert">
                    <p class="font-bold">Attention Needed</p>
                    <p>You have {{ $pendingGradingCount }} submission answer(s) pending manual review (Open Text Questions).
                    </p>
                    <a href="{{ route('admin.submissions.index') }}" class="underline mt-2 block">Go to Submissions</a>
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-medium mb-4">Welcome back, Lecturer!</h3>
                    <p>Manage your classes, subjects, and exams from here.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Quick Actions -->
                <a href="{{ route('admin.exams.create') }}"
                    class="block p-6 bg-white dark:bg-gray-800 rounded-lg shadow hover:bg-gray-50 dark:hover:bg-gray-700">
                    <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">Create New Exam
                    </h5>
                    <p class="font-normal text-gray-700 dark:text-gray-400">Set up a new assessment for your students.
                    </p>
                </a>

                <a href="{{ route('admin.classes.index') }}"
                    class="block p-6 bg-white dark:bg-gray-800 rounded-lg shadow hover:bg-gray-50 dark:hover:bg-gray-700">
                    <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">Manage Classes</h5>
                    <p class="font-normal text-gray-700 dark:text-gray-400">View and manage your student cohorts.</p>
                </a>

                <a href="{{ route('admin.submissions.index') }}"
                    class="block p-6 bg-white dark:bg-gray-800 rounded-lg shadow hover:bg-gray-50 dark:hover:bg-gray-700">
                    <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">View Submissions
                    </h5>
                    <p class="font-normal text-gray-700 dark:text-gray-400">Check student results and grade pending
                        answers.</p>
                </a>
            </div>
        </div>
    </div>
</x-app-layout>