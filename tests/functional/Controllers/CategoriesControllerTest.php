<?php

namespace Tests\functional\Controllers;

use Ramsey\Uuid\Uuid;
use Tests\functional\TestCase;

class CategoriesControllerTest extends TestCase
{
    public function testItemsAPIWorks()
    {
        $params = ['token' => $this->auth()];
        $r = $this->get('/categories', $params);

        $this->assertIsArray($r->result);

        $testName = 'blabla' . date('Y-m-d H:i:s');
        $r = $this->post('/categories/create', $params + ['name' => $testName]);
        $this->assertTrue($r->result->success);
        $this->assertEquals($testName, $r->result->created->name);
        $testCategoryId = $r->result->created->id;

        $categories = $this->get('/categories', $params)->result;
        $this->assertContains($testName, array_column($categories, 'name'));

        $r = $this->post('/categories/delete', $params + ['id' => $testCategoryId]);
        $this->assertTrue($r->result->success);

        $categories = $this->get('/categories', $params)->result;
        $this->assertNotContains($testName, array_column($categories, 'name'));
    }

    public function testcategoriesAPIDoesNotWorkWithWrongToken()
    {
        $r = $this->get('/categories', ['token' => 'blabla']);
        $this->assertObjectHasAttribute('token', $r->errors);

        $r = $this->get('/categories', ['token' => Uuid::uuid4()]);
        $this->assertObjectHasAttribute('token', $r->errors);
    }
}