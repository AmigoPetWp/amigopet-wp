<?php
namespace AmigoPetWp\Tests;

use AmigoPetWp;
use PHPUnit\Framework\TestCase;

class AmigoPetWpTest extends TestCase
{
    private $plugin;

    protected function setUp(): void
    {
        // Mock WordPress functions that are called in the constructor if necessary
        // However, we want to test the initialization logic.
        $this->plugin = AmigoPetWp::getInstance();
    }

    public function testControllersInitialization(): void
    {
        $reflection = new \ReflectionClass(AmigoPetWp::class);

        $adminControllersProp = $reflection->getProperty('admin_controllers');
        $adminControllersProp->setAccessible(true);

        $publicControllersProp = $reflection->getProperty('public_controllers');
        $publicControllersProp->setAccessible(true);

        // Trigger initialization if it hasn't happened
        // Note: is_admin() would need to be true for admin controllers
        $initAdminMethod = $reflection->getMethod('initAdminControllers');
        $initAdminMethod->setAccessible(true);
        $initAdminMethod->invoke($this->plugin);

        $initPublicMethod = $reflection->getMethod('initPublicControllers');
        $initPublicMethod->setAccessible(true);
        $initPublicMethod->invoke($this->plugin);

        $adminControllers = $adminControllersProp->getValue($this->plugin);
        $publicControllers = $publicControllersProp->getValue($this->plugin);

        $this->assertNotEmpty($adminControllers, 'Admin controllers should be initialized');
        $this->assertNotEmpty($publicControllers, 'Public controllers should be initialized');

        // Check for specific controllers we fixed/added
        $adminControllerClasses = array_map(function ($c) {
            return get_class($c); }, $adminControllers);
        $this->assertContains('AmigoPetWp\Controllers\Admin\AdminVolunteerController', $adminControllerClasses);
        $this->assertContains('AmigoPetWp\Controllers\Admin\DashboardController', $adminControllerClasses);
    }
}
