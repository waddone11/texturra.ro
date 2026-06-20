<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Models\AiGeneratedTattoo;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Artisan;
use Log;

class GenerateTattooPromptJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tattooData;

    public function __construct(array $tattooData)
    {
        $this->tattooData = $tattooData;
    }

    public function handle(): void
    {

        //artisan command to call the work:queue
        Artisan::call('queue:work --stop-when-empty');

        Log::info("Job started for tattoo ID: {$this->tattooData['id']}");

        $tattoo = AiGeneratedTattoo::find($this->tattooData['id']);

        if (!$tattoo) {
            Log::error("Tattoo record not found for ID: {$this->tattooData['id']}");
            return;
        }

        $apiKey = config('app.open_api_Key');
        $endpoint = 'https://api.openai.com/v1/chat/completions';

        $prompt = <<<EOT
            You are a tattoo design assistant specializing in creating detailed and unique tattoo concepts. Follow these steps carefully to construct a precise tattoo design description:

            1. **Extract the main subject:** Identify the key subject or idea from the description: "{$this->tattooData['prompt']}".
               - Focus on the primary subject, such as "phoenix," "dragon," or "rose," ensuring relevance to the given description.

            2. **Analyze tattoo style:** Based on the tattoo style "{$this->tattooData['tattooStyle']}", provide unique characteristics and features of this style. Use the style description: "{$this->tattooData['tattooStyle']}".
               - Highlight key design elements, stylistic traits, and how these features enhance the design.

            3. **Integrate subject and style:** Combine the main subject with the unique features of the tattoo style, considering placement "{$this->tattooData['bodyPart']}".
               - Provide detailed features for how the subject should appear in this style, taking into account the placement on the specified body part.

            4. **Describe the tattoo design structure:** Construct a tattoo design prompt that results in an image with the following structure:
               - **Left Half:** Display the tattoo design as it would appear on the specified body part "{$this->tattooData['bodyPart']}". Ensure the design aligns with the natural curvature and proportions of the body part.
               - **Right Half:** Display a 2D flat stencil version of the same tattoo design. The stencil should have clean black outlines, be suitable for tattoo transfer, and match the tattoo design exactly, omitting colors and textures.

            5. **Generate a refined prompt for image generation:** Create a single cohesive prompt summarizing the extracted subject, tattoo style features, detailed tattoo features, and the structured tattoo design prompt (both left and right halves). This refined prompt should:
               - Describe the tattoo design placement on "{$this->tattooData['bodyPart']}".
               - Include a vivid description of the subject and style.
               - Detail the structured design requirements for both left and right halves.

            ### **Output Requirement:**
            The final output **must be returned in this structured JSON format**:

            {
              "extracted_subject": "{$this->tattooData['prompt']}",
              "tattoo_style_features": "The {$this->tattooData['tattooStyle']} style is known for its unique characteristics. Highlighting {$this->tattooData['tattooStyle']} features will enhance the design.",
              "detailed_tattoo_features": "The tattoo depicts {$this->tattooData['prompt']} in a detailed and artistic way. Dynamic features and shading bring out the key aspects of the {$this->tattooData['tattooStyle']} style.",
              "tattoo_design_prompt": {
                "Left Half": "The left half of the tattoo will feature {$this->tattooData['prompt']} on the {$this->tattooData['bodyPart']}, aligning naturally with the body's curvature.",
                "Right Half": "The right half will include a 2D flat stencil, rendered in clean black outlines, omitting textures and colors, precisely matching the left half."
              },
              "refined_prompt_ai_for_image": "
                You are a tattoo design assistant specializing in creating detailed and unique tattoo concepts. Follow these steps carefully to construct a precise tattoo design description:

            1. **Design Concept:**
               Create a {$this->tattooData['tattooStyle']} tattoo for the {$this->tattooData['bodyPart']} featuring {$this->tattooData['prompt']}.
               - Incorporate dynamic shading to give the tattoo a three-dimensional appearance.
               - Align the design seamlessly with the curvature of the {$this->tattooData['bodyPart']}.

            2. **Body Integration (Left Half):**
               - The tattoo design must mold to the anatomy, like a second skin.
               - Ensure the design wraps naturally around the shape of the {$this->tattooData['bodyPart']}.
               - Highlight the flow and movement of the design for realistic integration.

            3. **Stencil Version (Right Half):**
               - The stencil must include only clean black outlines on a pure white background.
               - Exclude all shading, depth, and textures.
               - The stencil should match the tattoo design on the left exactly.

            4. **Alignment Between Halves:**
               - Ensure no discrepancies between the tattoo and stencil halves.

            5. **Technical Requirements:**
               - Use an image resolution of 1792x1024 pixels.
               - Avoid unnecessary decorative elements.

                Example for the left half:
                - "The tattoo aligns seamlessly with the {$this->tattooData['bodyPart']}, emphasizing natural flow."
                For the right half:
                - "The stencil will feature clean outlines, suitable for tattoo transfer."
            6. Generate a JSON response as described in the requirements.
            }
            EOT;

        try {
            Log::info("Sending request to OpenAI for tattoo ID: {$this->tattooData['id']}");

            $client = new \GuzzleHttp\Client();
            $response = $client->post($endpoint, [
                'headers' => [
                    'Authorization' => "Bearer {$apiKey}",
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'model' => 'gpt-4',
                    'messages' => [
                        ['role' => 'system', 'content' => 'You are a tattoo design assistant specializing in creating unique tattoo concepts.'],
                        ['role' => 'user', 'content' => $prompt],
                    ],
                    'max_tokens' => 700,
                    'temperature' => 0.7,
                ],
            ]);

            $responseBody = json_decode($response->getBody(), true);
            $responseData = $responseBody['choices'][0]['message']['content'] ?? null;

            if (!$responseData) {
                Log::warning("OpenAI returned no content for tattoo ID: {$this->tattooData['id']}");
                return;
            }
            $responseJson = json_decode($responseData, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::error("Invalid JSON in 'content' for tattoo ID: {$this->tattooData['id']}");
                return;
            }

            // Sanitize the refined_prompt_ai and open_ai_response fields
            $refinedPromptAi = isset($responseJson['refined_prompt_ai_for_image'])
                ? htmlspecialchars($responseJson['refined_prompt_ai_for_image'], ENT_QUOTES, 'UTF-8')
                : null;

            $sanitizedJson = json_encode($responseJson, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

            // Update the tattoo record
            $tattoo->update([
                'refined_prompt_ai' => $refinedPromptAi,
                'open_ai_response' => $sanitizedJson,
            ]);

            Log::info("Tattoo updated with OpenAI response for ID: {$this->tattooData['id']}");

            // Dispatch the next job if the tattoo_design_prompt exists
            if ($responseJson) {
                GenerateTattooImageJob::dispatch($tattoo->id, $responseJson);
                Log::info("Dispatched GenerateTattooImageJob for tattoo ID: {$tattoo->id}");
            } else {
                Log::warning("No tattoo design prompt generated for tattoo ID: {$this->tattooData['id']}");
            }
        } catch (\Exception $e) {
            Log::error("Error in GenerateTattooPromptJob for tattoo ID: {$this->tattooData['id']} - " . $e->getMessage());
        }
    }

}
