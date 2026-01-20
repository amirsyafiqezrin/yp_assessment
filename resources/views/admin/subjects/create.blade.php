<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ isset($subject) ? __('Edit Subject') : __('Create New Subject') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form
                        action="{{ isset($subject) ? route('admin.subjects.update', $subject) : route('admin.subjects.store') }}"
                        method="POST">
                        @csrf
                        @if(isset($subject))
                            @method('PUT')
                        @endif

                        <div class="mb-4">
                            <x-input-label for="name" :value="__('Subject Name')" />
                            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name"
                                :value="$subject->name ?? old('name')" required autofocus />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="code" :value="__('Subject Code (e.g. CS101)')" />
                            <x-text-input id="code" class="block mt-1 w-full" type="text" name="code"
                                :value="$subject->code ?? old('code')" required />
                            <x-input-error :messages="$errors->get('code')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('admin.subjects.index') }}"
                                class="text-gray-500 hover:text-gray-700 mr-4">Cancel</a>
                            <x-primary-button>
                                {{ isset($subject) ? __('Update Subject') : __('Create Subject') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>