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

        @if($users->isEmpty())
            <p class="text-sm text-gray-500">No users yet.</p>
        @else
            <table class="w-full text-sm border">
                <thead>
                    <tr class="bg-gray-100 dark:bg-zinc-700">
                        <th class="p-2 border text-left">Name</th>
                        <th class="p-2 border text-left">Email</th>
                        <th class="p-2 border text-left">Role</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $user)
                        <tr>
                            <td class="p-2 border">{{ $user->name }}</td>
                            <td class="p-2 border">{{ $user->email }}</td>
                            <td class="p-2 border">
                                {{ $user->is_admin ? 'Admin' : 'Staff' }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</x-layouts.app>
