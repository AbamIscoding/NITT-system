<x-layouts.app :title="__('Create User')">
    <div class="max-w-lg mx-auto py-8">
        <a href="{{ route('users.index') }}"
           class="inline-flex items-center gap-1 text-blue-600 hover:underline mb-4">
            ← Back to users
        </a>

        <h1 class="text-2xl font-bold mb-4">Create User</h1>

        @if ($errors->any())
            <div class="mb-4 p-3 bg-red-100 border border-red-300 rounded text-sm">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('users.store') }}" class="space-y-4">
            @csrf

            <div>
                <label class="block text-sm font-medium mb-1">Name</label>
                <input type="text" name="name" class="w-full border rounded p-2"
                       value="{{ old('name') }}" required>
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Email</label>
                <input type="email" name="email" class="w-full border rounded p-2"
                       value="{{ old('email') }}" required>
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Password</label>
                <input type="password" name="password" class="w-full border rounded p-2" required>
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Confirm Password</label>
                <input type="password" name="password_confirmation" class="w-full border rounded p-2" required>
            </div>

            <div class="flex items-center gap-2">
                <input type="checkbox" id="is_admin" name="is_admin" value="1"
                       class="border rounded"
                       {{ old('is_admin') ? 'checked' : '' }}>
                <label for="is_admin" class="text-sm">Admin account</label>
            </div>

            <button type="submit"
                    class="px-4 py-2 bg-blue-600 text-white rounded">
                Save User
            </button>
        </form>
    </div>
</x-layouts.app>
