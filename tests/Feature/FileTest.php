<?php

namespace Tests\Feature;

//use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Symfony\Component\HttpFoundation\Response;

class FileTest extends TestCase
{
    //use RefreshDatabase;

    public function test_file_upload()
    {
        $this->withoutExceptionHandling();

        $fileData = [
            'file' => UploadedFile::fake()->create('file.pdf', 50)
        ];
        $headers = $this->getUserAuthHeaders();
        $headers['Content-type'] = "multipart/form-data";
        $response = $this->post(self::FILE_ENDPOINT . "upload", $fileData, $headers);

        //$response =$this->call('POST', self::FILE_ENDPOINT . "upload", [], [], $fileData, $this->getUserAuthHeaders());
        $response->assertStatus(Response::HTTP_OK);
    }
}
