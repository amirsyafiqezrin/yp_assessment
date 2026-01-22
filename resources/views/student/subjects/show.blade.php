<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-black leading-tight">
                {{ $subject->name }} ({{ $subject->code }})
            </h2>
            <a href="{{ route('dashboard') }}"
                class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300 text-sm font-bold shadow-sm">
                &larr; Back to Dashboard
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Upcoming Exams -->
            <div class="bg-white overflow-hidden shadow-[0_4px_20px_rgba(0,0,0,0.15)] sm:rounded-lg">
                <div class="p-6 text-black">
                    <h3 class="text-lg font-bold mb-4">Upcoming / Available Exams</h3>

                    @if($upcomingExams->count() > 0)
                        <div class="relative overflow-x-auto">
                            <table class="w-full text-sm text-left rtl:text-right text-black">
                                <thead class="text-xs text-black uppercase bg-gray-100">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 font-bold">Title</th>
                                        <th scope="col" class="px-6 py-3 font-bold">Duration</th>
                                        <th scope="col" class="px-6 py-3 font-bold">Window</th>
                                        <th scope="col" class="px-6 py-3 font-bold">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($upcomingExams as $exam)
                                        <tr class="bg-white border-b border-gray-200 hover:bg-gray-50 text-black">
                                            <th scope="row" class="px-6 py-4 font-bold text-black whitespace-nowrap">
                                                {{ $exam->title }}
                                            </th>
                                            <td class="px-6 py-4">
                                                {{ $exam->time_limit }} mins
                                            </td>
                                            <td class="px-6 py-4">
                                                {{ $exam->start_time ? $exam->start_time->setTimezone('Asia/Kuala_Lumpur')->format('d M H:i') : 'Anytime' }}
                                                -
                                                {{ $exam->end_time ? $exam->end_time->setTimezone('Asia/Kuala_Lumpur')->format('d M H:i') : 'Indefinite' }}
                                            </td>
                                            <td class="px-6 py-4">
                                                @if($exam->end_time && now()->gt($exam->end_time))
                                                    <span class="text-red-500 font-bold">Missed</span>
                                                @elseif($exam->start_time && now()->lt($exam->start_time))
                                                    <span class="text-yellow-500 font-bold">Upcoming</span>
                                                @else
                                                    <a href="{{ route('student.exams.show', $exam) }}"
                                                        class="font-bold text-blue-700 hover:text-blue-900 hover:underline">Start</a>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-gray-500 italic">No exams available for this subject right now.</p>
                    @endif
                </div>
            </div>

            <!-- Exam History -->
            <div class="bg-white overflow-hidden shadow-[0_4px_20px_rgba(0,0,0,0.15)] sm:rounded-lg">
                <div class="p-6 text-black">
                    <h3 class="text-lg font-bold mb-4">Past Exams</h3>

                    @if($historyExams->count() > 0)
                        <div class="relative overflow-x-auto">
                            <table class="w-full text-sm text-left rtl:text-right text-black">
                                <thead class="text-xs text-black uppercase bg-gray-100">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 font-bold">Title</th>
                                        <th scope="col" class="px-6 py-3 font-bold">Submitted At</th>
                                        <th scope="col" class="px-6 py-3 font-bold">Score</th>
                                        <th scope="col" class="px-6 py-3 font-bold">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($historyExams as $exam)
                                        @php
                                            $submission = $exam->submissions->where('user_id', auth()->id())->first();
                                            $isMissed = !$submission || !$submission->submitted_at;
                                        @endphp
                                        <tr class="bg-white border-b border-gray-200 hover:bg-gray-50 text-black">
                                            <th scope="row" class="px-6 py-4 font-bold text-black whitespace-nowrap">
                                                {{ $exam->title }}
                                            </th>
                                            <td class="px-6 py-4">
                                                @if($isMissed)
                                                    <span class="text-red-500 font-bold">Missed</span>
                                                    <span class="text-xs text-gray-500 block">
                                                        (Due:
                                                        {{ $exam->end_time ? $exam->end_time->setTimezone('Asia/Kuala_Lumpur')->format('d M, H:i') : '-' }})
                                                    </span>
                                                @else
                                                    {{ $submission->submitted_at->setTimezone('Asia/Kuala_Lumpur')->format('d M Y, H:i') }}
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 font-bold">
                                                @if($isMissed)
                                                    <span class="text-gray-400">0</span>
                                                @else
                                                    {{ $submission->total_score ?? 'Pending' }}
                                                @endif
                                            </td>
                                            <td class="px-6 py-4">
                                                @if(!$isMissed)
                                                    <a href="{{ route('student.exams.review', $exam) }}"
                                                        class="font-bold text-blue-700 hover:text-blue-900 hover:underline">Review</a>
                                                @else
                                                    <span class="text-gray-400 cursor-not-allowed">No Review</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-gray-500 italic">No past exams found.</p>
                    @endif
                </div>
            </div>

        </div>
    </div>
</x-app-layout>