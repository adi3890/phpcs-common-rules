# PHP CodeSniffer Common Security Rules

A comprehensive set of security-focused PHP CodeSniffer rules designed to prevent common security vulnerabilities in PHP applications.

## Features

- 🔒 **Comprehensive Security Rules**: Covers RCE, command injection, information disclosure, and more
- 🔧 **Flexible Configuration**: Easily customize rules per project
- 📦 **Easy Integration**: Works with existing PHPCS configurations
- 🏢 **Organization-wide**: Consistent security standards across all projects
- 📚 **Well Documented**: Clear explanations for each security rule

## Installation

### Via Composer (Recommended)

```bash
composer require --dev adi3890/phpcs-common-rules
```

### Manual Installation

1. Clone this repository
2. Run the installation script:

```bash
./bin/install-security-standards
```

## Usage

### Basic Integration

Add to your existing `phpcs.xml`:

```xml
<?xml version="1.0"?>
<ruleset name="Your Project Standards">
    <!-- Your existing rules -->
    <rule ref="PSR12"/>
    
    <!-- Add security standards -->
    <rule ref="CommonSecurity"/>
    
    <file>.</file>
</ruleset>
```

### Flexible Integration

For projects that need to exclude certain security rules:

```xml
<?xml version="1.0"?>
<ruleset name="Your Project Standards">
    <!-- Use flexible security standards -->
    <rule ref="CommonSecurity/Flexible"/>
    
    <!-- Exclude specific functions for this project -->
    <rule ref="Generic.PHP.ForbiddenFunctions.Found">
        <exclude-pattern>*/src/SpecificFile.php</exclude-pattern>
    </rule>
    
    <file>.</file>
</ruleset>
```

### Generate Project-Specific Configuration

Use the interactive generator:

```bash
./bin/generate-project-config
```

This will create a `phpcs-security-custom.xml` file tailored to your project's needs.

## Security Rules Covered

### Critical Functions (Always Forbidden)

- **RCE Functions**: `eval()`, `assert()`, `create_function()`
- **Command Execution**: `exec()`, `shell_exec()`, `system()`, `passthru()`
- **Process Control**: `proc_open()`, `popen()`

### Network Functions

- **SSRF Risks**: `fsockopen()`, `pfsockopen()`
- **Socket Functions**: `socket_bind()`, `socket_listen()`

### Information Disclosure

- **Debug Functions**: `phpinfo()`, `highlight_file()`, `show_source()`
- **System Info**: Various `posix_*` functions

### Variable Manipulation

- **Dangerous Functions**: `extract()`, `parse_str()`
- **Configuration**: `ini_set()`, `putenv()`

## Customization

### Per-Project Exclusions

You can exclude specific functions for legitimate use cases:

```xml
<!-- Allow unserialize in specific legacy files -->
<rule ref="Generic.PHP.ForbiddenFunctions">
    <exclude-pattern>*/src/LegacyDataHandler.php</exclude-pattern>
</rule>
```

### File-Specific Exclusions

```xml
<!-- Allow mail() in email service classes -->
<rule ref="Generic.PHP.ForbiddenFunctions.Found">
    <exclude-pattern>*/src/EmailService.php</exclude-pattern>
</rule>
```

### Custom Severity Levels

```xml
<rule ref="CommonSecurity">
    <severity>8</severity> <!-- Make security violations high priority -->
</rule>
```

## Integration with CI/CD

### GitHub Actions

```yaml
name: Code Quality
on: [push, pull_request]

jobs:
  phpcs:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v2
      
      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: '8.1'
          
      - name: Install dependencies
        run: composer install --dev
        
      - name: Run PHPCS with security standards
        run: ./vendor/bin/phpcs --standard=phpcs.xml
```

### Pre-commit Hooks

```bash
#!/bin/sh
# .git/hooks/pre-commit

# Run PHPCS with security standards
./vendor/bin/phpcs --standard=phpcs.xml --colors

if [ $? -ne 0 ]; then
    echo "PHPCS security check failed. Please fix the issues before committing."
    exit 1
fi
```

## Examples

### WordPress Project

```xml
<?xml version="1.0"?>
<ruleset name="WordPress Project with Security">
    <rule ref="WordPress"/>
    <rule ref="CommonSecurity"/>
    
    <!-- WordPress-specific exclusions -->
    <rule ref="Generic.PHP.ForbiddenFunctions.Found">
        <exclude-pattern>*/templates/*</exclude-pattern>
    </rule>
    
    <file>.</file>
    <exclude-pattern>*/vendor/*</exclude-pattern>
</ruleset>
```

### Laravel Project

```xml
<?xml version="1.0"?>
<ruleset name="Laravel Project with Security">
    <rule ref="PSR12"/>
    <rule ref="CommonSecurity"/>
    
    <file>app</file>
    <file>config</file>
    <file>routes</file>
    
    <exclude-pattern>*/vendor/*</exclude-pattern>
</ruleset>
```

## Best Practices

1. **Start Strict**: Begin with the full `CommonSecurity` ruleset
2. **Document Exceptions**: Always document why you're excluding a security rule
3. **Regular Reviews**: Periodically review exclusions to see if they're still needed
4. **Team Training**: Ensure your team understands the security implications
5. **Gradual Adoption**: For legacy projects, gradually adopt rules over time

## Contributing

1. Fork the repository
2. Create a feature branch
3. Add tests for new rules
4. Submit a pull request

## Security Rule Updates

This package is regularly updated with new security rules. To stay current:

```bash
composer update adi3890/phpcs-common-rules
```

## Support

- 📖 [Documentation](https://github.com/adi3890/phpcs-common-rules/wiki)
- 🐛 [Issue Tracker](https://github.com/adi3890/phpcs-common-rules/issues)
- 💬 [Discussions](https://github.com/adi3890/phpcs-common-rules/discussions)

## License

MIT License - see [LICENSE](LICENSE) file for details.
