<?php namespace App\Commands;

use Bkoetsier\BaseConsole\Foundation\Composer;
use Illuminate\Console\Command;
use Symfony\Component\Finder\Finder;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Console\AppNamespaceDetectorTrait;
use Symfony\Component\Console\Input\InputArgument;

class AppNameCommand extends Command {

    use AppNamespaceDetectorTrait;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'app:name';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Set the application namespace";

    /**
     * The Composer class instance.
     *
     * @var \Bkoetsier\BaseConsole\Foundation\Composer
     */
    protected $composer;

    /**
     * The filesystem instance.
     *
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $files;

    /**
     * Current root application namespace.
     *
     * @var string
     */
    protected $currentRoot;

    /**
     * Create a new key generator command.
     * @param  \Bkoetsier\BaseConsole\Foundation\Composer  $composer
     * @param  \Illuminate\Filesystem\Filesystem  $files
     */
    public function __construct(Composer $composer, Filesystem $files)
    {
        parent::__construct();

        $this->files = $files;
        $this->composer = $composer;
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function fire()
    {
        $this->currentRoot = trim($this->getAppNamespace(), '\\');
        $this->setAppDirectoryNamespace();
        $this->setComposerNamespace();
        $this->info('Application namespace set!');
        $this->composer->dumpAutoloads();
    }

    /**
     * Set the namespace on the files in the app directory.
     *
     * @return void
     */
    protected function setAppDirectoryNamespace()
    {
        $files = Finder::create()
            ->in($this->laravel['path'])
            ->name('*.php');

        foreach ($files as $file)
        {
            $this->replaceNamespace($file->getRealPath());
        }
    }

    /**
     * Replace the App namespace at the given path.
     *
     * @param  string  $path
     */
    protected function replaceNamespace($path)
    {
        $search = [
            'namespace '.$this->currentRoot.';',
            $this->currentRoot.'\\',
        ];

        $replace = [
            'namespace '.$this->argument('name').';',
            $this->argument('name').'\\',
        ];

        $this->replaceIn($path, $search, $replace);
    }

    /**
     * Set the PSR-4 namespace in the Composer file.
     *
     * @return void
     */
    protected function setComposerNamespace()
    {
        $this->replaceIn(
            $this->getComposerPath(), $this->currentRoot.'\\\\', str_replace('\\', '\\\\', $this->argument('name')).'\\\\'
        );
    }



    /**
     * Replace the given string in the given file.
     *
     * @param  string  $path
     * @param  string|array  $search
     * @param  string|array  $replace
     * @return void
     */
    protected function replaceIn($path, $search, $replace)
    {
        $this->files->put($path, str_replace($search, $replace, $this->files->get($path)));
    }


    /**
     * Get the path to the Composer.json file.
     *
     * @return string
     */
    protected function getComposerPath()
    {
        return $this->laravel->basePath().'/composer.json';
    }


    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return array(
            array('name', InputArgument::REQUIRED, 'The desired namespace.'),
        );
    }

}