<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CreateService extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:service {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $name = $this->argument('name');
        $path = app()->path('Services');
        $content = $this->buildClassContent($name);
        if (file_exists(app()->path('Services').'/'.$name.'.php')) {
            $this->error("$name class already exist");
        } else if (file_put_contents($path . '/' . $name . '.php', $content) === false) {
            $this->error("Failed to create class: $name");
        } else {
            $this->info("Class created successfully: $path/$name.php");
        }
    }


    /**
     * Create class body
     *
     * @param string $name
     * @return string
     */
    private function buildClassContent(string $name): string
    {
        // Build the basic class structure
        $content = "<?php\n";
        $content .= "namespace App\Services;\n";
        $content .= "class $name {}\n";

        return $content;
    }
}
