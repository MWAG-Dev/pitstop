<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Profile Information') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __("") }}
        </p>
    </header>

    <div class="mt-6 space-y-6">
        <div>
            <x-input-label for="name" :value="__('Name')" />
            <div class="mt-1 block w-full">{{ $user->name }}</div>
        </div>

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <div class="mt-1 block w-full">{{ $user->email }}</div>

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <form id="send-verification" method="post" action="{{ route('verification.send') }}" class="mt-2">
                    @csrf
                    <p class="text-sm text-gray-800">
                        {{ __('Your email address is unverified.') }}
                        <button type="submit" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>
                </form>

                @if (session('status') === 'verification-link-sent')
                    <p class="mt-2 font-medium text-sm text-green-600">
                        {{ __('A new verification link has been sent to your email address.') }}
                    </p>
                @endif
            @endif
        </div>
    </div>
</section>
