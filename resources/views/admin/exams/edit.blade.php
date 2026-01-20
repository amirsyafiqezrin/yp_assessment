<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Edit Exam') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form action="{{ route('admin.exams.update', $exam) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <x-input-label for="title" :value="__('Exam Title')" />
                            <x-text-input id="title" class="block mt-1 w-full" type="text" name="title"
                                :value="$exam->title" required autofocus />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="subject_id" :value="__('Subject')" />
                            <select id="subject_id" name="subject_id"
                                class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                required>
                                @foreach(\App\Models\Subject::all() as $subject)
                                    <option value="{{ $subject->id }}" {{ $exam->subject_id == $subject->id ? 'selected' : '' }}>
                                        {{ $subject->name }} ({{ $subject->code }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="mb-4">
                                <x-input-label for="start_time" :value="__('Start Time (Optional)')" />
                                <x-text-input id="start_time" class="block mt-1 w-full" type="datetime-local"
                                    name="start_time" :value="$exam->start_time" />
                            </div>
                            <div class="mb-4">
                                <x-input-label for="end_time" :value="__('End Time (Optional)')" />
                                <x-text-input id="end_time" class="block mt-1 w-full" type="datetime-local"
                                    name="end_time" :value="$exam->end_time" />
                            </div>
                        </div>

                        <div class="mb-4">
                            <x-input-label for="time_limit" :value="__('Time Limit (in Minutes)')" />
                            <x-text-input id="time_limit" class="block mt-1 w-full" type="number" name="time_limit"
                                :value="$exam->time_limit" required min="5" />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('admin.exams.index') }}"
                                class="text-gray-500 hover:text-gray-700 mr-4">Cancel</a>
                            <x-primary-button>
                                {{ __('Update Exam') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>