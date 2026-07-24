{{-- Sticky site header: nav, auth-aware user menu. Catalog item appears for users with the catalog.add.view permission. --}}
@php
    $navLinks = [
        ['label' => 'Home', 'href' => '/'],
        ['label' => 'Services', 'href' => '/#services'],
        ['label' => 'How It Works', 'href' => '/partner-with-us'],
        ['label' => 'About', 'href' => '/about'],
    ];

    $companyDetails = \Illuminate\Support\Facades\Cache::remember(
        'cms_company_details',
        3600,
        fn () => \App\Models\Setting::where('group', 'company')->where('name', 'details')->first()?->payload ?? []
    );

    $companyName = (string) ($companyDetails['name'] ?? config('app.name', 'Company'));
    $companySubtitle = (string) ($companyDetails['tagline'] ?? 'Business Solutions');

    $user = auth()->user();
    $profileAvatarUrl = null;

    if ($user?->person?->hasMedia('avatars')) {
        $profileAvatarUrl = $user->person->getFirstMediaUrl('avatars', 'thumb') ?: $user->person->getFirstMediaUrl('avatars');
    } elseif ($user?->hasMedia('avatars')) {
        $profileAvatarUrl = $user->getFirstMediaUrl('avatars', 'thumb') ?: $user->getFirstMediaUrl('avatars');
    }


    $canAccessAdmin = (bool) $user?->canAccessPanel(\Filament\Facades\Filament::getPanel('admin'));
@endphp
<header class="cms-header sticky top-0 z-50 flex items-center justify-between px-8 py-5 backdrop-blur-md">
    <div class="flex items-center gap-3">
        {{-- White badge behind the emblem so the dark line-art + gold wreath read clearly on the dark header --}}
        <div class="w-10 h-10 rounded-full bg-white flex items-center justify-center shrink-0 cms-logo-badge">
            <img src="/images/gmj-llc-final-logo.jpg"
                 alt="Logo"
                 class="w-full h-full rounded-full"
                 style="object-fit: contain;">
        </div>
        <span class="cms-display text-[1.2rem] tracking-[0.06em]">{{ $companyName }}</span>
        <span class="cms-muted text-[0.6rem] tracking-[0.2em] mt-[2px]">{{ $companySubtitle }}</span>
    </div>

    <!-- Desktop nav -->
    <nav class="hidden md:flex items-center gap-8">
        @foreach($navLinks as $link)
            <a href="{{ $link['href'] }}" class="cms-nav-link">{{ $link['label'] }}</a>
        @endforeach
    </nav>

    <div class="hidden md:flex items-center gap-4">
        @auth
            <div class="relative" x-data="{ userMenuOpen: false }">
                <button type="button" @click="userMenuOpen = !userMenuOpen" @click.outside="userMenuOpen = false" class="cms-nav-link flex items-center gap-2">
                    @if($profileAvatarUrl)
                        <span class="relative inline-flex items-center justify-center h-[18px] w-[18px] shrink-0">
                            <img src="{{ $profileAvatarUrl }}"
                                 alt="{{ $user->username }} profile picture"
                                 class="cms-avatar cms-avatar--nav"
                                 onerror="this.style.display='none'; this.nextElementSibling.style.display='inline-flex';">
                            <svg viewBox="0 0 24 24" aria-hidden="true" class="h-[18px] w-[18px] cms-icon-gold" style="display: none;" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="8" r="4"></circle>
                                <path d="M4 20c1.8-3.8 5-6 8-6s6.2 2.2 8 6"></path>
                            </svg>
                        </span>
                    @else
                        <svg viewBox="0 0 24 24" aria-hidden="true" class="h-[18px] w-[18px] cms-icon-gold" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="8" r="4"></circle>
                            <path d="M4 20c1.8-3.8 5-6 8-6s6.2 2.2 8 6"></path>
                        </svg>
                    @endif
                    <span>{{ $user->username }}</span>
                    <i data-lucide="chevron-down" size="14" class="cms-accordion-icon" :class="userMenuOpen && 'cms-accordion-icon--open'"></i>
                </button>
                <div x-show="userMenuOpen" x-transition.opacity.duration.150ms class="cms-panel absolute right-0 top-full mt-3 w-56 py-2 z-50" x-cloak>
                    <a href="{{ route('cms.account.profile') }}" class="cms-dropdown-item"><i data-lucide="user" size="14" class="cms-icon-gold"></i> Profile</a>
                    <a href="{{ route('cms.account.password') }}" class="cms-dropdown-item"><i data-lucide="key-round" size="14" class="cms-icon-gold"></i> Change Password</a>
                    @if($canAccessAdmin)
                        <a href="/admin/control-panel" class="cms-dropdown-item"><i data-lucide="settings" size="14" class="cms-icon-gold"></i> Control Panel</a>
                    @endif
                    <hr class="cms-divider-soft my-2">
                    <form method="POST" action="{{ route('cms.logout') }}">
                        @csrf
                        <button type="submit" class="cms-dropdown-item w-full text-left"><i data-lucide="log-out" size="14" class="cms-icon-gold"></i> Logout</button>
                    </form>
                </div>
            </div>
        @else
            <a href="/admin/login" class="cms-btn cms-btn--primary cms-btn--sm">Login</a>
        @endauth
    </div>

    <!-- Mobile menu button -->
    <button class="md:hidden cms-icon-gold" @click="mobileMenuOpen = !mobileMenuOpen">
        <i x-show="!mobileMenuOpen" data-lucide="menu" size="22"></i>
        <i x-show="mobileMenuOpen" data-lucide="x" size="22" style="display: none;"></i>
    </button>
