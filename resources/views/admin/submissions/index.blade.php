<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Student Submissions') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <x-auth-session-status class="mb-4" :status="session('success')" />

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="relative overflow-x-auto">
                        <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                            <thead
                                class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                <tr>
                                    <th scope="col" class="px-6 py-3">Student</th>
                                    <th scope="col" class="px-6 py-3">Exam</th>
                                    <th scope="col" class="px-6 py-3">Submitted At</th>
                                    <th scope="col" class="px-6 py-3">Score</th>
                                    <th scope="col" class="px-6 py-3">Status</th>
                                    <th scope="col" class="px-6 py-3">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($submissions as $submission)
                                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                        <th scope="row"
                                            class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                            {{ $submission->user->name }}
                                        </th>
                                        <td class="px-6 py-4">{{ $submission->exam->title }}</td>
                                        <td class="px-6 py-4">
                                            {{ $submission->submitted_at ? $submission->submitted_at->format('d M H:i') : 'In Progress' }}
                                        </td>
                                        <td class="px-6 py-4 font-bold">{{ $submission->total_score }}</td>
                                        <td class="px-6 py-4">
                                            @php
                                                $pending = $submission->submissionQuestions->where('status', \App\Models\SubmissionQuestion::STATUS_PENDING)->count();
                                            @endphp
                                            @if($pending > 0)
                                                <span class="text-yellow-600 font-bold flex items-center gap-1">
                                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                                        fill="currentColor" class="size-4">
                                                        <path fill-rule="evenodd"
                                                            d="M18 10a8 8 0 1 1-16 0 8 8 0 0 1 16 0Zm-8-5a.75.75 0 0 1 .75.75v4.5a.75.75 0 0 1-1.5 0v-4.5A.75.75 0 0 1 10 5Zm0 10a1 1 0 1 0 0-2 1 1 0 0 0 0 2Z"
                                                            clip-rule="evenodd" />
                                                    </svg>
                                                    {{ $pending }} Pending Review
                                                </span>
                                            @else
                                                <span class="text-green-600 flex items-center gap-1">
                                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                                        fill="currentColor" class="size-4">
                                                        <path fill-rule="evenodd"
                                                            d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm3.857-9.809a.75.75 0 0 0-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 1 0-1.06 1.061l2.5 2.5a.75.75 0 0 0 1.137-.089l4-5.5Z"
                                                            clip-rule="evenodd" />
                                                    </svg>
                                                    Graded
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4">
                                            <!-- Grade/View -->
                                            <a href="{{ route('admin.submissions.show', $submission) }}"
                                                title="Grade / View Details"
                                                class="text-blue-600 dark:text-blue-500 hover:text-blue-800">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                    stroke-width="1.5" stroke="currentColor" class="size-5">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                                                </svg>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="mt-4">
                            {{ $submissions->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>