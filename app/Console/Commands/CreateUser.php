<?php

namespace App\Console\Commands;

use App\User;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CreateUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create-user';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a user';

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
     * @return mixed
     */
    public function handle()
    {
        $userData = [
            'first_name' => $this->ask('First Name'),
            'surname' => $this->ask('Surname'),
            'email' => $this->ask('Email Address'),
            'password' => $this->secret('Password'),
            'email_verified_at' => Carbon::now(),
        ];

        (new User($userData))->save();

        $this->info('User Created');
    }
}
