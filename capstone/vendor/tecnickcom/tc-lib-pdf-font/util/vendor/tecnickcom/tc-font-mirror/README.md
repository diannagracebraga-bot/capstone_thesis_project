# tc-font-mirror

> Curated mirror of open-source font assets used by the Tecnick PDF toolchain.

[![License](https://poser.pugx.org/tecnickcom/tc-font-mirror/license)](https://packagist.org/packages/tecnickcom/tc-font-mirror)
[![Downloads](https://poser.pugx.org/tecnickcom/tc-font-mirror/downloads)](https://packagist.org/packages/tecnickcom/tc-font-mirror)

[![Sponsor on GitHub](https://img.shields.io/badge/sponsor-github-EA4AAA.svg?logo=githubsponsors&logoColor=white)](https://github.com/sponsors/tecnickcom)

> 💖 Part of the [tc-lib-pdf / TCPDF](https://github.com/tecnickcom/tc-lib-pdf) ecosystem (100M+ installs). [Sponsor its maintenance →](https://github.com/sponsors/tecnickcom)

---

## Overview

`tc-font-mirror` is a data-only repository containing redistributed and normalized font resources used across the Tecnick PDF ecosystem.

The repository exists to make font asset delivery deterministic: downstream libraries can pin a version and rely on stable filenames, checksums, and metadata across environments. It is intentionally code-free and optimized for redistribution, packaging, and repeatable build pipelines.

---

## Features

### Included Sources
- GNU FreeFont (TTF source set)
- PDF/A Type1 derivatives from `tc-font-pdfa`
- DejaVu fonts
- Adobe Core14 AFM files
- GNU Unifont (TTF build artifacts)

### Repository Layout
- Organized by family (`core`, `pdfa`, `freefont`, `dejavu`, `unifont`, `cid0`)
- Designed for tooling and packaging pipelines
- No runtime PHP code or autoload layer

---

## Requirements

- No runtime requirements
- Any workflow that consumes these fonts (build tooling, font conversion, PDF generation)

---

## Installation

```bash
composer require tecnickcom/tc-font-mirror
```

---

## Quick Start

Use this package as a font asset source in your build or conversion workflow.

```text
tc-font-mirror/
  core/
  pdfa/
  freefont/
  dejavu/
  unifont/
  cid0/
```

---

## Development

```bash
make help
```

This repository is asset-focused, so development usually means updating sources, validating licensing, and tagging releases.

---

## Packaging

This repository is primarily distributed through Composer as a dependency for tools that need local font assets.
