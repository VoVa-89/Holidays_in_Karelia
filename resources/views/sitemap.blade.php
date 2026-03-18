<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">

    {{-- Главная страница --}}
    <url>
        <loc>https://karelia-go.ru/</loc>
        <changefreq>daily</changefreq>
        <priority>1.0</priority>
    </url>

    {{-- Список всех постов --}}
    <url>
        <loc>https://karelia-go.ru/posts</loc>
        <changefreq>daily</changefreq>
        <priority>0.9</priority>
    </url>

    {{-- Статические страницы --}}
    <url>
        <loc>https://karelia-go.ru/about</loc>
        <changefreq>monthly</changefreq>
        <priority>0.5</priority>
    </url>
    <url>
        <loc>https://karelia-go.ru/guidelines</loc>
        <changefreq>monthly</changefreq>
        <priority>0.4</priority>
    </url>

    {{-- Категории --}}
    @foreach($categories as $category)
    <url>
        <loc>https://karelia-go.ru/categories/{{ $category->slug }}</loc>
        <lastmod>{{ $category->updated_at?->toAtomString() ?? now()->toAtomString() }}</lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.8</priority>
    </url>
    @endforeach

    {{-- Опубликованные посты --}}
    @foreach($posts as $post)
    <url>
        <loc>https://karelia-go.ru/posts/{{ $post->slug }}</loc>
        <lastmod>{{ $post->updated_at?->toAtomString() ?? now()->toAtomString() }}</lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.7</priority>
    </url>
    @endforeach

</urlset>
