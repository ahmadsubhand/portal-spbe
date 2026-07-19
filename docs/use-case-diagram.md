# Use Case Diagram

## Purpose

This diagram captures the main interactions between the three actors defined in the PRD: `Guest`, `ASN`, and `Admin`.

## Diagram

```mermaid
flowchart LR
    Guest[Guest]
    ASN[ASN]
    Admin[Admin]

    subgraph PortalSPBE[Portal SPBE System]
        UC1((Browse Published Articles))
        UC2((Search And Filter Articles))
        UC3((View Article Detail))
        UC4((Login))
        UC5((Create Draft Article))
        UC6((Edit Own Draft))
        UC7((Submit Article For Review))
        UC8((Review Submitted Articles))
        UC9((Approve Article))
        UC10((Reject Article With Reason))
        UC11((Publish Approved Article))
        UC12((Manage Categories))
        UC13((Manage Tags))
        UC14((Manage Users))
        UC15((View Audit Logs))
        UC16((Browse Regulations))
        UC17((Browse Layanan))
        UC18((Browse Forum Diskusi))
        UC19((View Forum Discussion Detail))
        UC20((Access Neo4j Placeholder Page))
        UC21((Access Looker Studio Placeholder Page))
    end

    Guest --> UC1
    Guest --> UC2
    Guest --> UC3
    Guest --> UC16
    Guest --> UC17
    Guest --> UC18
    Guest --> UC19

    ASN --> UC4
    ASN --> UC5
    ASN --> UC6
    ASN --> UC7
    ASN --> UC16
    ASN --> UC17
    ASN --> UC18
    ASN --> UC19
    ASN --> UC20
    ASN --> UC21

    Admin --> UC4
    Admin --> UC8
    Admin --> UC9
    Admin --> UC10
    Admin --> UC11
    Admin --> UC12
    Admin --> UC13
    Admin --> UC14
    Admin --> UC15
    Admin --> UC16
    Admin --> UC17
    Admin --> UC18
    Admin --> UC19
    Admin --> UC20
    Admin --> UC21

    UC7 -. requires .-> UC6
    UC9 -. part of .-> UC8
    UC10 -. part of .-> UC8
    UC11 -. follows .-> UC9
```

## Scope Notes

- `Guest` is not modeled as a persisted user record.
- `Login` applies to authenticated internal users, especially ASN and Admin.
- Public `Regulasi`, `Layanan`, and `Forum Diskusi` pages are included because they are part of the approved initial design scope.
- Placeholder page access is included because it is explicitly in MVP scope, even though external integrations are not.
- `Dashboard` is treated as an integration-oriented placeholder, while `Basis Pengetahuan` is treated as a future-feature placeholder.
- Review, approval, rejection, and publishing are separated because the PRD treats them as distinct workflow actions with different rules.
