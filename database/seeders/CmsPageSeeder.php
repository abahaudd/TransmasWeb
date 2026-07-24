<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Cms\Block;
use App\Models\Cms\Page;
use App\Models\Setting;
use Illuminate\Database\Seeder;

/**
 * Seeds the CMS pages/blocks tables with demo content for a small-business
 * advisory site, plus a sample About page.
 *
 * Run with: php artisan db:seed --class=CmsPageSeeder
 */
class CmsPageSeeder extends Seeder
{
    public function run(): void
    {
        $companyName = $this->companyName();

        $home = Page::updateOrCreate(
            ['slug' => 'home'],
            [
                'title' => 'Home',
                'template' => 'default',
                'meta_title' => $companyName.' | Practical Support for Small Business Owners.',
                'meta_description' => 'Straightforward, affordable business advice for small businesses - bookkeeping, marketing, planning, and day-to-day support from a team that answers the phone.',
                'is_published' => true,
            ]
        );

        $this->seedBlocks($home, [
            [
                'type' => 'hero',
                'name' => 'Hero — small business advisors',
                'data' => [
                    'kicker' => 'Local Business Advisors',
                    'heading' => 'Practical Help<br>for <em>Small Business</em><br>Owners.',
                    'body' => 'Straightforward, affordable support with the day-to-day of running your business - from getting the books in order to finding your next customer. No jargon, no long contracts, just people who pick up the phone.',
                    'buttons' => [
                        ['label' => 'Book a Free Consultation', 'url' => '/contact', 'style' => 'primary'],
                        ['label' => 'See Our Services', 'url' => '/#services', 'style' => 'outline'],
                    ],
                    'bg_image' => 'https://images.unsplash.com/photo-1521791136064-7986c2920216?w=1600&h=1000&fit=crop&auto=format',
                    'side_image' => 'https://images.unsplash.com/photo-1522071820081-009f0129c71c?w=900&h=1200&fit=crop&auto=format',
                    'side_kicker' => 'CLIENT STORY',
                    'side_title' => 'Helping Maple Street Bakery Grow',
                    'side_meta' => 'Local bakery · Online orders up 3x in 6 months',
                    'pills_label' => 'We Help With',
                    'pills' => [
                        ['label' => 'Bookkeeping'],
                        ['label' => 'Marketing'],
                        ['label' => 'Operations'],
                        ['label' => 'Growth Planning'],
                    ],
                ],
            ],
            [
                'type' => 'stats',
                'name' => 'Stats bar',
                'data' => [
                    'items' => [
                        ['value' => '250+', 'label' => 'Small businesses served'],
                        ['value' => '4.9/5', 'label' => 'Average client rating'],
                        ['value' => '48 hrs', 'label' => 'Average response time'],
                        ['value' => '12 yrs', 'label' => 'Serving local businesses'],
                    ],
                ],
            ],
            [
                'type' => 'featured-products',
                'name' => 'Our Services',
                'data' => [
                    'anchor' => 'services',
                    'kicker' => 'What We Offer',
                    'heading' => 'Simple, <em>Transparent</em> Services',
                    'description' => 'Straightforward packages built for small budgets. Prices below, no hidden fees, no obligation to keep going after the first project.',
                    'link_label' => 'Ask a Question',
                    'link_url' => '/contact',
                    'items' => [
                        [
                            'title' => 'Bookkeeping & Financial Basics',
                            'sku' => 'Monthly support',
                            'price' => 'From $150/mo',
                            'description' => 'Keep your books tidy and know where your money is going, every month.',
                            'image' => 'https://images.unsplash.com/photo-1554224155-6726b3ff858f?w=600&h=600&fit=crop&auto=format',
                            'tag' => 'Most Popular',
                            'button_label' => 'Learn More',
                            'button_url' => '/contact',
                        ],
                        [
                            'title' => 'Marketing & Social Media Setup',
                            'sku' => 'One-time setup',
                            'price' => 'From $400',
                            'description' => 'Get your business found online with a simple, consistent marketing plan.',
                            'image' => 'https://images.unsplash.com/photo-1556740758-90de374c12ad?w=600&h=600&fit=crop&auto=format',
                            'tag' => 'Popular',
                            'button_label' => 'Learn More',
                            'button_url' => '/contact',
                        ],
                        [
                            'title' => 'Business Plan & Growth Roadmap',
                            'sku' => 'One-time project',
                            'price' => 'From $600',
                            'description' => 'A clear, realistic plan for your next 12 months - written in plain English.',
                            'image' => 'https://images.unsplash.com/photo-1552664730-d307ca884978?w=600&h=600&fit=crop&auto=format',
                            'tag' => '',
                            'button_label' => 'Learn More',
                            'button_url' => '/contact',
                        ],
                        [
                            'title' => 'Website & Online Store Setup',
                            'sku' => 'One-time project',
                            'price' => 'From $900',
                            'description' => 'A simple website or online store you can update yourself, no developer needed.',
                            'image' => 'https://images.unsplash.com/photo-1517245386807-bb43f82c33c4?w=600&h=600&fit=crop&auto=format',
                            'tag' => 'New',
                            'button_label' => 'Learn More',
                            'button_url' => '/contact',
                        ],
                    ],
                ],
            ],
            [
                'type' => 'feature',
                'name' => 'Why Choose Us',
                'data' => [
                    'kicker' => 'Why Small Businesses Choose Us',
                    'heading' => 'We Only Work<br>With <em>Small Businesses.</em>',
                    'body' => 'Big consulting firms are built for big budgets. We are built for yours - flexible engagements, plain-English advice, and a team that actually answers the phone.',
                    'image' => 'https://images.unsplash.com/photo-1600880292203-757bb62b4baf?w=1000&h=1200&fit=crop&auto=format',
                    'bullets' => [
                        ['title' => 'Flat, Transparent Pricing', 'subtitle' => 'No hidden fees'],
                        ['title' => 'Month-to-Month', 'subtitle' => 'Cancel anytime'],
                        ['title' => 'Real Humans', 'subtitle' => 'Direct access to your advisor'],
                        ['title' => 'Fast Turnaround', 'subtitle' => 'Most projects start within a week'],
                    ],
                    'button_label' => 'See How It Works',
                    'button_url' => '#how-it-works',
                ],
            ],
            [
                'type' => 'steps',
                'name' => 'How It Works',
                'data' => [
                    'anchor' => 'how-it-works',
                    'kicker' => 'The Process',
                    'heading' => 'How We Work With You',
                    'items' => [
                        ['icon' => 'phone-call', 'title' => 'Free Consultation', 'body' => 'A 30-minute call to understand your business and what is slowing you down. No pressure, no sales pitch.'],
                        ['icon' => 'clipboard-list', 'title' => 'A Plan You Can Follow', 'body' => 'We put together a simple, upfront proposal with clear pricing - so you know exactly what you are getting.'],
                        ['icon' => 'wrench', 'title' => 'We Get to Work', 'body' => 'Your advisor handles the details while keeping you in the loop, in plain language, every step of the way.'],
                        ['icon' => 'trending-up', 'title' => 'Ongoing Support', 'body' => 'Keep working with us month-to-month as your business grows, or wrap up after the project - your call.'],
                    ],
                ],
            ],
            [
                'type' => 'testimonials',
                'name' => 'Client testimonials',
                'data' => [
                    'anchor' => 'testimonials',
                    'kicker' => 'What Our Clients Say',
                    'heading' => 'Small Businesses, Real Results',
                    'items' => [
                        [
                            'quote' => 'They explained everything in plain English and never made me feel silly for asking basic questions. My books have never been this organized.',
                            'name' => 'Priya Nair',
                            'title' => 'Owner, Maple Street Bakery',
                            'city' => 'Austin, TX',
                            'rating' => 5,
                        ],
                        [
                            'quote' => 'We are a two-person landscaping crew and I never thought we could afford real marketing help. The pricing was clear from day one and it paid for itself in a month.',
                            'name' => 'Marcus Webb',
                            'title' => 'Owner, Webb & Son Landscaping',
                            'city' => 'Columbus, OH',
                            'rating' => 5,
                        ],
                        [
                            'quote' => 'No long contract, no upsell - just a solid growth plan and someone who actually answers when I call. Exactly what a small shop like mine needed.',
                            'name' => 'Elena Torres',
                            'title' => 'Owner, Torres Boutique',
                            'city' => 'Tampa, FL',
                            'rating' => 5,
                        ],
                    ],
                ],
            ],
            [
                'type' => 'cta',
                'name' => 'Get started',
                'data' => [
                    'anchor' => 'apply',
                    'kicker' => 'Get Started',
                    'heading' => 'Ready to Grow<br><em>Your Small Business?</em>',
                    'body' => 'Book a free 30-minute consultation. No pressure, no sales pitch - just honest advice about what would actually help.',
                    'button_label' => 'Book Free Consultation',
                    'button_url' => '/contact',
                    'footnote' => 'Free for businesses with under 50 employees.',
                ],
            ],
        ]);

        $about = Page::updateOrCreate(
            ['slug' => 'about'],
            [
                'title' => 'About Us',
                'template' => 'default',
                'meta_title' => 'About Us | '.$companyName,
                'meta_description' => 'A small team helping small businesses with practical, affordable advice.',
                'is_published' => true,
            ]
        );

        $this->seedBlocks($about, [
            [
                'type' => 'rich_text',
                'name' => 'About intro',
                'data' => [
                    'kicker' => 'Our Story',
                    'heading' => 'About '.$companyName,
                    'body' => '<p>'.$companyName.' started with a simple idea: small businesses deserve good advice without the big-firm price tag. We work with independent shops, local service businesses, and small teams who need practical help - not a 40-page strategy deck.</p><h2>Small by Design</h2><p>We deliberately keep our client list small so every business gets real attention. You will talk to the same advisor every time, and we will always tell you plainly if something is not worth your money.</p>',
                ],
            ],
        ]);

        $this->seedPolicyPages();
        $this->seedServicePages();
        $this->seedCompanyPages();
        $this->seedNavPages();
    }

