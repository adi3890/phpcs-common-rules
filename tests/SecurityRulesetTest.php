<?php
/**
 * Tests for Security Ruleset
 */

use PHPUnit\Framework\TestCase;

class SecurityRulesetTest extends TestCase
{
    private $tempDir;
    
    protected function setUp(): void
    {
        $this->tempDir = sys_get_temp_dir() . '/phpcs-test-' . uniqid();
        mkdir($this->tempDir);
    }
    
    protected function tearDown(): void
    {
        $this->removeDirectory($this->tempDir);
    }
    
    private function removeDirectory($dir)
    {
        if (is_dir($dir)) {
            $files = array_diff(scandir($dir), ['.', '..']);
            foreach ($files as $file) {
                $path = $dir . '/' . $file;
                is_dir($path) ? $this->removeDirectory($path) : unlink($path);
            }
            rmdir($dir);
        }
    }
    
    public function testDangerousFunctionDetection()
    {
        $testFile = $this->tempDir . '/test.php';
        file_put_contents($testFile, '<?php eval($_GET["code"]); ?>');
        
        $output = shell_exec("phpcs --standard=CommonSecurity {$testFile} 2>&1");
        
        $this->assertStringContainsString('eval', $output);
        $this->assertStringContainsString('remote code execution', strtolower($output));
    }
    
    public function testSafeFunctionAllowed()
    {
        $testFile = $this->tempDir . '/safe.php';
        file_put_contents($testFile, '<?php echo "Hello World"; ?>');
        
        $output = shell_exec("phpcs --standard=CommonSecurity {$testFile} 2>&1");
        
        // Should not contain any errors for safe code
        $this->assertStringNotContainsString('ERROR', $output);
    }
    
    public function testFlexibleRulesetExists()
    {
        $flexibleRuleset = dirname(__DIR__) . '/CommonSecurity/Flexible/ruleset.xml';
        $this->assertFileExists($flexibleRuleset);
        
        $content = file_get_contents($flexibleRuleset);
        $this->assertStringContainsString('CommonSecurity', $content);
    }
}
