<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-black leading-tight">
            {{ __('Admin Dashboard') }}
        </h2>
    </x-slot>

    <style>
        .custom-dashboard-grid {
            display: grid;
            grid-template-columns: repeat(1, minmax(0, 1fr));
            gap: 1.5rem;
        }

        @media (min-width: 640px) {
            .custom-dashboard-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (min-width: 1024px) {
            .custom-dashboard-grid {
                grid-template-columns: repeat(4, minmax(0, 1fr));
            }
        }
    </style>

    <div class="py-12 bg-gray-100">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Pending Grading Alert -->
            @if(isset($pendingGradingCount) && $pendingGradingCount > 0)
                <div class="relative w-full rounded-lg border border-yellow-200 bg-yellow-50 p-4 mb-8 shadow-sm mb-6"
                    role="alert">
                    <div class="flex justify-between items-center flex-wrap gap-4">
                        <div>
                            <p class="font-bold text-lg text-yellow-800">Attention Needed</p>
                            <p class="mt-1 text-sm text-yellow-700">
                                You have {{ $pendingGradingCount }} submission answer(s) pending manual review (Open Text
                                Questions).
                            </p>
                        </div>
                        <a href="{{ route('admin.submissions.index') }}"
                            class="whitespace-nowrap rounded border border-yellow-300 bg-white px-4 py-2 text-sm font-semibold text-yellow-700 hover:bg-yellow-100 transition">
                            Go to Submissions
                        </a>
                    </div>
                </div>
            @endif

            <!-- Welcome Header -->
            <h3 class="text-xl font-semibold text-black mb-6">Welcome back, {{ Auth::user()->name }}!</h3>

            <!-- Statistics Grid -->
            <div class="custom-dashboard-grid mb-10">
                <!-- Total Students -->
                <div class="p-6 rounded-xl bg-white border border-gray-200 shadow-[0_4px_20px_rgba(0,0,0,0.15)]">
                    <h4 class="text-sm font-bold text-gray-600">Total Students Enrolled</h4>
                    <p class="mt-2 text-4xl font-bold text-black">{{ $totalStudents ?? 0 }}</p>
                    <p class="mt-1 text-xs text-gray-500">Across all subjects</p>
                </div>

                <!-- Student Enrollment per Subject -->
                <!-- <div class="p-6 rounded-xl bg-white border border-gray-200 shadow-[0_4px_20px_rgba(0,0,0,0.15)]">
                    <h4 class="text-sm font-bold text-gray-600 mb-2">Student Enrollment per Subject</h4>
                    @if(isset($subjectStudentCounts) && count($subjectStudentCounts) > 0)
                        <div
                            class="space-y-2 max-h-24 overflow-y-auto scrollbar-thin scrollbar-thumb-gray-300 scrollbar-track-gray-100">
                            @foreach($subjectStudentCounts as $subject => $count)
                                <div class="text-xs text-gray-600">
                                    <span class="font-bold text-black">{{ $subject }}:</span> {{ $count }} Students
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm text-gray-400">No subjects assigned.</p>
                    @endif
                </div> -->

                <!-- Total Upcoming Exams -->
                <div class="p-6 rounded-xl bg-white border border-gray-200 shadow-sm">
                    <h4 class="text-sm font-medium text-gray-500">Total Available Exams</h4>
                    <p class="mt-2 text-4xl font-bold text-gray-900">{{ $totalAvailableExams ?? 0 }}</p>
                    <p class="mt-1 text-xs text-gray-500">Across all subjects</p>
                </div>

                <!-- Total Subjects -->
                <div class="p-6 rounded-xl bg-white border border-gray-200 shadow-[0_4px_20px_rgba(0,0,0,0.15)]">
                    <h4 class="text-sm font-bold text-gray-600">Total Subjects Assigned</h4>
                    <p class="mt-2 text-4xl font-bold text-black">{{ $totalSubjects ?? 0 }}</p>
                    <p class="mt-1 text-xs text-gray-500">Across all subjects</p>
                </div>

                <!-- Pending Submissions -->
                <div class="p-6 rounded-xl bg-white border border-gray-200 shadow-sm">
                    <h4 class="text-sm font-medium text-gray-500">Pending Submissions</h4>
                    <p class="mt-2 text-4xl font-bold text-gray-900">{{ $pendingSubmissionsCount ?? 0 }}</p>
                    @if(($pendingSubmissionsCount ?? 0) > 0)
                        <span class="inline-block text-xs font-semibold rounded">
                            Need grading review
                        </span>
                    @else
                        <p class="mt-1 text-xs text-gray-500">All caught up</p>
                    @endif
                </div>
            </div>

            <!-- Quick Actions Grid -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
                <!-- Action: Create New Exam -->
                <a href="{{ route('admin.exams.create') }}"
                    class="group block p-6 rounded-xl bg-white border border-gray-200 shadow-sm hover:border-gray-300 hover:shadow-md transition duration-300">
                    <div
                        class="mb-4 inline-flex h-12 w-12 items-center justify-center rounded-lg bg-blue-50 text-blue-600 group-hover:bg-blue-600 group-hover:text-white transition">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="h-6 w-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                    </div>
                    <h5 class="mb-1 text-lg font-bold text-gray-900">Create New Exam</h5>
                    <p class="text-sm text-gray-500">Set up a new assessment for your students.</p>
                </a>

                <!-- Action: Manage Classes -->
                <a href="{{ route('admin.classes.index') }}"
                    class="group block p-6 rounded-xl bg-white border border-gray-200 shadow-sm hover:border-gray-300 hover:shadow-md transition duration-300">
                    <div
                        class="mb-4 inline-flex h-12 w-12 items-center justify-center rounded-lg bg-blue-50 text-blue-600 group-hover:bg-blue-600 group-hover:text-white transition">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="h-6 w-6">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                        </svg>
                    </div>
                    <h5 class="mb-1 text-lg font-bold text-gray-900">Manage Classes</h5>
                    <p class="text-sm text-gray-500">View and manage your student cohorts.</p>
                </a>

                <!-- Action: View Submissions -->
                <a href="{{ route('admin.submissions.index') }}"
                    class="group block p-6 rounded-xl bg-white border border-gray-200 shadow-sm hover:border-gray-300 hover:shadow-md transition duration-300">
                    <div
                        class="mb-4 inline-flex h-12 w-12 items-center justify-center rounded-lg bg-blue-50 text-blue-600 group-hover:bg-blue-600 group-hover:text-white transition">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="h-6 w-6">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M3.75 12h16.5m-16.5 3.75h16.5M3.75 19.5h16.5M5.625 4.5h12.75a1.875 1.875 0 0 1 0 3.75H5.625a1.875 1.875 0 0 1 0-3.75Z" />
                        </svg>
                    </div>
                    <h5 class="mb-1 text-lg font-bold text-gray-900">View Submissions</h5>
                    <p class="text-sm text-gray-500">Check student results and grade pending answers.</p>
                </a>
            </div>
        </div>
    </div>
</x-app-layout>