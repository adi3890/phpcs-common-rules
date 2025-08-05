# Migration Guide

This guide helps you integrate Common Security Standards into existing projects without breaking your current workflow.

## Step-by-Step Migration

### 1. Install the Package

```bash
composer require --dev adi3890/phpcs-common-rules
```

### 2. Backup Your Current Configuration

```bash
cp phpcs.xml phpcs.xml.backup
```

### 3. Choose Your Integration Strategy

#### Strategy A: Gradual Adoption (Recommended for Legacy Projects)

Start with warnings only:

```xml
<?xml version="1.0"?>
<ruleset name="Your Project - Gradual Security">
    <!-- Your existing rules -->
    <rule ref="PSR12"/>
    
    <!-- Add security standards as warnings initially -->
    <rule ref="CommonSecurity">
        <severity>4</severity> <!-- Warning level -->
    </rule>
    
    <file>.</file>
</ruleset>
```

#### Strategy B: Immediate Adoption (For New Projects)

```xml
<?xml version="1.0"?>
<ruleset name="Your Project - Full Security">
    <!-- Your existing rules -->
    <rule ref="PSR12"/>
    
    <!-- Add security standards as errors -->
    <rule ref="CommonSecurity"/>
    
    <file>.</file>
</ruleset>
```

#### Strategy C: Selective Adoption

```xml
<?xml version="1.0"?>
<ruleset name="Your Project - Selective Security">
    <!-- Your existing rules -->
    <rule ref="PSR12"/>
    
    <!-- Add only critical security rules -->
    <rule ref="Generic.PHP.ForbiddenFunctions">
        <properties>
            <property name="forbiddenFunctions" type="array">
                <!-- Only the most critical functions -->
                <element key="eval" value="Critical security risk"/>
                <element key="exec" value="Command injection risk"/>
                <element key="system" value="Command injection risk"/>
            </property>
        </properties>
    </rule>
    
    <file>.</file>
</ruleset>
```

### 4. Test the Integration

```bash
# Run PHPCS to see what violations exist
./vendor/bin/phpcs --report=summary

# Get detailed report
./vendor/bin/phpcs --report=full
```

### 5. Address Violations

#### Common Violations and Solutions

**1. `eval()` usage:**
```php
// ❌ Dangerous
eval($code);

// ✅ Safe alternatives
match($operation) {
    'add' => $a + $b,
    'subtract' => $a - $b,
    default => throw new InvalidArgumentException()
};
```

**2. `exec()` usage:**
```php
// ❌ Dangerous
exec("ls " . $userInput);

// ✅ Safe alternatives
$files = scandir($directory);
// Or use symfony/process with proper validation
```

**3. `extract()` usage:**
```php
// ❌ Dangerous
extract($_POST);

// ✅ Safe alternatives
$name = $_POST['name'] ?? '';
$email = $_POST['email'] ?? '';
```

### 6. Handle Legitimate Use Cases

If you have legitimate use cases for forbidden functions:

```xml
<!-- Allow specific functions in specific files -->
<rule ref="Generic.PHP.ForbiddenFunctions.Found">
    <exclude-pattern>*/src/LegacyModule.php</exclude-pattern>
</rule>
```

Or create a project-specific configuration:

```bash
./bin/generate-project-config
```

### 7. Update CI/CD Pipeline

#### GitHub Actions

```yaml
# Add to your existing workflow
- name: Security Standards Check
  run: ./vendor/bin/phpcs --standard=CommonSecurity --report=checkstyle --report-file=phpcs-security.xml
  
- name: Upload Security Report
  uses: actions/upload-artifact@v3
  if: failure()
  with:
    name: phpcs-security-report
    path: phpcs-security.xml
```

#### GitLab CI

```yaml
security_check:
  stage: test
  script:
    - ./vendor/bin/phpcs --standard=CommonSecurity
  artifacts:
    when: on_failure
    reports:
      junit: phpcs-security.xml
```

### 8. Team Training

1. **Document Exceptions**: Create a team wiki explaining why certain exclusions exist
2. **Code Review Guidelines**: Include security standard checks in your review process
3. **Regular Audits**: Schedule quarterly reviews of security exclusions

## Troubleshooting

### Common Issues

**Issue**: Too many violations in legacy code
**Solution**: Use gradual adoption strategy with warnings first

**Issue**: False positives for legitimate use cases
**Solution**: Use file-specific exclusions with documentation

**Issue**: Performance impact on large codebases
**Solution**: Run security checks separately or on changed files only

### Getting Help

1. Check the [README](README.md)
2. Search [existing issues](https://github.com/adi3890/phpcs-common-rules/issues)
3. Create a new issue with:
   - Your PHP version
   - PHPCS version
   - Sample code causing issues
   - Your phpcs.xml configuration

## Rollback Plan

If you need to rollback:

```bash
# Restore your backup
cp phpcs.xml.backup phpcs.xml

# Or remove security standards temporarily
composer remove --dev adi3890/phpcs-common-rules
```

## Success Metrics

Track your progress:

- Number of security violations over time
- Code coverage of security checks
- Time to resolve security issues
- Team awareness of security practices
