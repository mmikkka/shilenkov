<?php


namespace App\Console;

use Illuminate\Console\Command;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        // Здесь регистрируем кастомные команды
        \App\Console\Commands\MakeDTO::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        // Загружаем все команды из папки `routes/console.php`
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
