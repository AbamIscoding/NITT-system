<x-layouts.app :title="__('Users')">
    <div class="max-w-5xl mx-auto py-8">
        {{-- Header --}}
        <div class="flex items-center justify-between mb-4">
            <div>
                <h1 class="text-2xl font-semibold text-slate-900 dark:text-slate-50">
                    Users
                </h1>
                <p class="text-xs text-slate-500 dark:text-slate-300 mt-1">
                    Manage staff access to the Northern Island operations portal.
                </p>
            </div>

            <a href="{{ route('users.create') }}"
               class="inline-flex items-center gap-1 px-3 py-2 bg-sky-600 text-white text-sm rounded-lg shadow hover:bg-sky-700">
                + New User
            </a>
        </div>

        {{-- Alerts --}}
        @if(session('success'))
            <div class="mb-4 rounded-lg border border-emerald-200 bg-emerald-50 px-3 py-2 text-xs text-emerald-800 dark:border-emerald-900 dark:bg-emerald-900/40 dark:text-emerald-200">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-4 rounded-lg border border-rose-200 bg-rose-50 px-3 py-2 text-xs text-rose-800 dark:border-rose-900 dark:bg-rose-900/40 dark:text-rose-200">
                {{ session('error') }}
            </div>
        @endif

        @if($users->isEmpty())
            <div class="border border-slate-200 dark:border-slate-700 rounded-xl bg-white dark:bg-slate-900/80 shadow-sm p-6">
                <p class="text-sm text-slate-500 dark:text-slate-300">
                    No users yet.
                </p>
            </div>
        @else
            <div class="border border-slate-200 dark:border-slate-700 rounded-xl bg-white dark:bg-slate-900/80 shadow-sm overflow-hidden">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-sky-50 dark:bg-slate-800 text-xs text-slate-600 dark:text-slate-300 border-b border-slate-200 dark:border-slate-700">
                            <th class="p-2 px-3 text-left">Name</th>
                            <th class="p-2 px-3 text-left">Email</th>
                            <th class="p-2 px-3 text-left">Role</th>
                            <th class="p-2 px-3 text-left">Status</th>
                            <th class="p-2 px-3 text-left">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @foreach ($users as $user)
                            <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/60">
                                {{-- Name --}}
                                <td class="p-2 px-3 text-slate-900 dark:text-slate-50">
                                    {{ $user->name }}
                                </td>

                                {{-- Email --}}
                                <td class="p-2 px-3 text-slate-700 dark:text-slate-200">
                                    {{ $user->email }}
                                </td>

                                {{-- Role --}}
                                <td class="p-2 px-3">
                                    @if($user->is_admin)
                                        <span class="inline-flex items-center rounded-full bg-sky-100 px-2 py-0.5 text-[11px] font-medium text-sky-700 dark:bg-sky-900/40 dark:text-sky-300">
                                            Admin
                                        </span>
                                    @else
                                        <span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-medium text-slate-700 dark:bg-slate-800 dark:text-slate-200">
                                            Staff
                                        </span>
                                    @endif
                                </td>

                                {{-- Status --}}
                                <td class="p-2 px-3">
                                    @if($user->is_active)
                                        <span class="inline-flex items-center px-2 py-0.5 text-[11px] font-medium rounded-full bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300">
                                            Active
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 text-[11px] font-medium rounded-full bg-rose-100 text-rose-700 dark:bg-rose-900/40 dark:text-rose-300">
                                            Disabled
                                        </span>
                                    @endif
                                </td>

                                {{-- Action --}}
                                <td class="p-2 px-3">
                                    @if(auth()->id() !== $user->id)
                                        <form method="POST" action="{{ route('users.toggle-active', $user) }}">
                                            @csrf
                                            @method('PATCH')

                                            @if($user->is_active)
                                                <button type="submit"
                                                        class="inline-flex items-center px-3 py-1 text-xs font-medium rounded-lg bg-rose-600 text-white hover:bg-rose-700">
                                                    Disable
                                                </button>
                                            @else
                                                <button type="submit"
                                                        class="inline-flex items-center px-3 py-1 text-xs font-medium rounded-lg bg-emerald-600 text-white hover:bg-emerald-700">
                                                    Enable
                                                </button>
                                            @endif
                                        </form>
                                    @else
                                        <span class="text-xs text-slate-400 dark:text-slate-500">
                                            This is you
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</x-layouts.app>
