<?php
/**
 * Unit Tests for Welcome Controller
 * 
 * @package App\Tests\Controllers
 */

namespace App\Tests\Controllers;

use PHPUnit\Framework\TestCase;

class WelcomeTest extends TestCase
{
    /**
     * Test that the Welcome class exists
     */
    public function testWelcomeClassExists()
    {
        $this->assertTrue(
            class_exists('App\\Controllers\\Welcome'),
            'Welcome class should exist'
        );
    }

    /**
     * Test that Welcome extends CI_Controller
     */
    public function testWelcomeExtendsCIController()
    {
        $reflection = new \ReflectionClass('App\\Controllers\\Welcome');
        $parent = $reflection->getParentClass();
        
        $this->assertTrue(
            $parent !== false && $parent->getName() === 'CI_Controller',
            'Welcome should extend CI_Controller'
        );
    }

    /**
     * Test that index method exists
     */
    public function testIndexMethodExists()
    {
        $this->assertTrue(
            method_exists('App\\Controllers\\Welcome', 'index'),
            'Welcome class should have index method'
        );
    }

    /**
     * Test that switch_language method exists
     */
    public function testSwitchLanguageMethodExists()
    {
        $this->assertTrue(
            method_exists('App\\Controllers\\Welcome', 'switch_language'),
            'Welcome class should have switch_language method'
        );
    }

    /**
     * Test that switch_language method accepts one parameter
     */
    public function testSwitchLanguageMethodParameter()
    {
        $reflection = new \ReflectionMethod('App\\Controllers\\Welcome', 'switch_language');
        $parameters = $reflection->getParameters();
        
        $this->assertCount(1, $parameters, 'switch_language should accept one parameter');
        $this->assertEquals('lang', $parameters[0]->getName(), 'Parameter should be named "lang"');
    }

    /**
     * Test constructor exists
     */
    public function testConstructorExists()
    {
        $this->assertTrue(
            method_exists('App\\Controllers\\Welcome', '__construct'),
            'Welcome class should have a constructor'
        );
    }

    /**
     * Test that the controller uses namespace App\Controllers
     */
    public function testWelcomeNamespace()
    {
        $reflection = new \ReflectionClass('App\\Controllers\\Welcome');
        $this->assertEquals(
            'App\\Controllers',
            $reflection->getNamespaceName(),
            'Welcome should be in App\\Controllers namespace'
        );
    }

    /**
     * Test that index method is public
     */
    public function testIndexMethodIsPublic()
    {
        $reflection = new \ReflectionMethod('App\\Controllers\\Welcome', 'index');
        $this->assertTrue(
            $reflection->isPublic(),
            'index method should be public'
        );
    }

    /**
     * Test that switch_language method is public
     */
    public function testSwitchLanguageMethodIsPublic()
    {
        $reflection = new \ReflectionMethod('App\\Controllers\\Welcome', 'switch_language');
        $this->assertTrue(
            $reflection->isPublic(),
            'switch_language method should be public'
        );
    }

    /**
     * Test that constructor is public
     */
    public function testConstructorIsPublic()
    {
        $reflection = new \ReflectionMethod('App\\Controllers\\Welcome', '__construct');
        $this->assertTrue(
            $reflection->isPublic(),
            'constructor should be public'
        );
    }
}
