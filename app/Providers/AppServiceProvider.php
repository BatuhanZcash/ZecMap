<?php

namespace App\Providers;

use App\Support\TranslationHelper;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Translation\Translator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->loadNestedJsonTranslations();

        View::composer(['layouts.*', 'layouts.partials.*'], function ($view): void {
            if (request()->is('admin', 'admin/*')) {
                return;
            }

            $view->with('currentLocale', app()->getLocale());
            $view->with('availableLocales', config('locales.supported', []));
        });
    }

    private function loadNestedJsonTranslations(): void
    {
        $this->app->booted(function (): void {
            /** @var Translator $translator */
            $translator = $this->app->make('translator');

            foreach (array_keys(config('locales.supported', [])) as $locale) {
                $path = lang_path("{$locale}.json");

                if (! is_file($path)) {
                    continue;
                }

                $decoded = json_decode((string) file_get_contents($path), true);

                if (! is_array($decoded)) {
                    continue;
                }

                $translator->addLines(TranslationHelper::flatten($decoded), $locale);
            }
        });
    }
}
