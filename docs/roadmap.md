# MVP Roadmap

## Goal

Deliver an MVP that allows authenticated users to manage betting-related reference data, create bets, and review basic statistics through a React frontend backed by a Symfony REST API.

## Principles

- deliver vertical slices
- keep backend rules in services
- expose only frontend-oriented API responses
- distinguish global dictionaries from user-owned data
- avoid moving domain workflows into EasyAdmin

## Stages

### 1. Authentication

Scope:

- session login and logout
- current user endpoint
- route protection in frontend
- role separation for admin and regular user

Status:

- partially implemented

Notes:

- backend session authentication exists
- frontend login and protected routes exist
- registration, password reset, and broader account management are not implemented

### 2. Sport

Scope:

- global sport dictionary
- admin CRUD
- frontend read access

Status:

- mostly implemented

Notes:

- entity, repository, API endpoint, fixtures, and EasyAdmin CRUD already exist
- dictionary field naming should be aligned with final standards if `icon` is replaced by `emoji`

### 3. Region

Scope:

- global region dictionary
- admin CRUD
- relation from competition to region

Status:

- planned

Notes:

- this stage should replace the current `countryCode` shortcut where appropriate

### 4. Competition

Scope:

- global competitions
- user-private competitions
- ownership-aware queries
- admin support only for global records

Status:

- partially implemented

Notes:

- current API supports listing and creating user-private competitions
- entity already supports nullable `owner`
- region relation and service-layer extraction are still missing

### 5. BetType

Scope:

- global bet types
- optional user-specific bet types
- clear separation between system and private dictionaries

Status:

- planned

### 6. Bookmaker

Scope:

- global bookmaker dictionary
- admin CRUD
- active/inactive management

Status:

- planned

### 7. Tags

Scope:

- user-owned tags
- assignment to bets
- filtering support

Status:

- planned

### 8. Bet

Scope:

- create and manage bets
- settlement status
- links to competition, bookmaker, bet type, and tags
- validation and calculation services

Status:

- in progress

Notes:

- the `BetSlip` vertical slice is implemented as the main coupon entity
- full roadmap completion for this stage remains blocked until schema validation, migrations, tests, frontend checks, and build are all confirmed in the project environment

### 9. Statistics

Scope:

- ROI and profit summaries
- grouped statistics by sport, competition, bookmaker, and tag
- period filtering

Status:

- planned

### 10. Dashboard

Scope:

- overview widgets
- current performance summary
- shortcuts to key actions

Status:

- planned

## Suggested Delivery Order

1. Finish authentication foundations.
2. Stabilize dictionary architecture with `Sport` and `Region`.
3. Refactor `Competition` into service-based backend flow.
4. Add `BetType`, `Bookmaker`, and `Tags`.
5. Implement `Bet` lifecycle and settlement.
6. Build statistics endpoints and dashboard views.
