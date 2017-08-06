<?php namespace Brackets\AdminGenerator\Generate;

use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputOption;

class Model extends ClassGenerator {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'admin:generate:model';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate a model class';

    /**
     * Path for view
     *
     * @var string
     */
    protected $view = 'model';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function fire()
    {
        //TODO check if exists
        //TODO make global for all generator
        //TODO also with prefix
        if(!empty($template = $this->option('template'))) {
            $this->view = 'templates.'.$template.'.model';
        }

        if(!empty($belongsToMany = $this->option('belongs_to_many'))) {
            $this->setBelongToManyRelation($belongsToMany);
        }

        $this->generateClass();

        // TODO think if we should use ide-helper:models ?

        $this->info('Generating '.$this->modelBaseName.' finished');

    }

    protected function buildClass() {

        return view('brackets/admin-generator::'.$this->view, [
            'modelBaseName' => $this->modelBaseName,
            'modelNameSpace' => $this->modelNamespace,

            'dates' => $this->readColumnsFromTable($this->tableName)->filter(function($column) {
                return $column['type'] == "datetime" || $column['type'] == "date";
            })->pluck('name'),
            'fillable' => $this->readColumnsFromTable($this->tableName)->filter(function($column) {
                return !in_array($column['name'], ['id', 'created_at', 'updated_at', 'deleted_at', 'remember_token']);
            })->pluck('name'),
            'hidden' => $this->readColumnsFromTable($this->tableName)->filter(function($column) {
                return in_array($column['name'], ['password', 'remember_token']);
            })->pluck('name'),
            'timestamps' => $this->readColumnsFromTable($this->tableName)->filter(function($column) {
                return in_array($column['name'], ['created_at', 'updated_at']);
            })->count() > 0,
            'hasSoftDelete' => $this->readColumnsFromTable($this->tableName)->filter(function($column) {
                return $column['name'] == "deleted_at";
            })->count() > 0,
            'tableName' => (!empty($this->option('model')) && $this->option('model') !== Str::studly(Str::singular($this->tableName))) ? $this->tableName : null,
            'relations' => $this->relations,
        ])->render();
    }

    protected function getOptions() {
        return [
            ['template', 't', InputOption::VALUE_OPTIONAL, 'Specify custom template'],
            ['belongs_to_many', 'btm', InputOption::VALUE_OPTIONAL, 'Specify belongs to many relations'],
        ];
    }

    protected function generateClassNameFromTable($tableName) {
        return Str::studly(Str::singular($tableName));
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace.'\Models';
    }
}