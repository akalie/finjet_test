<?php

namespace Tests\functional\Controllers;

use Tests\functional\TestCase;


class UsersControllerTest extends TestCase
{
    public function testAuthWorks()
    {
        $r = $this->post('/auth', [
            'login' => self::TEST_USER,
            'pass' => self::TEST_PASS,
        ]);

        $this->assertObjectHasAttribute('token', $r->result);

        $r = $this->get('/items', ['token' => 'blabla']);
        $this->assertEquals('Please provide token', $r->errors->token, 'does not work with incorrect token');
    }

    public function testAuthDoesNotWork()
    {
        $r = $this->post('/auth', [
            'login' => self::TEST_USER,
            'pass' => 'blabla',
        ]);

        $this->assertObjectHasAttribute('auth', $r->errors);
    }
}