# Security

## Purpose

This document defines the security baseline for Portal SPBE during MVP implementation. It follows the requirements in `AGENTS.md` and the PRD, with emphasis on Laravel-native protections, role-based access control, and safe handling of unpublished content.

## Security Goals

- Prevent unauthorized access to internal/admin functionality
- Ensure unpublished or rejected articles are not publicly exposed
- Validate all user input
- Protect secrets and infrastructure configuration
- Reduce common web risks aligned with OWASP Top 10 concerns
- Preserve traceability for sensitive content workflow actions

## Core Security Principles

- Default deny for privileged actions
- Centralized authorization using policies/gates
- Validation on every write path using Form Requests
- Least privilege for users and infrastructure
- Secure-by-default rendering and storage patterns
- Audit important content workflow actions

## Authentication

The application must support authenticated internal users and unauthenticated public guests.

Requirements:

- use Laravel authentication mechanisms
- hash passwords using Laravel defaults
- protect authenticated routes with auth middleware
- expire or invalidate sessions on logout
- use secure session configuration outside local development
- provision ASN and admin accounts through authorized administrators; public self-registration is disabled
- do not use consumer social login providers such as Google or Facebook for internal portal access
- prefer an approved government/institution identity provider if centralized SSO is introduced later

Current additional protections implemented:

- login throttling from Laravel auth stack
- inactive users are blocked from logging in
- `/register` only explains the access-request process and cannot create an account

## Authorization

Authorization is one of the highest-risk areas for this system.

Rules:

- Guest must not access internal/admin pages
- ASN must not approve, reject, publish, or manage users
- ASN must only manage their own articles where policy allows
- Admin controls review, publishing, taxonomy, users, settings, and audit visibility

Implementation requirements:

- use Laravel policies/gates
- avoid scattered inline role checks in controllers/views
- authorize both page access and state-changing actions
- enforce workflow transitions server-side even if UI hides actions

## Input Validation

All data submitted by users must be validated using Laravel Form Requests.

Examples:

- article create/update
- article submission
- article rejection reason
- category/tag management
- user management
- file uploads

Validation should cover:

- required fields
- string length
- allowed status transitions
- file type and size
- slug uniqueness where applicable

## Output Encoding And XSS Protection

Blade escaped output should be the default rendering mechanism.

Rules:

- use escaped Blade output by default
- avoid raw HTML rendering unless explicitly sanitized
- sanitize or constrain rich text inputs before rendering if rich editor support is added
- review any admin-entered rejection notes before allowing HTML output

If rich content is introduced later, a sanitization strategy must be documented and implemented.

## CSRF Protection

Laravel CSRF protection must remain enabled for state-changing web requests.

Requirements:

- use standard Blade forms with CSRF tokens
- protect all POST, PUT, PATCH, and DELETE operations
- do not bypass CSRF middleware without explicit justification

## File Upload Security

If article attachments or media uploads are added:

- validate MIME type and file extension
- enforce size limits
- store sensitive files outside the public web root where appropriate
- serve private files through authorized application endpoints
- generate safe filenames and paths
- do not trust client-provided filenames

## Data Access Security

### Public Data

- only `published` articles are exposed in public queries
- only active forum topics are exposed in public queries and interaction endpoints
- only active replies are exposed in public discussion threads and reply interaction endpoints
- approved public informational pages such as `Regulasi`, `Layanan`, and `Forum Diskusi` may be exposed without authentication if intended by the approved design and product scope
- when public pages later transition from static/design-driven content to managed content, the same publication, validation, and authorization rules appropriate to that module must be applied before exposing records publicly

### Internal Data

- drafts, submitted, approved, and rejected articles must be access-controlled
- review history should only be visible to authorized users
- audit logs should be admin-only

## Secrets Management

Requirements:

- never commit `.env`
- never hardcode credentials or tokens
- use environment variables for secrets
- rotate secrets if exposure is suspected

Examples of sensitive values:

- database credentials
- mail credentials
- API keys
- app secret key

## Database Security

- use least-privilege DB users
- separate environments where possible
- avoid running production with overly privileged DB accounts
- apply migrations through controlled deployment steps
- back up data regularly in production

## Session And Cookie Security

For non-local environments:

- set secure cookies over HTTPS
- set `APP_DEBUG=false`
- use appropriate same-site cookie behavior
- review session lifetime settings

## Error Handling

Requirements:

- disable debug mode outside local development
- avoid exposing stack traces to end users
- log security-relevant failures appropriately

Sensitive details must not leak through error pages.

## Audit And Traceability

The following actions should be logged or otherwise traceable:

- article submission
- article approval
- article rejection
- article publishing
- article unpublishing and deletion
- forum topic activation, deactivation, and deletion
- forum reply activation, deactivation, and deletion
- admin forum moderation pages and actions
- user management changes
- settings changes

Current implementation:

- `audit_logs` for administrative actions and article workflow actions

## Dependency And Supply Chain Security

- keep Composer dependencies updated
- keep npm dependencies updated
- review security advisories regularly
- avoid unnecessary packages

New packages should only be added when justified by clear feature needs.

## Secure Coding Rules For This Project

- thin controllers
- business logic in services
- authorization in policies/gates
- Form Requests for validation
- named routes
- Eloquent relationships instead of ad hoc query logic where possible

These rules reduce inconsistency and help prevent missed authorization/validation paths.

## Security Testing Requirements

Minimum required feature tests from the project rules:

- ASN cannot publish articles
- Guest cannot access admin pages
- Admin can approve and reject submitted articles
- Rejected articles are not publicly visible
- Published articles are publicly visible

Recommended additional security-focused tests:

- ASN cannot edit another ASN user's draft
- Admin rejection requires a reason
- only approved articles can be published
- public routes cannot access unpublished content by slug/id
- public informational routes do not unintentionally expose internal-only management actions

## Security Checklist For Implementation

- policies created for protected resources
- all write endpoints use Form Requests
- guest/admin/asn route boundaries enforced
- unpublished content excluded from public queries
- raw HTML output avoided or sanitized
- uploads validated and access-controlled
- `.env` ignored by Git
- debug disabled outside local

## Incident Response Baseline

If a security issue is suspected:

1. identify affected environment
2. restrict access if needed
3. rotate compromised credentials
4. review logs and audit trails
5. patch the issue
6. validate the fix with tests

## Current Status

Current application state already includes:

- role-based route protection
- policy checks for artikel/category/tag/user/audit/settings
- Form Requests on write paths for workflow, taxonomy, users, and settings
- public filtering so only published artikel records are exposed
- admin-only audit log access
- inactive-account login blocking

A complete security assessment based on the OWASP Top 10 guidelines has been conducted. Detailed findings and configurations are recorded in the new [security-assessment.md](security-assessment.md) file.
# Account Retention

Users cannot delete their own accounts directly. They must submit a reasoned deactivation request confirmed with their current password. Admin decisions are audited, and approval disables login while retaining organizational content. Permanent deletion is restricted to empty accounts without articles or forum topics.

Professional profile documents are optional and stored on the private filesystem disk. Direct public URLs are not exposed; download and deletion require ownership authorization. The portal must not request or store KTP, KK, NPWP, health records, or unrelated sensitive identity documents.
