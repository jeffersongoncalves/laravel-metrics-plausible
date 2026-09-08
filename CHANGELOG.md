# Changelog

All notable changes to this project will be documented in this file.

## v1.0.0 - 2026-09-08

First release.

Laravel client for the Plausible Analytics Stats API v2, plus the Sites and Goals endpoints of the v1 API. Works with Plausible Cloud and self-hosted Community Edition.

- `Plausible` facade: `aggregate`, `timeseries`, `breakdown` and the ready-made `pages`, `entryPages`, `exitPages`, `sources`, `channels`, `utm`, `countries`, `regions`, `cities`, `devices`, `browsers`, `operatingSystems`, `goalConversions`, `customEvents`, `realtimeVisitors`
- `StatsQuery` builder for custom queries: metrics, dimensions, filters, ordering, pagination, imports and time labels
- Sites and goals management: `sites`, `site`, `createSite`, `deleteSite`, `goals`, `createEventGoal`, `createPageGoal`, `deleteGoal`
- Typed exceptions: `AuthenticationException` (401/403, missing key or site id), `RateLimitException` (429), `PlausibleException`
- `Metric`, `Dimension`, `DateRange` and `GoalType` enums
- Settings stored via spatie/laravel-settings, seeded from `PLAUSIBLE_API_KEY`, `PLAUSIBLE_SITE_ID` and `PLAUSIBLE_BASE_URL`

Requires PHP 8.2+ and Laravel 11, 12 or 13.

## [Unreleased]
