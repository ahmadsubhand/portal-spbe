# Database Schema And ERD

## Purpose

This document translates the MVP requirements in [PRD.md](PRD.md) into a practical relational schema for Laravel. It is intentionally scoped to the current MVP and excludes real Neo4j and Looker Studio integration tables.

## Design Notes

- Authorization currently uses a string-based `users.role` column plus policies.
- Artikels belong to one author and can belong to one category.
- Tags are many-to-many with artikels through `artikel_tag`.
- Workflow/admin traceability currently uses `audit_logs`.
- Public visibility is controlled by `status = approved`, `is_draft = false`, and `published_at IS NOT NULL`.
- Forum topic visibility is controlled by `topics.is_active = true` for public exposure, with `visibility` controlling open vs. closed access.
- Placeholder pages are represented in the application layer and do not require dedicated domain tables in the MVP.
- Approved public informational pages such as `Regulasi`, `Layanan`, and the initial `Forum Diskusi` presentation can begin in the application layer without requiring extra domain tables if they are still informational/static in the MVP slice.

## Core Tables

### `users`

- `id`
- `role` (`admin`, `asn`, `guest`)
- `name`
- `email`
- `password`
- `avatar_path` (nullable, public profile avatar storage path)
- `nip` (nullable, unique 18-digit portal profile identifier)
- `phone_number` (nullable)
- `unit_kerja` (nullable)
- `jabatan` (nullable)
- `bidang_keahlian` (nullable)
- `bio` (nullable)
- `is_active`
- `created_at`
- `updated_at`

### `categories`

- `id`
- `name`
- `slug`
- `description`
- `created_by`
- `updated_by`
- `created_at`
- `updated_at`

### `tags`

- `id`
- `name`
- `slug`
- `created_by`
- `updated_by`
- `created_at`
- `updated_at`

### `artikels`

- `id`
- `user_id`
- `category_id`
- `judul`
- `konten`
- `status`
- `is_draft`
- `review_note`
- `submitted_at`
- `approved_at`
- `published_at`
- `created_at`
- `updated_at`

Current status values:

- `pending`
- `approved`
- `rejected`

Note:

- Draft state is represented by `is_draft = true`
- Submitted state is represented by `status = pending` and `is_draft = false`
- Published state is represented by `status = approved` and `published_at != null`
- Unpublishing preserves the approved article and clears `published_at`.

### `artikel_tag`

- `artikel_id`
- `tag_id`

### `audit_logs`

- `id`
- `user_id`
- `action`
- `subject_type`
- `subject_id`
- `metadata`
- `created_at`

This supports the PRD requirement for admin-accessible audit logs.

## Application-Layer Public Pages

The approved Figma includes public pages for:

- `Regulasi`
- `Layanan`
- `Forum Diskusi`
- `Basis Pengetahuan`
- `Dashboard`

Database note:

- `Artikel` is expected to be backed by the article workflow domain.
- Public design-driven sections may start without dedicated tables, but should move to real managed data when they become operational modules.
- `Dashboard` may remain an integration-oriented placeholder initially because its likely next step is an embed/configuration integration rather than a full relational module.
- `Basis Pengetahuan` may remain a future-feature placeholder initially because its likely next step is a larger graph-backed implementation rather than a simple table-backed replacement.
- `Regulasi`, `Layanan`, and `Forum Diskusi` do not require dedicated tables yet unless their content is promoted from static/design-driven content into managed records.

### `settings`

- `id`
- `key`
- `value`
- `updated_by`
- `created_at`
- `updated_at`

## ERD

