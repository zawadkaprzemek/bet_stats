# Architecture

## Overview

`bet_stats` uses a split architecture:

- Symfony backend as the source of business logic, authentication, persistence, and admin tools
- React frontend as the user-facing client application
- MySQL as the relational database
- Docker Compose as the local runtime environment

The system follows a backend-driven domain model and a frontend-consumer API model. The frontend must communicate with the backend exclusively through REST API endpoints.

## High-Level Structure

```text
Browser
  -> React SPA
  -> REST API calls with session cookie

React SPA
  -> /api/*
  -> JSON over HTTP

Nginx
  -> Symfony backend

Symfony
  -> Controllers
  -> Application services
  -> Doctrine repositories
  -> MySQL

EasyAdmin
  -> Admin-only back office for global dictionaries and administration
```

## Backend Architecture

### Responsibilities

The backend is responsible for:

- authentication and authorization
- domain validation
- business logic
- persistence through Doctrine ORM
- exposing REST endpoints tailored to frontend needs
- administrative back office through EasyAdmin

### Layers

Recommended backend structure:

- `Controller` for HTTP input and response mapping only
- `Service` for business logic and use-case orchestration
- `Repository` for persistence queries
- `Entity` for domain state and persistence mapping
- `Api` or `ViewFactory` classes for API response shaping

### Mandatory Rules

- Do not place business logic in controllers.
- Business logic must live in dedicated services.
- Controllers should validate request shape, call services, and return HTTP responses.
- Repositories should encapsulate query logic.
- API responses should return only fields needed by the frontend.
- Do not expose technical or internal fields unless the frontend actually needs them.

### Business Service Examples

- `BetCalculator`
- `StatisticsService`
- `CompetitionService`
- `SettlementService`

Some of these services are target architecture elements and are not yet implemented in the current codebase.

## Frontend Architecture

### Responsibilities

The frontend is responsible for:

- rendering user flows
- route protection
- form handling
- calling the REST API
- presenting domain data returned by the backend

### Mandatory Rules

- Frontend communicates exclusively through REST API.
- TanStack Query is responsible for server-state fetching and caching.
- React Hook Form and Zod are responsible for forms and validation.
- Frontend should not duplicate business logic that belongs to the backend.

### Current Frontend State

The current frontend already uses:

- React Router for navigation and route guards
- TanStack Query for fetching sports and competitions
- React Hook Form with Zod on the login and quick form flows
- a shared API client using `credentials: include`

## Admin Architecture

EasyAdmin is reserved for:

- management of global dictionaries
- administration features

It should not become the primary user workflow for domain operations such as user betting activity.

Current state note:

- the repository currently exposes EasyAdmin management for both `Sport` and `Competition`
- the target rule is narrower: EasyAdmin should remain focused on global dictionaries and administration

## Dictionary Model Rules

Every global dictionary should use the same core fields:

- `id`
- `name`
- `slug`
- `emoji` nullable
- `sortOrder`
- `isActive`
- `createdAt`
- `updatedAt`

This applies to entities such as:

- `Sport`
- `Region`
- `Bookmaker`
- global `BetType`

Current state note:

- `Sport` already contains most shared dictionary fields
- `Sport` currently uses `icon` instead of `emoji`
- `Region`, `Bookmaker`, and global `BetType` are planned but not yet implemented

## Ownership Rules

User-owned data must explicitly belong to a user when private.

This applies to entities such as:

- `Competition`
- `UserBetType`
- `Bet`
- `Tag`

Ownership rule:

- global record: `owner` or `user` is `null`
- private record: `owner` or `user` references the owning user

Current state note:

- `Competition` already follows this pattern through the nullable `owner` relation

## API Design Rules

- Build task-oriented REST endpoints.
- Return minimal response payloads.
- Keep backend entities decoupled from raw API output.
- Use dedicated response mappers or view factories.
- Prefer explicit request DTOs or service inputs as the domain grows.

Current state note:

- the codebase already uses `SportViewFactory`, `CompetitionViewFactory`, and `UserViewFactory`
- this is aligned with the rule to avoid exposing raw entities directly

## Current Implementation Snapshot

Implemented today:

- session-based authentication in Symfony
- REST endpoints for auth, health, sports, and competitions
- private and global competition visibility model
- EasyAdmin back office
- React application shell with protected routes

Not yet implemented today:

- dedicated backend service layer for all current use cases
- region dictionary
- bookmaker dictionary
- bet type modules
- tags, bets, settlement, statistics, dashboard domain modules

## Architectural Direction

The target architecture for MVP is:

1. Expand the domain through dedicated modules instead of controller growth.
2. Standardize dictionary entities with shared rules.
3. Introduce service-layer orchestration for all write operations and non-trivial reads.
4. Keep EasyAdmin limited to admin concerns.
5. Keep the frontend thin in domain logic and strong in API integration and UX.
