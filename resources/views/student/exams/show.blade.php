<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Exam Instructions') }}
        </h2>
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

                    <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6 text-blue-700">
                        <p class="font-bold">Important:</p>
                        <ul class="list-disc list-inside">
                            <li>Once you click "Start Exam", the timer will begin.</li>
                            <li>You typically cannot pause the timer once started.</li>
                            <li>Ensure you have a stable internet connection.</li>
                            <li>Do not refresh the page excessively during the exam.</li>
                        </ul>
                    </div>

                    <div class="flex justify-end">
                        <form action="{{ route('student.exams.start', $exam) }}" method="POST">
                            @csrf
                            <button type="submit"
                                class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-500 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Start Exam Now
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>