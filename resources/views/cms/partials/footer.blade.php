{{-- Site footer. Company identity comes from the Settings table (group "company", name "details"). --}}
@php
    $footerText = static function ($value, string $fallback = ''): string {
        if (is_string($value) || is_numeric($value)) {
            return (string) $value;
        }

        if (is_array($value)) {
            foreach ($value as $item) {
                if (is_string($item) || is_numeric($item)) {
                    return (string) $item;
                }
            }
        }

        return $fallback;
    };

    $companyDetails = \Illuminate\Support\Facades\Cache::remember(
        'cms_company_details',
        3600,
        fn () => \App\Models\Setting::where('group', 'company')->where('name', 'details')->first()?->payload ?? []
    );
    $companyName = $footerText($companyDetails['name'] ?? null, 'GMJ Group');
    $companyPhone = $footerText($companyDetails['phone'] ?? null, '');
    $companyEmail = $footerText($companyDetails['email'] ?? null, '');
    $companyWeb = $footerText($companyDetails['web'] ?? null, '');
    $companyAddress = implode(', ', array_filter([
        $footerText($companyDetails['address'] ?? null, ''),
        $footerText($companyDetails['city'] ?? null, ''),
        $footerText($companyDetails['Emirate'] ?? ($companyDetails['emirate'] ?? null), ''),
        $footerText($companyDetails['country'] ?? null, ''),
    ]));
@endphp
<footer class="cms-footer py-16">
    <div class="max-w-7xl mx-auto px-8 lg:px-16">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-12 mb-12">
            <div class="md:col-span-1">
                <div class="flex items-center gap-3 mb-5">
                    {{-- Same white-badge logo treatment as the header so the emblem reads clearly on the dark footer --}}
                    <div class="w-10 h-10 rounded-full bg-white flex items-center justify-center shrink-0 cms-logo-badge">
                        <img src="/images/gmj-llc-final-logo.jpg"
                             alt="{{ $companyName }} logo"
                             class="w-full h-full rounded-full"
                             style="object-fit: contain;">
                    </div>
                    <span class="cms-display text-base tracking-wide font-normal">{{ $companyName }}</span>
                </div>
                <p class="cms-muted text-[0.82rem] leading-relaxed mb-6">Business solutions for registered organizations.</p>
                <div class="cms-muted flex flex-col gap-2 text-[0.8rem]">
                    @if($companyAddress)<div class="flex gap-2"><i data-lucide="map-pin" size="12" class="cms-icon-gold"></i> {{ $companyAddress }}</div>@endif
                    @if($companyPhone !== '')<div class="flex gap-2"><i data-lucide="phone" size="12" class="cms-icon-gold"></i> <a href="tel:{{ $companyPhone }}" class="cms-link-muted cms-link-muted--gold-hover">{{ $companyPhone }}</a></div>@endif
                    @if($companyEmail !== '')<div class="flex gap-2"><i data-lucide="mail" size="12" class="cms-icon-gold"></i> <a href="mailto:{{ $companyEmail }}" class="cms-link-muted cms-link-muted--gold-hover">{{ $companyEmail }}</a></div>@endif
                    @if($companyWeb !== '')<div class="flex gap-2"><i data-lucide="globe" size="12" class="cms-icon-gold"></i> <a href="{{ $companyWeb }}" target="_blank" rel="noopener" class="cms-link-muted cms-link-muted--gold-hover">{{ preg_replace('#^https?://#', '', $companyWeb) }}</a></div>@endif
                </div>
            </div>
            @php $footerCols = [
                ['title'=>'Services','links'=>['Request Service Access'=>'/service-access']],
                ['title'=>'Company','links'=>['About Us'=>'/about','Contact Us'=>'/contact']],
            ]; @endphp
            @foreach($footerCols as $col)
                <div><div class="cms-footer-heading mb-4">{{ $col['title'] }}</div><ul class="flex flex-col gap-3">@foreach($col['links'] as $link => $linkUrl)<li><a href="{{ $linkUrl }}" class="cms-link-muted text-[0.82rem]">{{ $link }}</a></li>@endforeach@if($col['title'] === 'Company')<li><a href="https://www.instagram.com/gmjgoldfactory" target="_blank" rel="noopener" aria-label="Instagram" class="cms-link-muted text-[0.82rem] inline-flex items-center"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="32" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="cms-icon-gold"><rect x="2" y="2" width="20" height="20" rx="6" ry="6"></rect><circle cx="12" cy="12" r="4.5"></circle><circle cx="17.5" cy="6.5" r="1"></circle></svg></a></li>@endif</ul></div>
            @endforeach
        </div>
        <div class="flex flex-col md:flex-row items-center justify-between gap-4 pt-8 border-t cms-divider-soft"><div class="cms-muted text-[0.72rem] tracking-wide">© {{ date('Y') }} {{ $companyName }}. All rights reserved.</div><div class="flex gap-6">@foreach(['Privacy Policy' => '/privacy-policy', 'Cookie Policy' => '/cookie-policy'] as $policy => $policyUrl)<a href="{{ $policyUrl }}" class="cms-link-muted text-[0.72rem]">{{ $policy }}</a>@endforeach</div></div>
    </div>
</footer>
