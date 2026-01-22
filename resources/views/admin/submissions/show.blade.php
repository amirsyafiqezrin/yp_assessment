<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-black leading-tight">
                {{ __('Grading Submission') }} - {{ $submission->user->name }}
            </h2>
            <a href="{{ route('admin.submissions.index') }}"
                class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300 text-sm font-bold shadow-sm">
                &larr; Back to Submissions
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <x-auth-session-status class="mb-4" :status="session('success')" />

            @php
                $pendingCount = $submission->submissionQuestions->where('status', \App\Models\SubmissionQuestion::STATUS_PENDING)->count();
                $isfullyGraded = $pendingCount === 0;
            @endphp

            @if($isfullyGraded)
                <div class="space-y-6">
                    <div
                        class="bg-white p-6 rounded-lg shadow-[0_4px_20px_rgba(0,0,0,0.15)] flex justify-between items-center">
                        <div>
                            <h3 class="text-lg font-bold">Exam Result</h3>
                            <p class="text-sm text-gray-600">Total Score: <span
                                    class="text-black font-bold text-xl">{{ $submission->total_score }}</span> /
                                {{ $submission->exam->questions->sum('question_score') }}
                            </p>
                        </div>
                        <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full font-bold text-sm">Graded</span>
                    </div>

                    @foreach($submission->submissionQuestions as $index => $subQuestion)
                        <div class="bg-white overflow-hidden shadow-[0_4px_20px_rgba(0,0,0,0.15)] sm:rounded-lg p-6">
                            <div class="mb-2 flex justify-between">
                                <h4 class="font-bold text-black">
                                    Q{{ $index + 1 }}: {{ $subQuestion->question->question_title }}
                                </h4>
                                <span class="text-sm bg-gray-100 px-2 py-1 rounded font-bold">
                                    {{ $subQuestion->score }} / {{ $subQuestion->question->question_score }} pts
                                </span>
                            </div>

                            <div class="mb-4">
                                <p class="text-xs text-gray-500 uppercase font-bold mb-1">Student Answer:</p>
                                <div class="p-3 bg-gray-50 rounded border border-gray-200 text-black">
                                    @if($subQuestion->question->type == 1 && is_array(json_decode($subQuestion->submission_answer)))
                                        {{ implode(', ', json_decode($subQuestion->submission_answer)) }}
                                    @else
                                        {{ $subQuestion->submission_answer }}
                                    @endif
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                                <div>
                                    <p class="text-xs text-gray-500 uppercase font-bold mb-1">Correct Answer:</p>
                                    <p class="text-sm text-green-600 font-medium">
                                        {{ is_array($subQuestion->question->question_answer) ? implode(', ', $subQuestion->question->question_answer) : $subQuestion->question->question_answer }}
                                    </p>
                                </div>
                                @if($subQuestion->feedback)
                                    <div>
                                        <p class="text-xs text-gray-500 uppercase font-bold mb-1">Feedback:</p>
                                        <p class="text-sm text-gray-700 italic">"{{ $subQuestion->feedback }}"</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach

                    <div class="flex justify-end">
                        <button onclick="document.getElementById('regrade-form').classList.toggle('hidden')"
                            class="text-blue-600 text-sm hover:underline">Edit Grades</button>
                    </div>

                    <form id="regrade-form" action="{{ route('admin.submissions.update', $submission) }}" method="POST"
                        class="hidden mt-4 pt-4 border-t">
                        @csrf
                        @method('PUT')
                        <h4 class="font-bold text-lg mb-4">Edit Grades</h4>
                        @foreach($submission->submissionQuestions as $subQuestion)
                            <div class="mb-4 bg-gray-50 p-4 rounded">
                                <p class="font-bold text-sm mb-2">Q{{ $loop->iteration }}
                                    ({{ $subQuestion->question->question_score }} pts)</p>
                                <div class="grid grid-cols-2 gap-2">
                                    <input type="number" name="scores[{{ $subQuestion->question_id }}]"
                                        value="{{ $subQuestion->score }}" max="{{ $subQuestion->question->question_score }}"
                                        min="0" class="rounded border-gray-300">
                                    <input type="text" name="feedback[{{ $subQuestion->question_id }}]"
                                        value="{{ $subQuestion->feedback }}" placeholder="Feedback"
                                        class="rounded border-gray-300">
                                </div>
                            </div>
                        @endforeach
                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Update Grades</button>
                    </form>
                </div>

            @else
                <!-- Pending Grading Form -->
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
                                        class="p-3 bg-gray-50 dark:bg-gray-900 rounded border border-gray-200 dark:border-gray-700 mt-1 text-black">
                                        @if($subQuestion->question->type == 1 && is_array(json_decode($subQuestion->submission_answer)))
                                            {{ implode(', ', json_decode($subQuestion->submission_answer)) }}
                                        @else
                                            {{ $subQuestion->submission_answer }}
                                        @endif
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <p class="text-sm text-gray-500 uppercase font-bold">Correct / Model Answer:</p>
                                    <p class="text-sm text-green-600 mt-1">
                                        {{ is_array($subQuestion->question->question_answer) ? implode(', ', $subQuestion->question->question_answer) : $subQuestion->question->question_answer }}
                                    </p>
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
                                        <input type="text" name="feedback[{{ $subQuestion->question_id }}]"
                                            placeholder="Optional feedback..." class="w-full rounded border-gray-300"
                                            value="{{ $subQuestion->feedback }}">
                                    </div>
                                </div>

                            </div>
                        @endforeach
                    </div>

                    <div class="mt-8 flex justify-end fixed bottom-0 right-0 p-6 bg-white w-full border-t shadow-lg z-10">
                        <div class="max-w-7xl mx-auto w-full flex justify-end">
                            <button type="submit"
                                class="px-6 py-3 bg-blue-600 text-white font-bold rounded shadow hover:bg-blue-700">Save
                                Grades</button>
                        </div>
                    </div>
                </form>
                <div class="h-20"></div>
            @endif

        </div>
    </div>
</x-app-layout>