<div class="space-y-4">
    @forelse($feedback as $fb)
    <div class="rounded-lg bg-white shadow p-6">
        <div class="flex items-start justify-between">
            <div class="flex-1">
                <div class="flex items-center gap-3 mb-2">
                    @if($fb['profiles'] ?? null)
                        <a href="{{ route('admin.users.show', $fb['profiles']['id']) }}" class="text-sm font-medium text-blue-600 hover:text-blue-900">
                            {{ $fb['profiles']['display_name'] ?? $fb['profiles']['email'] ?? 'Unknown' }}
                        </a>
                    @else
                        <span class="text-sm text-gray-400">Anonymous</span>
                    @endif
                    @if($fb['rating'])
                        <span class="text-sm">
                            @for($i = 1; $i <= 5; $i++)
                                <span class="{{ $i <= $fb['rating'] ? 'text-yellow-400' : 'text-gray-300' }}">&#9733;</span>
                            @endfor
                        </span>
                    @endif
                    <span class="text-xs text-gray-500">{{ ($fb['created_at'] ?? null) ? \Carbon\Carbon::parse($fb['created_at'])->format('M j, Y g:i A') : '' }}</span>
                </div>
                <p class="text-sm text-gray-900">{{ $fb['message'] }}</p>

                @if(!empty($fb['metadata']) && is_array($fb['metadata']))
                <div class="mt-3 flex gap-4 text-xs text-gray-500">
                    @if(isset($fb['metadata']['app_version']))
                        <span>App: {{ $fb['metadata']['app_version'] }}</span>
                    @endif
                    @if(isset($fb['metadata']['os']))
                        <span>OS: {{ $fb['metadata']['os'] }}</span>
                    @endif
                    @if(isset($fb['metadata']['platform']))
                        <span>Platform: {{ $fb['metadata']['platform'] }}</span>
                    @endif
                </div>
                @endif
            </div>
            <div>
                <x-confirm-modal :action="route('admin.feedback.destroy', $fb['id'])" method="DELETE"
                    title="Delete Feedback" message="Are you sure you want to delete this feedback?"
                    buttonText="Delete" buttonColor="red">
                    <x-slot:trigger>
                        <button class="text-red-600 hover:text-red-900 text-sm">Delete</button>
                    </x-slot:trigger>
                </x-confirm-modal>
            </div>
        </div>
    </div>
    @empty
    <div class="rounded-lg bg-white shadow px-6 py-8 text-center text-sm text-gray-500">No feedback found.</div>
    @endforelse

    <x-pagination :page="$page" :total-pages="$totalPages" :total="$total" :per-page="$perPage" />
</div>
