<?php

namespace Tests\functional\Controllers;

use Ramsey\Uuid\Uuid;
use Tests\functional\TestCase;

class ItemsControllerTest extends TestCase
{
    public function testItemsAPIWorks()
    {
        $params = ['token' => $this->auth()];
        $r = $this->get('/items', $params);

        $this->assertIsArray($r->result);

        $testName = 'blabla' . date('Y-m-d H:i:s');
        $r = $this->post('/items/create', $params + ['name' => $testName]);
        $this->assertTrue($r->result->success);
        $this->assertEquals($testName, $r->result->created->name);
        $testItemId = $r->result->created->id;

        $items = $this->get('/items', $params)->result;
        $this->assertContains($testName, array_column($items, 'name'));

        $newTestName = $testName . 'AAA';
        $r = $this->post('/items/update', $params + ['id' => $testItemId, 'name' => $newTestName]);
        $this->assertTrue($r->result->success);

        $items = $this->get('/items', $params)->result;
        $this->assertContains($newTestName, array_column($items, 'name'));
        $this->assertNotContains($testName, array_column($items, 'name'));

        $r = $this->post('/items/delete', $params + ['id' => $testItemId]);
        $this->assertTrue($r->result->success);

        $items = $this->get('/items', $params)->result;
        $this->assertNotContains($testName, array_column($items, 'name'));
    }

    /**
     * @depends testItemsAPIWorks
     */
    public function testItemsWithCategoriesWorks()
    {
        $params = ['token' => $this->auth()];
        $testCategoryName = 'testItemsWithCategoriesC' . date('Y-m-d H:i:s');
        $r = $this->post('/categories/create', $params + ['name' => $testCategoryName]);
        $this->assertTrue($r->result->success);
        $category = $r->result->created;

        $testName = 'testItemsWithCategoriesI' . date('Y-m-d H:i:s');
        $r = $this->post('/items/create', $params + ['name' => $testName, 'categories' => [$testCategoryName]]);
        $this->assertTrue($r->result->success);

        $r = $this->get('/items/' . urlencode($testCategoryName), $params);

        $this->assertCount(1, $r->result);
        $item = $r->result[0];
        $this->assertContains($testName, array_column($r->result, 'name'));

        $r = $this->post('/categories/delete', $params + ['id' => $category->id]);
        $this->assertTrue($r->result->success);

        $r = $this->post('/items/delete', $params + ['id' => $item->id]);
        $this->assertTrue($r->result->success);
    }

    public function testItemsAPIDoesNotWorkWithWrongToken()
    {
        $r = $this->get('/items', ['token' => 'blabla']);
        $this->assertObjectHasAttribute('token', $r->errors);

        $r = $this->get('/items', ['token' => Uuid::uuid4()]);
        $this->assertObjectHasAttribute('token', $r->errors);
    }
}