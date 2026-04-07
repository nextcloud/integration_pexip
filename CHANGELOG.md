<!--
  - SPDX-FileCopyrightText: 2026 Nextcloud GmbH and Nextcloud contributors
  - SPDX-License-Identifier: AGPL-3.0-or-later
-->
# Change Log
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/)
and this project adheres to [Semantic Versioning](http://semver.org/).

## [Unreleased]

## 2.0.0 – 2026-04-07

### Added

- Migrate to Vue 3
- Modernize admin settings, fix picker submit
- Add psalm CI
- Add more CI actions

### Changed

- Use IAppConfig instead of AppConfig
- Use IReferenceManager instead of ReferenceManager
- Use PHP 8.2 in phpunit action
- Add reuse headers and fix REUSE compliance
- Remove copyright doc blocks

### Fixed

- Fix psalm issues
- Fix entity attr names, fix guest pin ui field

### Other

- Add licenses, ignore vendor dirs for l10n
- Add pr-feedback action

## 1.0.4 – 2023-10-03

### Changed

- update npm pkgs

### Fixed

- append "/express" to links, adjust reference provider

## 1.0.3 – 2023-08-22

### Fixed

- Broken DB insertion when using postgres

## 1.0.1 – 2023-08-04

### Changed

- use NC user name as participant name when joining a pexip call

### Fixed

- fix validity check on pins
- fix scrolling in the picker component
- fix RichContenteditable placeholder position when scrolling

## 1.0.0 – 2023-04-01
### Added
* the app
