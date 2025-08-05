# PHPCS Common Rules

Organization-wide PHP CodeSniffer security standards and common rules.

## Installation

```bash
composer require --dev adi3890/phpcs-common-rules
```

## Quick Setup

The package automatically generates `phpcs-common.xml` after installation. You can also generate it manually:

```bash
# Generate configuration file
composer run generate-config

# Or use the direct command
vendor/bin/generate-config
```

## Usage

After installation, run PHPCS with the generated configuration:

```bash
# Use the auto-generated config
vendor/bin/phpcs --standard=phpcs-common.xml

# Or with progress and colors
vendor/bin/phpcs -p --colors --standard=phpcs-common.xml .
```

## Manual Configuration

If you prefer manual setup, create a `phpcs.xml` file:

```xml
<?xml version="1.0"?>
<ruleset name="Project Standards">
    <description>Project coding standards</description>
    
    <!-- Include common security rules -->
    <rule ref="CommonSecurity"/>
    
    <!-- Your project files -->
    <file>.</file>
    
    <!-- Exclude vendor and other directories -->
    <exclude-pattern>*/vendor/*</exclude-pattern>
    <exclude-pattern>*/node_modules/*</exclude-pattern>
</ruleset>
```

## License

MIT
