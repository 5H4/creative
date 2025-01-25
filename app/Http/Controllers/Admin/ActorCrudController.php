<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\ActorRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Illuminate\Http\Request;

use FFMpeg;

/**
 * Class ActorCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class ActorCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     * 
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\Actor::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/actor');
        CRUD::setEntityNameStrings('actor', 'actors');
    }

    /**
     * Define what happens when the List operation is loaded.
     * 
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        CRUD::column('name')->label('Actor Name');

        // Add the thumbnail column to the table
        CRUD::addColumn([
            'name' => 'thumbnail',
            'type' => 'image',
            'label' => 'Video Thumbnail',
            'height' => '30px', // Optional: Set the height of the thumbnail
            'width' => '25px',  // Optional: Set the width of the thumbnail
            'disk' => 'public', // Use the public disk to store the images
        ]);
    }

    /**
     * Define what happens when the Create operation is loaded.
     * 
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation()
    {
        CRUD::setValidation(ActorRequest::class);
        CRUD::setFromDb(); // set fields from db columns.

        /**
         * Fields can be defined using the fluent syntax:
         * - CRUD::field('price')->type('number');
         */

        CRUD::field('name')->label('Actor Name');

        CRUD::addField([
            'name' => 'video',
            'type' => 'upload',
            'label' => 'Actor Video',
            'upload' => true,
            'disk' => 'public', // Store in the public disk
            'path' => 'videos/actors',
        ]);
    }
    /**
     * Define what happens when the Update operation is loaded.
     * 
     * @see https://backpackforlaravel.com/docs/crud-operation-update
     * @return void
     */
    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }

    public function setupEditOperation()
    {
        $this->setupCreateOperation();

        // Add a custom field to display the video thumbnail
        CRUD::addField([
            'name' => 'video',
            'type' => 'upload',
            'label' => 'Actor Video',
            'upload' => true,
            'disk' => 'public',
            'path' => 'videos/actors',
        ]);

        // Display thumbnail (if available)
        $actor = $this->crud->getEntry($this->crud->getCurrentEntryId());

        if ($actor->thumbnail) {
            CRUD::addField([
                'name' => 'thumbnail_preview',
                'type' => 'custom_html',
                'value' => '<img src="' . asset('storage/' . $actor->thumbnail) . '" height="100" />',
            ]);
        }
    }

    protected function setupShowOperation()
    {
        // Display the "Name" field
        CRUD::column('name')->label('Actor Name');

        // Display the "Video" field
        CRUD::column('video')->label('Video Path');

        // Display the "Created At" field
        CRUD::column('created_at')->label('Created')->type('datetime');

        // Display the "Updated At" field
        CRUD::column('updated_at')->label('Updated')->type('datetime');

        // Display the "Thumbnail" field as an image
        CRUD::addColumn([
            'name' => 'thumbnail',
            'type' => 'image',
            'label' => 'Video Thumbnail',
            'height' => '150px', // Optional: Set height of the thumbnail
            'width' => '150px',  // Optional: Set width of the thumbnail
            'disk' => 'public',  // Specify the disk where the thumbnail is stored
        ]);
    }


    public function generateThumbnail($videoPath)
    {
        // Detect OS type to configure paths
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            // Windows path
            $ffmpegPath = 'C:\\Users\\ramot\\AppData\\Local\\Microsoft\\WinGet\\Packages\\Gyan.FFmpeg.Essentials_Microsoft.Winget.Source_8wekyb3d8bbwe\\ffmpeg-7.1-essentials_build\\bin\\ffmpeg.exe';
            $ffprobePath = 'C:\\Users\\ramot\\AppData\\Local\\Microsoft\\WinGet\\Packages\\Gyan.FFmpeg.Essentials_Microsoft.Winget.Source_8wekyb3d8bbwe\\ffmpeg-7.1-essentials_build\\bin\\ffprobe.exe';
        } else {
            // Linux path
            $ffmpegPath = '/usr/bin/ffmpeg';  // Or your FFmpeg install location
            $ffprobePath = '/usr/bin/ffprobe';  // Or your FFProbe install location
        }

        // Ensure FFmpeg and FFProbe paths are correct
        if (!file_exists($ffmpegPath) || !file_exists($ffprobePath)) {
            throw new \Exception("FFmpeg or FFProbe not found. Please check installation paths.");
        }

        // Initialize FFMpeg with the correct paths
        $ffmpeg = FFMpeg\FFMpeg::create([
            'ffmpeg.binaries' => $ffmpegPath,
            'ffprobe.binaries' => $ffprobePath,
        ]);


        // Check if the video file exists
        if (!file_exists($videoPath)) {
            throw new \Exception("Video file not found: " . $videoPath);
        }

        // Define the thumbnail path (make sure to avoid duplicating the public path)
        $thumbnailPath = public_path('storage/thumbnails/' . basename($videoPath) . '.jpg'); // Correct path for thumbnail

        // Generate the thumbnail at 1 second
        $video = $ffmpeg->open($videoPath);
        $video->frame(FFMpeg\Coordinate\TimeCode::fromSeconds(1))
            ->save($thumbnailPath);

        return $thumbnailPath;
    }
}
