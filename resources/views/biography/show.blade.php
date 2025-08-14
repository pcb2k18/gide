<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    {{-- SEO & Meta Tags --}}
    <title>{{ $data['seo']['metaTitle'] ?? 'Biografie' }}</title>
    <meta name="description" content="{{ $data['seo']['metaDescription'] ?? 'Lesen Sie die vollständige Biografie.' }}">
    <meta name="robots" content="index, follow">
    
    <!-- Open Graph & Twitter Card Meta Tags -->
    <meta property="og:title" content="{{ $data['seo']['metaTitle'] ?? 'Biografie' }}">
    <meta property="og:description" content="{{ $data['seo']['metaDescription'] ?? 'Lesen Sie die vollständige Biografie.' }}">
    <meta property="og:image" content="{{ $data['hero']['mainImageUrl'] ?? '' }}">
    <meta property="og:type" content="article">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $data['seo']['metaTitle'] ?? 'Biografie' }}">
    <meta name="twitter:description" content="{{ $data['seo']['metaDescription'] ?? 'Lesen Sie die vollständige Biografie.' }}">
    <meta name="twitter:image" content="{{ $data['hero']['mainImageUrl'] ?? '' }}">
    
    <!-- CDNs and Fonts -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
    
    <style>
        html { scroll-behavior: smooth; }
        section[id] { scroll-margin-top: 100px; }
        .font-inter { font-family: 'Inter', system-ui, sans-serif; }
        .prose h2 { @apply text-3xl sm:text-4xl font-bold text-gray-900 mb-8 border-b border-gray-100 pb-4; }
        .prose h3 { @apply text-xl font-semibold text-gray-800 mt-8 mb-4; }
        .prose p { @apply mb-6 text-gray-700 leading-relaxed; }
        .prose ul { @apply list-disc pl-6 space-y-2 mb-6; }
        .prose li { @apply text-gray-700; }
    </style>
</head>

<body class="bg-gradient-to-br from-slate-50 to-blue-50 font-inter text-gray-900 leading-relaxed min-h-screen">
    <div x-data="biographyApp()" x-init="init()">
        <!-- Header -->
        <header class="sticky top-0 z-50 bg-white/80 backdrop-blur-sm border-b border-gray-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-16">
                    <div class="flex-shrink-0"><a href="/de" class="text-2xl font-bold text-gray-900 hover:text-blue-600">CelebBio</a></div>
                    <nav class="hidden md:flex space-x-8"><a href="/de" class="text-gray-600 hover:text-gray-900 text-sm font-medium">Home</a></nav>
                </div>
            </div>
        </header>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Breadcrumbs -->
            <nav class="mb-8">
                <ol class="flex items-center space-x-2 text-sm text-gray-600">
                    <li><a href="/de" class="hover:text-blue-600">Home</a></li>
                    <li><i data-lucide="chevron-right" class="w-4 h-4"></i></li>
                    <li class="text-gray-900 font-medium">{{ $data['fullName'] ?? 'Biografie' }}</li>
                </ol>
            </nav>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Main Content -->
                <main class="lg:col-span-2 order-2 lg:order-1">
                    <article class="bg-white rounded-2xl shadow-lg border border-gray-100 p-8 sm:p-10">
                       <!-- Author Byline & E-E-A-T -->
