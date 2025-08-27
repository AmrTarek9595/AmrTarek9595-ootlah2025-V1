<?php

namespace App\Helper;

class AdminHelper
{
    
    public static function repairSerializedFAQ($str)
    {
        return preg_replace_callback(
            '/s:(\d+):"(.*?)";/s',
            fn($m) => 's:' . strlen($m[2]) . ':"' . $m[2] . '";',
            $str
        );
    }

public static function deepDecodeFAQ($value)
{
    // If it's already an array, recurse
    if (is_array($value)) {
        foreach ($value as $k => $v) {
            $value[$k] = self::deepDecodeFAQ($v);
        }
        return $value;
    }

    // Try decode as JSON first (for new data)
    if (is_string($value)) {
        $json = json_decode($value, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            return self::deepDecodeFAQ($json);
        }
    }

    // If it's serialized data (old format)
    if (is_string($value) && preg_match('/^a:\d+:/', $value)) {
        $unser = @unserialize(self::repairSerializedFAQ($value));
        if ($unser !== false) {
            return self::deepDecodeFAQ($unser);
        }
    }

    // If it's base64 encoded + serialized + language tagged
    if (is_string($value) && preg_match_all('/\[(en|ar)\](.*?)\[\1\]/s', $value, $matches, PREG_SET_ORDER)) {
        $result = [];
        foreach ($matches as $m) {
            $lang    = $m[1];
            $decoded = $m[2];
            // try base64 decode
            $decodedBase64 = base64_decode($decoded, true);
            if ($decodedBase64 !== false && preg_match('/^a:\d+:/', $decodedBase64)) {
                $decoded = @unserialize(self::repairSerializedFAQ($decodedBase64));
            }
            $result[$lang] = self::deepDecodeFAQ($decoded);
        }
        return $result;
    }

    return $value;
}










    public static function repairSerializedCategiry_List($str) 
    {
        return preg_replace_callback(
            '/s:(\d+):"(.*?)";/s',
            function ($matches) {
                return 's:' . strlen($matches[2]) . ':"' . $matches[2] . '";';
            },
            $str
        );
    }
        public static function decodeLangAlt($str)
    {
        // detect [en]... [ar]...
        if (is_string($str) && preg_match_all('/\[(en|ar)\](.*?)\[\1\]/', $str, $matches, PREG_SET_ORDER)) {
            $decoded = [];
            foreach ($matches as $match) {
                $lang = $match[1];
                $val = $match[2];

                // حاول تفك base64 + unserialize
                $unserialized = @unserialize(base64_decode($val));
                if ($unserialized !== false) {
                    $decoded[$lang] = $unserialized;
                } else {
                    $decoded[$lang] = $val; // fallback
                }
            }
            return $decoded;
        }
        return $str;
    }

    public static function deepUnserializeCategiry_List($data) 
    {
        if (is_string($data) && preg_match('/^(a|O|s|i|d|b):/', $data)) {
            $data = @unserialize(self::repairSerializedCategiry_List($data));
        }

        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $data[$key] = self::deepUnserializeCategiry_List($value);
            }
        }
         elseif (is_string($data)) {
        $data = self::decodeLangAlt($data); 
        }

        return $data;
    }




    public static function decodeLangString($value) 
    {
        $result = [];
        if (preg_match_all('/\[(\w+)\]([^[]+)\[\1\]/u', $value, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $m) {
                $lang = strtolower($m[1]);
                $result[$lang] = trim($m[2]);
            }
        }
        return $result ?: $value;
    }
    public static function isValidBase64($string) 
    {
            // Quick check: length multiple of 4, only base64 chars
            if (!preg_match('/^[A-Za-z0-9+\/=]+$/', $string)) return false;
            $decoded = base64_decode($string, true);
            // Ensure decoded text is UTF-8 printable
            return $decoded !== false && mb_check_encoding($decoded, 'UTF-8');
    }

    public static function deepDecodeFooter($value) 
    {
        if (is_string($value) && preg_match('/^a:\d+:/', $value)) {
            $unser = @unserialize($value);
            if ($unser !== false) {
                return self::deepDecodeFooter($unser);
            }
        }

        if (is_string($value) && preg_match_all('/\[(\w+)\]([^[]+)\[\1\]/u', $value, $matches, PREG_SET_ORDER)) {
            $result = [];
            foreach ($matches as $m) {
                $lang = strtolower($m[1]);
                $decodedPart = $m[2];
                if (self::isValidBase64($decodedPart)) {
                    $decodedPart = base64_decode($decodedPart);
                }
                $result[$lang] = self::deepDecodeFooter($decodedPart);
            }
            return $result;
        }

        if (is_string($value) && self::isValidBase64($value)) {
            return self::deepDecodeFooter(base64_decode($value));
        }

        if (is_array($value)) {
            foreach ($value as $k => $v) {
                $value[$k] = self::deepDecodeFooter($v);
            }
            return $value;
        }

        return $value;
    }



    public static function utf8ize($mixed) {
    if (is_array($mixed)) {
        foreach ($mixed as $key => $value) {
            $mixed[$key] = self::utf8ize($value);
        }
    } elseif (is_string($mixed)) {
        return mb_convert_encoding($mixed, 'UTF-8', 'UTF-8');
    }
    return $mixed;
}

public static function wrapLang($enValue, $arValue) {
    $en = "[en]" . base64_encode(serialize([$enValue])) . "[en]";
    $ar = "[ar]" . base64_encode(serialize([$arValue])) . "[ar]";
    return $en . $ar;

}
public static function isSerialized($value)
{
    // لازم تكون سترينج
    if (!is_string($value)) {
        return false;
    }

    $value = trim($value);

    if ($value === 'N;') {
        return true; // special case for null
    }

    // لو unserialize اشتغلت من غير error يبقى serialized
    return (@unserialize($value) !== false);
}

}