    /**
     * Seeds the main-menu pages aimed at small business owners:
     * How It Works and the public FAQ.
     */
    private function seedNavPages(): void
    {
        $howItWorks = Page::updateOrCreate(
            ['slug' => 'partner-with-us'],
            [
                'title' => 'How It Works',
                'template' => 'default',
                'meta_title' => 'How It Works | '.$this->companyName(),
                'meta_description' => 'How small business owners work with us - what to expect, our process, and how to get started.',
                'is_published' => true,
            ]
        );

        $this->seedBlocks($howItWorks, [
            [
                'type' => 'rich_text',
                'name' => 'How it works intro',
                'data' => [
                    'kicker' => 'How It Works',
                    'heading' => 'Built for Small Business Owners',
                    'body' => '<p>We work with independent shops, home-service businesses, and small teams who need practical support without a long commitment. Most clients fall into one of two groups:</p>'
                        . '<h2>Just Starting Out</h2>'
                        . '<p>New businesses that need help getting the basics right - bookkeeping, a simple website, a plan for finding customers - without hiring a full-time team.</p>'
                        . '<h2>Ready to Grow</h2>'
                        . '<p>Established small businesses that have outgrown "figuring it out as we go" and want a clear plan, better systems, and someone to help carry the load.</p>'
                        . '<h2>Pricing, Upfront</h2>'
                        . '<p>Every project starts with a written proposal and a flat price before any work begins - no surprise invoices, no hourly guessing games.</p>',
                ],
            ],
            [
                'type' => 'steps',
                'name' => 'Onboarding steps',
                'data' => [
                    'anchor' => 'how-it-works',
                    'kicker' => 'The Process',
                    'heading' => 'From First Call to Finished Project',
                    'items' => [
                        ['icon' => 'phone-call', 'title' => 'Book a Free Call', 'body' => 'Tell us about your business and what is on your plate. Most calls are scheduled within 1-2 business days.'],
                        ['icon' => 'clipboard-list', 'title' => 'Get a Simple Proposal', 'body' => 'A one-page plan with clear scope and a flat price - no jargon, nothing hidden.'],
                        ['icon' => 'wrench', 'title' => 'We Get Started', 'body' => 'Work begins with a kickoff call and a straightforward timeline you can actually follow.'],
                        ['icon' => 'trending-up', 'title' => 'Stay in Touch', 'body' => 'Keep us on retainer month-to-month, or come back whenever you need help next - no pressure either way.'],
                    ],
                ],
            ],
            [
                'type' => 'cta',
                'name' => 'Get started CTA',
                'data' => [
                    'anchor' => 'apply',
                    'kicker' => 'Get Started',
                    'heading' => 'Have a Few<br><em>Questions First?</em>',
                    'body' => 'Send us a message and we will get back to you within a business day - no forms, no gatekeeping.',
                    'button_label' => 'Contact Us',
                    'button_url' => '/contact',
                    'footnote' => 'For small business owners and independent shops.',
                ],
            ],
        ]);

        $faq = Page::updateOrCreate(
            ['slug' => 'faq'],
            [
                'title' => 'FAQ',
                'template' => 'default',
                'meta_title' => 'FAQ | '.$this->companyName(),
                'meta_description' => 'Answers to common questions small business owners ask before getting started - pricing, contracts, timelines, and support.',
                'is_published' => true,
            ]
        );

        $this->seedBlocks($faq, [
            [
                'type' => 'faq',
                'name' => 'Small business FAQ',
                'data' => [
                    'kicker' => 'FAQ',
                    'heading' => 'Frequently Asked Questions',
                    'intro' => 'The answers below cover what most small business owners ask before getting started. Do not see your question? Send us a message.',
                    'items' => [
                        [
                            'question' => 'Do I need to be a big company to work with you?',
                            'answer' => 'Not at all - we specialize in small businesses. Most of our clients are solo owners, family businesses, or teams of under 10 people.',
                        ],
                        [
                            'question' => 'How much does it cost?',
                            'answer' => 'It depends on the project, but pricing is always flat and agreed upfront in writing before we start. You will never get a surprise invoice.',
                        ],
                        [
                            'question' => 'Do I have to sign a long-term contract?',
                            'answer' => 'No. Projects are billed per engagement, and ongoing support is month-to-month. Cancel anytime with no penalty.',
                        ],
                        [
                            'question' => 'How fast can we get started?',
                            'answer' => 'Most consultation calls happen within 1-2 business days, and projects typically kick off within a week of agreeing on scope.',
                        ],
                        [
                            'question' => 'I am brand new to running a business - can you still help?',
                            'answer' => 'Yes, many of our clients are first-time business owners. We explain things in plain language and will never assume you already know the jargon.',
                        ],
                        [
                            'question' => 'Do you work with businesses outside my city?',
                            'answer' => 'Yes. Most of our work happens by phone, video call, and email, so we can support businesses anywhere.',
                        ],
                        [
                            'question' => 'What if I am not happy with the work?',
                            'answer' => 'Tell us. We will make it right with revisions or corrections at no extra cost, as outlined in our service policy.',
                        ],
                        [
                            'question' => 'Can you help with just one specific thing, like my books or my website?',
                            'answer' => 'Absolutely. You can hire us for a single project or ongoing support - whatever fits your business right now.',
                        ],
                        [
                            'question' => 'How do I pay?',
                            'answer' => 'We accept card and bank transfer. One-time projects are typically split into two payments; monthly support is billed monthly.',
                        ],
                        [
                            'question' => 'What is your refund policy?',
                            'answer' => 'If we have not started work yet, we offer a full refund. Once work is underway, see our service policy for how corrections and credits are handled.',
                        ],
                    ],
                ],
            ],
        ]);
    }

