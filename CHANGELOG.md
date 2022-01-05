# Changelog

All notable changes to the Fit project will be documented in this file.

The format is based on Keep a Changelog, and this project adheres to Semantic
Versioning.

## [Unreleased]

## [2.0.0]

Added:
* Added the function `ReflectionFile::getPathName()` to get the pathname of the
  file.
* Added the function `ReflectionFile::getClasses()` to get `ReflectionClass`
  instances of the declared classes.
* Added the function `ReflectionFile::getTraitNames()` to get the names of the
  traits declared in the file.
* Added the function `ReflectionFile::getTraits()` to get `ReflectionClass`
  instances of the declared traits.
* Added the function `ReflectionFile::getInterfaceNames()` to get the names of
  the interfaces declared in the file.
* Added the function `ReflectionFile::getInterfaces()` to get the `ReflectionClass`
  instances of the declared interfaces.
* Added the function `ReflectionFile::getEnumNames()` to get the names of the
  enums declared in the file.
* Added the function `ReflectionFile::getEnums()` to get the `ReflectionEnum`
  instances of the declared enums.
* Added the function `ReflectionFile::getFunctionNames()` to get the names of the
  functions declared in the file.
* Added the function `ReflectionFile::getFunctions()` to get the
  `ReflectionFunction` instances of the declared functions.
* Added the function `ReflectionFile::getConstantNames()` to get the names of the
  constants declared in the file.

Changed:
* Renamed `ReflectionFile::getDeclaredClassnames()` to
  `ReflectionFile::getClassNames()`.
* Moved parsing of file from `ReflectionFile::getDeclaredClassnames()` (now
  `ReflectionFile::getClassNames()`) to the constructor.
* Errors from `file_get_contents` are no longer suppressed.
* The class names returned by `ReflectionFile::getDeclaredClassnames()` (now
  `ReflectionFile::getClassNames()`) are no longer fully qualified, and are
  now qualified instead.

## [1.0.0]

* Initial release

[unreleased]: https://github.com/marijnvanwezel/reflection-file/compare/v2.0.0...HEAD
[2.0.0]: https://github.com/marijnvanwezel/reflection-file/releases/tag/v2.0.0
[1.0.0]: https://github.com/marijnvanwezel/reflection-file/releases/tag/v1.0.0
