# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

Koriym.Baracoa is a PHP library that provides a simple interface for JavaScript server-side rendering (SSR). It executes bundled JavaScript applications in PHP using either V8Js extension or PhpExecJs as a fallback.

## Common Commands

```bash
# Run all tests (includes phpmd, phpcs, phpunit)
composer test

# Run PHPUnit only (faster for development)
./vendor/bin/phpunit

# Run single test
./vendor/bin/phpunit --filter testMethodName

# Fix coding standards
composer cs-fix
```

## Test Prerequisites

Tests require the Redux example to be built first:

```bash
cd docs/example/redux
npm install
npm run build
```

## Architecture

### Core Classes

- `Baracoa` - Main SSR renderer. Loads `{appName}.bundle.js` from a base directory and executes a `render(state, metas)` function
- `CacheBaracoa` - Performance-optimized renderer using V8Js snapshots with PSR-16 cache. Recommended for production
- `BaracoaInterface` - Common interface: `render(string $appName, array $store, array $metas = []): string`

### Execution Flow

1. PHP calls `render()` with app name and initial state
2. Bundle JS file is loaded from `{basePath}/{appName}.bundle.js`
3. JS is executed with state/metas JSON-encoded as arguments to the global `render()` function
4. HTML string is returned

### JS Application Contract

JS bundles must expose a global `render` function:

```javascript
// Entry file pattern
import render from './render';
global.render = render;

// render.js returns HTML string
const render = (state, metas) => `<html>...</html>`;
```

### Exception Handling

- `JsFileNotExistsException` - Thrown when bundle file not found
- `ExceptionHandlerInterface` - Handles V8JsScriptException, default implementation re-throws with enhanced error info

## Testing

### V8Js Tests

V8Js tests require the V8Js PHP extension which is difficult to install in CI environments (GitHub Actions). The V8 engine has complex build dependencies and API compatibility issues between versions.

- **CI**: Tests requiring V8Js are skipped using `#[RequiresPhpExtension('v8js')]`. Only PhpExecJs fallback tests run.
- **Local**: Install V8Js extension to run full test suite including V8Js tests.

#### Installing V8Js locally (macOS)

```bash
brew install shivammathur/extensions/v8js@8.4
```

See [README.md](README.md#install-v8js) for more installation options.
