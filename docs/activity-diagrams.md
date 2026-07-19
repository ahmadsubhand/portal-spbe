# Activity Diagrams

## Purpose

These diagrams model the key workflows implied by the MVP requirements in the PRD. They focus on the main operational scenarios rather than every minor UI interaction.

## Scenario 1: Guest Browses Published Articles

```mermaid
flowchart TD
    A([Start]) --> B[Open public article listing page]
    B --> C[System loads articles with status = published]
    C --> D{Published articles found?}
    D -->|Yes| E[Show article list]
    D -->|No| F[Show empty state]
    E --> G{Guest selects an article?}
    G -->|Yes| H[System opens published article detail]
    G -->|No| I([End])
    H --> I
    F --> I
```

## Scenario 2: Guest Searches And Filters Articles

```mermaid
flowchart TD
    A([Start]) --> B[Open public article listing page]
    B --> C[Enter keyword and or choose category or tags]
    C --> D[Submit search and filter request]
    D --> E[System applies filters to published articles only]
    E --> F{Results found?}
    F -->|Yes| G[Display filtered article list]
    F -->|No| H[Display no results message]
    G --> I([End])
    H --> I
```

## Scenario 3: ASN Creates A Draft Article

```mermaid
flowchart TD
    A([Start]) --> B[ASN logs in]
    B --> C[Open create article form]
    C --> D[Enter title, excerpt, body, category, and tags]
    D --> E[Save draft]
    E --> F[System validates request with Form Request]
    F --> G{Valid input?}
    G -->|Yes| H[Create article with status = draft]
    G -->|No| I[Return validation errors]
    I --> D
    H --> J([End])
```

## Scenario 4: ASN Edits Own Draft

```mermaid
flowchart TD
    A([Start]) --> B[ASN logs in]
    B --> C[Open own article]
    C --> D{Authorized and editable?}
    D -->|No| E[System denies access]
    D -->|Yes| F[Update article fields]
    F --> G[Save changes]
    G --> H[System validates request]
    H --> I{Valid input?}
    I -->|No| J[Return validation errors]
    J --> F
    I -->|Yes| K[Persist updated draft]
    E --> L([End])
    K --> L
```

## Scenario 5: ASN Submits Article For Review

```mermaid
flowchart TD
    A([Start]) --> B[ASN logs in]
    B --> C[Open own draft article]
    C --> D[Click submit for review]
    D --> E[System checks ownership and status]
    E --> F{Owned by ASN and status = draft?}
    F -->|No| G[Reject action]
    F -->|Yes| H[Set status = submitted]
    H --> I[Store submitted_at timestamp]
    I --> J[Create article_reviews record with action = submitted]
    J --> K([End])
    G --> K
```

## Scenario 6: Admin Approves Submitted Article

```mermaid
flowchart TD
    A([Start]) --> B[Admin logs in]
    B --> C[Open submitted article queue]
    C --> D[Select submitted article]
    D --> E[Review content]
    E --> F[Click approve]
    F --> G[System checks admin role and article status]
    G --> H{Role = admin and status = submitted?}
    H -->|No| I[Reject action]
    H -->|Yes| J[Set status = approved]
    J --> K[Store approved_at and reviewed_by]
    K --> L[Create article_reviews record with action = approved]
    L --> M([End])
    I --> M
```

## Scenario 7: Admin Rejects Submitted Article

```mermaid
flowchart TD
    A([Start]) --> B[Admin logs in]
    B --> C[Open submitted article queue]
    C --> D[Select submitted article]
    D --> E[Review content]
    E --> F[Enter rejection reason]
    F --> G[Click reject]
    G --> H[System validates reason and status]
    H --> I{Reason present and status = submitted?}
    I -->|No| J[Return error]
    J --> F
    I -->|Yes| K[Set status = rejected]
    K --> L[Store reviewed_by]
    L --> M[Create article_reviews record with action = rejected and note]
    M --> N([End])
```

## Scenario 8: Admin Publishes Approved Article

```mermaid
flowchart TD
    A([Start]) --> B[Admin logs in]
    B --> C[Open approved article]
    C --> D[Click publish]
    D --> E[System checks admin role and status]
    E --> F{Role = admin and status = approved?}
    F -->|No| G[Reject action]
    F -->|Yes| H[Set status = published]
    H --> I[Store published_at timestamp]
    I --> J[Create article_reviews record with action = published]
    J --> K([End])
    G --> K
```

## Scenario 9: Admin Manages Categories And Tags

```mermaid
flowchart TD
    A([Start]) --> B[Admin logs in]
    B --> C[Open taxonomy management page]
    C --> D{Manage category or tag?}
    D -->|Category| E[Create, edit, or delete category]
    D -->|Tag| F[Create, edit, or delete tag]
    E --> G[System validates input]
    F --> G
    G --> H{Valid and authorized?}
    H -->|No| I[Show error]
    H -->|Yes| J[Persist taxonomy change]
    I --> K([End])
    J --> K
```

## Scenario 10: Admin Views Audit Logs

```mermaid
flowchart TD
    A([Start]) --> B[Admin logs in]
    B --> C[Open audit log page]
    C --> D[System verifies admin authorization]
    D --> E{Authorized?}
    E -->|No| F[Reject access]
    E -->|Yes| G[Load audit log entries]
    G --> H[Display logs with actor, action, subject, and timestamp]
    F --> I([End])
    H --> I
```

## Scenario Coverage Notes

- Guest scenarios cover public access and search.
- ASN scenarios cover creation, editing, and submission.
- Admin scenarios cover the full review and publication workflow plus taxonomy and audit operations.
- Public `Regulasi`, `Layanan`, and `Forum Diskusi` pages are inside the approved page scope even if some of them begin as mostly informational/design-driven pages.
- Neo4j and Looker Studio placeholders are intentionally not modeled as separate activity diagrams because, in the MVP, they are simple access pages without business workflow.
- Placeholder intent differs: `Dashboard` is expected to evolve mostly through embed/integration replacement, while `Basis Pengetahuan` is expected to require a larger future feature build.