<div class="border-b border-gray-100 pb-6 mb-8">
    <div class="flex flex-wrap items-center text-sm text-slate-600 gap-x-6 gap-y-2 mb-3">
        <div class="flex items-center space-x-2">
            <span class="font-medium">Authored by:</span>
            <span class="font-semibold text-blue-700">{{ $data['authorship']['authorName'] ?? 'Research Team' }}</span>
        </div>
        
        {{-- ########## START: DYNAMIC FACT-CHECKER LOGIC ########## --}}
        @if($data['status'] === 'reviewed')
            <div class="flex items-center space-x-2">
                <span class="font-medium">Reviwed by:</span>
                <span class="font-semibold text-blue-700">{{ $data['authorship']['factCheckerName'] ?? 'Editorial Staff' }}</span>
            </div>
        @else
            <div class="flex items-center space-x-2">
                <span class="font-medium text-orange-700">To be Reviewed by:</span>
                <span class="font-semibold text-orange-700">Editorial Team</span>
            </div>
        @endif
        {{-- ########## END: DYNAMIC FACT-CHECKER LOGIC ########## --}}

    </div>
    <div class="flex items-center justify-between">
        <span class="text-sm text-slate-600 font-medium">Last Updated: <span class="text-gray-800">{{ \Carbon\Carbon::parse($data['updated_at'] ?? now())->format('F j, Y') }}</span></span>

        @if($data['status'] === 'reviewed')
            <div class="flex items-center space-x-2 bg-green-50 text-green-700 px-4 py-2 rounded-full border border-green-200 shadow-sm">
                <i data-lucide="check-circle" class="w-4 h-4"></i><span class="text-sm font-semibold">Reviewed</span>
            </div>
        @else
            <div class="flex items-center space-x-2 bg-yellow-50 text-orange-700 px-4 py-2 rounded-full border border-orange-200 shadow-sm">
                <i data-lucide="alert-circle" class="w-4 h-4"></i><span class="text-sm font-semibold">Under Review</span>
            </div>
        @endif
    </div>
</div>
                        <!-- Article Header -->
                        <header class="mb-10">
                            <h1 class="text-4xl sm:text-6xl font-black text-gray-900 mb-6 leading-tight">{!! $data['hero']['h1Title'] ?? ($data['fullName'] . ' Biografie') !!}</h1>
                            <div class="prose prose-lg max-w-none"><p class="text-xl text-slate-700 leading-relaxed font-medium">{!! $data['hero']['intro'] ?? '' !!}</p></div>
                        </header>

                        <!-- Table of Contents -->
                        @if(!empty($data['tableOfContents']))
                            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl border border-blue-200 p-6 mb-12">
                                <button @click="tocCollapsed = !tocCollapsed" class="flex items-center justify-between w-full text-left">
                                    <div class="flex items-center space-x-3"><i data-lucide="list" class="w-6 h-6 text-blue-600"></i><h2 class="text-xl font-bold text-gray-900 m-0 p-0 border-none">In This Article</h2></div>
                                    <i data-lucide="chevron-down" :class="{ 'rotate-180': tocCollapsed }" class="w-5 h-5 transition-transform"></i>
                                </button>
                                <ul x-show="!tocCollapsed" x-transition class="mt-4 space-y-3">
                                    <li class="lg:hidden"><a href="#quick-facts-mobile" class="text-blue-600 hover:text-blue-800 hover:underline font-medium">Quick Facts</a></li>
                                    @foreach($data['tableOfContents'] as $item)
                                        <li><a href="#{{ $item['anchorId'] ?? '' }}" class="text-blue-600 hover:text-blue-800 hover:underline font-medium">{{ $item['title'] ?? '' }}</a></li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <!-- Hero Image -->
                        @if(!empty($data['hero']['mainImageUrl']) && $data['hero']['mainImageUrl'] !== 'Unbekannt')
                            <figure class="mb-10 group">
                                <div class="overflow-hidden rounded-2xl shadow-xl ring-1 ring-gray-200"><img src="{{ $data['hero']['mainImageUrl'] }}" alt="{{ $data['hero']['imageAltText'] ?? '' }}" class="w-full h-auto object-cover"></div>
                                @if(!empty($data['hero']['imageCaption']))
                                    <figcaption class="text-center text-slate-600 mt-4 text-sm italic">{!! $data['hero']['imageCaption'] !!}</figcaption>
                                @endif
                            </figure>
                        @endif
                        
                        {{-- ########## START: DYNAMIC CONTENT SECTIONS ########## --}}
                        <div class="space-y-12">


                           <!-- Quick Facts - Mobile version -->
@if(!empty($data['quickFacts']['facts']))
    <section id="quick-facts-mobile" class="lg:hidden mb-12 not-prose">
        <div class="bg-white rounded-2xl shadow-lg p-6">
            <h2 class="text-2xl font-bold text-gray-900 mb-4 border-b border-gray-100 pb-2">{{ $data['quickFacts']['title'] ?? 'Quick Facts' }}</h2>
            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                @foreach($data['quickFacts']['facts'] as $fact)
                    <div class="bg-gradient-to-r from-gray-50 to-blue-50 rounded-lg p-3">
                        <dt class="text-xs font-semibold text-blue-700 uppercase tracking-wide">{{ $fact['label'] ?? '' }}</dt>
                        <dd class="text-gray-900 font-medium mt-1 text-sm">{!! $fact['value'] ?? '' !!}</dd>
                    </div>
                @endforeach
            </dl>
        </div>
    </section>
@endif

<!-- Replace the dynamic content sections in your blade with this: -->

<div class="prose prose-lg max-w-none space-y-12">
    <!-- Trending Focus Section -->
    @if(!empty($data['trendingFocus']['content']))
        <section id="trending-focus">
            <div class="content-card bg-gradient-to-br from-blue-50 to-indigo-50 border border-blue-100">
                <h2>{{ $data['trendingFocus']['title'] ?? 'Aktuelle Entwicklungen' }}</h2>
                <div>{!! $data['trendingFocus']['content'] !!}</div>
            </div>
        </section>
    @endif

    <!-- Early Life Section -->
    @if(!empty($data['earlyLife']['content']))
        <section id="early-life">
            <div class="content-card">
                <h2>{{ $data['earlyLife']['title'] ?? 'Frühe Jahre' }}</h2>
                <div>{!! $data['earlyLife']['content'] !!}</div>
            </div>
        </section>
    @endif

    <!-- Career Section -->
    @if(!empty($data['career']['timeline']) || !empty($data['career']['sections']))
        <section id="career">
            <div class="content-card">
                <h2>{{ $data['career']['title'] ?? 'Karriere' }}</h2>
                
                @if(!empty($data['career']['timeline']))
                    <div class="my-8 rounded-xl not-prose">
                        <h3 class="text-xl font-semibold text-gray-800 mt-0 mb-4 flex items-center">
                            <i data-lucide="clock" class="w-5 h-5 mr-2 text-blue-600"></i>Career Timeline
                        </h3>
                        <div class="space-y-4">
                            @foreach($data['career']['timeline'] as $item)
                                <div class="flex items-start space-x-4 p-3 bg-gray-50 rounded-lg">
                                    <div class="flex-shrink-0 w-14 h-14 bg-blue-100 rounded-full flex items-center justify-center font-bold text-blue-600">{{ $item['year'] ?? '' }}</div>
                                    <div>
                                        <div class="font-semibold text-gray-900">{!! $item['event'] ?? '' !!}</div>
                                        <div class="text-gray-600 text-sm mt-1">{!! $item['description'] ?? '' !!}</div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
                
                @if(!empty($data['career']['sections']))
                    @foreach($data['career']['sections'] as $subSection)
                        @if(!empty($subSection['subtitle']) && !empty($subSection['content']))
                            <h3>{!! $subSection['subtitle'] !!}</h3>
                            <div>{!! $subSection['content'] !!}</div>
                        @endif
                    @endforeach
                @endif
            </div>
        </section>
    @endif

    <!-- Personal Life Section -->
    @if(!empty($data['personalLife']['content']))
        <section id="personal-life">
            <div class="content-card">
                <h2>{{ $data['personalLife']['title'] ?? 'Privatleben' }}</h2>
                <div>{!! $data['personalLife']['content'] !!}</div>
            </div>
        </section>
    @endif
    
    <!-- Death Section -->
    @if(!empty($data['death']['content']))
        <section id="death">
            <div class="content-card">
                <h2>{{ $data['death']['title'] ?? 'Tod' }}</h2>
                <div>{!! $data['death']['content'] !!}</div>
            </div>
        </section>
    @endif

    <!-- Net Worth Section -->
    @if(!empty($data['netWorth']['content']))
        <section id="net-worth">
            <div class="content-card">
                <h2>{{ $data['netWorth']['title'] ?? 'Vermögen' }}</h2>
                <div>{!! $data['netWorth']['content'] !!}</div>
            </div>
        </section>
    @endif

    <!-- FAQ Section -->
    @if(!empty($data['faqs']['questions']))
        <section id="faqs">
            <div class="content-card">
                <h2>{{ $data['faqs']['title'] ?? 'Häufig gestellte Fragen' }}</h2>
                <div class="space-y-4 not-prose" x-data="{ openFaq: null }">
                    @foreach($data['faqs']['questions'] as $index => $faq)
                        <div class="border border-gray-200 rounded-xl overflow-hidden shadow-sm hover:shadow-md transition-all">
                            <button @click="openFaq = openFaq === {{ $index }} ? null : {{ $index }}" class="w-full px-6 py-5 text-left flex justify-between items-center hover:bg-gray-50 transition-all">
                                <span class="font-semibold text-gray-900 text-lg">{{ $faq['question'] ?? '' }}</span>
                                <i data-lucide="chevron-down" :class="{ 'rotate-180': openFaq === {{ $index }} }" class="w-6 h-6 text-blue-600 transform transition-transform"></i>
                            </button>
                            <div x-show="openFaq === {{ $index }}" x-transition class="px-6 pb-5 text-gray-700 bg-blue-50/50">
                                <div class="text-base leading-relaxed">{!! $faq['answer'] ?? '' !!}</div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <!-- Sources Section -->
    @if(!empty($data['sources']['sourceList']))
        <section id="quellen">
            <div class="content-card">
                <h2>{{ $data['sources']['title'] ?? 'Quellen und Referenzen' }}</h2>
                <ol class="space-y-4 text-gray-700 not-prose">
                    @foreach($data['sources']['sourceList'] as $source)
                        @if(is_array($source) && !empty($source['url']))
                            <li class="flex bg-gray-50 rounded-lg p-4 hover:bg-gray-100 transition-colors">
                                <span class="font-bold text-blue-600 mr-4 text-lg">{{ $loop->iteration }}.</span>
                                <div class="flex-1">
                                    <a href="{{ $source['url'] }}" class="text-blue-600 hover:text-blue-800 hover:underline font-semibold break-all" target="_blank" rel="noopener nofollow noreferrer">{{ $source['title'] ?? $source['url'] }}</a>
                                    @if(!empty($source['publisher']))<p class="text-slate-600 mt-1 text-sm">Publisher: {{ $source['publisher'] }}</p>@endif
                                </div>
                            </li>
                        @endif
                    @endforeach
                </ol>
            </div>
        </section>
    @endif
</div>
{{-- ########## END: DYNAMIC CONTENT SECTIONS ########## --}}
                        </div>
                    </article>
                </main>

                <!-- Sidebar -->
                <aside class="lg:col-span-1 order-1 lg:order-2">
                    <div class="sticky top-24">
                        <!-- Quick Facts - Desktop version -->
                        @if(!empty($data['quickFacts']['facts']))
                            <div class="hidden lg:block bg-white rounded-2xl shadow-lg p-6 mb-8">
                                <h2 class="text-xl font-bold text-gray-900 mb-4 border-b border-gray-100 pb-2">{{ $data['quickFacts']['title'] ?? 'Quick Facts' }}</h2>
                                <dl class="space-y-4">
                                    @foreach($data['quickFacts']['facts'] as $fact)
                                        <div class="bg-gradient-to-r from-gray-50 to-blue-50 rounded-lg p-3 hover:shadow-sm transition-all">
                                            <dt class="text-sm font-semibold text-blue-700 uppercase tracking-wide">{{ $fact['label'] }}</dt>
                                            <dd class="text-gray-900 font-medium mt-1">{!! $fact['value'] !!}</dd>
                                        </div>
                                    @endforeach
                                </dl>
                            </div>
                        @endif
                        
                        <!-- Related Articles -->
                        @if(!empty($data['related_articles']))
                            <div class="bg-white rounded-2xl shadow-lg p-6 mb-8">
                                <h3 class="text-xl font-bold text-gray-900 mb-4 border-b border-gray-100 pb-2">Related Biographies</h3>
                                <div class="space-y-4">
                                    @foreach($data['related_articles'] as $article)
                                        <a href="{{ $article['url'] }}" class="flex items-center space-x-4 p-3 bg-gray-50 rounded-lg hover:shadow-md transition-all group">
                                            <img src="{{ $article['image'] ?? 'https://via.placeholder.com/80x80.png?text=Bio' }}" alt="{{ $article['title'] }}" class="w-16 h-16 rounded-full object-cover ring-2 ring-blue-100 group-hover:ring-blue-300 transition-all">
                                            <div>
                                                <h4 class="font-semibold text-gray-900 group-hover:text-blue-700 transition-colors">{{ $article['title'] }}</h4>
                                                <p class="text-sm text-gray-600 line-clamp-2">{!! $article['excerpt'] !!}</p>
                                            </div>
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </aside>
            </div>
        </div>
    </div>
    
    <!-- Footer -->
    <footer class="bg-gray-900 text-white mt-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="text-center"><p>&copy; {{ date('Y') }} CelebBio. All rights reserved.</p></div>
        </div>
    </footer>
    
    <script>
        function biographyApp() {
            return {
                tocCollapsed: window.innerWidth < 1024,
                init() {
                    lucide.createIcons();
                }
            }
        }
    </script>
</body>
</html>
