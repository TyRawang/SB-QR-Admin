<button hx-post="{{ route('admin.users.toggle-role', $user['id']) }}"
        hx-target="#role-{{ $user['id'] }}"
        hx-swap="innerHTML"
        class="inline-flex items-center rounded-full px-2 py-1 text-xs font-medium cursor-pointer
               {{ $user['role'] === 'admin' ? 'bg-purple-50 text-purple-700 hover:bg-purple-100' : 'bg-blue-50 text-blue-700 hover:bg-blue-100' }}">
    {{ ucfirst($user['role']) }}
</button>
