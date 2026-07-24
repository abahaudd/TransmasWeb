{{-- Contact form block. Expects $data: kicker, heading (HTML), body. Submissions are stored in the enquiries table. --}}
@php
    $companyDetails = \Illuminate\Support\Facades\Cache::remember(
        'cms_company_details',
        3600,
        fn () => \App\Models\Setting::where('group', 'company')->where('name', 'details')->first()?->payload ?? []
    );
    $companyAddress = implode(', ', array_filter([
        $companyDetails['address'] ?? null,
        $companyDetails['city'] ?? null,
        $companyDetails['Emirate'] ?? ($companyDetails['emirate'] ?? null),
        $companyDetails['country'] ?? null,
    ]));
@endphp
<section class="max-w-7xl mx-auto px-8 lg:px-16 pt-16 pb-24">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-16">
        <div>
            @if(!empty($data['kicker']))
                <div class="flex items-center gap-3 mb-6">
                    <div class="cms-kicker-line"></div>
                    <span class="cms-kicker">{{ $data['kicker'] }}</span>
                </div>
            @endif
            <h1 class="cms-display text-[clamp(2rem,4vw,3rem)] leading-tight mb-6">{!! $data['heading'] ?? 'Contact Us' !!}</h1>
            @if(!empty($data['body']))
                <p class="cms-prose mb-10">{{ $data['body'] }}</p>
            @endif

            <div class="cms-muted flex flex-col gap-4 text-[0.9rem]">
                @if($companyAddress)
                    <div class="flex gap-3 items-start"><i data-lucide="map-pin" size="16" class="cms-icon-gold" style="margin-top:3px;"></i> {{ $companyAddress }}</div>
                @endif
                @if(!empty($companyDetails['phone']))
                    <div class="flex gap-3 items-center"><i data-lucide="phone" size="16" class="cms-icon-gold"></i> <a href="tel:{{ $companyDetails['phone'] }}" class="cms-link-muted cms-link-muted--gold-hover">{{ $companyDetails['phone'] }}</a></div>
                @endif
                @if(!empty($companyDetails['email']))
                    <div class="flex gap-3 items-center"><i data-lucide="mail" size="16" class="cms-icon-gold"></i> <a href="mailto:{{ $companyDetails['email'] }}" class="cms-link-muted cms-link-muted--gold-hover">{{ $companyDetails['email'] }}</a></div>
                @endif
            </div>
        </div>

        <div class="cms-panel p-8">
            @if(session('enquiry_success'))
                <div class="cms-alert--success mb-6 p-4 text-[0.85rem]">
                    Thank you for your enquiry. Our team will get back to you within 1–2 business days.
                </div>
            @endif
            @if($errors->any())
                <div class="cms-alert--error mb-6 p-4 text-[0.85rem]">
                    <ul class="list-disc pl-4">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('cms.enquiries.store') }}" class="flex flex-col gap-4">
                @csrf
                {{-- Honeypot field — hidden from humans, catches bots --}}
                <input type="text" name="website" value="" tabindex="-1" autocomplete="off" aria-hidden="true" style="position:absolute; left:-9999px;">

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <input type="text" name="name" value="{{ old('name') }}" placeholder="Your name *" required class="cms-input">
                    <input type="email" name="email" value="{{ old('email') }}" placeholder="Email address *" required class="cms-input">
                    <input type="text" name="phone" value="{{ old('phone') }}" placeholder="Phone" class="cms-input">
                    <input type="text" name="company" value="{{ old('company') }}" placeholder="Company" class="cms-input">
                </div>
                <input type="text" name="subject" value="{{ old('subject') }}" placeholder="Subject" class="cms-input">
                <textarea name="message" rows="5" placeholder="Your message *" required class="cms-input resize-y">{{ old('message') }}</textarea>
                <button type="submit" class="cms-btn cms-btn--primary self-start">
                    Send Enquiry <i data-lucide="arrow-right" size="14"></i>
                </button>
            </form>
        </div>
    </div>
</section>
