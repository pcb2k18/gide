<!DOCTYPE html>
<html lang="de" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="CelebBio - Die umfassendsten Biografien deutscher Persönlichkeiten und weitere interessante Artikel.">
    <meta name="keywords" content="Biografien, deutsche Persönlichkeiten, CelebBio, Gastbeiträge">
    <title>CelebBio - Deutsche Persönlichkeiten & Biografien</title>
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Google Fonts - Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>
<body class="bg-white text-gray-900 antialiased">
    <!-- Header -->
    <header class="bg-white shadow-sm border-b border-gray-100 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Logo -->
                <div class="flex-shrink-0">
                    <a href="/" class="flex items-center">
                        <div class="w-8 h-8 bg-gradient-to-br from-blue-600 to-purple-600 rounded-lg flex items-center justify-center mr-3">
                            <span class="text-white font-bold text-sm"></span>
                        </div>
                        <span class="text-xl font-bold text-gray-900">GI-DE</span>
                    </a>
                </div>
                
                <!-- Navigation -->
                <nav class="hidden md:flex space-x-8">
                    <a href="/" class="text-gray-900 hover:text-blue-600 px-3 py-2 text-sm font-medium transition-colors duration-200">
                        Startseite
                    </a>
                    <a href="/biografien" class="text-gray-600 hover:text-blue-600 px-3 py-2 text-sm font-medium transition-colors duration-200">
                        Biografien
                    </a>
                    <a href="/artikel" class="text-gray-600 hover:text-blue-600 px-3 py-2 text-sm font-medium transition-colors duration-200">
                        Artikel
                    </a>
                    <a href="/kontakt" class="text-gray-600 hover:text-blue-600 px-3 py-2 text-sm font-medium transition-colors duration-200">
                        Kontakt
                    </a>
                </nav>

                <!-- Mobile menu button -->
                <div class="md:hidden">
                    <button type="button" class="text-gray-600 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 p-2">
                        <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="bg-gradient-to-br from-blue-50 via-white to-purple-50 py-20 lg:py-28">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-bold text-gray-900 mb-6 leading-tight">
                    Willkommen bei
                    <span class="bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">GI-DE</span>
                </h1>
                <p class="text-xl sm:text-2xl text-gray-600 mb-8 max-w-3xl mx-auto leading-relaxed">
                    Die umfassendsten Biografien deutscher Persönlichkeiten.
                </p>
                <div class="flex flex-col sm:flex-row justify-center items-center space-y-4 sm:space-y-0 sm:space-x-4">
                    <a href="/biografien" class="bg-blue-600 text-white px-8 py-4 rounded-lg font-semibold hover:bg-blue-700 transition-colors duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                        Biografien entdecken
                    </a>
                    <a href="/artikel" class="bg-white text-gray-900 border-2 border-gray-200 px-8 py-4 rounded-lg font-semibold hover:bg-gray-50 transition-colors duration-200">
                        Artikel lesen
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Biographies Section -->
    <section class="py-16 lg:py-24 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl lg:text-4xl font-bold text-gray-900 mb-4">Neueste Biografien</h2>
                <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                    Entdecken Sie die faszinierenden Lebensgeschichten bekannter deutscher Persönlichkeiten.
                </p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mb-12">
                @foreach($biographies as $biography)
                <article class="group">
                    <a href="{{ route('biography.show', ['slug' => $biography->slug]) }}" class="block bg-white rounded-xl shadow-sm hover:shadow-xl transition-all duration-300 transform hover:-translate-y-2 border border-gray-100 overflow-hidden">
                        <div class="aspect-w-16 aspect-h-9 overflow-hidden">
                            <img 
                                src="{{ $biography->content_data['hero']['mainImageUrl'] ?? 'https://images.pexels.com/photos/220453/pexels-photo-220453.jpeg?auto=compress&cs=tinysrgb&w=800' }}" 
                                alt="{{ $biography->full_name }}"
                                class="w-full h-48 object-cover group-hover:scale-105 transition-transform duration-300"
                                loading="lazy"
                            >
                        </div>
                        <div class="p-6">
                            <h3 class="text-xl font-semibold text-gray-900 mb-3 group-hover:text-blue-600 transition-colors duration-200">
                                {{ $biography->full_name }}
                            </h3>
                            <p class="text-gray-600 leading-relaxed">
                                {{ Str::limit(strip_tags($biography->content_data['hero']['intro'] ?? 'Erfahren Sie mehr über das Leben und Wirken dieser bemerkenswerten Persönlichkeit.'), 100) }}
                            </p>
                        </div>
                    </a>
                </article>
                @endforeach
            </div>
            
            <div class="text-center">
                <a href="/biografien" class="inline-flex items-center text-blue-600 hover:text-blue-700 font-semibold transition-colors duration-200">
                    Alle Biografien anzeigen
                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                    </svg>
                </a>
            </div>
        </div>
    </section>

    <!-- Guest Posts Section -->
    <section class="py-16 lg:py-24 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl lg:text-4xl font-bold text-gray-900 mb-4">Weitere Artikel & Gastbeiträge</h2>
                <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                    Interessante Einblicke und Perspektiven von Experten und Gastautoren.
                </p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mb-12">
                @foreach($posts as $post)
                <article class="group">
                    <a href="{{ route('post.show', ['slug' => $post->slug]) }}" class="block bg-white rounded-xl shadow-sm hover:shadow-xl transition-all duration-300 transform hover:-translate-y-2 border border-gray-100 overflow-hidden">
                        <div class="aspect-w-16 aspect-h-9 overflow-hidden">
                            <img 
                                src="{{ Storage::url($post->featured_image) }}" 
                                alt="{{ $post->title }}"
                                class="w-full h-48 object-cover group-hover:scale-105 transition-transform duration-300"
                                loading="lazy"
                            >
                        </div>
                        <div class="p-6">
                            <h3 class="text-xl font-semibold text-gray-900 mb-3 group-hover:text-blue-600 transition-colors duration-200 leading-tight">
                                {{ $post->title }}
                            </h3>
                            <p class="text-gray-600 leading-relaxed">
                                {{ $post->excerpt }}
                            </p>
                        </div>
                    </a>
                </article>
                @endforeach
            </div>
            
            <div class="text-center">
                <a href="/artikel" class="inline-flex items-center text-blue-600 hover:text-blue-700 font-semibold transition-colors duration-200">
                    Alle Artikel anzeigen
                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                    </svg>
                </a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <!-- Logo and Description -->
                <div class="md:col-span-2">
                    <div class="flex items-center mb-4">
                        <div class="w-8 h-8 bg-gradient-to-br from-blue-600 to-purple-600 rounded-lg flex items-center justify-center mr-3">
                            <span class="text-white font-bold text-sm">CB</span>
                        </div>
                        <span class="text-xl font-bold">GI-DE</span>
                    </div>
                    <p class="text-gray-300 mb-4 max-w-md">
                        Die umfassendste Sammlung deutscher Biografien und interessanter Artikel über bemerkenswerte Persönlichkeiten.
                    </p>
                    <div class="flex space-x-4">
                        <a href="#" class="text-gray-300 hover:text-white transition-colors duration-200">
                            <span class="sr-only">Facebook</span>
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z"/>
                            </svg>
                        </a>
                        <a href="#" class="text-gray-300 hover:text-white transition-colors duration-200">
                            <span class="sr-only">Twitter</span>
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
                            </svg>
                        </a>
                    </div>
                </div>
                
                <!-- Quick Links -->
                <div>
                    <h3 class="text-lg font-semibold mb-4">Schnelle Links</h3>
                    <ul class="space-y-2">
                        <li><a href="/biografien" class="text-gray-300 hover:text-white transition-colors duration-200">Biografien</a></li>
                        <li><a href="/artikel" class="text-gray-300 hover:text-white transition-colors duration-200">Artikel</a></li>
                        <li><a href="/kategorien" class="text-gray-300 hover:text-white transition-colors duration-200">Kategorien</a></li>
                        <li><a href="/autoren" class="text-gray-300 hover:text-white transition-colors duration-200">Autoren</a></li>
                    </ul>
                </div>
                
                <!-- Info -->
                <div>
                    <h3 class="text-lg font-semibold mb-4">Information</h3>
                    <ul class="space-y-2">
                        <li><a href="/ueber-uns" class="text-gray-300 hover:text-white transition-colors duration-200">Über uns</a></li>
                        <li><a href="/kontakt" class="text-gray-300 hover:text-white transition-colors duration-200">Kontakt</a></li>
                        <li><a href="/impressum" class="text-gray-300 hover:text-white transition-colors duration-200">Impressum</a></li>
                        <li><a href="/datenschutz" class="text-gray-300 hover:text-white transition-colors duration-200">Datenschutz</a></li>
                    </ul>
                </div>
            </div>
            
            <div class="border-t border-gray-800 mt-8 pt-8 text-center">
                <p class="text-gray-300">&copy; {{ date('Y') }} GI-DE. Alle Rechte vorbehalten.</p>
            </div>
        </div>
    </footer>
</body>
</html>
