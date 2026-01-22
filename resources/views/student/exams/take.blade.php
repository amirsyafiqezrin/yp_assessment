<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-black leading-tight">
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
                                    @php
                                        $correctAnswers = $question->question_answer ?? [];
                                        $answerCount = count($correctAnswers);
                                        $inputType = $answerCount > 1 ? 'checkbox' : 'radio';
                                        $inputName = $answerCount > 1 ? "answers[{$question->id}][]" : "answers[{$question->id}]";
                                    @endphp

                                    @if($answerCount > 1)
                                        <p class="text-sm text-blue-600 mb-2 font-medium italic">(Please choose {{ $answerCount }}
                                            answers)</p>
                                    @endif

                                    <div class="space-y-3">
                                        @foreach($question->question_options as $option)
                                            <label
                                                class="flex items-center space-x-3 cursor-pointer p-3 rounded hover:bg-gray-50 border border-gray-200 dark:border-gray-700 dark:hover:bg-gray-700">
                                                <input type="{{ $inputType }}" name="{{ $inputName }}" value="{{ $option }}"
                                                    class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500">
                                                <span class="text-gray-700 dark:text-gray-300">{{ $option }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                @elseif($question->type == \App\Models\Question::TYPE_TEXT)
                                    <textarea name="answers[{{ $question->id }}]" rows="4"
                                        class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                        placeholder="Type your answer here..."></textarea>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-8 flex justify-center">
                    <button type="submit" id="submit-btn"
                        class="px-8 py-3 bg-blue-600 text-white font-bold rounded-lg hover:bg-blue-700 transition duration-150">
                        Submit Exam
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Custom Modal -->
    <div id="confirm-modal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 hidden">
        <div
            class="bg-white dark:bg-gray-800 rounded-lg shadow-xl p-6 w-11/12 max-w-md transform transition-all scale-100">
            <h3 id="modal-title" class="text-xl font-bold text-gray-900 dark:text-gray-100 mb-4">Confirmation</h3>
            <p id="modal-message" class="text-gray-600 dark:text-gray-300 mb-6 text-base leading-relaxed"></p>
            <div class="flex justify-end space-x-3">
                <button id="modal-cancel"
                    class="px-4 py-2 bg-gray-200 text-gray-800 rounded hover:bg-gray-300 font-medium transition">
                    Cancel
                </button>
                <button id="modal-confirm"
                    class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 font-bold transition shadow-md">
                    Confirm
                </button>
            </div>
        </div>
    </div>

    <!-- Countdown Timer Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            let remainingSeconds = {{ $remainingSeconds }};

            const timerElement = document.getElementById('timer');
            const formElement = document.getElementById('exam-form');
            const submitBtn = document.getElementById('submit-btn');

            // Modal Elements
            const modal = document.getElementById('confirm-modal');
            const modalTitle = document.getElementById('modal-title');
            const modalMessage = document.getElementById('modal-message');
            const modalConfirmBtn = document.getElementById('modal-confirm');
            const modalCancelBtn = document.getElementById('modal-cancel');

            let onConfirmAction = null;

            function showModal(title, message, onConfirm, isCritical = false) {
                modalTitle.textContent = title;
                modalMessage.innerHTML = message.replace(/\n/g, '<br>');
                onConfirmAction = onConfirm;

                if (isCritical) {
                    modalCancelBtn.classList.add('hidden');
                    modalConfirmBtn.textContent = "OK, Submit Now";
                    modalTitle.classList.add('text-red-600');
                    modalTitle.classList.remove('text-gray-900', 'dark:text-gray-100');
                } else {
                    modalCancelBtn.classList.remove('hidden');
                    modalConfirmBtn.textContent = "Submit Exam";
                    modalTitle.classList.remove('text-red-600');
                    modalTitle.classList.add('text-gray-900', 'dark:text-gray-100');
                }

                modal.classList.remove('hidden');
            }

            function closeModal() {
                modal.classList.add('hidden');
                onConfirmAction = null;
            }

            modalCancelBtn.addEventListener('click', closeModal);

            modalConfirmBtn.addEventListener('click', function () {
                if (onConfirmAction) onConfirmAction();
                closeModal();
            });

            // Manual Submit Validation
            submitBtn.addEventListener('click', function (e) {
                e.preventDefault();

                let unanswered = [];

                const questionBlocks = document.querySelectorAll('.shadow-sm.sm\\:rounded-lg.p-6');

                questionBlocks.forEach((block, index) => {
                    const qNum = index + 1;
                    const textInput = block.querySelector('textarea');
                    const checkboxes = block.querySelectorAll('input[type="checkbox"]');
                    const radios = block.querySelectorAll('input[type="radio"]');

                    let isAnswered = false;

                    if (textInput) {
                        if (textInput.value.trim().length > 0) isAnswered = true;
                    } else if (checkboxes.length > 0) {
                        for (let cb of checkboxes) {
                            if (cb.checked) {
                                isAnswered = true;
                                break;
                            }
                        }
                    } else if (radios.length > 0) {
                        for (let r of radios) {
                            if (r.checked) {
                                isAnswered = true;
                                break;
                            }
                        }
                    }

                    if (!isAnswered) {
                        unanswered.push(qNum);
                    }
                });

                let title = "Submit Exam?";
                let message = "Are you sure you want to submit? You cannot change answers after submitting.";

                if (unanswered.length > 0) {
                    title = "Unanswered Questions Warning";
                    message = `<strong class="text-red-600">You have NOT answered Question(s): ${unanswered.join(", ")}.</strong><br><br>Are you sure you want to submit without answering them?`;
                }

                showModal(title, message, function () {
                    submitForm();
                });
            });

            let isSubmitting = false;

            function submitForm() {
                isSubmitting = true;
                const inputs = formElement.querySelectorAll('[required]');
                inputs.forEach(input => input.removeAttribute('required'));
                formElement.submit();
            }

            // --- Navigation Interception Logic ---

            window.addEventListener('keydown', function (e) {
                if (isSubmitting) return;
                if ((e.ctrlKey && e.key === 'r') || e.key === 'F5') {
                    e.preventDefault();
                    showNavigationWarning();
                }
            });

            window.history.pushState({ page: 'exam' }, null, location.href);
            window.history.pushState({ page: 'exam' }, null, location.href);

            window.addEventListener('popstate', function (event) {
                if (isSubmitting) return;

                window.history.pushState({ page: 'exam' }, null, location.href);

                showNavigationWarning();
            });

            document.querySelectorAll('a').forEach(link => {
                link.addEventListener('click', function (e) {
                    if (isSubmitting) return;
                    e.preventDefault();
                    showNavigationWarning();
                });
            });

            window.addEventListener('beforeunload', function (e) {
                if (isSubmitting) return;
                e.preventDefault();
                e.returnValue = '';
            });

            function showNavigationWarning() {
                showModal(
                    "Leave Exam?",
                    "Leaving this page will automatically <strong class='text-red-600'>SUBMIT</strong> your exam.\n\nAre you sure you want to proceed?",
                    function () {
                        submitForm();
                    }
                );
            }

            // --- End Navigation Interception ---

            function updateTimerDisplay() {
                if (remainingSeconds <= 0) {
                    timerElement.innerHTML = "TIME EXPIRED";
                    timerElement.classList.add('animate-pulse');
                    timerElement.classList.remove('text-red-600', 'bg-red-100');
                    timerElement.classList.add('text-white', 'bg-red-600');
                    return;
                }

                const hours = Math.floor(remainingSeconds / 3600);
                const minutes = Math.floor((remainingSeconds % 3600) / 60);
                const seconds = Math.floor(remainingSeconds % 60);

                let timeString = "";
                if (hours > 0) {
                    timeString += hours + (hours === 1 ? " hour " : " hours ");
                }
                timeString += minutes + (minutes === 1 ? " minute " : " minutes ");
                timeString += seconds + (seconds === 1 ? " second" : " seconds");

                timerElement.innerHTML = timeString;

                if (remainingSeconds < 60) {
                    timerElement.classList.remove('text-red-600', 'bg-red-100');
                    timerElement.classList.add('text-white', 'bg-red-600', 'animate-pulse');
                }
            }

            updateTimerDisplay();

            const timerInterval = setInterval(function () {
                remainingSeconds--;

                if (remainingSeconds < 0) {
                    clearInterval(timerInterval);

                    showModal("Time Expired!", "Your time is up! The exam will be submitted automatically now.", function () {
                        submitForm();
                    }, true);

                    return;
                }

                updateTimerDisplay();

            }, 1000);
        });
    </script>
</x-app-layout>