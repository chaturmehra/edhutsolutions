<?php

namespace App\Services\Pricing;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Throwable;

class VisitorCountry
{
    private const SESSION_KEY = 'visitor_country';

    private const UNKNOWN = ['XX', 'T1', 'ZZ'];

    /**
     * ISO 3166-1 alpha-2 code of the country the current request comes from.
     */
    public function code(): string
    {
        $request = request();
        $code = $request->attributes->get(self::SESSION_KEY);
        if (! is_string($code)) {
            $code = $this->resolve($request);
            $request->attributes->set(self::SESSION_KEY, $code);
        }

        return $code;
    }

    private function resolve(Request $request): string
    {
        $headerName = (string) config('edhut.geoip.header');
        $header = $headerName !== '' ? $this->normalize($request->header($headerName)) : null;
        if ($header) {
            return $header;
        }

        $session = $request->hasSession() ? $request->session() : null;
        $ip = (string) $request->ip();
        $remembered = $session?->get(self::SESSION_KEY);
        if (is_array($remembered) && ($remembered['ip'] ?? null) === $ip && ($code = $this->normalize($remembered['code'] ?? null))) {
            return $code;
        }

        $code = $this->lookup($ip);
        if (! $code) {
            return $this->normalize(config('edhut.geoip.default_country')) ?? 'IN';
        }

        $session?->put(self::SESSION_KEY, ['ip' => $ip, 'code' => $code]);

        return $code;
    }

    private function lookup(string $ip): ?string
    {
        $url = (string) config('edhut.geoip.lookup_url');
        $public = filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE);
        if ($url === '' || ! $public) {
            return null;
        }

        $key = 'geoip:'.sha1($ip);
        $cached = Cache::get($key);
        if (is_string($cached)) {
            return $cached !== '' ? $cached : null;
        }

        try {
            $data = Http::timeout((int) config('edhut.geoip.timeout', 2))
                ->acceptJson()
                ->get(str_replace('{ip}', rawurlencode($ip), $url))
                ->throw()
                ->json();
            $code = is_array($data) ? $this->normalize($data['country'] ?? $data['country_code'] ?? $data['countryCode'] ?? null) : null;
        } catch (Throwable) {
            $code = null;
        }

        Cache::put($key, $code ?? '', $code ? now()->addDay() : now()->addMinutes(10));

        return $code;
    }

    private function normalize(mixed $value): ?string
    {
        $code = is_string($value) ? strtoupper(trim($value)) : '';

        return preg_match('/^[A-Z]{2}$/', $code) && ! in_array($code, self::UNKNOWN, true) ? $code : null;
    }
}
