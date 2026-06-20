<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use GuzzleHttp\Client;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use App\Models\SafirExcel;
use App\Models\Product;
use App\Models\ProductVariation;
use App\Models\Attribute;
use App\Models\Category;
use App\Models\AttributeValue;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

class ScrapeSafirExcelLinks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * Use --limit to restrict the number of records processed,
     * and --test to process only one record and dump its parsed data.
     *
     * @var string
     */
    protected $signature = 'scrape:safir-links {--limit=5} {--test : Test mode, process only one record and dump data}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scrape product pages from SafirExcel links and create products with additional SafirExcel fields';

    /**
     * Guzzle HTTP client instance.
     *
     * @var Client
     */
    protected Client $client;

    public function __construct()
    {
        parent::__construct();

        $this->client = new Client([
            'verify'  => false,
            'timeout' => 10,
        ]);
    }

    public function handle()
    {
        // Retrieve SafirExcel records with a valid link and that haven't been parsed
        $query = SafirExcel::where('safir_link_exist', true)
            ->where('safir_parsed', 0);

        $allCategories = Category::with('parent')->get()->map(function ($cat) {
            return [
                'id'       => $cat->id,
                'name'     => $cat->name,
                'parent'   => $cat->parent?->name,
                'parent_id'=> $cat->parent_id,
            ];
        });

        $localCategories = Category::all()->keyBy(function ($cat) {
            return strtolower(trim($cat->name));
        });

        if ($this->option('test')) {
            $query->limit(1);
        } else {
            $limit = (int)$this->option('limit');
            $query->limit($limit);
        }

        $records = $query->get();

        if ($records->isEmpty()) {
            $this->info("No SafirExcel records to process.");
            return 0;
        }

        $count = 0;
        $total = $records->count();

        foreach ($records as $safir) {
            $url = $safir->safir_excel_link;
            $this->info("Scraping URL: {$url}");

            try {
                $response = $this->client->request('GET', $url);
                $html = (string)$response->getBody();
            } catch (\Exception $e) {
                $this->error("Failed to retrieve {$url}: " . $e->getMessage());
                continue;
            }

            // Load HTML into DOMDocument and prepare XPath
            libxml_use_internal_errors(true);
            $dom = new \DOMDocument();
            $dom->loadHTML($html);
            libxml_clear_errors();
            $xpath = new \DOMXPath($dom);

            // 1. Extract product title (modify XPath as needed)
            $titleNodes = $xpath->query('//h1[contains(@class, "product_title")]');
            $title = ($titleNodes->length > 0)
                ? trim($titleNodes->item(0)->textContent)
                : $safir->safir_excel_name;

            // 2. Extract short description.
            // Adjust the XPath below to target your short description element.
            $descNodes = $xpath->query('//*[contains(@class, "short-description")]');
            $shortDescHtml = ($descNodes->length > 0)
                ? $this->getInnerHTML($descNodes->item(0))
                : '<p>No description available</p>';
            $descriptionPlain = trim(strip_tags($shortDescHtml));

            // 3. Extract product characteristics from a table (if present).
            $characteristics = [];
            $rows = $xpath->query('//table[contains(@class, "product-attributes")]//tr');
            foreach ($rows as $row) {
                $th = $xpath->query('.//th', $row);
                $td = $xpath->query('.//td', $row);
                if ($th->length && $td->length) {
                    $label = trim($th->item(0)->textContent);
                    $value = trim($td->item(0)->textContent);
                    if ($label && $value) {
                        $values = preg_split('/,\s*/', $value);
                        $characteristics[$label] = $values;
                    }
                }
            }

            // 4. Extract the primary image URL.
            $images = [];
            $imgNode = $xpath->query('//div[contains(@class, "product-images")]//img')->item(0);
            if ($imgNode) {
                $src = $imgNode->getAttribute('src');
                if ($src) {
                    try {
                        $imageContents = file_get_contents($src);
                        $filename = Str::slug($title) . '-' . uniqid() . '.jpg';
                        Storage::disk('public')->put("images/uploads/products/{$filename}", $imageContents);
                        $savedPath = "/storage/images/uploads/products/{$filename}";
                        $images[] = $savedPath;
                    } catch (\Exception $e) {
                        $this->error("Failed to download image {$src}: " . $e->getMessage());
                        $images[] = "";
                    }
                }
            }
            if (empty($images)) {
                $images = [""];
            }

            // 5. Extract all category links from the product page
            $categoryId = null;
            $categoryNodes = $xpath->query('//span[contains(@class, "posted_in")]//a');

            $matchedCategories = [];

            foreach ($categoryNodes as $node) {
                $scrapedName = strtolower(trim($node->textContent));

                if ($localCategories->has($scrapedName)) {
                    $category = $localCategories->get($scrapedName);
                    $matchedCategories[] = $category;
                }
            }

            // Prefer child category (with parent_id) over parent
            $preferredCategory = collect($matchedCategories)
                ->sortByDesc(fn($cat) => $cat['parent_id'] ? 1 : 0) // prioritize children
                ->first();

            if ($preferredCategory) {
                $categoryId = $preferredCategory['id'];
            } else {
                $categoryId = $safir->category_id ?? 1; // fallback
            }


            // In test mode, dump the parsed data and exit.
            if ($this->option('test')) {
                dd("Test mode: Parsed data for product:", [
                    'title' => $title,
                    'short_description_html' => $shortDescHtml,
                    'description_plain' => $descriptionPlain,
                    'characteristics' => $characteristics,
                    'images' => $images,
                    // Also include SafirExcel fields:
                    'safir_excel_id'   => $safir->safir_excel_id,
                    'safir_excel_name' => $safir->safir_excel_name,
                    'safir_excel_link' => $safir->safir_excel_link,
                ]);
            }

            // 5. Create a new Product record.
            $product = new Product();
            $product->name              = $title;
            $product->slug              = Str::slug($title);
            $product->description       = $shortDescHtml;
            $product->description_plain = $descriptionPlain;
            $product->brand_name        = 'safir';
            // Save additional SafirExcel data in the product
            $product->safir_excel_id    = $safir->safir_excel_id;
            $product->safir_excel_name  = $safir->safir_excel_name;
            $product->safir_excel_link  = $safir->safir_excel_link;
            $product->category_id       = $categoryId ?? 1;
            $product->vat_id            = 1;
            $product->images            = $images;
            $buc_set = $safir->safir_buc_set ?: ($safir->safir_buc_bax ?: ($safir->safir_set_bax ?: 1));
            $product->price = $safir->safir_sell_price / $buc_set;
            $product->buc_set           = $safir->safir_buc_set;
            $product->set_bax           = $safir->safir_set_bax;
            $product->buc_bax           = $safir->safir_buc_bax;
            $product->currency          = 'RON';
            $product->commission_percentage = 0;
            $product->general_stock     = 0;
            $product->status            = 1;
            $product->source_link       = $url;
            $product->save();

            // 6. Update product_code.
            $product->product_code = 'TEX-' . $product->id;
            $product->save();

            // 7. Create a default Product Variation.
            $variation = new ProductVariation();
            $variation->product_id = $product->id;
            $variation->sku        = 'TEX-SKU-' . $product->id;
            $variation->price      = 0;
            $variation->stock      = 9999;
            $variation->save();

            // 8. Attach characteristics as attributes.
            foreach ($characteristics as $label => $vals) {
                $attribute = Attribute::firstOrCreate(
                    ['name' => $label],
                    ['description' => $label]
                );
                foreach ($vals as $rawValue) {
                    $cleanValue = trim($rawValue);
                    if (!empty($cleanValue)) {
                        $attrValue = \App\Models\AttributeValue::firstOrCreate([
                            'attribute_id' => $attribute->id,
                            'value'        => $cleanValue,
                        ]);
                        $variation->attributeValues()->syncWithoutDetaching([$attrValue->id]);
                    }
                }
            }

            // 9. Mark the SafirExcel record as parsed.
            $safir->safir_parsed = 1;
            $safir->save();

            $this->info("Processed and saved product: {$product->name}");
            $count++;

            // Throttle requests every 30 records
            if ($count % 30 === 0 && $count < $total) {
                $this->info("Processed {$count} products. Sleeping for 10 seconds...");
                sleep(10);
            }
        }

        $this->info("Finished scraping and importing {$count} product(s).");
        return 0;
    }

    /**
     * Helper function to get the inner HTML of a DOMNode.
     *
     * @param \DOMNode $node
     * @return string
     */
    protected function getInnerHTML(\DOMNode $node)
    {
        $innerHTML = "";
        foreach ($node->childNodes as $child) {
            $innerHTML .= $node->ownerDocument->saveHTML($child);
        }
        return $innerHTML;
    }
}
