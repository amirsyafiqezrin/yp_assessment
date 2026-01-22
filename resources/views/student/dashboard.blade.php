<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-black leading-tight">
            {{ __('Student Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                <!-- Main Column: Upcoming Exams -->
                <div class="lg:col-span-2 space-y-6">
                    <h3 class="text-xl font-bold text-black">Upcoming Exams</h3>

                    @forelse($assignedExams as $exam)
                        <div class="bg-white overflow-hidden shadow-[0_4px_20px_rgba(0,0,0,0.15)] sm:rounded-lg p-6">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h4 class="text-lg font-bold text-black">{{ $exam->title }}</h4>
                                    <p class="text-sm text-gray-600">{{ $exam->subject->name }} ({{ $exam->subject->code }})
                                    </p>
                                    <div class="mt-2 text-sm text-black">
                                        <p><span class="font-bold">Duration:</span> {{ $exam->time_limit }} minutes</p>
                                        @if($exam->start_time)
                                            <p><span class="font-bold">Starts:</span>
                                                {{ $exam->start_time->setTimezone('Asia/Kuala_Lumpur')->format('d M Y, H:i') }}
                                            </p>
                                        @endif
                                        @if($exam->end_time)
                                            <p><span class="font-bold">Ends:</span>
                                                {{ $exam->end_time->setTimezone('Asia/Kuala_Lumpur')->format('d M Y, H:i') }}
                                            </p>
                                        @endif
                                    </div>
                                </div>
                                <div>
                                    <a @if($exam->start_time && now()->lt($exam->start_time))
                                        class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-bold text-xs text-white uppercase tracking-widest cursor-not-allowed pointer-events-none opacity-60">
                                        Upcoming
                                    @else
                                            href="{{ route('student.exams.show', $exam) }}"
                                            class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent
                                            rounded-md font-bold text-xs text-white uppercase tracking-widest hover:bg-blue-500
                                            focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2
                                            focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                            Take Exam
                                        @endif
                                    </a>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div
                            class="bg-white overflow-hidden shadow-[0_4px_20px_rgba(0,0,0,0.15)] sm:rounded-lg p-6 text-center text-gray-500">
                            No upcoming exams assigned at the moment.
                        </div>
                    @endforelse
                </div>

                <!-- Side Column: My Subjects -->
                <div class="space-y-6">
                    <h3 class="text-xl font-bold text-black">My Subjects</h3>

                    <div class="bg-white overflow-hidden shadow-[0_4px_20px_rgba(0,0,0,0.15)] sm:rounded-lg">
                        <ul class="divide-y divide-gray-200">
                            @forelse($subjects as $subject)
                                <li>
                                    <a href="{{ route('student.subjects.show', $subject) }}"
                                        class="block hover:bg-gray-50 p-4 transition duration-150 ease-in-out">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <p class="text-sm font-bold text-blue-700 truncate">{{ $subject->code }}</p>
                                                <p class="text-base font-bold text-black">{{ $subject->name }}</p>
                                            </div>
                                            <div class="text-gray-400">
                                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M9 5l7 7-7 7" />
                                                </svg>
                                            </div>
                                        </div>
                                    </a>
                                </li>
                            @empty
                                <li class="p-4 text-gray-500 text-sm">No subjects assigned yet.</li>
                            @endforelse
                        </ul>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>