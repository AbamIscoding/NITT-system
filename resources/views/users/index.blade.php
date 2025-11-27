<x-layouts.app :title="__('Users')">
    <div class="max-w-4xl mx-auto py-8">
        <div class="flex items-center justify-between mb-4">
            <h1 class="text-2xl font-bold">Users</h1>
            <a href="{{ route('users.create') }}"
               class="px-3 py-2 bg-blue-600 text-white text-sm rounded">
                + New User
            </a>
        </div>

        @if(session('success'))
            <div class="mb-4 p-3 border border-green-300 rounded text-sm">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-4 p-3 border border-red-300 rounded text-sm">
                {{ session('error') }}
            </div>
        @endif

        @if($users->isEmpty())
            <p class="text-sm text-gray-500">No users yet.</p>
        @else
            <table class="w-full text-sm border">
                <thead>
                    <tr class="bg-gray-100 dark:bg-zinc-700">
                        <th class="p-2 border text-left">Name</th>
                        <th class="p-2 border text-left">Email</th>
                        <th class="p-2 border text-left">Role</th>
                        <th class="p-2 border text-left">Status</th>
                        <th class="p-2 border text-left">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $user)
                        <tr class="border-b">
                            <td class="p-2 border">{{ $user->name }}</td>
                            <td class="p-2 border">{{ $user->email }}</td>
                            <td class="p-2 border">
                                {{ $user->is_admin ? 'Admin' : 'Staff' }}
                            </td>
                            <td class="p-2 border">
                                @if($user->is_active)
                                    <span class="inline-flex items-center px-2 py-1 text-xs rounded-full bg-green-100 text-green-700">
                                        Active
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-1 text-xs rounded-full bg-red-100 text-red-700">
                                        Disabled
                                    </span>
                                @endif
                            </td>
                            <td class="p-2 border">
                                @if(auth()->id() !== $user->id)
                                    <form method="POST" action="{{ route('users.toggle-active', $user) }}">
                                        @csrf
                                        @method('PATCH')
                                        @if($user->is_active)
                                            <button type="submit"
                                                    class="px-3 py-1 text-xs rounded bg-red-600 text-white">
                                                Disable
                                            </button>
                                        @else
                                            <button type="submit"
                                                    class="px-3 py-1 text-xs rounded bg-green-600 text-white">
                                                Enable
                                            </button>
                                        @endif
                                    </form>
                                @else
                                    <span class="text-xs text-gray-500">This is you</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</x-layouts.app>
