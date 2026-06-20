<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use GuzzleHttp\Client;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use App\Models\SafirProduct;
use App\Models\Product;
use App\Models\ProductVariation;
use App\Models\Attribute;
use App\Models\AttributeValue;

class ScrapeSafirProducts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * Use --limit option to restrict the number of products processed.
     *
     * @var string
     */
    protected $signature = 'scrape:safir-products {--id=} {--limit=5}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scrape Safir product pages and save details into products';

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
        $limit = (int)$this->option('limit');
        //$safirProducts = SafirProduct::limit($limit)->get();
        //$safirProducts = SafirProduct::where('id', 331)->get();

        if ($this->option('id')) {
            $safirProducts = SafirProduct::where('id', $this->option('id'))->get();
        } else {
            $limit = (int)$this->option('limit');
            $safirProducts = SafirProduct::limit($limit)->get();
        }

        $count = 0;
        $total = count($safirProducts);

        foreach ($safirProducts as $safir) {
            $url = $safir->product_link;
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

            // 1. Get the product title from <h1 class="product_title">
            $titleNodes = $xpath->query('//h1[contains(@class, "product_title")]');
            $title = ($titleNodes->length > 0)
                ? trim($titleNodes->item(0)->textContent)
                : $safir->product_title;

            // 2. Get the short description (trim spaces at beginning and end)
            $descNodes = $xpath->query('//*[contains(@class, "woocommerce-product-details__short-description")]');
            $shortDescHtml = '';
            if ($descNodes->length > 0) {
                $node = $descNodes->item(0);
                $shortDescHtml = $this->getInnerHTML($node);
            }
            $shortDescHtml = trim($shortDescHtml);
            $descriptionPlain = trim(strip_tags($shortDescHtml));

            // 3. Extract product characteristics from the attributes table.
            $characteristics = [];
            $rows = $xpath->query('//table[contains(@class, "woocommerce-product-attributes")]//tr');
            foreach ($rows as $row) {
                $th = $xpath->query('.//th', $row);
                $td = $xpath->query('.//td', $row);
                if ($th->length && $td->length) {
                    $label = trim($th->item(0)->textContent);
                    $value = trim($td->item(0)->textContent);
                    if ($label && $value) {
                        // Split by comma if multiple values exist.
                        $values = preg_split('/,\s*/', $value);
                        $characteristics[$label] = $values;
                    }
                }
            }

            // 4. Extract the product image URL from the main container.
            // We now take only the first image (to avoid duplicates from thumbnails).
            $images = [];
            $imgNode = $xpath->query('//div[contains(@class,"product-images") and contains(@class,"wd-grid-col")]//img')->item(0);
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

            // 5. Create the Product record with default values.
            $product = new Product();
            $product->name              = $title;
            $product->slug              = Str::slug($title);
            $product->description       = $shortDescHtml ?: '<p>No description available</p>';
            $product->description_plain = $descriptionPlain;
            $product->brand_name        = 'safir';
            $product->category_id       = $safir->category_id;
            $product->vat_id            = 1;
            $product->images            = $images;
            // Set default values for other required fields.
            $product->price             = 0;
            $product->currency          = 'RON';
            $product->commission_percentage = 0;
            $product->general_stock     = 0;
            $product->status            = 1; // Status set to 1
            $product->source_link       = $safir->product_link; // Status set to 1
            $product->save();

            // 6. Set product_code as "TEX-" plus the product's ID.
            $product->product_code = 'TEX-' . $product->id;
            $product->save();

            // 7. Create a default Product Variation.
            $variation = new ProductVariation();
            $variation->product_id = $product->id;
            $variation->sku        = 'TEX-SKU-'.$product->id;
            $variation->price      = 0;
            $variation->stock      = 9999;
            $variation->save();

            // 8. Save characteristics as Attributes and attach their values to the variation.
            foreach ($characteristics as $label => $vals) {
                $attribute = Attribute::firstOrCreate(
                    ['name' => $label],
                    ['description' => $label]
                );
                foreach ($vals as $rawValue) {
                    $cleanValue = trim($rawValue);
                    if (!empty($cleanValue)) {
                        $attrValue = AttributeValue::firstOrCreate([
                            'attribute_id' => $attribute->id,
                            'value'        => $cleanValue,
                        ]);
                        $variation->attributeValues()->syncWithoutDetaching([$attrValue->id]);
                    }
                }
            }

            $this->info("Saved product: {$product->name}");
            $count++;

            // Throttle: every 30 products, sleep for 10 seconds if there are more to process.
            if ($count % 30 === 0 && $count < $total) {
                $this->info("Processed {$count} products. Sleeping for 10 seconds...");
                sleep(10);
            }
        }

        $this->info("Finished scraping {$count} product(s).");
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
