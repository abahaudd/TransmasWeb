@extends('cms.layout')

@section('title', 'Change Password')

@section('content')
<section class="max-w-7xl mx-auto px-8 lg:px-16 pt-16 pb-24">
    <div class="max-w-xl mx-auto">
        <div class="flex items-center gap-3 mb-6">
            <div class="cms-kicker-line"></div>
            <span class="cms-kicker">My Account</span>
        </div>
        <h1 class="cms-display cms-h2 mb-10">Change Password</h1>

        <div class="cms-panel p-8">
            @if(session('account_success'))
                <div class="cms-alert--success mb-6 p-4 text-[0.85rem]">{{ session('account_success') }}</div>
            @endif

            <form method="POST" action="{{ route('cms.account.password.update') }}" class="flex flex-col gap-5">
                @csrf
                <div>
                    <label for="current_password" class="cms-muted block text-[0.78rem] tracking-[0.08em] uppercase mb-2">Current password *</label>
                    <input type="password" id="current_password" name="current_password" required autocomplete="current-password" class="cms-input">
                    @error('current_password')<p class="cms-alert--error mt-2 px-3 py-2 text-[0.78rem]">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="password" class="cms-muted block text-[0.78rem] tracking-[0.08em] uppercase mb-2">New password *</label>
                    <input type="password" id="password" name="password" required autocomplete="new-password" class="cms-input">
                    @error('password')<p class="cms-alert--error mt-2 px-3 py-2 text-[0.78rem]">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="password_confirmation" class="cms-muted block text-[0.78rem] tracking-[0.08em] uppercase mb-2">Confirm new password *</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" required autocomplete="new-password" class="cms-input">
                </div>
                <button type="submit" class="cms-btn cms-btn--primary self-start mt-2">Change Password</button>
            </form>
        </div>
    </div>
</section>
@endsection
