# Product Requirements Document

## Product Name

Portal SPBE

## Purpose

Portal SPBE is a Laravel-based knowledge and documentation portal for a government SPBE environment. It provides controlled internal article creation and review while exposing approved public knowledge content to external visitors. The first release should prioritize governance, clear publication workflow, and secure public access to published documentation.

## Background

The portal is intended to centralize SPBE knowledge assets in one system with distinct responsibilities between content contributors and administrators. It must support internal drafting and review without exposing unpublished content publicly. Future integrations for ontology visualization and analytics are planned, but the MVP should only provide placeholder pages for them.

The approved initial product design also includes public-facing sections for regulations, layanan, and forum diskusi. These are inside scope for the MVP even though they were not captured in the earliest draft of this PRD.

The initial public pages may begin as design-aligned or informational implementations. Over time, content that is logically managed by the system should transition to real application data instead of remaining static presentation content.

## Goals

- Provide a secure article publishing workflow with role-based permissions
- Enable ASN users to draft and submit knowledge articles for review
- Enable Admin users to review, approve, reject, and publish content
- Expose only published articles to public visitors
- Support content organization through categories, labels, search, and filters
- Provide the approved public informational/navigation sections from the initial Figma design
- Lay structural groundwork for future Neo4j and Looker Studio integrations

## Non-Goals

- No live Neo4j integration in the MVP
- No Looker Studio data integration logic in the MVP
- No microservices architecture
- No unnecessary abstraction beyond standard Laravel service, request, and policy layers

## Primary Users

### Guest

Public visitor who can browse and search published content only.

### ASN

Internal contributor who can create, edit, and submit their own article drafts.

### Admin

Internal administrator who manages review, publishing, taxonomy, users, and selected system settings.

## User Roles And Permissions

| Role | Allowed Actions | Restricted Actions |
|------|------------------|--------------------|
| Guest | View public article list and detail pages, use search and filters | Cannot create articles or access admin/internal pages |
| ASN | Create drafts, edit own drafts, submit drafts for review | Cannot publish, approve, reject, or manage users |
| Admin | Approve, reject, publish articles, manage categories/labels/users/settings, access audit logs | No role-specific restriction within MVP scope |

## Core User Stories

### Guest

- As a Guest, I want to browse published articles so I can access SPBE knowledge without signing in.
- As a Guest, I want to search and filter articles so I can find relevant information quickly.
- As a Guest, I want to browse public regulations so I can review SPBE-related legal references.
- As a Guest, I want to browse layanan information so I can find public and governmental service references.
- As a Guest, I want to browse forum diskusi pages so I can read knowledge-sharing discussions available in the approved public-facing design.

### ASN

- As an ASN user, I want to create a draft article so I can prepare content before submission.
- As an ASN user, I want to edit my own draft so I can improve it before review.
- As an ASN user, I want to submit a draft for review so an Admin can evaluate it.

### Admin

- As an Admin, I want to review submitted articles so I can control publication quality.
- As an Admin, I want to reject an article with a reason so the contributor understands what must be fixed.
- As an Admin, I want to publish approved content so it becomes visible to the public.
- As an Admin, I want to monitor and moderate forum discussions so I can deactivate or remove inappropriate topics.
- As an Admin, I want to deactivate or remove forum replies so discussion threads stay compliant.
- As an Admin, I want to manage categories and labels so content stays organized.
- As an Admin, I want the public `Artikel` section to show real published content from the article workflow instead of remaining static design content.
- As an Admin, I want public sections that later become managed modules to pull from real application data so the portal stays accurate and maintainable.

## Functional Requirements

### 1. Authentication And Authorization

- The system must support authenticated and unauthenticated access paths.
- The system must enforce role-based access control for Guest, ASN, and Admin users.
- Authorization must use Laravel policies and/or gates, not scattered inline role checks.
- Guests must be blocked from admin-only and internal content management pages.

### 2. Article Management

- ASN users must be able to create article drafts.
- ASN users must be able to edit their own drafts.
- Article data must support category and label assignment.
- Article inputs must be validated using Laravel Form Requests.

### 3. Article Workflow

Allowed status lifecycle:

```text
draft -> submitted -> approved -> published
                 -> rejected
```

Workflow rules:

1. New ASN-authored content starts as `draft`.
2. ASN can submit a draft for review, changing status to `submitted`.
3. Admin can approve submitted content, changing status to `approved`.
4. Admin can reject submitted content, changing status to `rejected`.
5. Rejection must include an admin note or reason.
6. Only Admin can publish approved content, changing status to `published`.
7. Rejected and unpublished content must not be publicly visible.
8. Admin can unpublish a published article without deleting it, or permanently delete an article when required.

### 4. Public Knowledge Pages

