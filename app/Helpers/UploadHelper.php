<?php

namespace App\Helpers;

use Storage;

class UploadHelper
{
	public static function upload($fieldname, $request, $folder, $id)
	{
		$filename = '';

		if ($request->hasFile($fieldname) && $request->file($fieldname)->isValid()) {
			$file = $request->file($fieldname);
			$filename = UploadHelper::cleanFileName($file->getClientOriginalName());
			Storage::put($folder . '/' . UploadHelper::idToFilepath($id) . '/' . $filename, file_get_contents($file->getRealPath()), 'public');
		}

		return $filename;
	}

	public static function download($url, $folder, $id)
	{
		$filename = '';

		if (strlen($url) > 0) {
			$filename = UploadHelper::cleanFileName(basename(explode('?', $url)[0]));
			Storage::put($folder . '/' . UploadHelper::idToFilepath($id) . '/' . $filename, file_get_contents($url), 'public');
		}

		return $filename;
	}

	public static function stream($folder, $id, $filename)
	{
		$file = $folder . '/' . UploadHelper::idToFilepath($id) . '/' . $filename;
		$stream = Storage::readStream($file);

		return response()->stream(function() use ($stream) {
		    fpassthru($stream);
		}, 200, [
		    "Content-Type" => Storage::getMimetype($file),
		    "Content-Length" => Storage::getSize($file),
		    "Content-disposition" => "attachment; filename=\"" . basename($file) . "\"",
		]);
	}

    public static function getFileContents($fieldname, $request)
    {
        $fcontents = '';

        if ($request->hasFile($fieldname) && $request->file($fieldname)->isValid()) {
            $file = $request->file($fieldname);
            $fcontents = file_get_contents($file->getRealPath());
        }

        return $fcontents;
    }

	public static function idToFilepath($id)
	{
		if (is_numeric($id)) {
			return implode('/', str_split(str_pad($id, 9, '0', STR_PAD_LEFT), 3));
		}
		return $id;
	}

	public static function cleanFileName($filename)
	{
		$filename_new = time() . '_' . str_random(5) . '_' . $filename;
		
		// Remove illegal characters from file names
        $filename_new = utf8_encode(strtr(utf8_decode($filename_new), utf8_decode('ŠŽšžŸÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÑÒÓÔÕÖØÙÚÛÜÝàáâãäåçèéêëìíîïñòóôõöøùúûüýÿ'), 'SZszYAAAAAACEEEEIIIINOOOOOOUUUUYaaaaaaceeeeiiiinoooooouuuuyy'));
		$filename_new = strtr($filename_new, ['Þ' => 'TH', 'þ' => 'th', 'Ð' => 'DH', 'ð' => 'dh', 'ß' => 'ss', 'Œ' => 'OE', 'œ' => 'oe', 'Æ' => 'AE', 'æ' => 'ae', 'µ' => 'u']);
		$filename_new = preg_replace(['/\s/', '/\.[\.]+/', '/[^\w_\.\-]/'], ['-', '.', ''], $filename_new);
		
		// Rename scripts with dangerous extensions
		if (substr($filename_new, -4) != '.txt' && preg_match('/\.(php|pl|py|cgi|asp|js)$/i', $filename_new)) {
			$filename_new = $filename_new . '.txt';
		}
		
		return $filename_new;
	}

	public static function deleteDirectory($folder, $id)
	{
		Storage::deleteDirectory($folder . '/' . UploadHelper::idToFilepath($id));
	}

	public static function moveFile($old, $new)
	{
		Storage::move($old, $new);
	}

    public static function copyFile($old, $new)
    {
        Storage::copy($old, $new);
    }
}
