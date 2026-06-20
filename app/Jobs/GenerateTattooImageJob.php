<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Models\AiGeneratedTattoo;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class GenerateTattooImageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tattooId;
    public $responseJson;

    /**
     * Create a new job instance.
     *
     * @param int $tattooId
     * @param array $responseJson
     */
    public function __construct(int $tattooId, array $responseJson)
    {
        $this->tattooId = $tattooId;
        $this->responseJson = $responseJson;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        Log::info("Starting GenerateTattooImageJob for tattoo ID: {$this->tattooId}");

        // Fetch the tattoo record
        $tattoo = AiGeneratedTattoo::find($this->tattooId);

        if (!$tattoo) {
            Log::error("Tattoo record not found for ID: {$this->tattooId}");
            return;
        }

        Log::info("Tattoo record found: ", $tattoo->toArray());

        $apiKey = config('app.open_api_Key');
        $endpoint = 'https://api.openai.com/v1/images/generations';

        try {
            // Generate combined tattoo and stencil image
            $formattedResponse = json_encode($this->responseJson, JSON_PRETTY_PRINT);

            Log::info("Sending formatted response to OpenAI for tattoo ID: {$this->tattooId}", ['response' => $formattedResponse]);

            $tattooImageUrl = $this->generateImage($endpoint, $apiKey, $formattedResponse);
            $tattooImagePath = $this->storeImage($tattooImageUrl, 'tattoo_combined');

            // Update the tattoo record with the generated image path
            $tattoo->update([
                'generated_image' => $tattooImagePath,
            ]);

            Log::info("Tattoo image generated and updated for ID: {$this->tattooId}");

            // After saving the combined image
            //SplitTattooImageJob::dispatch($tattoo->id, $tattooImagePath);
            //Log::info("Dispatched SplitTattooImageJob for tattoo ID: {$this->tattooId}");

        } catch (\Exception $e) {
            Log::error("Error generating tattoo image for ID {$this->tattooId}: {$e->getMessage()}");
        }
    }

    /**
     * Call the OpenAI API to generate an image.
     *
     * @param string $endpoint
     * @param string $apiKey
     * @param string $prompt
     * @return string|null
     */
    protected function generateImage($endpoint, $apiKey, $prompt)
    {
        try {
            $client = new Client();
            $response = $client->post($endpoint, [
                'headers' => [
                    'Authorization' => "Bearer {$apiKey}",
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'prompt' => $prompt,
                    'n' => 1, // Generate one image
                    'size' => '1792x1024', // Combined image resolution
                    'model' => 'dall-e-3', // Specify DALL-E 3 explicitly
                ],
            ]);

            $data = json_decode($response->getBody(), true);
            return $data['data'][0]['url'] ?? null;
        } catch (\Exception $e) {
            Log::error("Error calling OpenAI API: {$e->getMessage()}");
            throw $e;
        }
    }

    /**
     * Store the generated image.
     *
     * @param string $imageUrl
     * @param string $type
     * @return string
     * @throws \Exception
     */
    protected function storeImage($imageUrl, $type)
    {
        try {
            $imageContent = file_get_contents($imageUrl);
            if (!$imageContent) {
                throw new \Exception("Failed to download the generated image from {$imageUrl}");
            }

            // Ensure the directory exists
            $directory = 'images/ai_tattoos';
            if (!Storage::disk('public')->exists($directory)) {
                Storage::disk('public')->makeDirectory($directory);
            }

            // Generate a unique filename
            $filename = "{$type}_" . time() . "_" . uniqid() . '.jpg';
            $filePath = "{$directory}/{$filename}";

            // Save the image to the public disk
            Storage::disk('public')->put($filePath, $imageContent);

            return $filePath;
        } catch (\Exception $e) {
            Log::error("Error storing image: {$e->getMessage()}");
            throw $e;
        }
    }
}
