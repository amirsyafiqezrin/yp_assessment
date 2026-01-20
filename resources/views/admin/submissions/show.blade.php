<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Grading Submission') }} - {{ $submission->user->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <x-auth-session-status class="mb-4" :status="session('success')" />

            <form action="{{ route('admin.submissions.update', $submission) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="space-y-6">
                    @foreach($submission->submissionQuestions as $subQuestion)
                        <div
                            class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 {{ $subQuestion->status == \App\Models\SubmissionQuestion::STATUS_PENDING ? 'border-l-4 border-yellow-500' : '' }}">

                            <div class="mb-2 flex justify-between">
                                <h4 class="font-bold text-gray-800 dark:text-gray-200">
                                    Q: {{ $subQuestion->question->question_title }}
                                </h4>
                                <span class="text-sm bg-gray-100 px-2 py-1 rounded">Max:
                                    {{ $subQuestion->question->question_score }} pts</span>
                            </div>

                            <div class="mb-4">
                                <p class="text-sm text-gray-500 uppercase font-bold">Student Answer:</p>
                                <div
                                    class="p-3 bg-gray-50 dark:bg-gray-900 rounded border border-gray-200 dark:border-gray-700 mt-1">
                                    {{ $subQuestion->submission_answer }}
                                </div>
                            </div>

                            <div class="mb-4">
                                <p class="text-sm text-gray-500 uppercase font-bold">Correct / Model Answer:</p>
                                <p class="text-sm text-green-600 mt-1">{{ $subQuestion->question->question_answer }}</p>
                            </div>

                            <div
                                class="grid grid-cols-1 md:grid-cols-2 gap-4 bg-gray-50 dark:bg-gray-700 p-4 rounded text-sm">
                                <div>
                                    <label class="block font-bold mb-1">Score</label>
                                    <input type="number" name="scores[{{ $subQuestion->question_id }}]"
                                        value="{{ $subQuestion->score }}" max="{{ $subQuestion->question->question_score }}"
                                        min="0" class="w-full rounded border-gray-300">
                                </div>
                                <div>
                                    <label class="block font-bold mb-1">Feedback</label>
                                    <input type="text" name="feedbacks[{{ $subQuestion->question_id }}]"
                                        value="{{ $subQuestion->feedback }}" placeholder="Optional feedback..."
                                        class="w-full rounded border-gray-300">
                                </div>
                            </div>

                        </div>
                    @endforeach
                </div>

                <div class="mt-8 flex justify-end fixed bottom-0 right-0 p-6 bg-white w-full border-t shadow-lg">
                    <div class="max-w-7xl mx-auto w-full flex justify-end">
                        <button type="submit"
                            class="px-6 py-3 bg-blue-600 text-white font-bold rounded shadow hover:bg-blue-700">Save
                            Grades</button>
                    </div>
                </div>
            </form>

            <div class="h-20"></div> <!-- Spacer for fixed footer -->
        </div>
    </div>
</x-app-layout>