```mermaid
erDiagram
    USERS ||--o{ ARTIKELS : authors
    USERS ||--o{ AUDIT_LOGS : generates
    USERS ||--o{ SETTINGS : updates
    USERS ||--o{ TOPICS : starts
    USERS ||--o{ REPLIES : writes
    USERS ||--o{ TOPIC_LIKES : likes_topic
    USERS ||--o{ REPLY_LIKES : likes_reply
    USERS ||--o{ PROFILE_DOCUMENTS : owns_documents
    USERS ||--o{ ACCOUNT_DEACTIVATION_REQUESTS : requests_deactivation

    CATEGORIES ||--o{ ARTIKELS : classifies
    ARTIKELS ||--o{ ARTIKEL_TAG : has
    TAGS ||--o{ ARTIKEL_TAG : maps

    TOPICS ||--o{ REPLIES : contains
    TOPICS ||--o{ TOPIC_LIKES : has_likes
    REPLIES ||--o{ REPLIES : parent_child
    REPLIES ||--o{ REPLY_LIKES : has_likes

    USERS {
        bigint id PK
        string role
        string name
        string email UK
        string password
        string avatar_path
        string nip
        string phone_number
        string unit_kerja
        string jabatan
        string bidang_keahlian
        text bio
        boolean is_active
        timestamp created_at
        timestamp updated_at
    }

    CATEGORIES {
        bigint id PK
        string name
        string slug UK
        text description
        timestamp created_at
        timestamp updated_at
    }

    TAGS {
        bigint id PK
        string name
        string slug UK
        timestamp created_at
        timestamp updated_at
    }

    ARTIKELS {
        bigint id PK
        bigint user_id FK
        bigint category_id FK
        string judul
        text konten
        string status
        boolean is_draft
        text review_note
        timestamp submitted_at
        timestamp approved_at
        timestamp published_at
        timestamp created_at
        timestamp updated_at
    }

    ARTIKEL_TAG {
        bigint artikel_id FK
        bigint tag_id FK
    }

    AUDIT_LOGS {
        bigint id PK
        bigint user_id FK
        string action
        string subject_type
        bigint subject_id
        json metadata
        timestamp created_at
    }

    SETTINGS {
        bigint id PK
        string key UK
        text value
        bigint updated_by FK
        timestamp created_at
        timestamp updated_at
    }

    TOPICS {
        bigint id PK
        bigint user_id FK
        string title
        string slug UK
        text content
        string category
        string visibility
        integer views_count
        timestamp created_at
        timestamp updated_at
    }

    REPLIES {
        bigint id PK
        bigint topic_id FK
        bigint parent_id FK
        bigint user_id FK
        text content
        timestamp created_at
        timestamp updated_at
    }

    TOPIC_LIKES {
        bigint id PK
        bigint topic_id FK
        bigint user_id FK
        timestamp created_at
        timestamp updated_at
    }

    REPLY_LIKES {
        bigint id PK
        bigint reply_id FK
        bigint user_id FK
        timestamp created_at
        timestamp updated_at
    }

    PROFILE_DOCUMENTS {
        bigint id PK
        bigint user_id FK
        string type
        string title
        string path
        string original_name
        string mime_type
        unsigned_bigint size
        timestamp created_at
        timestamp updated_at
    }

    ACCOUNT_DEACTIVATION_REQUESTS {
        bigint id PK
        bigint user_id FK
        text reason
        string status
        text admin_note
        bigint reviewed_by FK
        timestamp reviewed_at
        timestamp created_at
        timestamp updated_at
    }
```

## Relationship Summary

- One user can author many articles.
- One category can contain many articles.
- One article can have many tags through `artikel_tag`.
- One user can generate many audit log records.
- One user can start many forum topics.
- Active forum topics use `topics.is_active = true`; inactive topics are retained but excluded from public queries and interactions.
- One user can write many forum replies.
- Active replies use `replies.is_active = true`; inactive replies are retained but excluded from public threads and reply interaction endpoints.
- One user can like many forum topics or replies.
- One user can own many profile documents.
- One user can submit many account deactivation requests.

## Laravel Modeling Guidance

- `User` hasMany `Artikel`
- `Artikel` belongsTo `User` as author
- `Artikel` belongsTo `Category`
- `Artikel` belongsToMany `Tag`
- `Category` hasMany `Artikel`
- `Tag` belongsToMany `Artikel`
- `User` hasMany `Topic`
- `User` hasMany `Reply`
- `Topic` belongsTo `User`
- `Topic` hasMany `Reply`
- `Reply` belongsTo `Topic`
- `Reply` belongsTo `User`
- `Reply` hasMany `Reply` as children (referencing `parent_id`)
- `User` hasMany `ProfileDocument`
- `ProfileDocument` belongsTo `User`
- `User` hasMany `AccountDeactivationRequest`
- `AccountDeactivationRequest` belongsTo `User`

## Constraints To Enforce In Code

- Only `published` articles are queryable in public routes.
- Only article owners can edit their own drafts and rejected articles, depending on policy rules.
- Only Admin can approve, reject, publish, and manage taxonomy.
- Rejection must require a non-empty review note.
- Submission should only be allowed from `draft`.
- Publish should only be allowed from `approved`.

# Account Deactivation Requests

`account_deactivation_requests` stores user-submitted account deactivation requests. Admin approval sets `users.is_active` to `false`; it does not delete the user, articles, forum topics, or audit history.

# Profile Documents

`profile_documents` stores metadata for optional professional supporting documents. Files are stored privately outside `public/storage`; access is limited to the document owner.

Allowed categories are competency certificates, training certificates, assignment letters, and other relevant supporting documents. Sensitive identity documents such as KTP are outside the portal scope.
