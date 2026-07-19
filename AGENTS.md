# AGENTS.md

## Project Overview

A private government SPBE knowledge/documentation portal built with Laravel. Supports article management, role-based access, and public knowledge browsing. Includes placeholder pages for future Neo4j ontology visualization and Looker Studio dashboard integration.

---

## Tech Stack

- **Framework**: Laravel (monorepo)
- **Language**: PHP
- **Frontend**: Blade or Inertia.js + TailwindCSS
- **Database**: MySQL or PostgreSQL
- **Admin Panel**: Filament (preferred)

Do not introduce microservices or unnecessary abstractions.

---

## User Roles

| Role  | Can Do | Cannot Do |
|-------|--------|-----------|
| **Guest** | View/search public articles and pages | Create articles, access internal pages |
| **ASN** | Create/edit own drafts, submit for review | Publish, approve/reject, manage users |
| **Admin** | Review/approve/reject/publish articles, manage users/categories/tags, access audit logs, configure settings | — |

---

## Article Statuses

```
draft → submitted → approved → published
                 ↘ rejected (with admin note/reason)
```

**Workflow:**
1. ASN creates a draft
2. ASN submits for review
3. Admin approves or rejects (rejection requires a reason)
4. Admin publishes approved articles
5. Published articles are visible to Guests

---

## MVP Scope

Implement:
- Authentication and role-based access control
- Article CRUD with submission workflow
- Admin review workflow (approve/reject/publish)
- Public article listing and detail pages
- Category and tag management
- Basic search and filter
- Placeholder Neo4j ontology page
- Placeholder Looker Studio dashboard page (iframe-ready)

Do **not** implement Neo4j integration or Looker Studio logic yet.

---

## Security Requirements

- Validate all user input via Form Requests
- Protect against CSRF, XSS, and other top 10 OWASP security standard
- Use Laravel policies/gates for authorization
- Validate uploaded files; prevent direct access to private files
- Never commit `.env` or hardcode credentials
- Use environment variables for all secrets

---

## Coding Conventions

- Thin controllers — business logic goes in service classes
- Form Requests for all validation
- Policies/gates for authorization (no scattered role checks)
- Eloquent relationships throughout
- Named routes

---

## Git Conventions

**Commits** (conventional commits):
```
feat: add article submission workflow
fix: prevent ASN from publishing articles
refactor: simplify article status handling
```

**Branches:**
```
feat/article-review
fix/auth-permission
docs/prd
```

---

## Tests Required

- ASN cannot publish articles
- Guest cannot access admin pages
- Admin can approve and reject submitted articles
- Rejected articles are not publicly visible
- Published articles are publicly visible

Use Laravel feature tests.

---

## Documentation

Maintain in `docs/`:
```
docs/
├── PRD.md
├── architecture.md
├── deployment.md
├── security.md
└── database-schema.md
```

Update when implementation decisions change.

---

## Delivery Priority

1. Authentication and roles
2. Article workflow
3. Admin review
4. Public article pages
5. Search and filter
6. Placeholder dashboard pages
7. Polish and security checks
