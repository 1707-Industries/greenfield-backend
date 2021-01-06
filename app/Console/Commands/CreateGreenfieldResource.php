<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

class CreateGreenfieldResource extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'greenfield:resource {resource}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create Greenfield Resource';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $resource = $this->argument('resource');
        $resourcePlural = Str::plural($resource);

        // Create Model & Migration
        $this->call('make:model', [
            'name' => $resource,
            '-m' => true,
        ]);

        // Create Nova Resource
        $this->call('nova:resource', [
            'name' => $resource,
        ]);

        // Create Controller
        $this->call('make:controller', [
            'name' => sprintf('%sController', $resourcePlural),
            '--api' => true,
        ]);

        // Create API Resource
        $this->call('make:resource', [
            'name' => sprintf('%sResource', $resourcePlural),
        ]);

        // Create Requests
        $requests = [
            'Create',
            'Update',
            'Store',
            'Show',
            'Index'
        ];

        foreach ($requests as $request) {
            $this->call('make:request', [
                'name' => sprintf('%s%sRequest', $resource, $request),
            ]);
        }

        return 0;
    }
}
