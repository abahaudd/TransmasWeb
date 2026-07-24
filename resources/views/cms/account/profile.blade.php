@extends('cms.layout')

@section('title', 'My Profile')

@section('content')
<section class="max-w-7xl mx-auto px-8 lg:px-16 pt-16 pb-24">
    <div class="max-w-xl mx-auto">
        <div class="flex items-center gap-3 mb-6">
            <div class="cms-kicker-line"></div>
            <span class="cms-kicker">My Account</span>
        </div>
        <h1 class="cms-display cms-h2 mb-10">Profile</h1>

        <div class="cms-panel p-8">
            @if(session('account_success'))
                <div class="cms-alert--success mb-6 p-4 text-[0.85rem]">{{ session('account_success') }}</div>
            @endif

            <div class="grid grid-cols-2 gap-5 mb-2 pb-5" style="border-bottom: 1px solid var(--menu-border);">
                <div>
                    <div class="cms-muted text-[0.78rem] tracking-[0.08em] uppercase mb-1">Username</div>
                    <div class="cms-text text-[0.9rem]">{{ $user->username }}</div>
                </div>
                <div>
                    <div class="cms-muted text-[0.78rem] tracking-[0.08em] uppercase mb-1">Login Email</div>
                    <div class="cms-text text-[0.9rem]">{{ $user->email }}</div>
                </div>
            </div>

            <form method="POST" action="{{ route('cms.account.profile.update') }}" class="flex flex-col gap-5">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label for="first_name" class="cms-muted block text-[0.78rem] tracking-[0.08em] uppercase mb-2">First name *</label>
                        <input type="text" id="first_name" name="first_name" value="{{ old('first_name', $person?->first_name) }}" required class="cms-input">
                        @error('first_name')<p class="cms-alert--error mt-2 px-3 py-2 text-[0.78rem]">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="last_name" class="cms-muted block text-[0.78rem] tracking-[0.08em] uppercase mb-2">Last name</label>
                        <input type="text" id="last_name" name="last_name" value="{{ old('last_name', $person?->last_name) }}" class="cms-input">
                        @error('last_name')<p class="cms-alert--error mt-2 px-3 py-2 text-[0.78rem]">{{ $message }}</p>@enderror
                    </div>
                </div>
                <div>
                    <label for="phone" class="cms-muted block text-[0.78rem] tracking-[0.08em] uppercase mb-2">Phone</label>
                    <input type="text" id="phone" name="phone" value="{{ old('phone', $person?->phone) }}" class="cms-input">
                    @error('phone')<p class="cms-alert--error mt-2 px-3 py-2 text-[0.78rem]">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="email" class="cms-muted block text-[0.78rem] tracking-[0.08em] uppercase mb-2">Contact email</label>
                    <input type="email" id="email" name="email" value="{{ old('email', $person?->email) }}" class="cms-input">
                    @error('email')<p class="cms-alert--error mt-2 px-3 py-2 text-[0.78rem]">{{ $message }}</p>@enderror
                    <p class="cms-muted text-[0.72rem] mt-2">This is separate from your login email above.</p>
                </div>
                <button type="submit" class="cms-btn cms-btn--primary self-start mt-2">Save Changes</button>
            </form>
        </div>
    </div>
</section>
@endsection
