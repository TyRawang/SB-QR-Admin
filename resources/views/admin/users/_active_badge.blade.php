<button hx-post="{{ route('admin.users.toggle-active', $user['id']) }}"
        hx-target="#active-{{ $user['id'] }}"
        hx-swap="innerHTML"
        class="inline-flex items-center rounded-full px-2 py-1 text-xs font-medium cursor-pointer
               {{ $user['is_active'] ? 'bg-green-50 text-green-700 hover:bg-green-100' : 'bg-red-50 text-red-700 hover:bg-red-100' }}">
    {{ $user['is_active'] ? 'Active' : 'Inactive' }}
</button>
