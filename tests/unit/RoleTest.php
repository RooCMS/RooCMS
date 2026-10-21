<?php declare(strict_types=1);

class RoleTest extends TestCase {

    private Role $role;


    protected function setUp(): void {
        $this->role = new Role();
    }


    public function testRoleLevelsGrowWithPrivileges(): void {
        $this->assertSame(1, $this->role->get_role_level(Role::USER));
        $this->assertSame(2, $this->role->get_role_level(Role::MODERATOR));
        $this->assertSame(3, $this->role->get_role_level(Role::ADMIN));
        $this->assertSame(4, $this->role->get_role_level(Role::SUPERUSER));
    }


    public function testUnknownRoleHasNoLevel(): void {
        $this->assertSame(0, $this->role->get_role_level('hacker'));
        $this->assertSame('Unknown role', $this->role->get_role_name('hacker'));
    }


    public function testAdminAccess(): void {
        $this->assertFalse($this->role->has_admin_access(Role::USER));
        $this->assertFalse($this->role->has_admin_access(Role::MODERATOR));
        $this->assertTrue($this->role->has_admin_access(Role::ADMIN));
        $this->assertTrue($this->role->has_admin_access(Role::SUPERUSER));
    }


    public function testModeratorAccess(): void {
        $this->assertFalse($this->role->has_moderator_access(Role::USER));
        $this->assertTrue($this->role->has_moderator_access(Role::MODERATOR));
        $this->assertTrue($this->role->has_moderator_access(Role::ADMIN));
        $this->assertTrue($this->role->has_moderator_access(Role::SUPERUSER));
    }


    public function testOnlySuperuserIsSuperuser(): void {
        $this->assertTrue($this->role->is_superuser(Role::SUPERUSER));
        $this->assertFalse($this->role->is_superuser(Role::ADMIN));
    }


    public function testRoleValidation(): void {
        $this->assertTrue($this->role->is_valid_role('a'));
        $this->assertFalse($this->role->is_valid_role('admin'));
        $this->assertFalse($this->role->is_valid_role(''));
    }


    public function testDefaultRoleIsUser(): void {
        $this->assertSame(Role::USER, $this->role->get_default_role());
    }


    public function testCanAccessRoleIsInclusive(): void {
        // can_access_role() uses ">=": equal levels are allowed.
        // Moderation (ban/unban) needs a STRICT comparison and compares levels directly.
        $this->assertTrue($this->role->can_access_role(Role::ADMIN, Role::MODERATOR));
        $this->assertTrue($this->role->can_access_role(Role::ADMIN, Role::ADMIN));
        $this->assertFalse($this->role->can_access_role(Role::MODERATOR, Role::ADMIN));
    }


    public function testRolesListContainsAllRoles(): void {
        $list = $this->role->get_roles_list();

        $this->assertSame(['u', 'm', 'a', 'su'], array_keys($list));
        $this->assertSame(4, $list['su']['level']);
    }
}
