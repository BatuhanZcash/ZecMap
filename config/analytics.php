<?php

return [

  /*
  |--------------------------------------------------------------------------
  | Yerel / özel IP'ler için varsayılan ülke
  |--------------------------------------------------------------------------
  |
  | localhost (127.0.0.1) gibi özel IP'lerde header veya GeoIP sonucu olmaz.
  | Geliştirme ortamında TR gibi bir kod vererek test edebilirsiniz.
  |
  */
  'private_ip_country' => env('ANALYTICS_PRIVATE_IP_COUNTRY', env('APP_ENV') === 'local' ? 'TR' : null),

  /*
  |--------------------------------------------------------------------------
  | MaxMind GeoLite2 Country veritabanı yolu
  |--------------------------------------------------------------------------
  |
  | Ücretsiz indirme: https://dev.maxmind.com/geoip/geolite2-free-geolocation-data
  | Dosyayı storage/app/geoip/GeoLite2-Country.mmdb olarak kaydedin.
  |
  */
  'geoip_database' => storage_path('app/geoip/GeoLite2-Country.mmdb'),

];
