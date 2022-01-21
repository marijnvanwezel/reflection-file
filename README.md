# marijnvanwezel/reflection-file

Library that allows reflection of files. 

This library can be used, among other things, to retrieve the classes,
interfaces, traits, enums, functions and constants declared in a file. 

This package uses static analysis instead of dynamic analysis for reflection,
and therefore has fewer false-positives to alternatives like
`get_declared_classes()` and works without including the reflected files in
your project.

It has full support for namespaces (braced and unbraced) and works with files
that have multiple namespaces.

This package requires PHP 8.1 or higher.

## Installation

You can add this library as a dependency to your project using
[Composer](https://getcomposer.org):

```
composer require marijnvanwezel/reflection-file
```

## Usage

To reflect a file, you can create a new ReflectionFile:

```php
use ReflectionFile\ReflectionFile;

$reflectionFile = new ReflectionFile('/path/to/file');
```

### Getting the filename

To get the filename of a file, use `getFileName`:

```php
$fileName = $reflectionFile->getFileName();
```

### Getting the pathname

To get the pathname of a file, use `getPathName`:

```php
$pathName = $reflectionFile->getPathName();
```

### Getting the source

To get the source of a file, use `getSource`:

```php
$source = $reflectionFile->getSource();
```

### Getting the declared classes

To get a file's declared classes, use `getClasses` or `getClassNames`:

```php
/** @var ReflectionClass[] $classes */
$classes = $reflectionFile->getClasses();

/** @var string[] $classes */
$classes = $reflectionFile->getClassNames();
```

### Getting the declared traits

To get a file's declared traits, use `getTraits` or `getTraitNames`:

```php
/** @var ReflectionClass[] $traits */
$traits = $reflectionFile->getTraits();

/** @var string[] $traits */
$traits = $reflectionFile->getTraitNames();
```

### Getting the declared interfaces

To get a file's declared interfaces, use `getInterfaces` or `getInterfaceNames`:

```php
/** @var ReflectionClass[] $interfaces */
$interfaces = $reflectionFile->getInterfaces();

/** @var string[] $interfaces */
$interfaces = $reflectionFile->getInterfaceNames();
```

### Getting the declared enums

To get a file's declared enums, use `getEnums` or `getEnumNames`:

```php
/** @var ReflectionEnum[] $enums */
$enums = $reflectionFile->getEnums();

/** @var string[] $enums */
$enums = $reflectionFile->getEnumNames();
```

### Getting the declared functions

To get a file's declared functions, use `getFunctions` or `getFunctionNames`:

```php
/** @var ReflectionFunction[] $functions */
$functions = $reflectionFile->getFunctions();

/** @var string[] $functions */
$functions = $reflectionFile->getFunctionNames();
```

### Getting the declared constants

To get a file's declared constants, use `getFunctionNames`:

```php
/** @var string[] $constants */
$constants = $reflectionFile->getConstantNames();
```
