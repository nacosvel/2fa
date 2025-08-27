# Changelog

All notable changes to `nacosvel/2fa` will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased](https://github.com/nacosvel/2fa)

## [2.0.0](https://github.com/nacosvel/2fa/compare/v1.0.0...v2.0.0) - 2025-08-27

### Added

- URI::buildURI

### Changed

- HOTP::getAuthUri => HOTP::buildURI
- TOTP::getAuthUri => TOTP::buildURI

### Removed

- Authentication::generateURI

## [1.0.0](https://github.com/nacosvel/2fa/releases/tag/v1.0.0) - 2025-08-26

- Authentication::generateSecret
- Authentication::generateURI
- HOTP::generateToken
- TOTP::generateToken
- HOTP::validate
- TOTP::validate
- HOTP::getAuthUri
- TOTP::getAuthUri
- URI::fromString
