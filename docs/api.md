# API

## Authentication

The frontend consumes the backend through session-authenticated REST endpoints under `/api`.

All domain endpoints except `/api/health` and `/api/login` require an authenticated user session.

## Response Conventions

- JSON only
- decimal financial values are returned as strings
- API payloads are shaped for frontend use and do not expose Doctrine entities directly
- user-owned resources are always scoped to the authenticated user

## Bet Slips

`BetSlip` is the main application entity for user betting history.

It represents the whole bookmaker coupon, not individual selections.

The application intentionally stores:

- one free-text description of the coupon
- bookmaker snapshot data needed for historical accuracy
- optional competitions across multiple sports
- optional tags
- settlement metadata

The application intentionally does not store:

- individual coupon selections
- event-level stake splits
- persisted predicted payout
- persisted realised profit
- persisted ROI

### List

`GET /api/bet-slips`

Query parameters:

- `page`
- `perPage`
- `status`
- `bookmaker`
- `stakeType`
- `timing`
- `competition`
- `tag`
- `placedFrom`
- `placedTo`
- `search`

Sorting:

- `placedAt DESC`
- `id DESC`

Response:

```json
{
  "items": [
    {
      "id": 123,
      "name": "Weekend accumulator",
      "description": "Real Madrid win, Bayern over 1.5, Alcaraz wins a set",
      "bookmaker": {
        "id": 1,
        "name": "Fortuna",
        "slug": "fortuna",
        "emoji": "🟢",
        "defaultTaxPercent": "12.00"
      },
      "stake": "50.00",
      "totalOdds": "5.820",
      "stakeType": "cash",
      "timing": "pre_match",
      "status": "pending",
      "selectionsCount": 3,
      "taxPercent": "12.00",
      "predictedPayout": "256.08",
      "actualPayout": null,
      "profit": null,
      "roi": null,
      "placedAt": "2026-07-31T18:00:00+02:00",
      "settledAt": null,
      "competitions": [],
      "tags": []
    }
  ],
  "pagination": {
    "page": 1,
    "perPage": 20,
    "total": 1
  }
}
```

### Details

`GET /api/bet-slips/{id}`

Returns the same shaped object as the list item with full description, relations, and calculated presentation values.

### Create

`POST /api/bet-slips`

Payload:

```json
{
  "name": "Weekend accumulator",
  "description": "Real Madrid win, Bayern over 1.5 team goals, Alcaraz wins a set",
  "bookmakerId": 1,
  "stake": "50.00",
  "totalOdds": "5.820",
  "stakeType": "cash",
  "timing": "pre_match",
  "selectionsCount": 3,
  "taxPercent": "12.00",
  "competitionIds": [10, 11],
  "tagIds": [3, 4],
  "placedAt": "2026-07-31T18:00:00+02:00"
}
```

Rules:

- `description` is required and trimmed
- `bookmakerId` must resolve to an active bookmaker
- `taxPercent` is stored on the bet slip as a historical snapshot
- competitions must be active and visible to the user, unless they are already historically assigned on the edited slip
- tags must be system tags or belong to the current user

### Update

`PUT /api/bet-slips/{id}`

Uses the same payload and validation rules as create.

### Delete

`DELETE /api/bet-slips/{id}`

Deletes only the current user’s bet slip.

### Settle

`PATCH /api/bet-slips/{id}/settle`

Payload:

```json
{
  "status": "won",
  "actualPayout": "256.08",
  "settledAt": "2026-07-31T22:15:00+02:00"
}
```

Settlement rules:

- `pending`: clears `actualPayout` and `settledAt`
- `won`: `actualPayout` must be positive; if omitted, the backend uses the calculated predicted payout
- `lost`: forces `actualPayout = 0`
- `void`: defaults `actualPayout` to the stored stake, but allows override
- `cashout` and `partial_cashout`: require non-negative `actualPayout`
- settled statuses require `settledAt`; if omitted, the backend sets the current timestamp

## Related Endpoints Used By Bet Slip Forms

- `GET /api/bookmakers`
- `GET /api/competitions`
- `GET /api/competitions?sport={id}`
- `GET /api/tags`
