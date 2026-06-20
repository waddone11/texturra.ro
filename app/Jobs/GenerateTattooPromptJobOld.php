<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Models\AiGeneratedTattoo;
use GuzzleHttp\Client;
use Log;

class GenerateTattooPromptJobOld implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tattooData;

    public function __construct(array $tattooData)
    {
        $this->tattooData = $tattooData;
    }

    public function handle(): void
    {
        Log::info("Job started for tattoo ID: {$this->tattooData['id']}");

        $tattoo = AiGeneratedTattoo::find($this->tattooData['id']);

        if (!$tattoo) {
            Log::error("Tattoo record not found for ID: {$this->tattooData['id']}");
            return;
        }

        Log::info("Tattoo record found: ", $tattoo->toArray());

        $apiKey = config('app.open_api_Key');
        $endpoint = 'https://api.openai.com/v1/chat/completions';

//        $prompt = <<<EOT
//You are a tattoo design assistant specializing in creating detailed and unique tattoo concepts. Follow these steps carefully to construct a detailed and precise tattoo design prompt:
//
//1. **Extract the main subject:** Identify the key subject or idea from the description: "{$this->tattooData['prompt']}".
//   - Focus on the primary subject, such as "phoenix," "dragon," or "rose," ensuring relevance to the given description.
//
//2. **Analyze tattoo style:** Based on the tattoo style "{$this->tattooData['tattooStyle']}", provide unique characteristics and features of this style. Use the style description: "{$this->tattooData['tattooStyle']}".
//   - Highlight key design elements, stylistic traits, and how these features enhance the design.
//
//3. **Integrate subject and style:** Combine the main subject with the unique features of the tattoo style, considering placement "{$this->tattooData['bodyPart']}".
//   - Provide detailed features for how the subject should appear in this style, taking into account the placement on the specified body part.
//
//4. **Image Structure:** Construct a tattoo design prompt that results in an image with the following structure:
//   - **Left Half:** Display the tattoo design as it would appear on the specified body part "{$this->tattooData['bodyPart']}". Ensure the design aligns with the natural curvature and proportions of the body part.
//   - **Right Half:** Display a 2D flat stencil version of the same tattoo design. The stencil should have clean black outlines, be suitable for tattoo transfer, and match the tattoo design exactly, omitting colors and textures.
//
//5. **Key Points:**
//   - Be extremely careful to ensure the stencil version matches the tattoo design on the body precisely.
//   - The stencil must accurately reflect all design elements, optimized for clarity and transfer.
//
//6. **Create a refined prompt for image generation:** Combine the extracted subject, tattoo style features, detailed tattoo features, and the structured image requirements into a single cohesive text prompt. This refined prompt should describe the tattoo design in a way suitable for generating an image with DALL-E. It must include the following:
//   - Placement on the body part.
//   - Description of the tattoo design in the chosen style.
//   - Structured details about the left and right halves.
//
//### **Output Requirement:**
//The final output **must be returned in this structured JSON format**:
//
//{
//  "extracted_subject": "...",               // Extracted main subject from the description
//  "tattoo_style_features": "...",           // Features and characteristics of the chosen tattoo style
//  "detailed_tattoo_features": "...",        // Detailed description of how the subject integrates with the style
//  "tattoo_design_prompt": "...",            // Complete tattoo design prompt, including details about "Left Half" and "Right Half"
//  "refined_prompt_ai_for_image": "..."      // Combined and cohesive prompt for image generation
//}
//
//### Example:
//For a tattoo positioned on the back:
//- `tattoo_design_prompt`:
//  {
//    "Left Half": "The tattoo design will show a phoenix with its wings spread wide, aligned with the natural curvature of the back.",
//    "Right Half": "The stencil will include clean black outlines, omitting colors and textures, and precisely match the left half."
//  }
//- `refined_prompt_ai_for_image`: "Design a hyper-realistic tattoo for the back featuring a phoenix with its wings spread wide, aligned with the back's natural curvature. The design should show vibrant, fiery feathers with dynamic shading. Split the image into two halves: the left half for the tattoo design on the back and the right half for the stencil version, with clean black outlines matching the design precisely."
//
//EOT;

//        $prompt = <<<EOT
//You are a tattoo design assistant specializing in creating detailed and unique tattoo concepts. Follow these steps carefully to construct a precise tattoo design description:
//
//1. **Extract the main subject:** Identify the key subject or idea from the description: "{$this->tattooData['prompt']}".
//   - Focus on the primary subject, such as "phoenix," "dragon," or "rose," ensuring relevance to the given description.
//
//2. **Analyze tattoo style:** Based on the tattoo style "{$this->tattooData['tattooStyle']}", provide unique characteristics and features of this style. Use the style description: "{$this->tattooData['tattooStyle']}".
//   - Highlight key design elements, stylistic traits, and how these features enhance the design.
//
//3. **Integrate subject and style:** Combine the main subject with the unique features of the tattoo style, considering placement "{$this->tattooData['bodyPart']}".
//   - Provide detailed features for how the subject should appear in this style, taking into account the placement on the specified body part.
//
//4. **Describe the tattoo design structure:** Construct a tattoo design prompt that results in an image with the following structure:
//   - **Left Half:** Display the tattoo design as it would appear on the specified body part "{$this->tattooData['bodyPart']}". Ensure the design aligns with the natural curvature and proportions of the body part.
//   - **Right Half:** Display a 2D flat stencil version of the same tattoo design. The stencil should have clean black outlines, be suitable for tattoo transfer, and match the tattoo design exactly, omitting colors and textures.
//
//5. **Generate a refined prompt for image generation:** Create a single cohesive prompt summarizing the extracted subject, tattoo style features, detailed tattoo features, and the structured tattoo design prompt (both left and right halves). This refined prompt should:
//   - Describe the tattoo design placement on "{$this->tattooData['bodyPart']}".
//   - Include a vivid description of the subject and style.
//   - Detail the structured design requirements for both left and right halves.
//
//### **Output Requirement:**
//The final output **must be returned in this structured JSON format**:
//
//{
//  "extracted_subject": "...",               // Extracted main subject from the description
//  "tattoo_style_features": "...",           // Features and characteristics of the chosen tattoo style
//  "detailed_tattoo_features": "...",        // Detailed description of how the subject integrates with the style
//  "tattoo_design_prompt": {                 // Complete tattoo design prompt, including details about "Left Half" and "Right Half"
//    "Left Half": "...",
//    "Right Half": "..."
//  },
//  "refined_prompt_ai_for_image": "..."      // Combined and cohesive prompt for image generation
//}
//
//### Example Output:
//{
//  "extracted_subject": "Phoenix",
//  "tattoo_style_features": "The Realism style is known for its highly detailed, lifelike tattoos that resemble real images. It often features portraits, animals, or nature scenes. This style is characterized by its dynamic shading, intricate details, and a depth that gives a three-dimensional appearance.",
//  "detailed_tattoo_features": "The phoenix will be depicted as a magnificent, fiery bird, rising from the ashes. Its feathers will be vibrant, with flames subtly incorporated into the design, creating a lifelike representation of a phoenix. The design will show the phoenix in the midst of flight, its wings spread wide and tail trailing behind, full of movement and energy. Dynamic shading will give the phoenix a three-dimensional appearance.",
//  "tattoo_design_prompt": {
//    "Left Half": "The left half of the design will display the phoenix tattoo on the back, with the wings of the phoenix aligning with the natural curvature of the shoulders. The design will be highly detailed with vibrant colors, dynamic shading, and intricate details.",
//    "Right Half": "The right half will present a 2D flat stencil of the phoenix tattoo. This version will be in clean black lines, precisely matching the left half design but omitting colors and textures. It will be suitable for tattoo transfer."
//  },
//  "refined_prompt_ai_for_image": "
//    You are a tattoo design assistant specializing in creating detailed and unique tattoo concepts. Follow these steps carefully to construct a precise tattoo design description:
//
//1. **Design Concept:**
//   Create a hyper-realistic tattoo for the leg featuring a phoenix rising from the ashes. The phoenix's wings should wrap around the sides of the leg, and its tail should flow naturally down toward the ankle or calf.
//   - Incorporate **vibrant fiery colors** and **dynamic shading** to give the tattoo a three-dimensional appearance.
//   - The phoenix should align seamlessly with the natural curvature of the leg.
//
//2. **Body Integration (Left Half):**
//   - The tattoo design must **mold to the leg's anatomy**, like a second skin.
//   - Ensure the design wraps around the cylindrical shape of the leg, accounting for the natural curves and muscles (e.g., thighs, calves, and shin).
//   - Wings should wrap dynamically around the sides of the leg, creating the illusion of movement and depth.
//   - The tail feathers should trail gracefully along the curve of the calf and toward the ankle.
//
//3. **Stencil Version (Right Half):**
//   - The stencil must include only **clean black outlines** on a **pure white background**.
//   - Exclude all shading, depth, and textures.
//   - The stencil should match the tattoo design on the left exactly, ensuring it is suitable for direct tattoo transfer.
//
//4. **Alignment Between Halves:**
//   - The **left half** (tattoo on the body) and the **right half** (stencil version) must correspond exactly, ensuring no discrepancies in design.
//
//5. **Technical Requirements:**
//   - Ensure the tattoo design fits naturally and molds seamlessly to the cylindrical shape of the leg.
//   - Use an image resolution of **1792x1024 pixels**.
//   - Avoid adding decorative elements outside the main tattoo design.
//
//### Example:
//For the left half:
//- "The phoenix tattoo will mold perfectly to the leg, with its wings wrapping around the sides and the tail trailing along the calf."
//For the right half:
//- "The stencil will consist of clean black outlines, omitting all colors and textures, and precisely matching the left half."
//
//  "
//}
//EOT;

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
              "
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
                    'max_tokens' => 1500,
                    'temperature' => 0.7,
                ],
            ]);

            $responseData = json_decode($response->getBody(), true)['choices'][0]['message']['content'] ?? null;

            if (!$responseData) {
                Log::warning("OpenAI returned no response for tattoo ID: {$this->tattooData['id']}");
                return;
            }

            $responseJson = json_decode($responseData, true);
            Log::info("OpenAI response for tattoo ID {$this->tattooData['id']}: ", $responseJson);
            // Update the tattoo record with the generated prompt
            $tattoo->update([
                'refined_prompt_ai' => $responseJson['refined_prompt_ai_for_image'] ?? null,
                'open_ai_response' => $responseData,
            ]);

            Log::info("Tattoo updated with refined prompt for ID: {$this->tattooData['id']}");

            // Dispatch the next job
            if (!empty($responseJson['tattoo_design_prompt'])) {
                $tattoo = AiGeneratedTattoo::find($this->tattooData['id']); // Fetch the model instance
                // Correctly pass the tattoo ID to GenerateTattooImageJob
                if ($tattoo) {
                    // Pass tattoo ID and response JSON to the job
                    GenerateTattooImageJob::dispatch($tattoo->id, $responseJson);
                    Log::info("Dispatched GenerateTattooImageJob for tattoo ID: {$tattoo->id}");
                } else {
                    Log::error("Failed to find tattoo record for ID: {$this->tattooData['id']} during image job dispatch.");
                }

            } else {
                Log::warning("No tattoo design prompt generated for tattoo ID: {$this->tattooData['id']}");
            }
        } catch (\Exception $e) {
            Log::error("Error in GenerateTattooPromptJob for tattoo ID: {$this->tattooData['id']} - " . $e->getMessage());
        }
    }

}
