<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Artisan;

class CommandController extends Controller
{

    public function createStorageLink()
    {
        try {
            Artisan::call('storage:link');
            return response()->json([
                'status' => 'success',
                'message' => 'Storage link created successfully!',
                'output' => Artisan::output(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create storage link',
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function clearCache()
    {
        try {
            Artisan::call('config:clear');
            Artisan::call('cache:clear');
            Artisan::call('view:clear');
            Artisan::call('route:clear');

            return response()->json([
                'status' => 'success',
                'message' => 'Cache cleared successfully',
                'output' => Artisan::output(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to clear cache',
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function startQueueWorker($queue = null)
    {
        try {
            $command = $queue ? "queue:work --queue={$queue} --stop-when-empty" : "queue:work --stop-when-empty";
            Artisan::call($command);
            // Artisan::call('queue:work --stop-when-empty');

            return response()->json([
                'status' => 'success',
                'message' => "Queue worker executed successfully for queue: " . ($queue ?? 'default'),
                'output' => Artisan::output(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to execute queue worker',
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function generatePrompts()
    {
        try {
            // Log that the method has been triggered
            \Log::info('generatePrompts method triggered');

            // Call the Artisan command
            Artisan::call('tattoo:generate-prompts');

            // Log the output of the Artisan command
            \Log::info('Artisan command executed successfully', ['output' => Artisan::output()]);

            // Return the response
            return response()->json([
                'status' => 'success',
                'message' => 'AI prompts generation executed successfully',
                'output' => Artisan::output(),
            ]);
        } catch (\Exception $e) {
            // Log the exception message
            \Log::error('Error in generatePrompts method', ['error' => $e->getMessage()]);

            // Return the error response
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to execute AI prompts generation',
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function generateImages()
    {
        try {
            // Log that the method has been triggered
            \Log::info('generateImages method triggered');

            // Call the Artisan command
            Artisan::call('tattoo:generate-images');

            // Log the output of the Artisan command
            \Log::info('Artisan command executed successfully', ['output' => Artisan::output()]);

            // Return the response
            return response()->json([
                'status' => 'success',
                'message' => 'AI image generation executed successfully',
                'output' => Artisan::output(),
            ]);
        } catch (\Exception $e) {
            // Log the exception message
            \Log::error('Error in generateImages method', ['error' => $e->getMessage()]);

            // Return the error response
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to execute AI image generation',
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function sendBookingReminders()
    {
        try {
            Artisan::call('reminders:upcoming');
            return response()->json([
                'status' => 'success',
                'message' => 'Booking reminders sent successfully',
                'output' => Artisan::output(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to send booking reminders',
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function sendCreatedBookingReminders()
    {
        try {
            Artisan::call('reminders:created');
            return response()->json([
                'status' => 'success',
                'message' => 'Booking creation reminders sent successfully',
                'output' => Artisan::output(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to send booking creation reminders',
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function triggerQueue($queue = null)
    {
        try {
            $command = $queue ? "queue:work --queue={$queue} --stop-when-empty" : "queue:work --stop-when-empty";
            Artisan::call($command);

            return response()->json([
                'status' => 'success',
                'message' => "Queue worker executed successfully for queue: " . ($queue ?? 'default'),
                'output' => Artisan::output(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to execute queue worker',
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function populateDescriptionPlain()
    {
        try {
            Artisan::call('populate:description-plain');
            return response()->json([
                'status' => 'success',
                'message' => 'Descriptions processed successfully.',
                'output' => Artisan::output(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to process descriptions.',
                'error' => $e->getMessage(),
            ]);
        }
    }
}

