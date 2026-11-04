<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, follow">
    <title>Makoons Sitemap</title>
    <style>
        :root { --bg: #f5f4f8; --card: #fff; --text: #2b2b33; --muted: #6b6b78; --accent: #6b2d8f; --line: #e8e6ee; }
        @media (prefers-color-scheme: dark) {
            :root { --bg: #17161b; --card: #211f27; --text: #ececf1; --muted: #9a98a8; --accent: #c58bf0; --line: #34313d; }
        }
        * { box-sizing: border-box; }
        body { margin: 0; padding: 32px 16px; background: var(--bg); color: var(--text); font: 16px/1.5 system-ui, -apple-system, "Segoe UI", sans-serif; }
        .card { max-width: 1000px; margin: 0 auto; background: var(--card); border-radius: 16px; padding: 28px; box-shadow: 0 4px 24px rgba(0, 0, 0, .06); }
        h1 { margin: 0 0 8px; font-size: 1.6rem; }
        .badge { font-size: .7rem; vertical-align: middle; background: var(--accent); color: #fff; padding: 2px 8px; border-radius: 6px; }
        p { color: var(--muted); margin: 0 0 16px; }
        .count { display: inline-block; background: rgba(107, 45, 143, .12); color: var(--accent); padding: 4px 12px; border-radius: 999px; font-size: .9rem; margin-bottom: 20px; }
        .back { display: inline-block; margin-bottom: 16px; color: var(--accent); text-decoration: none; }
        table { width: 100%; border-collapse: collapse; }
        th, td { text-align: left; padding: 12px; border-bottom: 1px solid var(--line); word-break: break-all; }
        th { font-size: .75rem; letter-spacing: .05em; text-transform: uppercase; color: var(--muted); }
        td.date { white-space: nowrap; color: var(--muted); font-size: .9rem; word-break: normal; }
        a { color: var(--accent); }
        a:hover { text-decoration: none; }
    </style>
</head>
<body>
    <main class="card">
        <h1>Makoons Sitemap <span class="badge">XML</span></h1>
        <p>This sitemap helps search engines like Google discover and index all posts, stories, printables, and video sessions on Makoons.</p>

        @unless ($isIndex)
            <a class="back" href="{{ route('sitemap.show', 'sitemap.xml') }}">&larr; Back to sitemap index</a><br>
        @endunless

        <span class="count">
            @if ($isIndex)
                This sitemap index contains {{ count($entries) }} sitemaps.
            @else
                {{ $file }} contains {{ count($entries) }} URLs.
            @endif
        </span>

        <table>
            <thead>
                <tr>
                    <th>{{ $isIndex ? 'Sitemap URL' : 'URL' }}</th>
                    <th>Last modified</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($entries as $entry)
                    <tr>
                        <td><a href="{{ $entry['loc'] }}">{{ $entry['loc'] }}</a></td>
                        <td class="date">{{ $entry['lastmod'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </main>
</body>
</html>
