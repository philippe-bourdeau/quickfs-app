<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\TransformsRequest;
use Illuminate\Support\Str;

class TweakTickerMiddleware extends TransformsRequest
{
    /**
     * Transform the given value.
     *
     * @param string $key
     * @param mixed $value
     * @return mixed
     */
    protected function transform($key, $value)
    {
        $value = Str::upper($value);

        if (Str::endsWith($value, 'MM')) {
            $value = Str::replaceLast('MM', 'MX', $value);
        } elseif (Str::endsWith($value,'LN')) {
            $value = Str::replaceLast('LN', 'UK', $value);
        }

        return $value;
    }
}
