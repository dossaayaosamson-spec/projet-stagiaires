<?php

namespace App\Console\Commands;

use App\Models\Author;
use App\Models\Book;
use App\Models\Event;
use App\Models\ShortStory;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class OptimizeImages extends Command
{
    protected $signature = 'images:optimize';

    protected $description = 'Convert existing PNG/JPG images to WebP and update database records';

    public function handle()
    {
        $manager = new ImageManager(new Driver);
        $this->info('Starting image optimization...');

        // 1. Books
        $this->optimizeModelImages(Book::class, 'cover_path', 'books/covers', $manager);

        // 2. Short Stories
        $this->optimizeModelImages(ShortStory::class, 'cover_path', 'stories/covers', $manager);

        // 3. Authors
        $this->optimizeModelImages(Author::class, 'avatar', 'authors/avatars', $manager);

        // 4. Events
        $this->optimizeModelImages(Event::class, 'image_path', 'events', $manager);

        // 5. Hero Images (Fixed files in public)
        $this->optimizePublicImages('images/hero', $manager);

        $this->info('Image optimization completed!');
    }

    private function optimizePublicImages($directory, $manager)
    {
        $this->info("Processing public directory: $directory...");
        $fullPath = public_path($directory);
        if (! file_exists($fullPath)) {
            return;
        }

        $files = scandir($fullPath);
        foreach ($files as $file) {
            if (str_ends_with(strtolower($file), '.png') || str_ends_with(strtolower($file), '.jpg') || str_ends_with(strtolower($file), '.jpeg')) {
                $oldPath = $fullPath.'/'.$file;
                $newPath = $fullPath.'/'.pathinfo($file, PATHINFO_FILENAME).'.webp';

                try {
                    $image = $manager->decode(file_get_contents($oldPath));
                    $image->save($newPath, quality: 80);
                    $this->line("Converted public image: $file -> .webp");
                    // Optionally delete the old one? Lighthouse suggests it.
                    // unlink($oldPath);
                } catch (\Exception $e) {
                    $this->error("Failed to convert public image $file: ".$e->getMessage());
                }
            }
        }
    }

    private function optimizeModelImages($modelClass, $column, $directory, $manager)
    {
        $items = $modelClass::whereNotNull($column)->get();
        $this->info('Processing '.class_basename($modelClass).'...');

        foreach ($items as $item) {
            $oldPath = $item->$column;

            // Skip if already webp
            if (str_ends_with(strtolower($oldPath), '.webp')) {
                continue;
            }

            if (! Storage::disk('public')->exists($oldPath)) {
                $this->warn("File not found: $oldPath");

                continue;
            }

            $oldFullPath = storage_path('app/public/'.$oldPath);
            $newFilename = pathinfo($oldPath, PATHINFO_FILENAME).'.webp';
            $newRelativePath = $directory.'/'.$newFilename;
            $newFullPath = storage_path('app/public/'.$newRelativePath);

            // Ensure directory exists
            $dir = dirname($newFullPath);
            if (! file_exists($dir)) {
                mkdir($dir, 0755, true);
            }

            try {
                $image = $manager->decode(file_get_contents($oldFullPath));

                // For book covers, ensure standard ratio if needed (optional but good)
                if ($modelClass === Book::class || $modelClass === ShortStory::class) {
                    $image->cover(400, 600);
                }

                $image->save($newFullPath, quality: 80);

                // Update DB
                $item->$column = $newRelativePath;
                $item->save();

                // Delete old file
                Storage::disk('public')->delete($oldPath);

                $this->line("Converted: $oldPath -> $newRelativePath");
            } catch (\Exception $e) {
                $this->error("Failed to convert $oldPath: ".$e->getMessage());
            }
        }
    }
}
