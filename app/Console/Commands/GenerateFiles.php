<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class GenerateFiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:files';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create relevant admin files.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(Filesystem $files)
    {
        parent::__construct();

        $this->files = $files;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $password = $this->ask('Password?');

        if ($password != 'xxx21') {
            $this->error('Wrong password!');
            return;
        }

        $in_class = $this->ask('What is the class name?');
        $in_table = $this->ask('What is the table name? Also used for view folder name, route name and files storage.');
        $in_image = $this->ask('Image folder name? Type x for N/A.');
        $in_permission = $this->ask('Permission string? E.g. content/pages');
        $in_main_cat = $this->ask('Breadcrumbs - main item? E.g. Content.');
        $in_sub_cat = $this->ask('Breadcrumbs - sub item? E.g. Pages.');

        $paths = [
            'controller' => base_path() . '/app/Http/Controllers/Admin/',
            'service' => base_path() . '/app/Services/Admin/',
            'model' => base_path() . '/app/Models/',
            'request' => base_path() . '/app/Http/Requests/Admin/',
            'views' => base_path() . '/resources/views/admin/'
        ];

        $has_error = false;
        $error_list = '';

        if ($this->files->exists($paths['controller'] . $in_class . 'Controller.php')) {
            $has_error = true;
            $error_list .= 'Controller exists! ';
        }

        if ($this->files->exists($paths['service'] . $in_class . 'Service.php')) {
            $has_error = true;
            $error_list .= 'Service exists! ';
        }

        if ($this->files->exists($paths['model'] . $in_class . '.php')) {
            $has_error = true;
            $error_list .= 'Model exists! ';
        }

        if ($this->files->exists($paths['request'] . $in_class . 'Request.php')) {
            $has_error = true;
            $error_list .= 'Request exists! ';
        }

        if ($this->files->exists($paths['views'] . $in_table . '/index.blade.php')) {
            $has_error = true;
            $error_list .= 'Views exists! ';
        }

        if ($has_error) {
            $this->error($error_list);
            return;
        }

        // Check files don't already exist for all files to be generated

        $in_table_dashes = str_replace('_', '-', $in_table);

        $replaces = [
            ['{{class}}', '{{permission}}', '{{bc_main}}', '{{bc_sub}}', '{{table}}', '{{img_folder}}', '{{table-dashes}}'], 
            [$in_class, $in_permission, $in_main_cat, $in_sub_cat, $in_table, $in_image, $in_table_dashes]
        ];

        $stub = $this->files->get(base_path() . '/resources/command-stubs/controller.stub');
        $controller = str_replace($replaces[0], $replaces[1], $stub);

        $stub = $this->files->get(base_path() . '/resources/command-stubs/service.stub');
        $service = str_replace($replaces[0], $replaces[1], $stub);

        $stub = $this->files->get(base_path() . '/resources/command-stubs/model.stub');
        $model = str_replace($replaces[0], $replaces[1], $stub);

        $stub = $this->files->get(base_path() . '/resources/command-stubs/request.stub');
        $request = str_replace($replaces[0], $replaces[1], $stub);

        $stub = $this->files->get(base_path() . '/resources/command-stubs/view-index.stub');
        $view_index = str_replace($replaces[0], $replaces[1], $stub);

        $stub = $this->files->get(base_path() . '/resources/command-stubs/view-manage.stub');
        $view_manage = str_replace($replaces[0], $replaces[1], $stub);

        // Controller
        $this->files->put($paths['controller'] . '/' . $in_class . 'Controller.php', $controller);

        // Service
        $this->files->put($paths['service'] . '/' . $in_class . 'Service.php', $service);

        // Model
        $this->files->put($paths['model'] . '/' . $in_class . '.php', $model);

        // Request
        $this->files->put($paths['request'] . '/' . $in_class . 'Request.php', $request);

        // Views
        $this->files->makeDirectory($paths['views'] . '/' . $in_table_dashes);

        $this->files->put($paths['views'] . '/' . $in_table_dashes . '/index.blade.php', $view_index);
        $this->files->put($paths['views'] . '/' . $in_table_dashes . '/manage.blade.php', $view_manage);
        
        // Migration
        $this->call('make:migration', [
            'name' => 'create_' . $in_table . '_table', '--create' => $in_table
        ]);

        // Seeder
        $this->call('make:seeder', [
            'name' => $in_class . 'TableSeeder'
        ]);

        $this->info("Done! Add the following entry to routes in the '" . $in_main_cat . "' section: '" . $in_table_dashes . "' => '" . $in_class . "' and add the seeder to DatabaseSeeder.");
    }
}
