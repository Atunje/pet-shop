<?php

namespace Tests\Feature;

//use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\File;
use Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Symfony\Component\HttpFoundation\Response;

class FileTest extends TestCase
{
    //use RefreshDatabase;

    public function test_user_can_upload_file()
    {
        $fileData = [
            'file' => UploadedFile::fake()->create('file.pdf', 50)
        ];
        $headers = $this->getUserAuthHeaders();
        $headers['Content-type'] = "multipart/form-data";
        $response = $this->post(self::FILE_ENDPOINT . "upload", $fileData, $headers);
        $response->assertStatus(Response::HTTP_OK);
    }

    public function test_admin_can_upload_file()
    {
        $fileData = [
            'file' => UploadedFile::fake()->create('file.pdf', 50)
        ];
        $headers = $this->getAdminAuthHeaders();
        $headers['Content-type'] = "multipart/form-data";
        $response = $this->post(self::FILE_ENDPOINT . "upload", $fileData, $headers);
        $response->assertStatus(Response::HTTP_OK);
    }


    public function test_guest_cannot_upload_file()
    {
        $fileData = [
            'file' => UploadedFile::fake()->create('file.pdf', 50)
        ];
        $headers = [];
        $headers['Content-type'] = "multipart/form-data";
        $response = $this->post(self::FILE_ENDPOINT . "upload", $fileData, $headers);
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }


    public function test_anybody_can_view_file()
    {
        $file = File::factory()->create();

        $response = $this->get(self::FILE_ENDPOINT . $file->uuid);
        $response->assertStatus(Response::HTTP_OK);
    }
}
