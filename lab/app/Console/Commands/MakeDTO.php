<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class MakeDTO extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:dto {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new DTO class';

    /**
     * Create a new DTO class.
     *
     * @return void
     */
    public function handle()
    {
        $name = $this->argument('name'); // Получаем имя класса

        // Путь к папке для DTO классов
        $path = app_path('DTO');

        // Если папка не существует, создаем ее
        if (!File::exists($path)) {
            File::makeDirectory($path, 0755, true);
        }

        // Путь для нового DTO класса
        $filePath = $path . '/' . $name . '.php';

        // Проверяем, существует ли уже файл
        if (File::exists($filePath)) {
            $this->error('DTO класс уже существует!');
            return;
        }

        // Шаблон для нового DTO класса
        $stub = "<?php\n\nnamespace App\DTO;\n\nclass {$name}\n{\n    // Свойства и методы для DTO\n}\n";

        // Создаем файл
        File::put($filePath, $stub);

        $this->info("DTO класс {$name} успешно создан!");
    }
}
