<?php
/**
 * Security Ruleset Manager
 * 
 * Helps manage project-specific security rule overrides
 */

namespace Adi3890\PHPCSCommonRules;

class SecurityRulesetManager
{
    /**
     * Generate a project-specific security ruleset
     * 
     * @param array $excludedFunctions Functions to exclude from security checks
     * @param array $customRules Additional custom rules
     * @param string $outputPath Path to save the generated ruleset
     */
    public static function generateProjectRuleset(
        array $excludedFunctions = [],
        array $customRules = [],
        string $outputPath = 'phpcs-security-custom.xml'
    ): void {
        $xml = new \DOMDocument('1.0', 'UTF-8');
        $xml->formatOutput = true;
        
        // Create root ruleset element
        $ruleset = $xml->createElement('ruleset');
        $ruleset->setAttribute('name', 'Project Security Standards');
        $xml->appendChild($ruleset);
        
        // Add description
        $description = $xml->createElement('description', 'Project-specific security standards with custom overrides');
        $ruleset->appendChild($description);
        
        // Include base security rules
        $baseRule = $xml->createElement('rule');
        $baseRule->setAttribute('ref', 'CommonSecurity');
        $ruleset->appendChild($baseRule);
        
        // Add exclusions if any
        if (!empty($excludedFunctions)) {
            $comment = $xml->createComment(' Project-specific exclusions ');
            $ruleset->appendChild($comment);
            
            foreach ($excludedFunctions as $function => $reason) {
                $excludeRule = $xml->createElement('rule');
                $excludeRule->setAttribute('ref', 'Generic.PHP.ForbiddenFunctions.Found');
                
                $exclude = $xml->createElement('exclude-pattern');
                $exclude->setAttribute('type', 'relative');
                $exclude->nodeValue = "*{$function}*";
                $excludeRule->appendChild($exclude);
                
                if ($reason) {
                    $excludeComment = $xml->createComment(" Excluded {$function}: {$reason} ");
                    $ruleset->appendChild($excludeComment);
                }
                
                $ruleset->appendChild($excludeRule);
            }
        }
        
        // Add custom rules
        if (!empty($customRules)) {
            $comment = $xml->createComment(' Project-specific custom rules ');
            $ruleset->appendChild($comment);
            
            foreach ($customRules as $rule) {
                $customRule = $xml->createElement('rule');
                $customRule->setAttribute('ref', $rule);
                $ruleset->appendChild($customRule);
            }
        }
        
        // Save the file
        file_put_contents($outputPath, $xml->saveXML());
    }
    
    /**
     * Get list of all forbidden functions from the security ruleset
     */
    public static function getForbiddenFunctions(): array
    {
        return [
            // Critical RCE Functions
            'eval' => 'Remote Code Execution risk',
            'assert' => 'Remote Code Execution risk',
            'create_function' => 'Remote Code Execution risk',
            'preg_replace' => 'Remote Code Execution risk with /e modifier',
            
            // Command Execution
            'exec' => 'Command injection risk',
            'shell_exec' => 'Command injection risk',
            'system' => 'Command injection risk',
            'passthru' => 'Command injection risk',
            'popen' => 'Command injection risk',
            'proc_open' => 'Command injection risk',
            
            // Network
            'fsockopen' => 'SSRF attack risk',
            'pfsockopen' => 'SSRF attack risk',
            
            // Information Disclosure
            'phpinfo' => 'Information disclosure',
            'highlight_file' => 'Source code disclosure',
            'show_source' => 'Source code disclosure',
            
            // Variable Manipulation
            'extract' => 'Variable overwriting risk',
            'parse_str' => 'Variable overwriting risk',
            
            // Configuration Changes
            'ini_set' => 'Runtime configuration changes',
            'ini_alter' => 'Runtime configuration changes',
            'putenv' => 'Environment variable manipulation',
            
            // File System
            'tmpfile' => 'Temporary file security risk',
            'link' => 'Hard link creation risk',
            'symlink' => 'Symbolic link creation risk',
            'fpassthru' => 'File content disclosure',
            
            // Dynamic Loading
            'dl' => 'Dynamic extension loading',
            
            // POSIX Functions
            'posix_kill' => 'Process signal manipulation',
            'posix_setuid' => 'User ID manipulation',
            'posix_setgid' => 'Group ID manipulation',
            
            // Socket Functions
            'socket_bind' => 'Network socket binding',
            'socket_listen' => 'Network socket listening',
            'stream_socket_server' => 'Network server creation',
            
            // Execution Control
            'set_time_limit' => 'Execution limit manipulation',
            'ignore_user_abort' => 'User abort handling',
        ];
    }
    
    /**
     * Generate a basic project configuration interactively
     */
    public static function interactiveSetup(): void
    {
        echo "Common Security Standards - Project Configuration\n";
        echo "================================================\n\n";
        
        $excludedFunctions = [];
        $functions = self::getForbiddenFunctions();
        
        echo "Available security functions that can be excluded:\n";
        $i = 1;
        $functionList = [];
        foreach ($functions as $func => $reason) {
            echo sprintf("%2d. %-20s - %s\n", $i, $func, $reason);
            $functionList[$i] = $func;
            $i++;
        }
        
        echo "\nEnter function numbers to exclude (comma-separated, or 'none'): ";
        $input = trim(fgets(STDIN));
        
        if ($input !== 'none' && !empty($input)) {
            $numbers = array_map('trim', explode(',', $input));
            foreach ($numbers as $num) {
                if (isset($functionList[(int)$num])) {
                    $func = $functionList[(int)$num];
                    echo "Reason for excluding '{$func}': ";
                    $reason = trim(fgets(STDIN));
                    $excludedFunctions[$func] = $reason ?: 'Project requirement';
                }
            }
        }
        
        self::generateProjectRuleset($excludedFunctions);
        echo "\nGenerated: phpcs-security-custom.xml\n";
        echo "Add this to your phpcs.xml: <rule ref=\"./phpcs-security-custom.xml\"/>\n";
    }
}
