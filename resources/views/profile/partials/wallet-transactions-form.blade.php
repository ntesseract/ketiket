<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Wallet Information') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __('View your current balance and transaction history.') }}
        </p>
    </header>

    <div class="mt-6">
        <div class="bg-gray-100 p-4 rounded-lg">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-sm text-gray-600">{{ __('Current Balance') }}</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $user->getFormattedBalanceAttribute() }}</p>
                </div>
                <a href="#" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    {{ __('Top Up') }}
                </a>
            </div>
        </div>
    </div>

    <div class="mt-6">
        <h3 class="text-lg font-medium text-gray-900">{{ __('Recent Transactions') }}</h3>
        
        @if($user->transactions->count() > 0)
            <div class="mt-4 space-y-3">
                @foreach($user->transactions->take(5) as $transaction)
                    <div class="flex items-center justify-between p-3 bg-white border rounded-lg">
                        <div>
                            <p class="font-medium">{{ $transaction->type }}</p>
                            <p class="text-sm text-gray-600">{{ $transaction->created_at->format('d M Y, H:i') }}</p>
                        </div>
                        <div class="{{ $transaction->amount > 0 ? 'text-green-600' : 'text-red-600' }} font-medium">
                            {{ $transaction->amount > 0 ? '+' : '' }} Rp {{ number_format($transaction->amount / 100, 0, ',', '.') }}
                        </div>
                    </div>
                @endforeach
            </div>
            
            <div class="mt-4">
                <a href="#" class="text-sm text-indigo-600 hover:text-indigo-900">
                    {{ __('View all transactions') }} →
                </a>
            </div>
        @else
            <p class="mt-4 text-sm text-gray-600">{{ __('No transactions found.') }}</p>
        @endif
    </div>
</section>