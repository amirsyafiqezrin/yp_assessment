<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-black leading-tight">
                {{ __('Review Exam') }}: {{ $exam->title }}
            </h2>
            <a href="{{ route('student.exams.history') }}"
                class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300 text-sm font-bold shadow-sm">
                &larr; Back to History
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <!-- Overall Result -->
            <div class="bg-white overflow-hidden shadow-[0_4px_20px_rgba(0,0,0,0.15)] sm:rounded-lg mb-6 p-6 flex justify-between items-center">
                <div>
                    <h3 class="text-lg font-bold">Total Score</h3>
                    <p class="text-sm text-gray-600">You scored: <span class="text-black font-bold text-xl">{{ $submission->total_score }}</span> / {{ $exam->questions->sum('question_score') }}</p>
                </div>
                <div>
                     <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full font-bold text-sm">Submitted on {{ $submission->submitted_at->format('d M Y, H:i') }}</span>
                </div>
            </div>

            <div class="space-y-6">
                @foreach($exam->questions as $index => $question)
                    @php
                        $subQuestion = $submission->submissionQuestions->where('question_id', $question->id)->first();
                        $userAnswer = $subQuestion ? $subQuestion->submission_answer : null;
                        
                        if($question->type == \App\Models\Question::TYPE_MCQ && $userAnswer && is_string($userAnswer)) {
                             if(str_starts_with($userAnswer, '[') && str_ends_with($userAnswer, ']')) {
                                  $decoded = json_decode($userAnswer, true);
                                  if(is_array($decoded)) $userAnswer = $decoded;
                                  else $userAnswer = [$userAnswer];
                             } else {
                                  $userAnswer = [$userAnswer];
                             }
                        }

                        $borderClass = '';
                        $statusBadge = '';
                        
                        if($subQuestion) {
                            if ($subQuestion->status == \App\Models\SubmissionQuestion::STATUS_CORRECT) {
                                $borderClass = 'border-l-4 border-green-500';
                                $statusBadge = '<span class="text-xs font-bold bg-green-100 text-green-700 px-2 py-1 rounded">Correct</span>';
                            } elseif ($subQuestion->status == \App\Models\SubmissionQuestion::STATUS_INCORRECT) {
                                $borderClass = 'border-l-4 border-red-500';
                                $statusBadge = '<span class="text-xs font-bold bg-red-100 text-red-700 px-2 py-1 rounded">Incorrect</span>';
                            } else {
                                $borderClass = 'border-l-4 border-yellow-500';
                                $statusBadge = '<span class="text-xs font-bold bg-yellow-100 text-yellow-700 px-2 py-1 rounded">Pending Review</span>';
                            }
                        }
                    @endphp

                    <div class="bg-white overflow-hidden shadow-[0_4px_20px_rgba(0,0,0,0.15)] sm:rounded-lg p-6 {{ $borderClass }}">
                        <div class="mb-4 flex justify-between items-start">
                             <div>
                                <span class="font-bold text-lg text-gray-500">Q{{ $index + 1 }}.</span>
                                <span class="text-lg font-medium text-gray-900 ml-2">{{ $question->question_title }}</span>
                                <span class="text-sm text-gray-400 ml-2">({{ $subQuestion->score ?? 0 }} / {{ $question->question_score }} pts)</span>
                             </div>
                             <div>{!! $statusBadge !!}</div>
                        </div>

                        <div class="mt-4">
                            @if($question->type == \App\Models\Question::TYPE_MCQ)
                                <div class="space-y-3">
                                    @php
                                        $correctAnswers = $question->question_answer ?? [];
                                        $inputType = count($correctAnswers) > 1 ? 'checkbox' : 'radio';
                                    @endphp

                                    @if(count($correctAnswers) > 1)
                                        <p class="text-xs text-blue-600 mb-2 font-medium italic">(Multiple Answers Question)</p>
                                    @endif

                                    @foreach($question->question_options ?? [] as $option)
                                        @php
                                            $isChecked = false;
                                            if (is_array($userAnswer)) {
                                                $isChecked = in_array($option, $userAnswer);
                                            } elseif (!is_null($userAnswer)) {
                                                $isChecked = (string)$userAnswer === (string)$option;
                                            }

                                            $isCorrect = in_array($option, $question->question_answer ?? []);
                                            
                                            $optionClass = "border-gray-200 text-gray-700";
                                            $badge = "";

                                            if ($isChecked) {
                                                if ($isCorrect) {
                                                    $optionClass = "bg-green-50 border-green-300 text-green-800 font-bold";
                                                    $badge = '<span class="text-green-600 text-xs ml-2 font-bold">✓ Your Answer</span>';
                                                } else {
                                                    $optionClass = "bg-red-50 border-red-300 text-red-800 line-through";
                                                    $badge = '<span class="text-red-600 text-xs ml-2 font-bold">✗ Your Answer</span>';
                                                }
                                            } elseif ($isCorrect) {
                                                $optionClass = "bg-green-50 border-green-200 text-green-800 border-dashed border-2";
                                                $badge = '<span class="text-green-600 text-xs ml-2 font-bold">← Correct Answer</span>';
                                            }
                                        @endphp
                                        <div class="flex items-center space-x-3 p-3 rounded border {{ $optionClass }}">
                                            <input type="{{ $inputType }}" disabled {{ $isChecked ? 'checked' : '' }}
                                                class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-0 cursor-not-allowed disabled:opacity-50">
                                            <span>{{ $option }}</span>
                                            {!! $badge !!}
                                        </div>
                                    @endforeach
                                </div>
                            @elseif($question->type == \App\Models\Question::TYPE_TEXT)
                                <div class="mt-2">
                                    <p class="text-xs text-gray-500 uppercase font-bold mb-1">Your Answer:</p>
                                    <div class="p-3 bg-gray-50 rounded border border-gray-200 text-black">
                                        {{ $userAnswer }}
                                    </div>
                                    
                                    @if($subQuestion->status != \App\Models\SubmissionQuestion::STATUS_CORRECT && $subQuestion->status != \App\Models\SubmissionQuestion::STATUS_PENDING)
                                         <div class="mt-3">
                                            <p class="text-xs text-gray-500 uppercase font-bold mb-1">Correct Answer:</p>
                                            <div class="p-3 bg-green-50 rounded border border-green-200 text-green-800">
                                                {{ is_array($question->question_answer) ? implode(', ', $question->question_answer) : $question->question_answer }}
                                            </div>
                                         </div>
                                    @endif
                                </div>
                            @endif
                        </div>
                        
                        @if($subQuestion && $subQuestion->feedback)
                            <div class="mt-4 p-3 bg-gray-100 rounded text-sm italic text-gray-700">
                                <strong>Feedback:</strong> {{ $subQuestion->feedback }}
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
            
            <div class="mt-8 mb-12 flex justify-center">
                 <a href="{{ route('student.exams.history') }}" class="px-6 py-3 bg-gray-800 text-white font-bold rounded shadow hover:bg-gray-700">Return to History</a>
            </div>
        </div>
    </div>
</x-app-layout>
