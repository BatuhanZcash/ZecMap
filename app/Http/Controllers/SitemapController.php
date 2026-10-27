<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use App\Models\Business;
use App\Models\Community;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function index(): Response
    {
        $urls = [];

        // Static, high-value pages.
        $static = [
            ['/', 'daily', '1.0'],
            ['/map', 'daily', '0.9'],
            ['/browse', 'daily', '0.9'],
            ['/accept-zec', 'weekly', '0.8'],
            ['/communities', 'weekly', '0.8'],
            ['/communities/map', 'weekly', '0.8'],
            ['/adoption', 'weekly', '0.7'],
            ['/zcash-wallet-guide', 'monthly', '0.8'],
            ['/blog', 'weekly', '0.6'],
            ['/support', 'monthly', '0.4'],
            ['/analytics', 'weekly', '0.4'],
        ];
        foreach ($static as [$path, $freq, $prio]) {
            $urls[] = $this->url(url($path), null, $freq, $prio);
        }

        // Country / community pages.
        foreach (Community::query()->get(['slug', 'updated_at']) as $c) {
            $urls[] = $this->url(
                route('communities.show', $c->slug),
                $c->updated_at,
                'weekly',
                '0.7',
            );
        }

        // Published blog posts.
        foreach (BlogPost::query()->published()->get(['slug', 'updated_at']) as $post) {
            $urls[] = $this->url(route('blog.show', $post->slug), $post->updated_at, 'monthly', '0.6');
        }

        // Every active business listing.
        Business::query()
            ->where('status', Business::STATUS_ACTIVE)
            ->select(['slug', 'updated_at'])
            ->orderBy('id')
            ->chunk(1000, function ($rows) use (&$urls) {
                foreach ($rows as $b) {
                    $urls[] = $this->url(route('business.show', $b->slug), $b->updated_at, 'weekly', '0.6');
                }
            });

        $xml = '<?xml version="1.0" encoding="UTF-8"?>'."\n"
            .'<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\n"
            .implode('', $urls)
            .'</urlset>'."\n";

        return response($xml, 200, ['Content-Type' => 'application/xml; charset=UTF-8']);
    }

    private function url(string $loc, $lastmod, string $changefreq, string $priority): string
    {
        $out = '  <url>'."\n".'    <loc>'.htmlspecialchars($loc, ENT_XML1).'</loc>'."\n";
        if ($lastmod) {
            $out .= '    <lastmod>'.$lastmod->toAtomString().'</lastmod>'."\n";
        }
        $out .= '    <changefreq>'.$changefreq.'</changefreq>'."\n"
            .'    <priority>'.$priority.'</priority>'."\n"
            .'  </url>'."\n";

        return $out;
    }
}
