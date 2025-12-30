<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function index()
    {
        $baseUrl = config('app.url');

        $urls = [];

        // Static Pages
        $urls[] = ['loc' => $baseUrl, 'priority' => '1.0', 'changefreq' => 'daily'];
        $urls[] = ['loc' => $baseUrl . '/about', 'priority' => '0.8', 'changefreq' => 'monthly'];
        $urls[] = ['loc' => $baseUrl . '/pricing', 'priority' => '0.8', 'changefreq' => 'monthly'];
        $urls[] = ['loc' => $baseUrl . '/courses', 'priority' => '0.9', 'changefreq' => 'daily'];
        $urls[] = ['loc' => $baseUrl . '/teachers', 'priority' => '0.8', 'changefreq' => 'weekly'];
        $urls[] = ['loc' => $baseUrl . '/register', 'priority' => '0.8', 'changefreq' => 'monthly'];
        $urls[] = ['loc' => $baseUrl . '/login', 'priority' => '0.8', 'changefreq' => 'monthly'];

        // Dynamic Courses
        $courses = Course::where('is_published', true)->get();
        foreach ($courses as $course) {
            $urls[] = [
                'loc' => $baseUrl . '/courses/' . $course->slug,
                'priority' => '0.9',
                'changefreq' => 'weekly',
                'lastmod' => $course->updated_at->toAtomString(),
            ];
        }

        // XML Construction
        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

        foreach ($urls as $url) {
            $xml .= '<url>';
            $xml .= '<loc>' . $url['loc'] . '</loc>';
            if (isset($url['lastmod'])) {
                $xml .= '<lastmod>' . $url['lastmod'] . '</lastmod>';
            }
            $xml .= '<changefreq>' . $url['changefreq'] . '</changefreq>';
            $xml .= '<priority>' . $url['priority'] . '</priority>';
            $xml .= '</url>';
        }

        $xml .= '</urlset>';

        return response($xml, 200, [
            'Content-Type' => 'application/xml',
        ]);
    }
}
