<?php

namespace Brackets\AdminGenerator\Tests\Feature\Views;

use Brackets\AdminGenerator\Tests\TestCase;
use File;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class FullFormTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    function view_full_form_should_get_auto_generated(){
        $formPath = resource_path('views/admin/category/form.blade.php');
        $formJsPath = resource_path('assets/js/admin/category/Form.js');

        $this->assertFileNotExists($formPath);
        $this->assertFileNotExists($formJsPath);

        $this->artisan('admin:generate:full-form', [
            'table_name' => 'categories'
        ]);

        $this->assertFileExists($formPath);
        $this->assertFileExists($formJsPath);
        $this->assertStringStartsWith('@extends(\'brackets/admin::admin.layout.form\')', File::get($formPath));
        $this->assertStringStartsWith('var base = require(\'../components/Form/Form\');

Vue.component(\'category-form\', {
    mixins: [base]
});', File::get($formJsPath));
    }

    /** @test */
    function you_can_pass_your_own_file_path(){
        $formPath = resource_path('views/admin/profile/edit-password.blade.php');
        $formJsPath = resource_path('assets/js/admin/profile-edit-password/Form.js');

        $this->assertFileNotExists($formPath);
        $this->assertFileNotExists($formJsPath);

        $this->artisan('admin:generate:full-form', [
            'table_name' => 'categories',
            '--file-name' => 'profile/edit-password'
        ]);

        $this->assertFileExists($formPath);
        $this->assertFileExists($formJsPath);
        $this->assertStringStartsWith('@extends(\'brackets/admin::admin.layout.form\')', File::get($formPath));
        $this->assertContains(':action="\'{{ route(\'admin/profile/edit-password\', [\'category\' => $category]) }}\'"', File::get($formPath));
        $this->assertStringStartsWith('var base = require(\'../components/Form/Form\');

Vue.component(\'profile-edit-password-form\', {
    mixins: [base]
});', File::get($formJsPath));
    }

}