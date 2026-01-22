<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-black leading-tight">
            {{ __('Available Exams') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <x-auth-session-status class="mb-4" :status="session('success')" />
            <x-input-error :messages="$errors->all()" class="mb-4" />

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    @if(isset($exams) && $exams->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach($exams as $exam)
                                <div
                                    class="border border-gray-200 dark:border-gray-700 rounded-lg p-6 hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                                    <h3 class="text-xl font-bold mb-2">{{ $exam->title }}</h3>
                                    <p class="text-gray-600 dark:text-gray-400 mb-2">{{ $exam->subject->name }}
                                        ({{ $exam->subject->code }})</p>
                                    <div class="text-sm text-gray-500 mb-4">
                                        <p><span class="font-bold">Duration:</span> {{ $exam->time_limit }} mins</p>
                                        <p><span class="font-bold">Status:</span> Available</p>
                                    </div>
                                    <a href="{{ route('student.exams.show', $exam) }}"
                                        class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-bold text-xs text-white uppercase tracking-widest hover:bg-blue-500 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                        View Details
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500 text-center">No exams available for your class at the moment.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>