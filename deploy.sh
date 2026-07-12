#!/bin/bash
echo "🚀 Starting Deployment..."
cd /home/makoon/laravel-blog

# GitHub से नया कोड पुल करें
git pull origin main

# ea-php82 और --ignore-platform-reqs का उपयोग करके डिपेंडेंसी इंस्टॉल करें
/usr/local/bin/ea-php82 /usr/local/bin/composer install --no-dev --optimize-autoloader --no-interaction --ignore-platform-reqs
npm ci
npm run build

# ea-php82 का उपयोग करके Laravel सेटिंग्स रन करें
/usr/local/bin/ea-php82 artisan config:cache
/usr/local/bin/ea-php82 artisan route:cache
/usr/local/bin/ea-php82 artisan view:cache
/usr/local/bin/ea-php82 artisan storage:link

# सैटमैप जनरेट करें (Tinker से)
/usr/local/bin/ea-php82 artisan tinker --execute="app(App\Services\SitemapService::class)->generate()"

# सिमलिंक बनाएं ( सभी पाथ के लिए )
ln -sfn /home/makoon/laravel-blog/public /home/makoon/public_html/blogs
ln -sfn /home/makoon/laravel-blog/public /home/makoon/public_html/stories
ln -sfn /home/makoon/laravel-blog/public /home/makoon/public_html/printables
ln -sfn /home/makoon/laravel-blog/public /home/makoon/public_html/sessions
ln -sfn /home/makoon/laravel-blog/public /home/makoon/public_html/author
ln -sfn /home/makoon/laravel-blog/public /home/makoon/public_html/author-sana-kapoor

# सही परमिशन सेट करें
chown -R makoon:makoon /home/makoon/laravel-blog
echo "✅ Deployment Finished Successfully!"
