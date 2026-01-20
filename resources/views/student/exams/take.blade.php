<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ $exam->title }}
            </h2>
            <div class="text-red-600 font-bold text-xl bg-red-100 px-4 py-2 rounded" id="timer">
                Loading Timer...
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <form id="exam-form" action="{{ route('student.exams.submit', $exam) }}" method="POST">
                @csrf

                <div class="space-y-6">
                    @foreach($exam->questions as $index => $question)
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                            <div class="mb-4">
                                <span class="font-bold text-lg text-gray-500 dark:text-gray-400">Q{{ $index + 1 }}.</span>
                                <span
                                    class="text-lg font-medium text-gray-900 dark:text-gray-100 ml-2">{{ $question->question_title }}</span>
                                <span class="text-sm text-gray-400 ml-2">({{ $question->question_score }} pts)</span>
                            </div>

                            <div class="mt-4">
                                @if($question->type == \App\Models\Question::TYPE_MCQ)
                                    <div class="space-y-3">
                                        @foreach($question->question_options as $option)
                                            <label
                                                class="flex items-center space-x-3 cursor-pointer p-3 rounded hover:bg-gray-50 dark:hover:bg-gray-700">
                                                <input type="radio" name="answers[{{ $question->id }}]" value="{{ $option }}"
                                                    class="form-radio h-5 w-5 text-blue-600" required>
                                                <span class="text-gray-700 dark:text-gray-300">{{ $option }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                @elseif($question->type == \App\Models\Question::TYPE_TEXT)
                                    <textarea name="answers[{{ $question->id }}]" rows="4"
                                        class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                        placeholder="Type your answer here..." required></textarea>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-8 flex justify-center">
                    <button type="submit"
                        onclick="return confirm('Are you sure you want to submit? You cannot change answers after submitting.')"
                        class="px-8 py-3 bg-blue-600 text-white font-bold rounded-lg hover:bg-blue-700 transition duration-150">
                        Submit Exam
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Countdown Timer Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Calculate deadline based on Started At + Time Limit
            const startedAt = new Date("{{ $submission->started_at }}").getTime();
            const timeLimitMinutes = {{ $exam->time_limit }};
            const deadline = startedAt + (timeLimitMinutes * 60 * 1000);

            const timerElement = document.getElementById('timer');
            const formElement = document.getElementById('exam-form');

            const timerInterval = setInterval(function () {
                const now = new Date().getTime();
                const distance = deadline - now;

                if (distance < 0) {
                    clearInterval(timerInterval);
                    timerElement.innerHTML = "TIME EXPIRED";
                    timerElement.classList.add('animate-pulse');

                    // Auto submit logic
                    alert('Time Expired! Submitting your exam automatically.');
                    // Create dummy inputs for required fields if empty so form submits? 
                    // Actually, better to remove 'required' attributes before submitting or let backend handle incomplete.
                    // For simplicity here, we assume user filled something or we hard submit.

                    // Remove required attributes to force submit
                    const inputs = formElement.querySelectorAll('[required]');
                    inputs.forEach(input => input.removeAttribute('required'));

                    formElement.submit();
                    return;
                }

                // Calculations
                const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((distance % (1000 * 60)) / 1000);

                timerElement.innerHTML =
                    (hours > 0 ? hours + "h " : "") +
                    minutes + "m " +
                    seconds + "s ";

                // Color warning
                if (distance < 60000) { // Less than 1 min
                    timerElement.classList.remove('text-red-600', 'bg-red-100');
                    timerElement.classList.add('text-white', 'bg-red-600', 'animate-pulse');
                }

            }, 1000);
        });
    </script>
</x-app-layout>