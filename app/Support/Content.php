<?php

namespace App\Support;

use App\Services\Settings\SiteSettings;
use RuntimeException;

/**
 * Static website copy kept in resources/content/{page}.json.
 * Content::get('corporate.programs.items') reads "programs.items" from corporate.json.
 * Any "{setting_key}" inside a string is replaced with that admin site setting.
 */
class Content
{
    /** @var array<string, array<mixed>> */
    private static array $files = [];

    public static function get(string $key, mixed $default = null): mixed
    {
        [$file, $path] = array_pad(explode('.', $key, 2), 2, null);
        $data = self::file($file);

        return self::fill($path === null ? $data : data_get($data, $path, $default));
    }

    public static function flush(): void
    {
        self::$files = [];
    }

    /**
     * @return array<mixed>
     */
    private static function file(string $name): array
    {
        if (! array_key_exists($name, self::$files)) {
            $path = resource_path('content/'.$name.'.json');
            if (! is_file($path)) {
                throw new RuntimeException("Content file resources/content/{$name}.json does not exist.");
            }

            $data = json_decode((string) file_get_contents($path), true);
            if (! is_array($data)) {
                throw new RuntimeException("Content file resources/content/{$name}.json is not valid JSON: ".json_last_error_msg());
            }

            self::$files[$name] = $data;
        }

        return self::$files[$name];
    }

    private static function fill(mixed $value): mixed
    {
        if (is_array($value)) {
            return array_map(fn ($item) => self::fill($item), $value);
        }

        if (! is_string($value) || ! str_contains($value, '{')) {
            return $value;
        }

        $settings = app(SiteSettings::class);

        return preg_replace_callback('/\{([a-z0-9_]+)\}/', fn ($match) => $settings->get($match[1], $match[0]), $value);
    }
}
