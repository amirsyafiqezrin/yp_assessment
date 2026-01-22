<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-black leading-tight">
                {{ $exam->title }}
            </h2>
            <a href="{{ route('admin.exams.index') }}" class="text-sm text-gray-600 dark:text-gray-400 hover:underline">Back to List</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <x-auth-session-status class="mb-4" :status="session('success')" />
            <x-input-error :messages="$errors->all()" class="mb-4" />

            <!-- Exam Details & Assignment -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="col-span-1 bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100 space-y-2">
                        <h3 class="text-lg font-bold mb-4">Exam Info</h3>
                        <p><strong>Subject:</strong> {{ $exam->subject->name }}</p>
                        <p><strong>Time Limit:</strong> {{ $exam->time_limit }} mins</p>
                        <p><strong>Total Score:</strong> {{ $exam->questions->sum('question_score') }}</p>
                        
                        <hr class="my-4 border-gray-200 dark:border-gray-700">
                        
                        <div class="flex justify-between items-center mb-2">
                            <h4 class="font-bold">Assigned Classes</h4>
                            <button onclick="document.getElementById('assign-class-modal').classList.remove('hidden')" class="text-xs text-blue-600 hover:underline">Assign</button>
                        </div>
                        <ul class="list-disc list-inside text-sm">
                            @forelse($exam->classes as $class)
                                <li>{{ $class->name }}</li>
                            @empty
                                <li class="text-gray-500 italic">No classes assigned.</li>
                            @endforelse
                        </ul>
                    </div>
                </div>

                <!-- Questions List -->
                <div class="col-span-2 bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-bold">Questions</h3>
                            <button onclick="document.getElementById('add-question-modal').classList.remove('hidden')" class="px-3 py-1 bg-green-600 text-white text-xs rounded hover:bg-green-700">Add Question</button>
                        </div>

                        <div class="space-y-4">
                            @forelse($exam->questions as $index => $question)
                                <div class="p-4 border border-gray-200 dark:border-gray-700 rounded-lg">
                                    <div class="flex justify-between">
                                        <h4 class="font-bold">Q{{ $index + 1 }}. {{ $question->question_title }}</h4>
                                        <span class="text-sm bg-gray-100 px-2 py-1 rounded dark:bg-gray-700">{{ $question->question_score }} pts</span>
                                    </div>
                                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                        Type: {{ $question->type == 1 ? 'Multiple Choice' : 'Open Text' }}
                                    </p>
                                    @if($question->type == 1)
                                        <div class="mt-2 text-sm text-gray-500">
                                            <p>Options: {{ implode(', ', $question->question_options) }}</p>
                                            <p class="text-green-600">Correct Answer: {{ is_array($question->question_answer) ? implode(', ', $question->question_answer) : $question->question_answer }}</p>
                                        </div>
                                    @endif

                                    <div class="mt-4 flex space-x-2">
                                        <button onclick="openEditModal({{ json_encode($question) }})" class="text-xs bg-yellow-100 text-yellow-700 px-2 py-1 rounded hover:bg-yellow-200">Edit</button>
                                        
                                        <form action="{{ route('admin.exams.questions.destroy', [$exam, $question]) }}" method="POST" onsubmit="return confirm('Are you sure?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-xs bg-red-100 text-red-700 px-2 py-1 rounded hover:bg-red-200">Remove</button>
                                        </form>
                                    </div>
                                </div>
                            @empty
                                <p class="text-gray-500 text-center py-4">No questions added yet.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Assign Class Modal -->
    <div id="assign-class-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white dark:bg-gray-800">
            <h3 class="text-lg font-medium text-center dark:text-white">Assign Exam to Classes</h3>
            <form action="{{ route('admin.exams.assign-class', $exam) }}" method="POST" class="mt-4">
                @csrf
                <div class="max-h-60 overflow-y-auto space-y-2 mb-4">
                    @php
                        $eligibleClasses = $exam->subject->classes; 
                    @endphp

                    @if($eligibleClasses->count() > 0)
                        @foreach($eligibleClasses as $class)
                            <label class="flex items-center space-x-2">
                                <input type="checkbox" name="class_ids[]" value="{{ $class->id }}" 
                                    {{ $exam->classes->contains($class) ? 'checked' : '' }}
                                    class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500">
                                <span class="text-gray-700 dark:text-gray-300">{{ $class->name }}</span>
                            </label>
                        @endforeach
                    @else
                        <p class="text-gray-500 text-sm italic">No classes are assigned to this subject ({{ $exam->subject->name }}). Assign the subject to a class first.</p>
                    @endif
                </div>
                <div class="flex justify-end space-x-2">
                    <button type="button" onclick="document.getElementById('assign-class-modal').classList.add('hidden')" class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Save</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Add Question Modal -->
    <div id="add-question-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-10 mx-auto p-5 border w-[600px] shadow-lg rounded-md bg-white dark:bg-gray-800">
            <h3 class="text-lg font-medium text-center dark:text-white mb-4">Add New Question</h3>
            <form action="{{ route('admin.exams.questions.store', $exam) }}" method="POST" onsubmit="return validateQuestionForm(this)">
                @csrf
                
                <div class="mb-4">
                    <x-input-label for="q_title" :value="__('Question Text')" />
                    <x-text-input id="q_title" class="block mt-1 w-full" type="text" name="question_title" required />
                </div>

                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <x-input-label for="type" :value="__('Type')" />
                        <select id="type" name="type" onchange="toggleOptions(this.value, 'add')" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">
                            <option value="1">Multiple Choice (MCQ)</option>
                            <option value="2">Open Text</option>
                        </select>
                    </div>
                    <div>
                        <x-input-label for="score" :value="__('Score')" />
                        <x-text-input id="score" class="block mt-1 w-full" type="number" name="question_score" value="1" min="1" required />
                    </div>
                </div>

            <!-- MCQ Options Section -->
                <div id="mcq_section_add" class="mb-4 p-4 bg-gray-50 dark:bg-gray-700 rounded">
                    <label class="block mb-2 font-bold text-sm">Options & Correct Answer</label>
                    <p class="text-xs text-gray-500 mb-2">Check the box next to the correct answer(s).</p>
                    
                    <div id="options_container_add" class="space-y-2">
                        <div class="flex items-center space-x-2 option-row">
                            <input type="checkbox" name="question_answer[]" class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500 answer-checkbox">
                            <input type="text" name="question_options[]" placeholder="Option 1" required class="block w-full text-sm border-gray-300 rounded option-input">
                            <button type="button" class="text-red-500 hover:text-red-700 remove-option hidden">&times;</button>
                        </div>
                        <div class="flex items-center space-x-2 option-row">
                            <input type="checkbox" name="question_answer[]" class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500 answer-checkbox">
                            <input type="text" name="question_options[]" placeholder="Option 2" required class="block w-full text-sm border-gray-300 rounded option-input">
                            <button type="button" class="text-red-500 hover:text-red-700 remove-option hidden">&times;</button>
                        </div>
                    </div>

                    <button type="button" onclick="addOption('add')" class="mt-2 text-sm text-blue-600 hover:underline">+ Add Option</button>
                </div>

                <div id="text_section_add" class="mb-4 hidden">
                     <x-input-label for="model_answer_add" :value="__('Model Answer (For AI Reference)')" />
                     <textarea id="model_answer_add" name="question_answer" rows="3" disabled class="block mt-1 w-full border-gray-300 rounded shadow-sm"></textarea>
                </div>

                <div class="flex justify-end space-x-2">
                    <button type="button" onclick="document.getElementById('add-question-modal').classList.add('hidden')" class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">Add Question</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Question Modal -->
    <div id="edit-question-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-10 mx-auto p-5 border w-[600px] shadow-lg rounded-md bg-white dark:bg-gray-800">
            <h3 class="text-lg font-medium text-center dark:text-white mb-4">Edit Question</h3>
            <form id="edit-question-form" method="POST" onsubmit="return validateQuestionForm(this)">
                @csrf
                @method('PUT')
                
                <div class="mb-4">
                    <x-input-label for="edit_q_title" :value="__('Question Text')" />
                    <x-text-input id="edit_q_title" class="block mt-1 w-full" type="text" name="question_title" required />
                </div>

                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <x-input-label for="edit_type" :value="__('Type')" />
                        <select id="edit_type" name="type" onchange="toggleOptions(this.value, 'edit')" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">
                            <option value="1">Multiple Choice (MCQ)</option>
                            <option value="2">Open Text</option>
                        </select>
                    </div>
                    <div>
                        <x-input-label for="edit_score" :value="__('Score')" />
                        <x-text-input id="edit_score" class="block mt-1 w-full" type="number" name="question_score" required min="1" />
                    </div>
                </div>

                <!-- MCQ Options Section -->
                <div id="mcq_section_edit" class="mb-4 p-4 bg-gray-50 dark:bg-gray-700 rounded">
                    <label class="block mb-2 font-bold text-sm">Options & Correct Answer</label>
                    <div id="options_container_edit" class="space-y-2">
                    </div>
                    <button type="button" onclick="addOption('edit')" class="mt-2 text-sm text-blue-600 hover:underline">+ Add Option</button>
                </div>

                <!-- Text Answer Section -->
                <div id="text_section_edit" class="mb-4 hidden">
                     <x-input-label for="model_answer_edit" :value="__('Model Answer (For AI Reference)')" />
                     <textarea id="model_answer_edit" name="question_answer" rows="3" class="block mt-1 w-full border-gray-300 rounded shadow-sm"></textarea>
                </div>

                <div class="flex justify-end space-x-2">
                    <button type="button" onclick="document.getElementById('edit-question-modal').classList.add('hidden')" class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Update Question</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            setupOptionListeners('add');
            setupOptionListeners('edit');
        });

        function setupOptionListeners(mode) {
             const container = document.getElementById(`options_container_${mode}`);
             
             container.addEventListener('input', function(e) {
                if (e.target.classList.contains('option-input')) {
                    const row = e.target.closest('.option-row');
                    const checkbox = row.querySelector('.answer-checkbox');
                    checkbox.value = e.target.value;
                }
            });

             container.addEventListener('click', function(e) {
                if (e.target.classList.contains('remove-option')) {
                    e.target.closest('.option-row').remove();
                    updateRemoveButtons(mode);
                }
            });
        }

        function addOption(mode, value = '', isCorrect = false) {
            const container = document.getElementById(`options_container_${mode}`);
            const rowCount = container.querySelectorAll('.option-row').length + 1;
            
            const newRow = document.createElement('div');
            newRow.className = 'flex items-center space-x-2 option-row';
            newRow.innerHTML = `
                <input type="checkbox" name="question_answer[]" value="${value}" ${isCorrect ? 'checked' : ''} class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500 answer-checkbox">
                <input type="text" name="question_options[]" value="${value}" placeholder="Option" required class="block w-full text-sm border-gray-300 rounded option-input">
                <button type="button" class="text-red-500 hover:text-red-700 remove-option text-lg font-bold px-2">&times;</button>
            `;
            container.appendChild(newRow);
            updateRemoveButtons(mode);
        }

        function updateRemoveButtons(mode) {
            const container = document.getElementById(`options_container_${mode}`);
            const rows = container.querySelectorAll('.option-row');
            rows.forEach(row => {
                const btn = row.querySelector('.remove-option');
                if (rows.length > 2) {
                    btn.classList.remove('hidden');
                } else {
                    btn.classList.add('hidden');
                }
            });
        }

        function toggleOptions(type, mode) {
            const mcqSection = document.getElementById(`mcq_section_${mode}`);
            const textSection = document.getElementById(`text_section_${mode}`);
            const mcqInputs = mcqSection.querySelectorAll('input, select');
            const textInput = document.getElementById(`model_answer_${mode}`);

            if (type == '1') {
                mcqSection.classList.remove('hidden');
                textSection.classList.add('hidden');
                textInput.disabled = true;
                textInput.name = ""; 
                mcqInputs.forEach(el => el.disabled = false);
            } else {
                mcqSection.classList.add('hidden');
                textSection.classList.remove('hidden');
                textInput.disabled = false;
                textInput.name = "question_answer";
                mcqInputs.forEach(el => el.disabled = true);
            }
        }

        function openEditModal(question) {
            const modal = document.getElementById('edit-question-modal');
            const form = document.getElementById('edit-question-form');
            
            form.action = `/admin/exams/${question.exam_id}/questions/${question.id}`;
            
            document.getElementById('edit_q_title').value = question.question_title;
            document.getElementById('edit_score').value = question.question_score;
            document.getElementById('edit_type').value = question.type;
            
            const container = document.getElementById('options_container_edit');
            container.innerHTML = '';
            
            const textArea = document.getElementById('model_answer_edit');
            textArea.value = '';

            if (question.type == 1) {
                const correctAnswers = Array.isArray(question.question_answer) ? question.question_answer : [question.question_answer];
                
                question.question_options.forEach(opt => {
                     const isCorrect = correctAnswers.includes(opt);
                     addOption('edit', opt, isCorrect);
                });
            } else {
                textArea.value = Array.isArray(question.question_answer) ? question.question_answer[0] : question.question_answer;
                 addOption('edit');
                 addOption('edit');
            }

            toggleOptions(question.type, 'edit');
            modal.classList.remove('hidden');
        }

        function validateQuestionForm(form) {
            const typeSelect = form.querySelector('select[name="type"]');
            
            if (typeSelect.value == '1') {
                const checkboxes = form.querySelectorAll('input[name="question_answer[]"]');
                let checked = false;
                checkboxes.forEach(cb => {
                    if (cb.checked) checked = true;
                });

                if (!checked) {
                    alert('Please select at least one correct answer for the MCQ.');
                    return false;
                }
            }
            return true;
        }
    </script>
</x-app-layout>
