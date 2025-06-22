<div>
    @if($message)
        <div class="bg-{{ $status }}-100 border-l-4 border-{{ $status }}-500 text-{{ $status }}-700 p-4 mb-4">
            {{ $message }}
        </div>
    @endif

    <div class="bg-gray-800/50 rounded-3xl p-6 shadow-lg border border-gray-700 mb-6">
        <h2 class="text-xl font-semibold text-white mb-4">User Accounts</h2>
        
        <p class="text-gray-300 mb-4">
            There {{ $missingAccounts == 1 ? 'is' : 'are' }} {{ $missingAccounts }} employee{{ $missingAccounts == 1 ? '' : 's' }} without user accounts.
        </p>
        
        @if($missingAccounts > 0)
            <button wire:click="createMissingAccounts" class="bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-xl transition-colors">
                Create Missing Accounts
            </button>
        @endif
    </div>
</div>
