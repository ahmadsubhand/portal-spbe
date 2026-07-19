# Architecture

## Purpose

This document defines the intended MVP architecture for Portal SPBE based on the product requirements in [PRD.md](PRD.md), the ERD in [database-schema.md](database-schema.md), and the project rules in `AGENTS.md`.

## Architecture Summary

Portal SPBE will be implemented as a single Laravel monolith.

- Backend: Laravel
- Frontend: Blade with Tailwind CSS
- Admin panel: Blade-based admin pages within the same Laravel app
- Database: MySQL or PostgreSQL
- Auth and authorization: Laravel auth plus policies/gates
- File storage: Laravel filesystem with protected/private storage where needed

Approved public-facing modules in the MVP include:

- Beranda
- Regulasi
- Artikel
- Layanan
- Forum Diskusi
- Basis Pengetahuan
- Dashboard

The system will not use microservices, separate SPA deployments, or external workflow engines in the MVP.

## Current Decision

The frontend direction currently implemented for the MVP is:

- Public portal pages: Blade
- Internal contributor pages: Blade
- Admin management pages: Blade

This keeps the implementation aligned with the CRUD-heavy workflow and the current repository shape without introducing a second admin stack.

## High-Level Structure

```text
Browser
  |
  v
Laravel Routes
  |
  +--> Web Controllers
  |      |
  |      +--> Form Requests
  |      +--> Policies / Gates
  |      +--> Service Classes
  |      +--> Eloquent Models
  |      +--> Blade Views
  |
Eloquent Models <--> MySQL/PostgreSQL
Laravel Filesystem <--> Local/managed storage
```

## Application Layers

### 1. Presentation Layer

Responsible for rendering views and handling user interaction.

- Blade views for public and ASN-facing pages
- Blade pages for admin workflows
- Tailwind CSS for styling
- Minimal JavaScript only where necessary

Expected folders:

- `resources/views/`
- `resources/css/`
- `resources/js/`

### 2. Routing Layer

Responsible for URL mapping and route grouping.

- Public routes in `routes/web.php`
- Authenticated ASN routes grouped behind auth middleware
- Admin routes handled in `routes/web.php` and protected by authorization rules
- Named routes for all application endpoints

Route groups should be separated by access level:

- Public
- Authenticated contributor
- Admin

The public route layer should cover:

- homepage
- public article listing and detail
- regulations
- layanan
- forum diskusi
- placeholder basis pengetahuan
- placeholder dashboard

### 3. Controller Layer

Controllers should stay thin.

Responsibilities:

- Receive requests
- Delegate validation to Form Requests
- Authorize via policies/gates
- Pass business operations to services
- Return Blade views or redirects

Controllers should not contain workflow-heavy business logic.

### 4. Request Validation Layer

Form Requests will validate all write operations.

Examples:

- create article
- update article
- submit article
- approve article
- reject article
- publish article
- manage categories/tags

### 5. Authorization Layer

Authorization must be centralized through Laravel policies and gates.

Expected policy coverage:

- `ArticlePolicy`
- `CategoryPolicy`
- `TagPolicy`
- `UserPolicy`
- audit/settings access policies as needed

Examples of enforced rules:

- ASN can edit only their own drafts
- ASN cannot publish
- Admin can approve/reject/publish
- Guest cannot access internal pages

### 6. Service Layer

Business logic should live in service classes, especially around workflow transitions.

Current service candidates:

- `TaxonomyService`
- `UserManagementService`
- `AuditLogService`
- `SettingService`

Example responsibilities:

- validate legal article state transitions
- record review actions
- enforce rejection note requirement
- generate audit events

### 7. Domain/Data Layer

Eloquent models represent the main domain entities.

Current core models:

- `User`
- `Artikel`
- `Category`
- `Tag`
- `AuditLog`
- `Setting`

Additional currently-used public module models:

- `Regulasi`
- `Topic`
- `Reply`
- `TopicLike`

Relationships should follow the ERD document.

## Planned Modules

### Authentication And Identity

- login/logout
- session-based auth
- string-based role assignment on `users.role`
- active/inactive account control via `users.is_active`

### Article Management

- create draft
- edit own draft
- submit for review
- public article display

### Review Workflow

- approve article
- reject article with reason
- publish approved article
- audit-log-based traceability for admin actions

### Taxonomy

- category management
- tag management
- taxonomy-based filtering

### Public Portal

- published article list
- article detail page
- search and filter
- regulations page
- layanan page
- forum diskusi page(s)
- basis pengetahuan placeholder
- dashboard placeholder

### Admin Operations

- user management
- audit logs
- settings

### Placeholder Integrations

- Basis Pengetahuan placeholder page, configurable via `settings`
- Dashboard placeholder/embed page, configurable via `settings`

These do not represent the same future implementation shape:

- `Dashboard` is an integration-oriented placeholder and is expected to evolve mostly by replacing placeholder content with a real Looker Studio embed, configuration, and access handling.
- `Basis Pengetahuan` is a future-feature placeholder and is expected to require additional domain, query, and visualization implementation when Neo4j-backed knowledge exploration is introduced.

The intended `Basis Pengetahuan` integration path is documented in [knowledge-graph-integration.md](knowledge-graph-integration.md). Its default direction is an API-backed graph visualization served through Laravel and powered by a NestJS API over Neo4j Aura. A raw iframe embed should not be the primary approach unless the embedded service is explicitly public-safe.

## Request Flow Examples

### Public Read Flow

```text
Guest -> Route -> Controller -> Query published articles -> Blade view
```

### ASN Draft Submission Flow

```text
ASN -> Route -> Controller -> Form Request -> Policy -> ArticleWorkflowService
    -> Article + ArticleReview update -> Redirect
```

### Admin Approval Flow

```text
Admin -> Filament/Page Action -> Policy -> ArticleWorkflowService
      -> status update + review log + audit log -> Redirect
```

## Data And State Rules

- Only `published` articles are public
- `draft -> submitted -> approved -> published` is the happy path
- `submitted -> rejected` requires a reason
- Publish is only legal from `approved`
- Ownership and role rules must be enforced in policy and service layers

## Frontend Placement

The frontend stays inside the Laravel app.

- Public templates: `resources/views/public/`
- Approved design-driven pages may initially live under generic public page templates before being fully backed by dynamic data
- Public sections that later become operationally managed should be migrated to real data sources instead of remaining static templates
- ASN templates: `resources/views/asn/`
- Shared layouts/components: `resources/views/layouts/`, `resources/views/components/`
- Admin UI: Filament resources/pages

Suggested Blade organization:

```text
resources/views/
├── components/
├── layouts/
├── public/
│   ├── articles/
│   ├── pages/
├── asn/
│   ├── articles/
│   ├── dashboard/
└── admin/
```

Note: Filament will own much of the actual admin UI, so `resources/views/admin/` may remain minimal.

## Cross-Cutting Concerns

### Auditability

- important admin and workflow actions should create audit records
- review actions should be traceable through `article_reviews`

### Security

- CSRF protection
- XSS-safe output by default through Blade escaping
- strict authorization
- validated uploads
- no direct access to private files

### Maintainability

- named routes
- thin controllers
- service classes for business logic
- policies instead of inline role checks

## Non-MVP Items

The following are intentionally excluded from the current architecture scope:

- real Neo4j connectivity
- real Looker Studio integration logic
- multi-service deployments
- event-driven distributed workflow
- separate frontend SPA

## Initial Implementation Order

1. Authentication baseline
2. Roles and authorization structure
3. Article domain schema and models
4. Workflow service and policies
5. Public article pages
6. Filament admin resources
7. Search/filter and placeholder pages
