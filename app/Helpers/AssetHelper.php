<?php

namespace App\Helpers;

use Storage;
use Image;
use File;

class AssetHelper
{
	public static function asset($asset, $absolute = false)
	{
		$asset_modified = 0;
		if (file_exists(public_path() . '/' . $asset)) {
			$asset_modified = filemtime(public_path() . '/' . $asset);
		}
		
		$cache_buster = '?_m=' . $asset_modified;
		
		if ($absolute) {
			return asset($asset . $cache_buster);
		} else {
			return '/' . $asset . $cache_buster;
		}
	}

	public static function file($folder, $id, $filename, $sub = '', $absolute = false)
	{
		if ($id == 0 || strlen($filename) == 0) {
			return '';
		}

		// Better idea: save paths in Redis cache, if exists in cache, it exists, so no need to check filesysem which might be slower
		$path = $folder . '/' . UploadHelper::idToFilepath($id) . '/' . (strlen($sub) > 0 ? $sub . '/' : '') . $filename;

		// Only generate thumbnails for images where a sub-folder was specified
		if (strlen($sub) > 0 && !Storage::exists($path)) {
			$original_image = Storage::get(str_replace('/' . $sub . '/', '/', $path));
			$sizes = explode('x', $sub);
			$image_data = Image::make($original_image)->orientate();

			if ($sizes[0] > 0 && $sizes[1] > 0) {
				$image_data->fit($sizes[0], $sizes[1]);
			} else if ($sizes[0] > 0 && $sizes[1] == 0) {
				$image_data->resize($sizes[0], null, function ($constraint) {
					$constraint->aspectRatio();
					$constraint->upsize();
				});
			} else if ($sizes[0] == 0 && $sizes[1] > 0) {
				$image_data->resize(null, $sizes[1], function ($constraint) {
					$constraint->aspectRatio();
					$constraint->upsize();
				});
			}

			$image_data->encode(File::extension($path));

			Storage::put($path, $image_data->__toString(), 'public');
		}

		if (config('filesystems.default') == 's3') {
			return 'https://' . config('filesystems.disks.s3.bucket') . '.s3-' . config('filesystems.disks.s3.region') . '.amazonaws.com/' . $path;
		} else {
			if (!$absolute) {
				return '/files/' . $path;
			} else {
				return asset('/files/' . $path);
			}
		}
	}
}
