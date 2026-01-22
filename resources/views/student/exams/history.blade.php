<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-black leading-tight">
            {{ __('Exam History') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    
                    @if($exams->count() > 0)
                        <div class="relative overflow-x-auto">
                            <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                    <tr>
                                        <th scope="col" class="px-6 py-3">Exam Title</th>
                                        <th scope="col" class="px-6 py-3">Subject</th>
                                        <th scope="col" class="px-6 py-3">Submitted At</th>
                                        <th scope="col" class="px-6 py-3">Total Score</th>
                                        <th scope="col" class="px-6 py-3">Status</th>
                                        <th scope="col" class="px-6 py-3">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                @foreach($exams as $exam)
                                    @php
                                        $submission = $exam->submissions->first();
                                        $isMissed = !$submission || !$submission->submitted_at;
                                    @endphp
                                    <tr class="bg-white border-b border-gray-200 hover:bg-gray-50 text-black">
                                        <th scope="row" class="px-6 py-4 font-bold text-black whitespace-nowrap">
                                            {{ $exam->title }}
                                        </th>
                                        <td class="px-6 py-4">
                                            {{ $exam->subject->name }}
                                        </td>
                                        <td class="px-6 py-4">
                                            @if($isMissed)
                                                <span class="text-red-500 font-bold">Missed</span>
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
                                            @if($isMissed)
                                                <span class="bg-red-100 text-red-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded dark:bg-red-900 dark:text-red-300">Missed</span>
                                            @else
                                                @php
                                                    $hasPending = $submission->submissionQuestions->contains('status', \App\Models\SubmissionQuestion::STATUS_PENDING);
                                                @endphp
                                                @if($hasPending)
                                                    <span class="bg-yellow-100 text-yellow-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded dark:bg-yellow-900 dark:text-yellow-300">Pending Review</span>
                                                @else
                                                    <span class="bg-green-100 text-green-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded dark:bg-green-900 dark:text-green-300">Graded</span>
                                                @endif
                                            @endif
                                        </td>
                                        <td class="px-6 py-4">
                                             @if(!$isMissed)
                                                <a href="{{ route('student.exams.review', $exam) }}" class="font-medium text-blue-600 dark:text-blue-500 hover:underline">Review</a>
                                             @else
                                                <span class="text-gray-400 cursor-not-allowed">N/A</span>
                                             @endif
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-8">
                            <p class="text-gray-500">You haven't taken any exams yet.</p>
                            <a href="{{ route('student.dashboard') }}" class="text-blue-500 hover:underline mt-2 inline-block">Go to Dashboard</a>
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
