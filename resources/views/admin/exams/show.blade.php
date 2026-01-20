<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
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
                                            <p class="text-green-600">Correct Answer: {{ $question->question_answer }}</p>
                                        </div>
                                    @endif
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
                        // Filter classes: Only those that have the exam's subject assigned
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
            <form action="{{ route('admin.exams.questions.store', $exam) }}" method="POST">
                @csrf
                
                <div class="mb-4">
                    <x-input-label for="q_title" :value="__('Question Text')" />
                    <x-text-input id="q_title" class="block mt-1 w-full" type="text" name="question_title" required />
                </div>

                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <x-input-label for="type" :value="__('Type')" />
                        <select id="type" name="type" onchange="toggleOptions(this.value)" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">
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
                <div id="mcq_section" class="mb-4 p-4 bg-gray-50 dark:bg-gray-700 rounded">
                    <label class="block mb-2 font-bold text-sm">Options (Enter at least 2)</label>
                    <div class="space-y-2">
                        <input type="text" name="question_options[]" placeholder="Option A" class="block w-full text-sm border-gray-300 rounded">
                        <input type="text" name="question_options[]" placeholder="Option B" class="block w-full text-sm border-gray-300 rounded">
                        <input type="text" name="question_options[]" placeholder="Option C" class="block w-full text-sm border-gray-300 rounded">
                        <input type="text" name="question_options[]" placeholder="Option D" class="block w-full text-sm border-gray-300 rounded">
                    </div>
                    <div class="mt-4">
                         <x-input-label for="answer" :value="__('Correct Answer (Must match one option exactly)')" />
                         <x-text-input id="answer" class="block mt-1 w-full" type="text" name="question_answer" placeholder="e.g. Option A's text" />
                    </div>
                </div>

                <!-- Text Answer Section (Hidden by default) -->
                <div id="text_section" class="mb-4 hidden">
                     <x-input-label for="model_answer" :value="__('Model Answer (For AI Reference)')" />
                     <textarea id="model_answer" name="question_answer" rows="3" disabled class="block mt-1 w-full border-gray-300 rounded shadow-sm"></textarea>
                </div>

                <div class="flex justify-end space-x-2">
                    <button type="button" onclick="document.getElementById('add-question-modal').classList.add('hidden')" class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">Add Question</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function toggleOptions(type) {
            const mcqSection = document.getElementById('mcq_section');
            const textSection = document.getElementById('text_section');
            const mcqInputs = mcqSection.querySelectorAll('input, select');
            const textInput = document.getElementById('model_answer');

            if (type == '1') { // MCQ
                mcqSection.classList.remove('hidden');
                textSection.classList.add('hidden');
                textInput.disabled = true;
                mcqInputs.forEach(el => el.disabled = false);
            } else { // Text
                mcqSection.classList.add('hidden');
                textSection.classList.remove('hidden');
                textInput.disabled = false;
                mcqInputs.forEach(el => el.disabled = true);
            }
        }
    </script>
</x-app-layout>
