<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Teacher;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    /**
     * Generate XML sitemap
     */
    public function index()
    {
        $sitemap = '<?xml version="1.0" encoding="UTF-8"?>';
        $sitemap .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

        // Homepage
        $sitemap .= $this->addUrl(url('/'), '1.0', 'daily');

        // Static pages
        $staticPages = [
            '/courses' => ['priority' => '0.9', 'changefreq' => 'daily'],
            '/teachers' => ['priority' => '0.8', 'changefreq' => 'weekly'],
            '/pricing' => ['priority' => '0.7', 'changefreq' => 'weekly'],
            '/about' => ['priority' => '0.6', 'changefreq' => 'monthly'],
        ];

        foreach ($staticPages as $url => $config) {
            $sitemap .= $this->addUrl(
                url($url),
                $config['priority'],
                $config['changefreq']
            );
        }

        // All published courses
        $courses = Course::published()->get();
        foreach ($courses as $course) {
            $sitemap .= $this->addUrl(
                url("/courses/{$course->id}"),
                '0.8',
                'weekly',
                $course->updated_at->toAtomString()
            );
        }

        // All active teachers
        $teachers = Teacher::where('status', 'approved')->get();
        foreach ($teachers as $teacher) {
            $sitemap .= $this->addUrl(
                url("/teachers/{$teacher->id}"),
                '0.7',
                'monthly'
            );
        }

        $sitemap .= '</urlset>';

        return response($sitemap, 200)
            ->header('Content-Type', 'text/xml');
    }

    /**
     * Add URL to sitemap
     */
    private function addUrl($loc, $priority = '0.5', $changefreq = 'weekly', $lastmod = null)
    {
        $url = '<url>';
        $url .= '<loc>' . htmlspecialchars($loc) . '</loc>';

        if ($lastmod) {
            $url .= '<lastmod>' . $lastmod . '</lastmod>';
        }

        $url .= '<changefreq>' . $changefreq . '</changefreq>';
        $url .= '<priority>' . $priority . '</priority>';
        $url .= '</url>';

        return $url;
    }
}