- Guests must be able to view a public article listing page.
- Guests must be able to open article detail pages for published content.
- Deactivated forum topics must not appear in public listings or accept new interactions.
- Topic owners and Admin can deactivate, reactivate, or permanently delete forum topics.
- Deactivated replies must not appear in public discussion threads or accept new interactions.
- Admin can deactivate or permanently delete forum replies.
- The public listing must support basic search and filtering.
- The approved `Artikel` page in the public navigation is the intended public article surface and should be backed by real published article records.
- Real article records should originate from the internal authoring and review workflow.

### 5. Public Informational Pages

- The application must include a public `Regulasi` page aligned with the approved design direction.
- The application must include a public `Layanan` page aligned with the approved design direction.
- The application must include a public `Forum Diskusi` listing page and discussion detail page aligned with the approved design direction.
- The initial implementation of these pages may begin as design-aligned informational/static pages, but they are inside MVP scope.
- If these public sections later become managed modules, they should be backed by real application data instead of remaining static templates.

### 6. Taxonomy Management

- Admin must be able to manage categories.
- Admin must be able to manage labels.
- Categories and labels must be available for article organization and filtering.

### 7. Placeholder Pages

- The application must include a placeholder page for Neo4j ontology visualization.
- The application must include a placeholder page for a Looker Studio dashboard.
- The Looker Studio page should be iframe-ready in structure, but not integrated yet.
- The `Dashboard` placeholder is integration-oriented and should be designed so it can later be replaced primarily by a real Looker Studio embed and related access/configuration handling.
- The application may also include a placeholder `Basis Pengetahuan` page in the approved navigation until that section is wired to real knowledge structures.
- The `Basis Pengetahuan` placeholder is a future-feature placeholder and will likely require a more substantial implementation when graph-backed knowledge exploration is introduced.

### 8. Audit And Administration

- Admin must have access to user management.
- Admin must have access to audit logs for key administrative and workflow actions.
- Admin must have access to selected settings/configuration pages within MVP boundaries.
- Public support labels and contact information may be sourced from system settings instead of hardcoded templates.

## Security Requirements

- All user input must be validated through Form Requests.
- The application must use CSRF protection provided by Laravel.
- The application must protect against XSS and common OWASP Top 10 risks.
- Authorization must be enforced consistently through policies/gates.
- Uploaded files must be validated and protected from unsafe direct access.
- Secrets must be stored in environment variables.
- `.env` must never be committed.

## Technical Constraints

- Framework: Laravel
- Language: PHP
- Frontend: Blade or Inertia.js with TailwindCSS
- Database: MySQL or PostgreSQL
- Admin panel preference: Filament
- Architecture: monorepo only
- Avoid microservices and unnecessary abstractions

## Success Criteria

- ASN users can create drafts and submit them for review
- Admin users can approve, reject, and publish according to workflow rules
- Rejection requires a reason
- Guests can only see published articles
- Guests cannot access admin pages
- Search and filter work on public article pages
- Public `Regulasi`, `Layanan`, and `Forum Diskusi` pages exist according to the approved design scope
- Placeholder pages exist for Neo4j and Looker Studio
- Core admin support surfaces for taxonomy, user management, settings, and audit visibility are accessible from the internal UI

## Acceptance Criteria

### Access Control

- Guest users attempting to access admin pages are redirected or denied.
- ASN users cannot publish articles.
- Only Admin users can approve, reject, and publish.

### Workflow

- A draft can be submitted for review by its ASN author.
- A submitted article can be approved or rejected by Admin.
- A rejected article stores an Admin reason.
- An approved article can be published by Admin.
- Only published articles are visible in public pages.

### Public Experience

- Public users can browse a list of published articles.
- Public users can view article detail pages for published items.
- Public users can use basic search and filtering.
- Public users can access the approved `Regulasi`, `Layanan`, and `Forum Diskusi` pages.
- `Basis Pengetahuan` and `Dashboard` may remain placeholder pages during MVP implementation.
- The product documentation must recognize that placeholder status is not identical: `Dashboard` is intended for later embed-oriented replacement, while `Basis Pengetahuan` is intended for later feature implementation.
- Public support/contact copy may be driven by admin-managed settings.

## Required Tests

Laravel feature tests must cover at minimum:

- ASN cannot publish articles
- Guest cannot access admin pages
- Admin can approve and reject submitted articles
- Rejected articles are not publicly visible
- Published articles are publicly visible

## Delivery Priority

1. Authentication and roles
2. Article workflow
3. Admin review
4. Public article pages
5. Public informational pages from approved design
6. Search and filter
7. Placeholder dashboard pages
8. Polish and security checks

## Open Implementation Decisions

These decisions can be finalized during implementation as long as they stay within project constraints:

- Blade or Inertia.js for the UI layer
- MySQL or PostgreSQL for the primary database
- Specific Filament structure for admin pages
- Exact audit log implementation approach
- Exact long-term behavior of `Forum Diskusi` beyond the approved initial page scope
- Whether `Basis Pengetahuan` remains a standalone section or later converges with the article knowledge surface
- Which public informational pages should later be promoted from design-driven pages into fully managed data modules
