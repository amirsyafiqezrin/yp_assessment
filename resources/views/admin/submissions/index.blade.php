<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-black leading-tight">
            {{ __('Student Submissions') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <x-auth-session-status class="mb-4" :status="session('success')" />

            <!-- <div class="space-y-8"> -->

            <!-- Section 1: Pending Reviews -->
            <div class="bg-white overflow-hidden shadow-[0_4px_20px_rgba(0,0,0,0.15)] sm:rounded-lg mb-3">
                <div class="p-6 text-black">
                    <h3 class="font-bold text-lg mb-4 flex items-center gap-2 text-yellow-600">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-5">
                            <path fill-rule="evenodd"
                                d="M18 10a8 8 0 1 1-16 0 8 8 0 0 1 16 0Zm-8-5a.75.75 0 0 1 .75.75v4.5a.75.75 0 0 1-1.5 0v-4.5A.75.75 0 0 1 10 5Zm0 10a1 1 0 1 0 0-2 1 1 0 0 0 0 2Z"
                                clip-rule="evenodd" />
                        </svg>
                        Pending Reviews
                    </h3>

                    @if($pendingSubmissions->isEmpty())
                        <p class="text-gray-500 italic">No submissions pending review.</p>
                    @else
                        <div class="relative overflow-x-auto">
                            <table class="w-full text-sm text-left rtl:text-right text-black">
                                <thead class="text-xs text-black uppercase bg-gray-100">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 font-bold">Student</th>
                                        <th scope="col" class="px-6 py-3 font-bold">Class</th>
                                        <th scope="col" class="px-6 py-3 font-bold">Exam</th>
                                        <th scope="col" class="px-6 py-3 font-bold">Submitted At</th>
                                        <th scope="col" class="px-6 py-3 font-bold">Status</th>
                                        <th scope="col" class="px-6 py-3 font-bold text-right">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($pendingSubmissions as $submission)
                                        <tr class="bg-white border-b border-gray-200 hover:bg-gray-50 text-black">
                                            <th scope="row" class="px-6 py-4 font-bold text-black whitespace-nowrap">
                                                {{ $submission->user->name }}
                                            </th>
                                            <td class="px-6 py-4">{{ $submission->user->schoolClass?->name ?? 'N/A' }}</td>
                                            <td class="px-6 py-4">{{ $submission->exam->title }}</td>
                                            <td class="px-6 py-4">
                                                {{ $submission->submitted_at ? $submission->submitted_at->format('d M H:i') : 'In Progress' }}
                                            </td>
                                            <td class="px-6 py-4">
                                                <span class="text-yellow-600 font-bold text-xs">PENDING</span>
                                            </td>
                                            <td class="px-6 py-4 text-right">
                                                <a href="{{ route('admin.submissions.show', $submission) }}"
                                                    class="text-blue-600 hover:underline font-bold">
                                                    Grade Now
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Section 2: Exam Results (Graded) -->
            <div class="bg-white overflow-hidden shadow-[0_4px_20px_rgba(0,0,0,0.15)] sm:rounded-lg mt-3">
                <div class="p-6 text-black">
                    <h3 class="font-bold text-lg mb-4 flex items-center gap-2 text-green-600">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-5">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm3.857-9.809a.75.75 0 0 0-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 1 0-1.06 1.061l2.5 2.5a.75.75 0 0 0 1.137-.089l4-5.5Z"
                                clip-rule="evenodd" />
                        </svg>
                        Exam Results
                    </h3>

                    @if($gradedSubmissions->isEmpty())
                        <p class="text-gray-500 italic">No exams graded yet.</p>
                    @else
                        <div class="space-y-6">
                            @foreach($gradedSubmissions as $className => $submissions)
                                <div class="border rounded-lg overflow-hidden">
                                    <div class="bg-gray-200 px-4 py-2 font-bold text-black border-b border-gray-300">
                                        Class: {{ $className }}
                                    </div>
                                    <div class="relative overflow-x-auto">
                                        <table class="w-full text-sm text-left rtl:text-right text-black">
                                            <thead class="text-xs text-black uppercase bg-gray-50">
                                                <tr>
                                                    <th scope="col" class="px-6 py-3 font-bold">Student</th>
                                                    <th scope="col" class="px-6 py-3 font-bold">Exam</th>
                                                    <th scope="col" class="px-6 py-3 font-bold">Submitted At</th>
                                                    <th scope="col" class="px-6 py-3 font-bold">Score</th>
                                                    <th scope="col" class="px-6 py-3 font-bold text-right">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($submissions as $submission)
                                                    <tr class="bg-white border-b border-gray-200 hover:bg-gray-50 text-black">
                                                        <th scope="row" class="px-6 py-4 font-bold text-black whitespace-nowrap">
                                                            {{ $submission->user->name }}
                                                        </th>
                                                        <td class="px-6 py-4">{{ $submission->exam->title }}</td>
                                                        <td class="px-6 py-4">
                                                            {{ $submission->submitted_at ? $submission->submitted_at->format('d M H:i') : '-' }}
                                                        </td>
                                                        <td class="px-6 py-4 font-bold text-lg">
                                                            {{ $submission->total_score }} /
                                                            {{ $submission->exam->questions->sum('question_score') }}
                                                        </td>
                                                        <td class="px-6 py-4 text-right">
                                                            <a href="{{ route('admin.submissions.show', $submission) }}"
                                                                class="text-gray-600 hover:text-black font-bold text-xs border border-gray-300 px-2 py-1 rounded hover:bg-gray-100">
                                                                View Paper
                                                            </a>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <!-- </div> -->
        </div>
    </div>
</x-app-layout>