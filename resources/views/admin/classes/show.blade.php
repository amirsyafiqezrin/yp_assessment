<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-black leading-tight">
                {{ $class->name }}
            </h2>
            <a href="{{ route('admin.classes.index') }}" class="text-sm text-gray-600 dark:text-gray-400 hover:underline">Back to List</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <x-auth-session-status class="mb-4" :status="session('success')" />

            <div class="grid grid-cols-1 gap-6">
                <!-- Assigned Subjects (Now Top) -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-bold">Assigned Subjects</h3>
                            <button onclick="document.getElementById('assign-modal').classList.remove('hidden')" class="text-sm bg-blue-100 text-blue-600 px-3 py-1 rounded hover:bg-blue-200">
                                + Manage Subjects
                            </button>
                        </div>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                            @forelse($class->subjects as $subject)
                                <div class="p-4 border border-gray-200 dark:border-gray-700 rounded-lg flex items-center justify-between">
                                    <div>
                                        <p class="font-bold">{{ $subject->name }}</p>
                                        <p class="text-sm text-gray-500">{{ $subject->code }}</p>
                                    </div>
                                    <span class="text-green-500">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                          <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                        </svg>
                                    </span>
                                </div>
                            @empty
                                <p class="text-gray-500 italic col-span-full">No subjects assigned yet.</p>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Enrolled Students (Now Bottom & Styled as List/Table) -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <h3 class="text-lg font-bold mb-4">Enrolled Students ({{ $class->students->count() }})</h3>
                        
                        @if($class->students->count() > 0)
                            <div class="relative overflow-x-auto">
                                <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                        <tr>
                                            <th scope="col" class="px-6 py-3">Student Name</th>
                                            <th scope="col" class="px-6 py-3">Email</th>
                                            <th scope="col" class="px-6 py-3">Joined Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($class->students as $student)
                                            <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                                <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                                    {{ $student->name }}
                                                </th>
                                                <td class="px-6 py-4">{{ $student->email }}</td>
                                                <td class="px-6 py-4">{{ $student->created_at->format('d M Y') }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-gray-500 italic">No students assigned to this class.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Assign Modal -->
    <div id="assign-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white dark:bg-gray-800">
            <div class="mt-3 text-center">
                <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white">Assign Subjects</h3>
                <form action="{{ route('admin.classes.assign-subject', $class->id) }}" method="POST" class="mt-4 text-left">
                    @csrf
                    <div class="max-h-60 overflow-y-auto space-y-2 mb-4">
                        @foreach(\App\Models\Subject::all() as $subject)
                            <label class="flex items-center space-x-2">
                                <input type="checkbox" name="subject_ids[]" value="{{ $subject->id }}" 
                                    {{ $class->subjects->contains($subject) ? 'checked' : '' }}
                                    class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500">
                                <span class="text-gray-700 dark:text-gray-300">{{ $subject->name }} ({{ $subject->code }})</span>
                            </label>
                        @endforeach
                    </div>
                    
                    <div class="items-center px-4 py-3">
                        <button id="ok-btn" type="submit" class="px-4 py-2 bg-blue-600 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-300">
                            Save Changes
                        </button>
                        <button type="button" onclick="document.getElementById('assign-modal').classList.add('hidden')" class="mt-3 px-4 py-2 bg-gray-200 text-gray-800 text-base font-medium rounded-md w-full shadow-sm hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-300">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
