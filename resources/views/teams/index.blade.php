@extends('layouts.app')

@section('title', 'Teams')

@section('content')
<div class="max-w-7xl mx-auto">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h1 class="text-2xl md:text-3xl lg:text-4xl font-bold text-gray-900 dark:text-white">Teams</h1>
                            <p class="text-gray-600 dark:text-gray-400 mt-1">Manage your organization's teams</p>
                        </div>
                        @if(auth()->user()->isAdmin())
                        <button id="add-team-btn" class="bg-teal-600 hover:bg-teal-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            <span>Create Team</span>
                        </button>
                        @endif
                    </div>

                    @if(session('success'))
                    <div class="mb-6 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-800 dark:text-green-300 px-4 py-3 rounded-lg">
                        {{ session('success') }}
                    </div>
                    @endif

                    @if($errors->any())
                    <div class="mb-6 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-800 dark:text-red-300 px-4 py-3 rounded-lg">
                        <ul class="list-disc list-inside">
                            @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @forelse($teams as $team)
                        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6 hover:shadow-lg transition-shadow">
                            <div class="flex items-center justify-between mb-4">
                                <a href="{{ route('teams.show', $team->id) }}" class="text-lg md:text-xl font-semibold text-gray-900 dark:text-white hover:text-teal-600 dark:hover:text-teal-400">
                                    {{ $team->name }}
                                </a>
                                <div class="flex items-center space-x-2">
                                    <div class="flex items-center justify-center w-10 h-10 bg-teal-600 rounded-full text-white font-semibold">
                                        {{ $team->employees_count }}
                                    </div>
                                </div>
                            </div>
                            <div class="space-y-2 mb-4">
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600 dark:text-gray-400">Employees:</span>
                                    <span class="font-medium text-gray-900 dark:text-white">{{ $team->employees_count }}</span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600 dark:text-gray-400">Gross Salary:</span>
                                    <span class="font-medium text-gray-900 dark:text-white">₹{{ number_format($team->total_salary, 2) }}</span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600 dark:text-gray-400">Net Pay:</span>
                                    <span class="font-medium text-green-600 dark:text-green-400">₹{{ number_format($team->total_net_pay, 2) }}</span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600 dark:text-gray-400">Total Loans:</span>
                                    <span class="font-medium text-gray-900 dark:text-white">₹{{ number_format($team->total_loan, 2) }}</span>
                                </div>
                            </div>
                            @if(auth()->user()->isAdmin())
                            <div class="flex items-center justify-end space-x-2 pt-4 border-t border-gray-200 dark:border-gray-700">
                                <button onclick="openEditModal({{ $team->id }}, '{{ addslashes($team->name) }}')" class="px-3 py-2 text-sm bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg transition-colors flex items-center space-x-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                    <span>Edit</span>
                                </button>
                                <button onclick="openDeleteModal({{ $team->id }}, '{{ addslashes($team->name) }}')" class="px-3 py-2 text-sm bg-red-50 hover:bg-red-100 dark:bg-red-900/20 dark:hover:bg-red-900/30 text-red-600 dark:text-red-400 rounded-lg transition-colors flex items-center space-x-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                    <span>Delete</span>
                                </button>
                            </div>
                            @endif
                        </div>
                        @empty
                        <div class="col-span-full bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-12 text-center">
                            <svg class="w-16 h-16 mx-auto text-gray-400 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                            <h3 class="text-base md:text-lg font-medium text-gray-900 dark:text-white mb-2">No teams yet</h3>
                            <p class="text-gray-600 dark:text-gray-400 mb-4">Get started by creating your first team.</p>
                            @if(auth()->user()->isAdmin())
                            <button onclick="document.getElementById('add-team-btn').click()" class="bg-teal-600 hover:bg-teal-700 text-white px-4 py-2 rounded-lg transition-colors">
                                Create Team
                            </button>
                            @endif
                        </div>
                        @endforelse
                    </div>
                </div>

    <div id="add-team-modal"class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-md w-full">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                <h2 class="text-xl md:text-2xl font-bold text-gray-900 dark:text-white">Create New Team</h2>
                <button id="close-modal-btn" class="text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <form action="{{ route('teams.store') }}" method="POST" class="p-6">
                @csrf
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Team Name *</label>
                    <input type="text" name="name" required class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-teal-500 dark:bg-gray-700 dark:text-white">
                </div>

                <div class="flex items-center justify-end space-x-4">
                    <button type="button" id="cancel-btn" class="px-4 py-2 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white rounded-lg transition-colors">
                        Create Team
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div id="edit-team-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-md w-full">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                <h2 class="text-xl md:text-2xl font-bold text-gray-900 dark:text-white">Edit Team</h2>
                <button onclick="closeEditModal()" class="text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <form id="edit-team-form" method="POST" class="p-6">
                @csrf
                @method('PUT')
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Team Name *</label>
                    <input type="text" id="edit-team-name" name="name" required class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-teal-500 dark:bg-gray-700 dark:text-white">
                </div>

                <div class="flex items-center justify-end space-x-4">
                    <button type="button" onclick="closeEditModal()" class="px-4 py-2 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white rounded-lg transition-colors">
                        Update Team
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div id="delete-team-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-md w-full">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-xl md:text-2xl font-bold text-gray-900 dark:text-white">Delete Team</h2>
            </div>

            <div class="p-6">
                <p class="text-gray-700 dark:text-gray-300 mb-4">Are you sure you want to delete the team "<span id="delete-team-name" class="font-semibold"></span>"?</p>
                <p class="text-sm text-red-600 dark:text-red-400">This action cannot be undone. All team assignments will be removed.</p>
            </div>

            <div class="p-6 border-t border-gray-200 dark:border-gray-700 flex items-center justify-end space-x-4">
                <button type="button" onclick="closeDeleteModal()" class="px-4 py-2 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
                    Cancel
                </button>
                <form id="delete-team-form" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors">
                        Delete Team
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        const sidebar = document.getElementById('sidebar');
        const sidebarToggle = document.getElementById('sidebar-toggle');
        const mobileSidebarToggle = document.getElementById('mobile-sidebar-toggle');

        function toggleSidebar() {
            sidebar.classList.toggle('w-64');
            sidebar.classList.toggle('w-20');
            const isCollapsed = sidebar.classList.contains('w-20');
            localStorage.setItem('sidebarCollapsed', isCollapsed);
        }

        sidebarToggle?.addEventListener('click', toggleSidebar);
        mobileSidebarToggle?.addEventListener('click', toggleSidebar);

        if (localStorage.getItem('sidebarCollapsed') === 'true') {
            sidebar.classList.remove('w-64');
            sidebar.classList.add('w-20');
        }

        const profileMenuBtn = document.getElementById('profile-menu-btn');
        const profileDropdown = document.getElementById('profile-dropdown');

        profileMenuBtn?.addEventListener('click', () => {
            profileDropdown.classList.toggle('hidden');
        });

        document.addEventListener('click', (e) => {
            if (!profileMenuBtn?.contains(e.target) && !profileDropdown?.contains(e.target)) {
                profileDropdown?.classList.add('hidden');
            }
        });

        const addTeamBtn = document.getElementById('add-team-btn');
        const addTeamModal = document.getElementById('add-team-modal');
        const closeModalBtn = document.getElementById('close-modal-btn');
        const cancelBtn = document.getElementById('cancel-btn');

        addTeamBtn?.addEventListener('click', () => {
            addTeamModal.classList.remove('hidden');
        });

        closeModalBtn?.addEventListener('click', () => {
            addTeamModal.classList.add('hidden');
        });

        cancelBtn?.addEventListener('click', () => {
            addTeamModal.classList.add('hidden');
        });

        addTeamModal?.addEventListener('click', (e) => {
            if (e.target === addTeamModal) {
                addTeamModal.classList.add('hidden');
            }
        });

        const darkModeToggle = document.getElementById('dark-mode-toggle');
        const html = document.documentElement;

        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            html.classList.add('dark');
        } else {
            html.classList.remove('dark');
        }

        darkModeToggle?.addEventListener('click', () => {
            if (html.classList.contains('dark')) {
                html.classList.remove('dark');
                localStorage.theme = 'light';
            } else {
                html.classList.add('dark');
                localStorage.theme = 'dark';
            }
        });

        function openEditModal(teamId, teamName) {
            const modal = document.getElementById('edit-team-modal');
            const form = document.getElementById('edit-team-form');
            const nameInput = document.getElementById('edit-team-name');
            
            form.action = `/teams/${teamId}`;
            nameInput.value = teamName;
            modal.classList.remove('hidden');
        }

        function closeEditModal() {
            document.getElementById('edit-team-modal').classList.add('hidden');
        }

        function openDeleteModal(teamId, teamName) {
            const modal = document.getElementById('delete-team-modal');
            const form = document.getElementById('delete-team-form');
            const nameSpan = document.getElementById('delete-team-name');
            
            form.action = `/teams/${teamId}`;
            nameSpan.textContent = teamName;
            modal.classList.remove('hidden');
        }

        function closeDeleteModal() {
            document.getElementById('delete-team-modal').classList.add('hidden');
        }

        document.getElementById('edit-team-modal')?.addEventListener('click', (e) => {
            if (e.target === document.getElementById('edit-team-modal')) {
                closeEditModal();
            }
        });

        document.getElementById('delete-team-modal')?.addEventListener('click', (e) => {
            if (e.target === document.getElementById('delete-team-modal')) {
                closeDeleteModal();
            }
        });
    </script>
@endsection
