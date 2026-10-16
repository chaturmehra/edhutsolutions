<?php

return [

    /*
    | demo  = the Google button signs in the seeded demo student
    | live  = Laravel Socialite redirects to Google OAuth
    */
    'google_login_mode' => env('GOOGLE_LOGIN_MODE', 'demo'),

    'demo_google_email' => env('DEMO_GOOGLE_EMAIL', 'demo@edhut.test'),

    /*
    | Catalog prices are entered in INR. Visitors outside the home country see every price
    | converted to the foreign currency: INR amount x rate, plus a flat markup per item.
    */
    'pricing' => [
        'home_country' => 'IN',
        'foreign_currency' => 'USD',
        'inr_to_foreign_rate' => (float) env('PRICING_INR_TO_USD_RATE', 0.0105),
        'foreign_markup' => (float) env('PRICING_USD_MARKUP', 5),
    ],

    /*
    | Visitor country: a trusted proxy header first, then a lookup URL with {ip}. The lookup must
    | return JSON with country, country_code or countryCode. Only set GEOIP_COUNTRY_HEADER (e.g.
    | CF-IPCountry) when every request passes through that proxy, otherwise visitors can send the
    | header themselves. Leave GEOIP_LOOKUP_URL empty to turn the lookup off.
    */
    'geoip' => [
        'header' => env('GEOIP_COUNTRY_HEADER', ''),
        'lookup_url' => env('GEOIP_LOOKUP_URL', 'https://api.country.is/{ip}'),
        'timeout' => 2,
        'default_country' => env('GEOIP_DEFAULT_COUNTRY', 'IN'),
    ],

    'google_reviews_url' => 'https://www.google.com/search?q=edhutsolutions&oq=edhut+&gs_lcrp=EgZjaHJvbWUqBggBEEUYOzIGCAAQRRg5MgYIARBFGDsyBggCEAAYHjIGCAMQRRhBMgYIBBBFGDwyBggFEEUYPDIGCAYQRRg8MgYIBxBFGDzSAQgyODgxajBqNKgCALACAQ&sourceid=chrome&ie=UTF-8&sei=lAmZZ_mYOKmY4-EP_JKYgA#lrd=0x390ce39089d46f45:0x8d7526b303412a60,1',

];
