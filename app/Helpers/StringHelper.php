<?php

namespace App\Helpers;

use Cache;
use App\Models\Block;
use App\Models\Member;
use App\Models\DiscountCode;

class StringHelper
{
    public static function chop($string, $chars, $graceful = true)
    {
        $ret = $string;
        
        if (strlen($string) > $chars) {
            if ($graceful) {
                $pos = strpos($string, ' ', $chars);

                if ($pos !== false) {
                    $ret = substr($string, 0, $pos);
                }

                $ret .= '...';
            } else {
                $ret = substr($string, 0, $chars - 3) . '...';
            }
        }
        
        return $ret;
    }

    public static function block($id)
    {
        if (Cache::has('Block.Records')) {
            $blocks = Cache::get('Block.Records');
        } else {
            $results = Block::where('status', 1)->get();
            $blocks = [];

            foreach ($results as $result) {
                $blocks[$result->id] = $result->contents;
            }

            Cache::put('Block.Records', $blocks, 0.25);
        }

        $value = $blocks[$id];

        return $value;
    }

    public static function slug($string)
    {
        return str_slug($string);
    }

    public static function videoEmbed($url)
    {
        $video_id = '';

        if (stripos($url, 'youtube.com') !== false || stripos($url, 'youtu.be') !== false) {
            $parts = parse_url($url);

            if (isset($parts['query'])) {
                parse_str($parts['query'], $qs);
                if (isset($qs['v'])) {
                    $video_id = $qs['v'];
                } elseif (isset($qs['vi'])) {
                    $video_id = $qs['vi'];
                }
            }
            if (isset($parts['path']) && strlen($video_id) == 0) {
                $path = explode('/', trim($parts['path'], '/'));
                $video_id = $path[count($path) - 1];
            }

            if (strlen($video_id) > 0) {
                $url = 'https://www.youtube.com/embed/' . $video_id;
            }
        } elseif (stripos($url, 'vimeo.com') !== false) {
            if (preg_match('#(?:https?://)?(?:www.)?(?:player.)?vimeo.com/(?:[a-z]*/)*([0-9]{6,11})[?]?.*#', $url, $m)) {
                $video_id = $m[1];
                if (strlen($video_id) > 0) {
                    $url = 'https://player.vimeo.com/video/' . $video_id;
                }
            }
        }

        return $url;
    }

    public static function tokenReplace($serialized_tokens_array, $string)
    {
        $ret = $string;

        if (strlen($serialized_tokens_array) > 0) {
            $tas = unserialize($serialized_tokens_array);
            foreach ($tas as $k => $v) {
                $ret = str_replace('[#' . $k . '#]', $v, $ret);
            }
        }

        return $ret;
    }

    public static function generateDiscountCode()
    {
        do {
            $code = strtoupper(str_random(10));
            $cnt = DiscountCode::where('code', $code)->count();
        } while ($cnt > 0);

        return $code;
    }

    public static function renderLink($link)
    {
        return (stripos($link, 'http') === false ? 'http://' : '') . $link;
    }
}
