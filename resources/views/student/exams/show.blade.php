<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Exam Instructions') }}
            </h2>
            <a href="{{ route('student.exams.index') }}"
                class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300 text-sm font-bold shadow-sm">
                &larr; Back
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-2xl font-bold mb-4">{{ $exam->title }}</h3>
                    <div class="mb-6 space-y-2 text-gray-600 dark:text-gray-300">
                        <p><strong>Subject:</strong> {{ $exam->subject->name }}</p>
                        <p><strong>Time Limit:</strong> {{ $exam->time_limit }} minutes</p>
                        <p><strong>Total Questions:</strong> {{ $exam->questions->count() }}</p>
                    </div>

                    <!-- Important message hidden as per request -->

                    <div class="flex flex-col items-end gap-4">
                        @if(isset($submission) && $submission->submitted_at)
                            <div class="text-right">
                                <div class="mb-2">
                                    <span class="font-bold text-gray-700 dark:text-gray-300">Status:</span>
                                    <span
                                        class="px-2 py-1 bg-green-100 text-green-700 rounded font-bold text-sm">Submitted</span>
                                </div>
                                <div>
                                    <span class="font-bold text-gray-700 dark:text-gray-300">Total Marks:</span>
                                    <span class="font-bold text-xl text-black dark:text-white">
                                        {{ $submission->total_score }} / {{ $exam->questions->sum('question_score') }}
                                    </span>
                                </div>
                            </div>

                            <button disabled
                                class="inline-flex items-center px-4 py-2 bg-gray-400 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest cursor-not-allowed">
                                You've taken this exam
                            </button>
                        @else
                            <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6 text-blue-700 w-full text-left">
                                <p class="font-bold">Important:</p>
                                <ul class="list-disc list-inside">
                                    <li>Once you click "Start Exam", the timer will begin.</li>
                                    <li>You typically cannot pause the timer once started.</li>
                                    <li>Ensure you have a stable internet connection.</li>
                                    <li>Do not refresh the page excessively during the exam.</li>
                                </ul>
                            </div>

                            <form action="{{ route('student.exams.start', $exam) }}" method="POST">
                                @csrf
                                <button type="submit"
                                    class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-500 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    Start Exam Now
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>