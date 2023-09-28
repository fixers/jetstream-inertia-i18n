<?php

namespace Fixers\JetstreamI18n\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use function Laravel\Prompts\multiselect;
use function Laravel\Prompts\info;
use function Laravel\Prompts\note;
use function Laravel\Prompts\confirm;
use function Laravel\Prompts\warning;

class PublishComponents extends Command
{
    protected $signature = 'fixers:publish-components';

    protected $description = 'Publish Jetstream Vue components for translation';

    public function handle(): void
    {
        $this->updateNodePackages(function ($packages) {
            return [
                    'vue-i18n' => '^9.4.1',
                ] + $packages;
        });


        warning("This might make irreversible changes to your code. Make sure you have a backup of your code before proceeding.");

        info("This command will update your app.js file and publish the Jetstream Vue components for translation, possibly overwriting existing edits.");
        note('This command is best suited to be run on a fresh Laravel installation with a newly installed Jetstream with the Inertia stack.');

        if (!confirm(
            label: "Do you want to continue?",
            default: false)) {
            $this->info('Publishing cancelled.');
            return;
        }

        $mapped = [];
        foreach(glob(__DIR__ . '/../../../stubs/lang/*.json') as $lang) {
            $mapped[$lang] = str_replace('.json', '', basename($lang));
        }

        $languages = multiselect(
            label: 'Select languages to publish',
            options: $mapped,
            required: true
        );

        $importLang = '';
        $importMap = '';

        (new Filesystem)->ensureDirectoryExists(base_path('lang'));
        foreach($languages as $language) {
            $languageIdent = str_replace('.json', '', basename($language));
            $languageAbbr = str_replace('.json', '', basename($language)) . 'Lang';

            $importLang .= 'import ' . $languageAbbr . ' from \'../../lang/' . $languageIdent . '.json\';' . PHP_EOL;
            $importMap .= '    \'' . $languageIdent . '\': ' . $languageAbbr . ',' . PHP_EOL;

            copy($language, base_path('lang/' . $languageIdent . '.json'));
        }

        // Update app.js only if i18n isn't already present in the app.js file
        if (strpos(file_get_contents(base_path('resources/js/app.js')), 'import { createI18n } from \'vue-i18n\';') === false) {

            $this->updateNodePackages(function ($packages) {
                return [
                        'vue-i18n' => '^9.4.1',
                    ] + $packages;
            });

            $this->replaceInFile(
                'import { ZiggyVue } from \'../../vendor/tightenco/ziggy/dist/vue.m\';',
                'import { ZiggyVue } from \'../../vendor/tightenco/ziggy/dist/vue.m\';' . PHP_EOL . 'import { createI18n } from \'vue-i18n\';',
                base_path('resources/js/app.js')
            );

            $this->replaceInFile(
                '.use(ZiggyVue)',
                '.use(ZiggyVue)' . PHP_EOL . str_repeat(' ', 12) . '.use(i18n)',
                base_path('resources/js/app.js')
            );


            $i18nstring = str_replace(['{{ lang_import }}', '{{ lang_map}}'], [$importLang, $importMap], file_get_contents(__DIR__ . '/../../../stubs/i18n.js'));


            $this->replaceInFile(
                'const appName = import.meta.env.VITE_APP_NAME || \'Laravel\';',
                'const appName = import.meta.env.VITE_APP_NAME || \'Laravel\';' . PHP_EOL . PHP_EOL . $i18nstring,
                base_path('resources/js/app.js')
            );
        }

        $this->call('vendor:publish', [
            '--tag' => 'fixers-jetstream-i18n',
            '--force' => true,
        ]);

        $this->info('Jetstream Vue components published for translation.');
    }

    protected function replaceInFile($search, $replace, $path)
    {
        file_put_contents($path, str_replace($search, $replace, file_get_contents($path)));
    }


    protected static function updateNodePackages(callable $callback, $dev = true)
    {
        if (!file_exists(base_path('package.json'))) {
            return;
        }

        $configurationKey = $dev ? 'devDependencies' : 'dependencies';

        $packages = json_decode(file_get_contents(base_path('package.json')), true);

        $packages[$configurationKey] = $callback(
            array_key_exists($configurationKey, $packages) ? $packages[$configurationKey] : [],
            $configurationKey
        );

        ksort($packages[$configurationKey]);

        file_put_contents(
            base_path('package.json'),
            json_encode($packages, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) . PHP_EOL
        );
    }

}
