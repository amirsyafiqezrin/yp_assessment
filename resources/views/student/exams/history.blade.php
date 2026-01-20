<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Exam History') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    
                    @if($submissions->count() > 0)
                        <div class="relative overflow-x-auto">
                            <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                    <tr>
                                        <th scope="col" class="px-6 py-3">Exam Title</th>
                                        <th scope="col" class="px-6 py-3">Subject</th>
                                        <th scope="col" class="px-6 py-3">Submitted At</th>
                                        <th scope="col" class="px-6 py-3">Total Score</th>
                                        <th scope="col" class="px-6 py-3">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($submissions as $submission)
                                        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                            <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                                {{ $submission->exam->title }}
                                            </th>
                                            <td class="px-6 py-4">
                                                {{ $submission->exam->subject->code }}
                                            </td>
                                            <td class="px-6 py-4">
                                                {{ $submission->submitted_at->format('d M Y, H:i') }}
                                            </td>
                                            <td class="px-6 py-4 font-bold text-blue-600">
                                                {{ $submission->total_score }}
                                            </td>
                                            <td class="px-6 py-4">
                                                @php
                                                    $hasPending = $submission->submissionQuestions->contains('status', \App\Models\SubmissionQuestion::STATUS_PENDING);
                                                @endphp
                                                @if($hasPending)
                                                    <span class="bg-yellow-100 text-yellow-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded dark:bg-yellow-900 dark:text-yellow-300">Pending Review</span>
                                                @else
                                                    <span class="bg-green-100 text-green-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded dark:bg-green-900 dark:text-green-300">Graded</span>
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
