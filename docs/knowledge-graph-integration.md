# Knowledge Graph Integration Plan

## Purpose

This document defines the planned integration path for the public `Basis Pengetahuan` page when the knowledge graph stack is ready. The current Laravel page remains a placeholder, but it should evolve toward an API-backed graph visualization instead of a raw iframe embed.

The knowledge graph developer has indicated the expected external stack:

- Neo4j Aura as the graph database
- NestJS as the API layer that connects to Neo4j Aura
- Laravel as the public portal and user-facing UI surface

## Recommended Architecture

Use Laravel as the page surface and NestJS as the graph data provider.

```text
Browser
  |
  v
Laravel /basis-pengetahuan
  |
  v
Graph-ready Blade UI
  |
  v
NestJS Knowledge Graph API
  |
  v
Neo4j Aura
```

Default access model: **public-filtered**.

The page may remain publicly accessible, but the NestJS API must only return graph data that is safe for public visitors. This access model is provisional until it is confirmed with the project supervisor and stakeholders.

## Why Not Raw Embed First

A raw embed is risky if the embedded page or its network calls expose graph data that has not been filtered for public access. Even if Neo4j Aura credentials are not exposed directly, users can inspect browser requests and reuse any public endpoint that serves the embedded graph.

For this project, iframe usage should be treated as a secondary option only when the embedded NestJS page is explicitly public-safe.

## Integration Responsibilities

### Laravel

- Keep `/basis-pengetahuan` as the primary public route.
- Render the graph container, page header, loading state, empty state, and error state.
- Read the graph API base URL from configuration or environment variables.
- Avoid hardcoded Neo4j credentials, API secrets, or internal service URLs in Blade templates.
- Continue showing a safe placeholder when the graph API is unavailable or not configured.

### NestJS

- Connect to Neo4j Aura using server-side environment variables.
- Own all Cypher queries and Neo4j driver usage.
- Filter graph data before it reaches the browser.
- Expose a public-safe JSON endpoint for the Laravel page.
- Apply CORS restrictions and rate limiting.

### Neo4j Aura

- Store ontology, topic, document, and relationship data.
- Keep credentials outside the Laravel repository and outside frontend code.
- Expose data only through the NestJS API layer.

## Initial API Contract

The first NestJS endpoint should be:

```http
GET /knowledge/graph
```

Minimal response shape:

```json
{
  "nodes": [
    {
      "id": "spbe",
      "label": "SPBE",
      "type": "topic",
      "summary": "Sistem Pemerintahan Berbasis Elektronik",
      "url": null
    }
  ],
  "edges": [
    {
      "id": "edge-1",
      "source": "spbe",
      "target": "arsitektur-spbe",
      "label": "terkait"
    }
  ],
  "meta": {
    "visibility": "public",
    "generated_at": "2026-06-23T00:00:00Z"
  }
}
```

Contract rules:

- `nodes[].id` must be stable and unique.
- `nodes[].type` is optional but strongly recommended because Laravel uses it to build dynamic filters.
- `edges[].id` must be stable and unique.
- `edges[].source` and `edges[].target` must reference valid `nodes[].id` values.
- Public responses must not include credentials, raw Cypher queries, private storage paths, unpublished document metadata, or internal-only labels.
- Empty graph responses should return valid JSON with empty `nodes` and `edges` arrays.
- API errors should not expose stack traces or internal Neo4j details.

## Frontend Data Assumptions

The exact graph components are not finalized yet. Laravel must therefore avoid hardcoded filters such as only `Topik` and `Dokumen`.

Expected frontend behavior:

- Always show a `Semua relasi` filter.
- Build additional filter buttons dynamically from unique `nodes[].type` values.
- Use a fallback type such as `other` or display label `Lainnya` when a node has no type.
- Convert machine labels into readable labels, for example `domain_spbe` or `domain-spbe` to `Domain Spbe`.
- Keep rendering graph data as long as each node has an `id` and each edge references valid node IDs.

Anticipated data cases:

- Node types may change or expand, such as `regulasi`, `domain_spbe`, `indikator`, `instansi`, `aplikasi`, or `layanan`.
- Some nodes may be missing `type`, `summary`, or `url`.
- API responses may contain zero nodes.
- Some edges may reference nodes that are not present in the response.
- Node labels may be long, especially for regulation titles, agency names, and service names.
- Large graph responses may become hard to read and may need search, pagination, focus-node loading, or server-side limits.
- Type filters may not be enough; future filters may need relation type, keyword, SPBE domain, year, or agency.
- Public and internal graph data may need to be split after stakeholder review.
- The API contract should remain stable even if Neo4j labels and relationship names evolve.

## Security Requirements

- Neo4j Aura URI, username, password, and database name must live in the NestJS environment.
- Laravel may store only the public graph API URL, for example `KNOWLEDGE_GRAPH_API_URL`.
- NestJS CORS should allow only approved Laravel origins.
- Public graph endpoints should use rate limiting.
- Filtering must happen in NestJS before data is returned, not only in frontend rendering.
- If stakeholder review later decides that graph data is not public, the route and API must move behind authentication.

## Implementation Phases

### Phase 1: Documentation And UI Readiness

- Maintain this integration plan in `docs/`.
- Keep Laravel's `Basis Pengetahuan` page as a placeholder until the graph API exists.
- Prepare the Blade page to become graph-ready with loading, empty, error, and disconnected states.
- Use mock or sample graph data only when needed for UI development.

### Phase 2: NestJS API Readiness

- Build the NestJS endpoint `GET /knowledge/graph`.
- Configure Neo4j Aura credentials through environment variables.
- Implement public-safe Cypher queries.
- Return JSON using the initial API contract.
- Add rate limiting, CORS, and safe error responses.

### Phase 3: Laravel Integration

- Add Laravel configuration for `KNOWLEDGE_GRAPH_API_URL`.
- Load graph data from the configured endpoint.
- Render graph nodes and edges in the Laravel page.
- Keep fallback UI when the endpoint is unset, unreachable, or returns an empty graph.

### Phase 4: Stakeholder Hardening

- Confirm which graph data is public, internal, or admin-only.
- Split public and internal graph endpoints if required.
- Add monitoring or logging for API failures.
- Update this document after access rules and data ownership are finalized.

## Test Scenarios

- Guest can open `/basis-pengetahuan` without authentication.
- When the API URL is not configured, the page shows a safe placeholder.
- When the API returns an empty graph, the page shows an empty state.
- When the API fails, the page shows a safe error state without stack traces or internal URLs.
- When the API succeeds, the page renders graph nodes and edges from the JSON response.
- Filter buttons are generated from unique `nodes[].type` values instead of hardcoded graph component names.
- NestJS does not return internal-only graph fields in public responses.
- CORS blocks unapproved origins from reading the public graph endpoint.
- Neo4j credentials never appear in Laravel views, browser source, JavaScript bundles, or API responses.

## Open Decisions

- Final stakeholder-approved access model for graph data.
- Whether Laravel should fetch the NestJS API directly from the browser or through a Laravel backend proxy.
- Which graph visualization library should be used in the Laravel frontend.
- Whether internal ASN/Admin graph views need more data than the public graph.
