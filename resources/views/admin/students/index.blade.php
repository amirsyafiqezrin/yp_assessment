<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-black leading-tight">
                {{ __('Manage Students') }}
            </h2>
            <a href="{{ route('admin.students.create') }}"
                class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 text-sm font-medium">Add New
                Student</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <x-auth-session-status class="mb-4" :status="session('success')" />

            <div class="bg-white overflow-hidden shadow-[0_4px_20px_rgba(0,0,0,0.15)] sm:rounded-lg">
                <div class="p-6 text-black">
                    <div class="relative overflow-x-auto">
                        <table class="w-full text-sm text-left rtl:text-right text-black">
                            <thead class="text-xs text-black uppercase bg-gray-100">
                                <tr>
                                    <th scope="col" class="px-6 py-3 font-bold">Name</th>
                                    <th scope="col" class="px-6 py-3 font-bold">Email</th>
                                    <th scope="col" class="px-6 py-3 font-bold">Class</th>
                                    <th scope="col" class="px-6 py-3 font-bold">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($students as $student)
                                    <tr class="bg-white border-b border-gray-200 hover:bg-gray-50 text-black">
                                        <th scope="row" class="px-6 py-4 font-bold text-black whitespace-nowrap">
                                            {{ $student->name }}
                                        </th>
                                        <td class="px-6 py-4">{{ $student->email }}</td>
                                        <td class="px-6 py-4">
                                            @if($student->schoolClass)
                                                <span
                                                    class="bg-blue-100 text-blue-900 border border-blue-200 text-xs font-bold px-2.5 py-0.5 rounded">
                                                    {{ $student->schoolClass->name }}
                                                </span>
                                            @else
                                                <span class="text-gray-500 italic">No Class</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 flex items-center space-x-3">
                                            <a href="{{ route('admin.students.edit', $student) }}" title="Edit"
                                                class="text-yellow-700 hover:text-yellow-900 font-bold">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                    stroke-width="2" stroke="currentColor" class="size-5">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125" />
                                                </svg>
                                            </a>
                                            <form action="{{ route('admin.students.destroy', $student) }}" method="POST"
                                                class="inline-block" onsubmit="return confirm('Are you sure?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" title="Delete"
                                                    class="text-red-700 hover:text-red-900 font-bold pt-1">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                        stroke-width="2" stroke="currentColor" class="size-5">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                                    </svg>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <div class="mt-4">
                            {{ $students->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>