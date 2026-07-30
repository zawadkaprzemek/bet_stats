# Architektura

## Przegląd

`bet_stats` używa architektury rozdzielonej:

- backend Symfony jako źródło logiki biznesowej, autoryzacji, persystencji i narzędzi administracyjnych
- frontend React jako aplikacja użytkownika
- MySQL jako relacyjna baza danych
- Docker Compose jako lokalne środowisko uruchomieniowe

System opiera się na backendowym modelu domenowym oraz modelu frontendowego klienta API. Frontend powinien komunikować się z backendem wyłącznie przez REST API.

## Struktura wysokiego poziomu

```text
Przeglądarka
  -> React SPA
  -> wywołania REST API z ciasteczkiem sesji

React SPA
  -> /api/*
  -> JSON po HTTP

Nginx
  -> backend Symfony

Symfony
  -> Kontrolery
  -> Serwisy aplikacyjne
  -> Repozytoria Doctrine
  -> MySQL

EasyAdmin
  -> panel administracyjny dla globalnych słowników i administracji
```

## Architektura backendu

### Odpowiedzialności

Backend odpowiada za:

- uwierzytelnianie i autoryzację
- walidację domenową
- logikę biznesową
- persystencję przez Doctrine ORM
- wystawianie endpointów REST dopasowanych do potrzeb frontendu
- panel administracyjny przez EasyAdmin

### Warstwy

Rekomendowana struktura backendu:

- `Controller` tylko do mapowania wejścia HTTP i odpowiedzi
- `Service` do logiki biznesowej i orkiestracji use case'ów
- `Repository` do logiki zapytań
- `Entity` do stanu domenowego i mapowania persystencji
- klasy `Api` lub `ViewFactory` do kształtowania odpowiedzi API

### Obowiązujące zasady

- Nie umieszczaj logiki biznesowej w kontrolerach.
- Logika biznesowa musi znajdować się w dedykowanych serwisach.
- Kontrolery powinny walidować kształt requestu, wywoływać serwisy i zwracać odpowiedzi HTTP.
- Repozytoria powinny enkapsulować logikę zapytań.
- API powinno zwracać tylko pola potrzebne frontendowi.
- Nie wystawiaj pól technicznych ani wewnętrznych, jeśli frontend ich nie używa.

### Przykładowe serwisy biznesowe

- `BetCalculator`
- `StatisticsService`
- `CompetitionService`
- `SettlementService`

Część z tych serwisów należy do architektury docelowej i nie jest jeszcze zaimplementowana w obecnym kodzie.

## Architektura frontendu

### Odpowiedzialności

Frontend odpowiada za:

- renderowanie flow użytkownika
- ochronę tras
- obsługę formularzy
- komunikację z REST API
- prezentację danych domenowych zwróconych przez backend

### Obowiązujące zasady

- Frontend komunikuje się wyłącznie przez REST API.
- TanStack Query odpowiada za pobieranie i cache'owanie danych serwerowych.
- React Hook Form i Zod odpowiadają za formularze i walidację.
- Frontend nie powinien duplikować logiki biznesowej należącej do backendu.

### Aktualny stan frontendu

Obecny frontend już używa:

- React Router do nawigacji i guardów tras
- TanStack Query do pobierania sportów i rozgrywek
- React Hook Form z Zod w logowaniu i prostych formularzach
- współdzielonego klienta API z `credentials: include`

## Architektura panelu administracyjnego

EasyAdmin jest przeznaczony do:

- zarządzania globalnymi słownikami
- funkcji administracyjnych

Nie powinien stać się głównym interfejsem użytkownika dla operacji domenowych takich jak aktywność związana z zakładami.

Uwaga o obecnym stanie:

- repozytorium udostępnia dziś zarządzanie w EasyAdmin zarówno dla `Sport`, jak i `Competition`
- zasada docelowa jest węższa: EasyAdmin powinien pozostać ograniczony do globalnych słowników i administracji

## Zasady modelu słowników

Każdy globalny słownik powinien używać wspólnego zestawu pól:

- `id`
- `name`
- `slug`
- `emoji` nullable
- `sortOrder`
- `isActive`
- `createdAt`
- `updatedAt`

Dotyczy to encji takich jak:

- `Sport`
- `Region`
- `Bookmaker`
- globalny `BetType`

Uwaga o obecnym stanie:

- `Sport` zawiera już większość wspólnych pól słownikowych
- `Sport` używa dziś `icon` zamiast `emoji`
- `Region`, `Bookmaker` i globalny `BetType` są planowane, ale jeszcze nie istnieją

## Zasady własności danych

Dane użytkownika muszą mieć jawnego właściciela, jeśli są prywatne.

Dotyczy to encji takich jak:

- `Competition`
- `UserBetType`
- `Bet`
- `Tag`

Zasada własności:

- rekord globalny: `owner` lub `user` ma wartość `null`
- rekord prywatny: `owner` lub `user` wskazuje właściciela

Uwaga o obecnym stanie:

- `Competition` już działa według tego wzorca przez relację `owner` nullable

## Zasady projektowania API

- Buduj task-oriented endpointy REST.
- Zwracaj minimalny potrzebny payload.
- Trzymaj encje backendowe oddzielone od surowej odpowiedzi API.
- Używaj dedykowanych mapperów odpowiedzi lub view factory.
- Wraz ze wzrostem domeny preferuj jawne DTO requestów lub wejścia serwisów.

Uwaga o obecnym stanie:

- kod już używa `SportViewFactory`, `CompetitionViewFactory` i `UserViewFactory`
- to jest zgodne z zasadą niewystawiania surowych encji bezpośrednio

## Migawka obecnej implementacji

Zaimplementowane dzisiaj:

- uwierzytelnianie sesyjne w Symfony
- endpointy REST dla auth, health, sports i competitions
- model widoczności rozgrywek prywatnych i globalnych
- panel EasyAdmin
- shell aplikacji React z chronionymi trasami

Jeszcze niezaimplementowane dzisiaj:

- dedykowana warstwa serwisów backendowych dla wszystkich obecnych use case'ów
- słownik regionów
- słownik bookmakerów
- moduły bet type
- moduły tags, bets, settlement, statistics i dashboard

## Kierunek architektoniczny

Docelowa architektura MVP zakłada:

1. Rozbudowę domeny przez dedykowane moduły zamiast rozrostu kontrolerów.
2. Standaryzację encji słownikowych według wspólnych zasad.
3. Wprowadzenie orkiestracji warstwy serwisów dla wszystkich operacji zapisu i nietrywialnych odczytów.
4. Ograniczenie EasyAdmin do zadań administracyjnych.
5. Utrzymanie cienkiego frontendu pod względem logiki domenowej i mocnego w integracji API oraz UX.