    /**
     * Seeds the pages linked from the footer's Company column
     * (About Us is seeded separately above).
     */
    private function seedCompanyPages(): void
    {
        $companyPages = [
            [
                'slug' => 'our-factory',
                'title' => 'Our Approach',
                'meta_description' => 'How we work with small businesses - our values and working style.',
                'kicker' => 'Company',
                'body' => '<p>We keep things simple on purpose. Small businesses do not need enterprise process - they need clear advice, fair pricing, and someone who follows through.</p>'
                    . '<h2>Plain Language, Always</h2>'
                    . '<p>If we cannot explain a recommendation in a sentence or two, we have not done our job. No jargon, no unnecessary complexity.</p>'
                    . '<h2>Small Client List, On Purpose</h2>'
                    . '<p>We take on a limited number of clients at a time so every business gets real attention from a real person, not a rotating cast of account managers.</p>',
            ],
            [
                'slug' => 'quality-standards',
                'title' => 'Quality Standards',
                'meta_description' => 'How we make sure the work we deliver is worth your money.',
                'kicker' => 'Company',
                'body' => '<p>Every project is reviewed before it lands in your inbox, so you can trust what you are getting.</p>'
                    . '<h2>What We Promise</h2>'
                    . '<p>Clear scope agreed in writing, work reviewed by a second team member before delivery, and a named advisor you can reach directly with questions.</p>'
                    . '<h2>If Something Is Not Right</h2>'
                    . '<p>Tell us and we will fix it. See our service policy for details on corrections and credits.</p>',
            ],
        ];

        foreach ($companyPages as $companyPage) {
            $page = Page::updateOrCreate(
                ['slug' => $companyPage['slug']],
                [
                    'title' => $companyPage['title'],
                    'template' => 'default',
                    'meta_title' => $companyPage['title'].' | '.$this->companyName(),
                    'meta_description' => $companyPage['meta_description'],
                    'is_published' => true,
                ]
            );

            $this->seedBlocks($page, [
                [
                    'type' => 'rich_text',
                    'name' => $companyPage['title'],
                    'data' => [
                        'kicker' => $companyPage['kicker'],
                        'heading' => $companyPage['title'],
                        'body' => $companyPage['body'],
                    ],
                ],
            ]);
        }

        $contact = Page::updateOrCreate(
            ['slug' => 'contact'],
            [
                'title' => 'Contact Us',
                'template' => 'default',
                'meta_title' => 'Contact Us | '.$this->companyName(),
                'meta_description' => 'Get in touch - free consultations for small business owners.',
                'is_published' => true,
            ]
        );

        $this->seedBlocks($contact, [
            [
                'type' => 'contact_form',
                'name' => 'Contact form',
                'data' => [
                    'kicker' => 'Contact',
                    'heading' => 'Let\'s <em>Talk.</em>',
                    'body' => 'Whether you have a quick question or want to book a free consultation, send us a message and we will get back to you within one business day.',
                ],
            ],
        ]);
    }

