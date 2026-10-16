<?php

/**
 * One-off converter: database/data/pdu_user.sql → database/data/pdu_users.csv
 * Columns match the admin CSV import: name,email,phone,gender,city,country_code
 */

$root = dirname(__DIR__, 2);
require $root.'/vendor/autoload.php';
$app = require $root.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$sqlPath = $root.'/database/data/pdu_user.sql';
$csvPath = $root.'/database/data/pdu_users.csv';

if (! is_file($sqlPath)) {
    fwrite(STDERR, "Missing {$sqlPath}\n");
    exit(1);
}

$sql = file_get_contents($sqlPath);
preg_match_all('/\(([^()]*(?:\([^()]*\)[^()]*)*)\)/', $sql, $matches);

$countries = App\Models\Country::query()->get(['code', 'name']);
$byName = [];
foreach ($countries as $country) {
    $byName[normalizeCountry($country->name)] = $country->code;
}

$aliases = [
    'united states of america' => 'US',
    'united states' => 'US',
    'usa' => 'US',
    'u.s.a.' => 'US',
    'uk' => 'GB',
    'united kingdom' => 'GB',
    'united arab erimates' => 'AE',
    'united arab emirates' => 'AE',
    'uae' => 'AE',
    'nambia' => 'NA',
    'namibia' => 'NA',
    'cote divoire' => 'CI',
    "cote d'ivoire" => 'CI',
    'cote dlvoire' => 'CI',
    'saudi arabia' => 'SA',
    'ksa' => 'SA',
];

$rows = [];
$seen = [];
$skipped = 0;

foreach ($matches[1] as $raw) {
    $fields = splitSqlFields($raw);
    if (count($fields) < 14 || ! ctype_digit($fields[0])) {
        continue;
    }

    $name = trim($fields[1]);
    $email = strtolower(trim($fields[2]));
    $phone = preg_replace('/\s+/', '', trim($fields[3]));
    $gender = normalizeGender($fields[5]);
    $city = trim($fields[6]);
    $countryCode = resolveCountry($fields[7], $byName, $aliases);

    if ($email === '' || ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $skipped++;

        continue;
    }

    if (isset($seen[$email])) {
        $skipped++;

        continue;
    }

    $seen[$email] = true;
    $rows[] = [$name, $email, $phone, $gender, $city, $countryCode];
}

$out = fopen($csvPath, 'w');
fwrite($out, "\xEF\xBB\xBF");
fputcsv($out, ['name', 'email', 'phone', 'gender', 'city', 'country_code']);
foreach ($rows as $row) {
    fputcsv($out, $row);
}
fclose($out);

echo 'Wrote '.count($rows).' users to '.$csvPath.' (skipped '.$skipped.")\n";

function splitSqlFields(string $raw): array
{
    $fields = [];
    $current = '';
    $inString = false;
    $len = strlen($raw);

    for ($i = 0; $i < $len; $i++) {
        $ch = $raw[$i];
        if ($inString) {
            if ($ch === "'" && ($i + 1) < $len && $raw[$i + 1] === "'") {
                $current .= "'";
                $i++;

                continue;
            }
            if ($ch === '\\' && ($i + 1) < $len) {
                $current .= $raw[++$i];

                continue;
            }
            if ($ch === "'") {
                $inString = false;

                continue;
            }
            $current .= $ch;

            continue;
        }

        if ($ch === "'") {
            $inString = true;

            continue;
        }
        if ($ch === ',') {
            $fields[] = $current === 'NULL' ? '' : $current;
            $current = '';

            continue;
        }
        $current .= $ch;
    }

    $fields[] = $current === 'NULL' ? '' : $current;

    return $fields;
}

function normalizeGender(string $value): string
{
    $value = strtolower(trim($value));

    return match ($value) {
        'male', 'm' => 'male',
        'female', 'f' => 'female',
        'other' => 'other',
        default => '',
    };
}

function normalizeCountry(string $value): string
{
    $value = mb_strtolower(trim($value));
    $value = str_replace(["\u{2019}", '’', '`'], "'", $value);
    $value = preg_replace('/[^a-z0-9\s\']+/u', ' ', $value) ?? $value;

    return preg_replace('/\s+/', ' ', trim($value)) ?? '';
}

function resolveCountry(string $name, array $byName, array $aliases): string
{
    $key = normalizeCountry($name);
    if ($key === '') {
        return '';
    }

    return $aliases[$key] ?? $byName[$key] ?? '';
}
