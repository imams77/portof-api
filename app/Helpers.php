<?php

namespace App;

class Helpers {

    public static function slugify($text)
    {
        // replace non letter or digits by -
        $text = preg_replace('~[^\pL\d]+~u', '-', $text);

        // transliterate
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

        // remove unwanted characters
        $text = preg_replace('~[^-\w]+~', '', $text);

        // trim
        $text = trim($text, '-');

        // remove duplicate -
        $text = preg_replace('~-+~', '-', $text);

        // lowercase
        $text = strtolower($text);

        if (empty($text)) {
            return 'n-a';
        }

        return $text;
    }


    public static function generateResponse($message = null, $data = null, $status_code = null) {
        $res = (object) [
            "success"   => null,
            "fail"      => null
        ];

        $res->success = response()->json([
                "success"       => true,
                "message"       => $message ? $message : "Success.",
                "status_code"   => $status_code ? $status_code : 201,
                "data"          => $data ? $data : ""
            ]);

        $res->fail = response()->json([
                "success"       => false,
                "message"       => $message ? $message : "Failed.",
                "status_code"   => $status_code ? $status_code : 400,
                "data"          => $data ? $data : ""
            ]);
            
        return $res;
    }

    // generate 5 digit number e.g 00001
    public static function generateDigits($num) {
        return sprintf("%'.05d", $num);
    }

}