<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ isset($class) ? __('Edit Class') : __('Create New Class') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form
                        action="{{ isset($class) ? route('admin.classes.update', $class) : route('admin.classes.store') }}"
                        method="POST">
                        @csrf
                        @if(isset($class))
                            @method('PUT')
                        @endif

                        <div class="mb-4">
                            <x-input-label for="name" :value="__('Class Name')" />
                            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name"
                                :value="$class->name ?? old('name')" required autofocus />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('admin.classes.index') }}"
                                class="text-gray-500 hover:text-gray-700 mr-4">Cancel</a>
                            <x-primary-button>
                                {{ isset($class) ? __('Update Class') : __('Create Class') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>