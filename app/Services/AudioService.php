<?php
namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;

class AudioService
{
    public function processAudio(UploadedFile $file, $postId)
    {
        $path = $this->storeAudio($file, $postId);
        $absolutePath = Storage::disk('local')->path($path);

        if ($this->getAudioBitrate($absolutePath) > 128) {
            return $this->compressAudio($absolutePath, $path);
        }

        return $path;
    }

    private function storeAudio(UploadedFile $file, $postId)
    {
        $filename = $postId . '_' . time() . '.mp3';
        return $file->storeAs('audios', $filename, 'local');
    }

        private function getAudioBitrate($path)
    {
        $command = "ffprobe -v error -select_streams a:0 -show_entries stream=bit_rate -of default=noprint_wrappers=1:nokey=1 " . escapeshellarg($path);
        
        $bitrate = shell_exec($command);

        
        return (float)($bitrate / 1000);
    }
    private function compressAudio($absolutePath, $relativePath)
    {
        
        $compressedPath = 'compressed/' . basename($relativePath);

        $command = "ffmpeg -i " . escapeshellarg($absolutePath) . " -acodec libmp3lame -ab 128k -ac 1 " . escapeshellarg(Storage::disk('local')->path($compressedPath));

        $output = shell_exec($command);

        if (strpos($output, 'Error') !== false) {
            throw new \Exception('Error compressing audio: ' . $output);
        }

        Storage::delete($relativePath);

        return $compressedPath;
    }
}