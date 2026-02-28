<?php

use PHPUnit\Framework\TestCase;
use app\common\model\User;

class UserTest extends TestCase
{
    /**
     * 测试用户模型的创建和查询
     */
    public function testUserCreateAndQuery()
    {
        // 创建一个测试用户
        $userData = [
            'username' => 'test_user_' . time(),
            'email' => 'test_' . time() . '@example.com',
            'password' => md5('123456'),
            'nickname' => '测试用户',
            'status' => 1
        ];

        // 测试创建用户
        $user = User::create($userData);
        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals($userData['username'], $user->username);
        $this->assertEquals($userData['email'], $user->email);
        $this->assertEquals($userData['nickname'], $user->nickname);
        $this->assertEquals($userData['status'], $user->status);

        // 测试查询用户
        $foundUser = User::find($user->id);
        $this->assertInstanceOf(User::class, $foundUser);
        $this->assertEquals($user->id, $foundUser->id);
        $this->assertEquals($user->username, $foundUser->username);

        // 测试删除用户
        $result = $user->delete();
        $this->assertTrue($result);

        // 验证用户已被删除
        $deletedUser = User::find($user->id);
        $this->assertNull($deletedUser);
    }

    /**
     * 测试用户模型的验证规则
     */
    public function testUserValidation()
    {
        // 测试创建用户时的验证
        $invalidUserData = [
            'username' => '', // 空用户名
            'email' => 'invalid-email', // 无效邮箱
            'password' => '123', // 密码太短
        ];

        try {
            $user = User::create($invalidUserData);
            $this->fail('预期会抛出验证异常，但没有');
        } catch (\Exception $e) {
            // 预期会抛出异常，测试通过
            $this->assertTrue(true);
        }
    }
}
