<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Models\AiGeneratedTattoo;
use Intervention\Image\Facades\Image;

class SplitTattooImageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tattooId;
    public $imagePath;

    /**
     * Create a new job instance.
     *
     * @param int $tattooId
     * @param string $imagePath
     */
    public function __construct(int $tattooId, string $imagePath)
    {
        $this->tattooId = $tattooId;
        $this->imagePath = $imagePath;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        Log::info("Starting SplitTattooImageJob for tattoo ID: {$this->tattooId}");

        $tattoo = AiGeneratedTattoo::find($this->tattooId);

        if (!$tattoo) {
            Log::error("Tattoo record not found for ID: {$this->tattooId}");
            return;
        }

        // Load the original image
        $imagePath = storage_path("app/public/{$this->imagePath}");
        if (!file_exists($imagePath)) {
            Log::error("Image not found at path: {$imagePath}");
            return;
        }

        $image = Image::make($imagePath);
        $width = $image->width();
        $height = $image->height();

        // Ensure the image dimensions match expectations
        if ($width !== 1792 || $height !== 1024) {
            Log::error("Image dimensions do not match expected 1792x1024. Skipping split.");
            return;
        }

        // Split the image
        $leftHalf = $image->crop(896, 1024, 0, 0); // Left half
        $rightHalf = $image->crop(896, 1024, 896, 0); // Right half

        // Save the left half
        $leftFilename = "left_" . time() . "_" . uniqid() . ".jpg";
        $leftPath = "images/ai_tattoos/{$leftFilename}";
        Storage::disk('public')->put($leftPath, (string)$leftHalf->encode('jpg'));

        // Save the right half
        $rightFilename = "right_" . time() . "_" . uniqid() . ".jpg";
        $rightPath = "images/ai_tattoos/{$rightFilename}";
        Storage::disk('public')->put($rightPath, (string)$rightHalf->encode('jpg'));

        // Update the tattoo record
        $tattoo->update([
            'generated_tattoo' => $leftPath,
            'generated_stencil' => $rightPath,
        ]);

        Log::info("Successfully split and updated tattoo record for ID: {$this->tattooId}");
    }
}