</header>

<!-- Mobile menu drawer -->
<div x-show="mobileMenuOpen" x-transition.opacity.duration.300ms class="cms-drawer fixed inset-0 z-40 flex flex-col pt-20 px-8 gap-6 overflow-y-auto" x-cloak>
    @foreach($navLinks as $link)
        <a href="{{ $link['href'] }}" @click="mobileMenuOpen = false" class="cms-display text-[1.5rem] font-normal">{{ $link['label'] }}</a>
    @endforeach
    <hr class="cms-divider">
    @auth
        <div class="cms-muted flex items-center gap-2 text-[0.85rem]">
            @if($profileAvatarUrl)
                <span class="relative inline-flex items-center justify-center h-4 w-4 shrink-0">
                    <img src="{{ $profileAvatarUrl }}"
                         alt="{{ $user->username }} profile picture"
                         class="cms-avatar cms-avatar--mobile"
                         onerror="this.style.display='none'; this.nextElementSibling.style.display='inline-flex';">
                    <svg viewBox="0 0 24 24" aria-hidden="true" class="h-4 w-4 cms-icon-gold" style="display: none;" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="8" r="4"></circle>
                        <path d="M4 20c1.8-3.8 5-6 8-6s6.2 2.2 8 6"></path>
                    </svg>
                </span>
            @else
                <svg viewBox="0 0 24 24" aria-hidden="true" class="h-4 w-4 cms-icon-gold" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="8" r="4"></circle>
                    <path d="M4 20c1.8-3.8 5-6 8-6s6.2 2.2 8 6"></path>
                </svg>
            @endif
            {{ $user->username }}
        </div>
        <a href="{{ route('cms.account.profile') }}" class="cms-link-gold text-[0.85rem] tracking-[0.1em]">PROFILE</a>
        <a href="{{ route('cms.account.password') }}" class="cms-link-gold text-[0.85rem] tracking-[0.1em]">CHANGE PASSWORD</a>
        @if($canAccessAdmin)
            <a href="/admin/control-panel" class="cms-link-gold text-[0.85rem] tracking-[0.1em]">CONTROL PANEL</a>
        @endif
        <form method="POST" action="{{ route('cms.logout') }}">
            @csrf
            <button type="submit" class="cms-link-gold text-[0.85rem] tracking-[0.1em]">LOGOUT →</button>
        </form>
    @else
        <a href="/admin/login" class="cms-link-gold text-[0.85rem] tracking-[0.1em]">LOGIN →</a>
    @endauth
</div>
