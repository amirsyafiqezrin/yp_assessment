<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ $subject->name }} ({{ $subject->code }})
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Upcoming Exams -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-bold mb-4">Upcoming / Available Exams</h3>

                    @if($upcomingExams->count() > 0)
                        <div class="relative overflow-x-auto">
                            <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                                <thead
                                    class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                    <tr>
                                        <th scope="col" class="px-6 py-3">Title</th>
                                        <th scope="col" class="px-6 py-3">Duration</th>
                                        <th scope="col" class="px-6 py-3">Window</th>
                                        <th scope="col" class="px-6 py-3">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($upcomingExams as $exam)
                                        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                            <th scope="row"
                                                class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                                {{ $exam->title }}
                                            </th>
                                            <td class="px-6 py-4">
                                                {{ $exam->time_limit }} mins
                                            </td>
                                            <td class="px-6 py-4">
                                                {{ $exam->start_time ? $exam->start_time->format('d M H:i') : 'Anytime' }}
                                                -
                                                {{ $exam->end_time ? $exam->end_time->format('d M H:i') : 'Indefinite' }}
                                            </td>
                                            <td class="px-6 py-4">
                                                <a href="{{ route('student.exams.show', $exam) }}"
                                                    class="font-medium text-blue-600 dark:text-blue-500 hover:underline">Start</a>
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
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-bold mb-4">Past Exams</h3>

                    @if($historyExams->count() > 0)
                        <div class="relative overflow-x-auto">
                            <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                                <thead
                                    class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                    <tr>
                                        <th scope="col" class="px-6 py-3">Title</th>
                                        <th scope="col" class="px-6 py-3">Submitted At</th>
                                        <th scope="col" class="px-6 py-3">Score</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($historyExams as $exam)
                                        @php
                                            $submission = $exam->submissions->where('user_id', auth()->id())->first();
                                        @endphp
                                        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                            <th scope="row"
                                                class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                                {{ $exam->title }}
                                            </th>
                                            <td class="px-6 py-4">
                                                {{ $submission->submitted_at->format('d M Y, H:i') }}
                                            </td>
                                            <td class="px-6 py-4">
                                                {{ $submission->total_score ?? 'Pending' }}
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