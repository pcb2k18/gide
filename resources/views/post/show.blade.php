
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $post->title }} - CelebBio</title>
    <meta name="description" content="{{ $post->excerpt ?? Str::limit(strip_tags($post->content), 155) }}">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <style>
        .font-inter { font-family: 'Inter', system-ui, sans-serif; }
        .prose h2 { @apply text-2xl font-bold mt-8 mb-4; }
        .prose h3 { @apply text-xl font-bold mt-6 mb-3; }
        .prose p { @apply mb-6 leading-relaxed; }
        .prose ul, .prose ol { @apply list-disc pl-6 space-y-2 mb-6; }
    </style>
</head>
<body class="bg-slate-50 font-inter text-gray-800">

    <!-- Header -->
    <header class="bg-white border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex-shrink-0"><a href="/de" class="text-2xl font-bold text-gray-900 hover:text-blue-600">CelebBio</a></div>
                <nav class="hidden md:flex space-x-8"><a href="/de" class="text-gray-600 hover:text-gray-900 text-sm font-medium">Home</a></nav>
            </div>
        </div>
    </header>

    <!-- Main Article -->
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <article class="bg-white rounded-lg shadow-md p-6 sm:p-8 md:p-12">
            
            <!-- Post Header -->
            <header class="border-b pb-6 mb-6">
                <h1 class="text-3xl sm:text-4xl font-extrabold text-gray-900 mb-4">{{ $post->title }}</h1>
                <div class="text-sm text-gray-500">
                    <span>By {{ $post->author_name ?? 'Guest Author' }}</span>
                    <span class="mx-2">&middot;</span>
                    <span>Published on {{ $post->created_at->format('F j, Y') }}</span>
                </div>
            </header>

           {{-- Featured Image --}}
@php
    use Illuminate\Support\Str;
    use Illuminate\Support\Facades\Storage;

    $path = trim($post->featured_image ?? '', '/');

    if (Str::startsWith($path, 'public/')) {
        $path = Str::after($path, 'public/');
    }

    $isExternal = Str::startsWith($path, ['http://', 'https://']);
    $imageUrl  = $isExternal ? $path : Storage::disk('public')->url($path);
    $exists    = $isExternal || Storage::disk('public')->exists($path);
@endphp

@if($exists)
    <figure class="mb-8">
        <img src="{{ $imageUrl }}" alt="{{ $post->title }}" class="w-full h-auto rounded-lg shadow-md">
    </figure>
@endif

            <!-- Post Content -->
            <div class="prose prose-lg max-w-none text-gray-700">
                {!! $post->content !!}
            </div>

        </article>
    </div>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white mt-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="text-center"><p>&copy; {{ date('Y') }} CelebBio. All rights reserved.</p></div>
        </div>
    </footer>

</body>
</html>
