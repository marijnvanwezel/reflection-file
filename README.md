# marijnvanwezel/reflection-file

Library that allows reflection of files. 

This library can be used, among other things, to retrieve the classes,
interfaces, traits, enums, functions and constants declared in a file. 

This package uses static analysis instead of dynamic analysis for reflection,
and has therefore fewer false-positives to alternatives like
`get_declared_classes()` and works without ever including the reflected files
in your project.

It has full support for namespaces (braced and unbraced) and works with file
that have multiple namespaces.

## Installation

You can add this library as a dependency to your project using
[Composer](https://getcomposer.org):

```
composer require marijnvanwezel/reflection-file
```
