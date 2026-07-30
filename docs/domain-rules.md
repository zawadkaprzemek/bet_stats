# Domain Rules

## Current Domain Scope

As of July 30, 2026, the implemented domain is still limited. The codebase currently contains:

- `User`
- `Sport`
- `Competition`

Authentication, sports listing, and competition management are already present. The rules below describe both the current domain behavior and the agreed rules for the next modules.

## Sport

- `Sport` is a global dictionary.
- `Sport` is created and managed by administrators only.
- `Sport` is available to the whole system.
- `Sport` should be ordered by `sortOrder` and then by name.
- `Sport` should remain deactivatable through `isActive` instead of being physically removed in normal workflows.

Current implementation note:

- this rule is already reflected in the API and admin panel
- `GET /api/sports` returns active sports ordered for frontend usage

## Region

- `Region` is a global dictionary.
- `Region` is created and managed by administrators only.
- `Region` may represent:
  - a country
  - a sports region
  - a continent
  - the world
  - a supranational organization

Examples:

- `England`
- `Scotland`
- `Spain`
- `Europe`
- `World`
- `International`

Current implementation note:

- `Region` does not exist yet as an entity
- current `Competition` uses optional `countryCode`, which is a temporary simplification and not the target domain model

## Competition

- `Competition` may be global or user-private.
- `Competition` belongs to `Sport`.
- `Competition` may optionally belong to `Region`.
- a private competition must have an `owner` or `user`
- a global competition has no `owner`

Current implementation note:

- `Competition` already belongs to `Sport`
- `Competition` already supports global and private ownership through nullable `owner`
- `Competition` does not yet reference `Region`
- uniqueness is currently enforced per `sport + owner + name`

## Global Dictionaries

Each global dictionary should contain these fields:

- `id`
- `name`
- `slug`
- `emoji` nullable
- `sortOrder`
- `isActive`
- `createdAt`
- `updatedAt`

This rule applies to:

- `Sport`
- `Region`
- `Bookmaker`
- global `BetType`

Current implementation note:

- `Sport` is close to this shape, but currently uses `icon` instead of `emoji`

## User-Owned Data

Private user data must always have an owner reference.

This applies to:

- `Competition`
- `UserBetType`
- `Bet`
- `Tag`

Rules:

- private records belong to exactly one user
- global records are visible system-wide
- API queries for private data must always be scoped to the authenticated user

Current implementation note:

- competition queries already follow this principle by returning global records plus records owned by the authenticated user

## Business Logic Placement

- Business logic must not be placed in controllers.
- Controllers should stay thin and HTTP-oriented.
- Business logic should live in dedicated services.

Examples of intended services:

- `BetCalculator`
- `StatisticsService`
- `CompetitionService`
- `SettlementService`

Current implementation note:

- the current codebase still contains competition creation and validation flow directly in the controller
- this should be moved into dedicated services as the domain expands

## API Rules

- API returns only data required by the frontend.
- API should not expose technical fields when they are not used by the frontend.
- API should shape responses explicitly for frontend use cases.

Current implementation note:

- the project already uses dedicated API view factories, which is consistent with this rule

## EasyAdmin Rules

- EasyAdmin is for global dictionaries and administration only.
- EasyAdmin is not the target interface for end-user workflows.

Current implementation note:

- `Sport` fits this rule
- `Competition` is currently also managed through EasyAdmin, but that should be treated as transitional rather than final architecture