    /**
     * Seeds the pages linked from the footer's Services column.
     */
    private function seedServicePages(): void
    {
        $getQuote = Page::updateOrCreate(
            ['slug' => 'service-access'],
            [
                'title' => 'Get a Free Quote',
                'template' => 'default',
                'meta_title' => 'Get a Free Quote | '.$this->companyName(),
                'meta_description' => 'Tell us about your business and get a free, no-obligation quote within two business days.',
                'is_published' => true,
            ]
        );

        $this->seedBlocks($getQuote, [
            [
                'type' => 'rich_text',
                'name' => 'Get a quote intro',
                'data' => [
                    'kicker' => 'Get a Free Quote',
                    'heading' => 'Tell Us About Your Business',
                    'body' => '<p>Every service on this site is open to any small business - no approval process, no hidden pricing tiers.</p>'
                        . '<h2>What We Will Ask</h2>'
                        . '<p>A short description of your business, what you need help with, and the best way to reach you. That is it.</p>'
                        . '<h2>What You Get Back</h2>'
                        . '<p>A free, written quote within two business days - flat pricing, no obligation to move forward.</p>',
                ],
            ],
            [
                'type' => 'cta',
                'name' => 'Get quote CTA',
                'data' => [
                    'anchor' => 'apply',
                    'kicker' => 'Get Started',
                    'heading' => 'Get Your Free<br><em>Quote Today.</em>',
                    'body' => 'Send us a message and we will follow up within two business days.',
                    'button_label' => 'Contact Us',
                    'button_url' => '/contact',
                    'footnote' => 'Free for all small businesses - no obligation.',
                ],
            ],
        ]);

        $servicePolicies = [
            [
                'slug' => 'delivery-policy',
                'title' => 'Delivery Policy',
                'meta_description' => 'How we handle project timelines and communication.',
                'kicker' => 'Services',
                'body' => '<p>This policy explains how we deliver projects and stay in touch along the way.</p>'
                    . '<h2>How Projects Are Delivered</h2>'
                    . '<p>Every project starts with a short written plan covering what we are doing and roughly when. We check in at each milestone so there are no surprises.</p>'
                    . '<h2>Timelines</h2>'
                    . '<p>Most one-time projects wrap up within 2-4 weeks; ongoing support runs monthly. Exact timing is confirmed in your proposal.</p>'
                    . '<h2>What We Need From You</h2>'
                    . '<p>Timely replies and access to whatever we need (logins, files, a quick call) help us hit the agreed timeline.</p>'
                    . '<h2>Questions?</h2>'
                    . '<p>Reach out to your advisor directly, or use our contact page any time.</p>',
            ],
            [
                'slug' => 'service-policy',
                'title' => 'Service Policy',
                'meta_description' => 'Our corrections and refund policy for small business clients.',
                'kicker' => 'Services',
                'body' => '<p>We stand behind our work. This policy explains what happens if something is not right.</p>'
                    . '<h2>Corrections</h2>'
                    . '<p>If a deliverable does not match what we agreed on in writing, we will fix it at no extra cost.</p>'
                    . '<h2>What Is Not Covered</h2>'
                    . '<p>Changes outside the original agreed scope, or delays caused by missing information on your end, may need a small additional quote - we will always tell you before doing any extra work.</p>'
                    . '<h2>How to Report an Issue</h2>'
                    . '<p>Contact your advisor directly with details, and we will respond within one business day with a plan to fix it.</p>'
                    . '<h2>Refunds</h2>'
                    . '<p>Work not yet started is fully refundable. Once work has begun, any agreed credit is applied within 5 business days.</p>',
            ],
        ];

        foreach ($servicePolicies as $policy) {
            $page = Page::updateOrCreate(
                ['slug' => $policy['slug']],
                [
                    'title' => $policy['title'],
                    'template' => 'default',
                    'meta_title' => $policy['title'].' | '.$this->companyName(),
                    'meta_description' => $policy['meta_description'],
                    'is_published' => true,
                ]
            );

            $this->seedBlocks($page, [
                [
                    'type' => 'rich_text',
                    'name' => $policy['title'],
                    'data' => [
                        'kicker' => $policy['kicker'],
                        'heading' => $policy['title'],
                        'body' => $policy['body'],
                    ],
                ],
            ]);
        }
    }

