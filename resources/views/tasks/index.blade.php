@extends('../layouts/master')

@section('content')
    <x-app-layout>
        <x-slot name="header">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Manage Tasks') }}
            </h2>
        </x-slot>

        <section class="bg-gray-50 dark:bg-gray-900 p-3 sm:p-5">
            <div class="mx-auto max-w-screen-xl px-4 lg:px-12">
                <!-- Start coding here -->
                <div class="bg-white dark:bg-gray-800 relative shadow-md sm:rounded-lg overflow-hidden">
                    <div
                        class="flex flex-col md:flex-row items-center justify-between space-y-3 md:space-y-0 md:space-x-4 p-4">
                        {{-- Message info --}}
                        {{-- {{ dd(Session::all()) }} --}}


                        @if (Session::has('status'))
                            <div id="alert-1"
                                class="flex items-center p-4 mb-4 text-blue-800 rounded-lg bg-blue-50 dark:bg-gray-800 dark:text-blue-400 w-full max-w-4xl mx-auto"
                                role="alert">
                                <svg class="flex-shrink-0 w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                    fill="currentColor" viewBox="0 0 20 20">
                                    <path
                                        d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z" />
                                </svg>
                                <span class="sr-only">Info</span>
                                <div class="ms-10 text-sm font-medium">
                                    {{ Session::get('message') }}
                                </div>
                            </div>
                        @endif

                        {{-- Tombol Add New Task --}}
                        <div id="button-container"
                            class="flex items-center space-x-3 w-full md:w-auto transition-all duration-300 ease-in-out {{ Session::has('status') ? 'ml-0' : 'ml-auto' }}">
                            <a href="{{ route('tasks.create') }}"
                                class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">
                                Add New Task
                            </a>
                        </div>
                    </div>

                    @if ($tasks->isEmpty())
                        <div class="flex justify-center items-center p-8">
                            <h1 class="font-bold text-2xl text-center text-gray-700 dark:text-white">
                                Belum tersedia data, Silahkan tambahkan data task.
                            </h1>
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                                <thead
                                    class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                    <tr>
                                        <th scope="col" class="px-4 py-3">TITLE</th>
                                        <th scope="col" class="px-4 py-3">DESCRIPTION</th>
                                        <th scope="col" class="px-4 py-3">STATUS</th>
                                        <th scope="col" class="px-4 py-3 text-center">ACTION</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($tasks as $task)
                                        <tr class="border-b dark:border-gray-700">
                                            <th scope="row"
                                                class="px-4 py-3 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                                {{ $task->title }}</th>
                                            <td class="px-4 py-3">{{ $task->description }}</td>
                                            <td class="px-4 py-3">{{ $task->status }}</td>
                                            <td class="px-4 py-3 text-center">
                                                <div class="flex justify-center space-x-2">
                                                    <!-- Edit Button -->
                                                    <a href="{{ route('tasks.edit', $task->id) }}"
                                                        class="rounded-lg border border-blue-700 px-4 py-2 text-center text-sm font-medium text-blue-700 hover:bg-blue-700 hover:text-white focus:outline-none focus:ring-4 focus:ring-blue-300 dark:border-blue-500 dark:text-blue-500 dark:hover:bg-blue-600 dark:hover:text-white dark:focus:ring-blue-900 lg:w-auto">
                                                        Edit
                                                    </a>
                                                    <!-- View Button -->
                                                    <a href="{{ route('tasks.show', $task->id) }}"
                                                        class="rounded-lg border border-green-700 px-4 py-2 text-center text-sm font-medium text-green-700 hover:bg-green-700 hover:text-white focus:outline-none focus:ring-4 focus:ring-green-300 dark:border-green-500 dark:text-green-500 dark:hover:bg-green-600 dark:hover:text-white dark:focus:ring-green-900 lg:w-auto">
                                                        View
                                                    </a>
                                                    <!-- Delete Button -->
                                                    <form action="{{ route('tasks.destroy', $task) }}" method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                            class="rounded-lg border border-red-700 px-4 py-2 text-center text-sm font-medium text-red-700 hover:bg-red-700 hover:text-white focus:outline-none focus:ring-4 focus:ring-red-300 dark:border-red-500 dark:text-red-500 dark:hover:bg-red-600 dark:hover:text-white dark:focus:ring-red-900 lg:w-auto">
                                                            Delete
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif

                    <!-- Pagination Links -->
                    <div class="mt-4">
                        {{ $tasks->links() }}
                    </div>
                </div>
            </div>
        </section>
    </x-app-layout>
@endsection
