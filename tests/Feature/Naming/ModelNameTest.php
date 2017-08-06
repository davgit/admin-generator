<?php

namespace Brackets\AdminGenerator\Tests\Feature\Naming;

use Artisan;
use Brackets\AdminGenerator\Generate\Model;
use Brackets\AdminGenerator\Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ModelNameTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    function testing_correct_name_for_standard_naming(){
        $filePath = 'App/Models/Category.php';

        $this->assertFileNotExists(base_path($filePath));

        $this->artisan('admin:generate:model', [
            'table_name' => 'categories'
        ]);

        $this->assertFileExists(base_path($filePath));
    }

    /** @test */
    function testing_correct_name_for_namespaced_naming(){
        $filePath = 'App/Models/Billing/Category.php';

        $this->assertFileNotExists(base_path($filePath));

        $this->artisan('admin:generate:model', [
            'table_name' => 'categories',
            'class_name' => 'Billing\\Category',
        ]);

        $this->assertFileExists(base_path($filePath));
    }

    /** @test */
    function testing_correct_name_for_name_outside_default_folder(){
        $filePath = 'App/Billing/Category.php';

        $this->assertFileNotExists(base_path($filePath));

        $this->artisan('admin:generate:model', [
            'table_name' => 'categories',
            'class_name' => 'App\\Billing\\Category',
        ]);

        $this->assertFileExists(base_path($filePath));
    }

}
