<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;
   public function testRegisterAdminSuccess()
   {
       $this->withHeader('Authorization', '123')->
       post("/api/users/register-admin", [
           "email" => "admin@example.com",
           "username" => "admin",
           "password" => "123"
       ])->assertStatus(201)
           ->assertJson([
               "success" => true,
               "data"=> [
                   "message" => "admin registered successfully"
               ]
           ]);
   }

   public function testRegisterAdminFailed()
   {
       $this->testRegisterAdminSuccess();
       $this->withHeader('Authorization', '123')->
       post("/api/users/register-admin", [
           "email" => "admin@example.com",
           "username" => "admin",
           "password" => "123"
       ])->assertStatus(400)
           ->assertJson([
               "success" => false,
               "data"=> [
                   "message" => "username already registered"
               ]
           ]);
   }

    public function testRegisterEditorSuccess()
    {
        $this->withHeader('Authorization', '123')->
        post("/api/users/register-editor", [
            "email" => "editor@example.com",
            "username" => "editor",
            "password" => "123"
        ])->assertStatus(201)
            ->assertJson([
                "success" => true,
                "data"=> [
                    "message" => "editor registered successfully"
                ]
            ]);
    }

    public function testRegisterEditorFailed()
    {
        $this->testRegisterEditorSuccess();
        $this->withHeader('Authorization', '123')->
        post("/api/users/register-editor", [
            "email" => "editor@example.com",
            "username" => "editor",
            "password" => "123"
        ])->assertStatus(400)
            ->assertJson([
                "success" => false,
                "data"=> [
                    "message" => "username already registered"
                ]
            ]);
    }

    public function testRegisterSuccess()
    {
        $this->withHeader('Authorization', '123')->
        post("/api/users/register", [
            "email" => "user@example.com",
            "username" => "user",
            "password" => "123"
        ])->assertStatus(201)
            ->assertJson([
                "success" => true,
                "data" => [
                    "user" => [
                        "id" => 1,
                        "role" => "Reader",
                    ]
                ]
            ]);
    }

    public function testRegisterFailed()
    {
        $this->testRegisterSuccess();
        $this->withHeader('Authorization', '123')->
        post("/api/users/register", [
            "email" => "user@example.com",
            "username" => "user",
            "password" => "123"
        ])->assertStatus(400)
            ->assertJson([
                "success" => false,
                "data"=> [
                    "message" => "username already registered"
                ]
            ]);
    }

    public function testLoginAdminEditorSuccess()
    {
        $this->testRegisterAdminSuccess();
        $this->testRegisterEditorSuccess();

        $this->withHeader('Authorization', '123')->
        post("/api/users/login-admin-editor", [
            "username" => "admin",
            "password" => "123"
        ])->assertStatus(200)
            ->assertJson([
                "success" => true,
                "data" => [
                    "user" => [
                        "id" => 1,
                        "role" => "Admin",
                    ]
                ]
            ]);

        $this->withHeader('Authorization', '123')->
        post("/api/users/login-admin-editor", [
            "username" => "editor",
            "password" => "123"
        ])->assertStatus(200)
            ->assertJson([
                "success" => true,
                "data" => [
                    "user" => [
                        "id" => 2,
                        "role" => "Editor",
                    ]
                ]
            ]);
    }

    public function testLoginAdminEditorUserNotFound()
    {
        $this->withHeader('Authorization', '123')->
        post("/api/users/login-admin-editor", [
            "username" => "admin",
            "password" => "123"
        ])->assertStatus(401)
            ->assertJson([
                "success" => false,
                "message" => "user not found"
            ]);

        $this->withHeader('Authorization', '123')->
        post("/api/users/login-admin-editor", [
            "username" => "editor",
            "password" => "123"
        ])->assertStatus(401)
            ->assertJson([
                "success" => false,
                "message" => "user not found"
            ]);
    }

    public function testLoginAdminEditorNotAuthorized()
    {
        $this->testRegisterSuccess();

        $this->withHeader('Authorization', '123')->
        post("/api/users/login-admin-editor", [
            "username" => "user",
            "password" => "123"
        ])->assertStatus(401)
            ->assertJson([
                "success" => false,
                "message" => "access denied: role not authorized"
            ]);
    }

    public function testLoginAdminEditorPwWrong()
    {
        $this->testRegisterAdminSuccess();

        $this->withHeader('Authorization', '123')->
        post("/api/users/login-admin-editor", [
            "username" => "admin",
            "password" => "1234"
        ])->assertStatus(401)
            ->assertJson([
                "success" => false,
                "message" => "password wrong"
            ]);
    }

    public function testLoginAdminEditorAlreadyLogin()
    {
        $this->testLoginAdminEditorSuccess();

        $this->withHeader('Authorization', '123')->
        post("/api/users/login-admin-editor", [
            "username" => "admin",
            "password" => "123"
        ])->assertStatus(403)
            ->assertJson([
                "success" => false,
            ]);
    }
}