    /**
     * Seeds the footer policy pages as simple rich-text pages.
     */
    private function seedPolicyPages(): void
    {
        $companyName = $this->companyName();

        $policies = [
            [
                'slug' => 'privacy-policy',
                'title' => 'Privacy Policy',
                'meta_description' => 'How '.$companyName.' collects, uses, and protects your personal information.',
                'body' => '<p>'.$companyName.' ("we", "us") is committed to protecting the privacy of website visitors and clients. This policy explains what information we collect, how we use it, and the choices you have.</p>'
                    . '<h2>Information We Collect</h2>'
                    . '<p>When you submit an enquiry or become a client, we collect basic contact details such as your name, business name, email address, and phone number. We also collect basic website usage data to improve the site.</p>'
                    . '<h2>How We Use Your Information</h2>'
                    . '<p>We use the information we collect to respond to enquiries, deliver services, and communicate about your project. We do not sell your personal information to anyone.</p>'
                    . '<h2>Sharing of Information</h2>'
                    . '<p>We only share information with trusted service providers who help us operate - such as hosting, email, and payment providers - and only where required to provide our services or by law.</p>'
                    . '<h2>Data Retention &amp; Security</h2>'
                    . '<p>We keep client records for as long as needed for service delivery and accounting requirements, and apply reasonable measures to protect your data.</p>'
                    . '<h2>Your Rights</h2>'
                    . '<p>You may request access to, correction of, or deletion of your personal information at any time through our Contact page.</p>'
                    . '<h2>Contact</h2>'
                    . '<p>For any privacy-related questions, please contact us through our Contact page.</p>',
            ],
            [
                'slug' => 'cookie-policy',
                'title' => 'Cookie Policy',
                'meta_description' => 'How this website uses cookies and similar technologies.',
                'body' => '<p>This Cookie Policy explains how the '.$companyName.' website uses cookies and similar technologies.</p>'
                    . '<h2>What Are Cookies</h2>'
                    . '<p>Cookies are small text files stored on your device when you visit a website. They help the site remember your session and preferences.</p>'
                    . '<h2>Cookies We Use</h2>'
                    . '<p><strong>Essential cookies</strong> - required for the site to function, including session cookies that help keep your access secure and forms working. These cannot be switched off.</p>'
                    . '<p><strong>Preference cookies</strong> - remember choices you make, such as interface preferences, to improve your browsing experience.</p>'
                    . '<h2>Third-Party Cookies</h2>'
                    . '<p>We do not use third-party advertising cookies. Embedded content (such as externally hosted images) may set its own cookies governed by the relevant provider\'s policy.</p>'
                    . '<h2>Managing Cookies</h2>'
                    . '<p>Most browsers let you block or delete cookies via their settings. Blocking essential cookies may prevent parts of the site from working correctly.</p>'
                    . '<h2>Contact</h2>'
                    . '<p>Questions about this policy can be submitted through our Contact page.</p>',
            ],
        ];

        foreach ($policies as $policy) {
            $page = Page::updateOrCreate(
                ['slug' => $policy['slug']],
                [
                    'title' => $policy['title'],
                    'template' => 'default',
                    'meta_title' => $policy['title'].' | '.$companyName,
                    'meta_description' => $policy['meta_description'],
                    'is_published' => true,
                ]
            );

            $this->seedBlocks($page, [
                [
                    'type' => 'rich_text',
                    'name' => $policy['title'],
                    'data' => [
                        'kicker' => 'Legal',
                        'heading' => $policy['title'],
                        'body' => $policy['body'],
                    ],
                ],
            ]);
        }
    }

    /**
     * Replaces a page's blocks with the given definitions, positioned in order.
     */
    private function seedBlocks(Page $page, array $blocks): void
    {
        $page->blocks()->delete();

        foreach ($blocks as $position => $block) {
            Block::create([
                'page_id' => $page->id,
                'type' => $block['type'],
                'name' => $block['name'],
                'data' => $block['data'],
                'position' => $position,
                'is_active' => true,
            ]);
        }
    }

    private function companyName(): string
    {
        return (string) Setting::getPayloadValue('company', 'details', 'name', 'Harbor Lane Business Advisors');
    }
}
