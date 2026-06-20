<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Category;
use App\Models\SafirProduct;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

class ParseSafirProducts extends Command
{
    protected $signature = 'safir:parse-products';
    protected $description = 'Scrape product links from PlatinAmbalaje child categories with source = safir';

    // We'll use Guzzle to fetch pages
    protected Client $client;

    public function __construct()
    {
        parent::__construct();
        $this->client = new Client([
            'verify'  => false,  // For SSL issues
            'timeout' => 10,
        ]);
    }

    public function handle()
    {
        // Grab all categories with source = 'safir'
        $categories = Category::where('source', 'safir')->get();

        foreach ($categories as $cat) {
            $url = $cat->source_link;
            if (!$url) {
                $this->warn("Category [{$cat->name}] has no source_link. Skipping.");
                continue;
            }
            // Use regex to check if the URL contains two segments after '/categorie/'
            // For example, a child category URL might look like:
            // https://www.platinambalaje.ro/categorie/bureti-metalici-si-lavete-umede/bureti-metalici/
            if (!preg_match('#/categorie/[^/]+/[^/]+/?$#', $url)) {
                // Skip parent categories that don't have a second segment.
                $this->info("Skipping parent category '{$cat->name}' with URL: $url");
                continue;
            }

            $this->info("Scraping child category '{$cat->name}' at URL: $url");

            // Scrape recursively for multiple pages
            $this->scrapeCategory($cat->id, $url);
        }

        $this->info('Scraping done!');
        return Command::SUCCESS;
    }

    /**
     * Scrape a single category page (and subsequent paginated pages).
     */
    protected function scrapeCategory($categoryId, $url)
    {
        while ($url) {
            try {
                $this->info("GET $url");
                $response = $this->client->request('GET', $url);
                $html = (string)$response->getBody();

                // Extract product links
                $newLinks = $this->extractProductLinks($html);

                // Save the product links
                $this->saveProductLinks($categoryId, $newLinks);

                // Find the URL for the next page
                $url = $this->findNextPageLink($html);
            } catch (ClientException $e) {
                // If it's 404 or 410 etc., skip and continue
                if ($e->getCode() == 404) {
                    $this->warn("   -> Skipping 404 link $url");
                    return; // stop looping for this category
                }

                // Alternatively, if status code is 404 mark the category as invalid
                if (isset($response) && $response->getStatusCode() == 404) {
                    Category::where('id', $categoryId)->update([
                        'source_status' => -1, // mark as invalid
                    ]);
                    $this->warn("Category $categoryId link is invalid. Marked as broken.");
                    return;
                }

                $this->warn("   -> Client error for $url: " . $e->getMessage());
                return;
            } catch (\Exception $e) {
                $this->error("   -> Request failed for $url: " . $e->getMessage());
                return;
            }
        }
    }

    /**
     * Extract all product links from a single category HTML page.
     */
    protected function extractProductLinks($html)
    {
        $links = [];
        $dom = new \DOMDocument();
        libxml_use_internal_errors(true); // ignore malformed HTML
        $dom->loadHTML($html);
        libxml_clear_errors();
        $xpath = new \DOMXPath($dom);

        // Example: find <a> elements with class 'product-image-link'
        $productNodes = $xpath->query('//a[contains(@class,"product-image-link")]');
        foreach ($productNodes as $node) {
            $href = $node->getAttribute('href');
            if ($href) {
                $links[] = $href;
            }
        }

        return $links;
    }

    /**
     * Save all newly discovered product links to the DB.
     */
    protected function saveProductLinks($categoryId, array $links)
    {
        foreach ($links as $link) {
            SafirProduct::firstOrCreate([
                'product_link' => $link,
            ], [
                'category_id' => $categoryId,
            ]);
        }
    }

    /**
     * Check if there's a "next page" link. Return its URL or null if none.
     */
    protected function findNextPageLink($html)
    {
        $dom = new \DOMDocument();
        libxml_use_internal_errors(true);
        $dom->loadHTML($html);
        libxml_clear_errors();
        $xpath = new \DOMXPath($dom);

        // Look for the "next page" link (e.g., <a class="next page-numbers" href="...">)
        $nextNode = $xpath->query('//a[@class="next page-numbers"]')->item(0);
        if ($nextNode) {
            return $nextNode->getAttribute('href');
        }

        return null;
    }
}
