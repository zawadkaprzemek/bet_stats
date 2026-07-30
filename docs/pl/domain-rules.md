# Zasady domenowe

## Aktualny zakres domeny

Na dzień 30 lipca 2026 zaimplementowana domena jest jeszcze ograniczona. Kod aktualnie zawiera:

- `User`
- `Sport`
- `Competition`

Uwierzytelnianie, lista sportów i zarządzanie rozgrywkami są już obecne. Poniższe zasady opisują zarówno aktualne zachowanie domeny, jak i uzgodnione reguły dla kolejnych modułów.

## Sport

- `Sport` jest globalnym słownikiem.
- `Sport` jest tworzony i zarządzany wyłącznie przez administratorów.
- `Sport` jest dostępny dla całego systemu.
- `Sport` powinien być sortowany po `sortOrder`, a następnie po nazwie.
- `Sport` powinien pozostać możliwy do dezaktywacji przez `isActive` zamiast fizycznego usuwania w standardowych workflow.

Uwaga o obecnej implementacji:

- ta zasada jest już odzwierciedlona w API i panelu administracyjnym
- `GET /api/sports` zwraca aktywne sporty posortowane pod potrzeby frontendu

## Region

- `Region` jest globalnym słownikiem.
- `Region` jest tworzony i zarządzany wyłącznie przez administratorów.
- `Region` może oznaczać:
  - państwo
  - region sportowy
  - kontynent
  - świat
  - organizację ponadnarodową

Przykłady:

- `England`
- `Scotland`
- `Spain`
- `Europe`
- `World`
- `International`

Uwaga o obecnej implementacji:

- `Region` nie istnieje jeszcze jako encja
- obecne `Competition` używa opcjonalnego `countryCode`, co jest tymczasowym uproszczeniem, a nie docelowym modelem domenowym

## Competition

- `Competition` może być globalna albo prywatna użytkownika.
- `Competition` należy do `Sport`.
- `Competition` może opcjonalnie należeć do `Region`.
- prywatna rozgrywka musi mieć `owner` lub `user`
- globalna rozgrywka nie ma `owner`

Uwaga o obecnej implementacji:

- `Competition` już należy do `Sport`
- `Competition` już wspiera własność globalną i prywatną przez nullable `owner`
- `Competition` nie referuje jeszcze do `Region`
- unikalność jest dziś wymuszana per `sport + owner + name`

## Globalne słowniki

Każdy globalny słownik powinien zawierać pola:

- `id`
- `name`
- `slug`
- `emoji` nullable
- `sortOrder`
- `isActive`
- `createdAt`
- `updatedAt`

Ta zasada dotyczy:

- `Sport`
- `Region`
- `Bookmaker`
- globalnego `BetType`

Uwaga o obecnej implementacji:

- `Sport` jest blisko tego kształtu, ale obecnie używa `icon` zamiast `emoji`

## Dane użytkownika

Prywatne dane użytkownika zawsze muszą mieć referencję do właściciela.

Dotyczy to:

- `Competition`
- `UserBetType`
- `Bet`
- `Tag`

Zasady:

- rekordy prywatne należą dokładnie do jednego użytkownika
- rekordy globalne są widoczne systemowo
- zapytania API o dane prywatne muszą być zawsze scope'owane do uwierzytelnionego użytkownika

Uwaga o obecnej implementacji:

- zapytania o rozgrywki już działają według tej zasady, zwracając rekordy globalne oraz rekordy należące do zalogowanego użytkownika

## Umiejscowienie logiki biznesowej

- Logika biznesowa nie może znajdować się w kontrolerach.
- Kontrolery powinny pozostać cienkie i zorientowane na HTTP.
- Logika biznesowa powinna znajdować się w dedykowanych serwisach.

Przykłady docelowych serwisów:

- `BetCalculator`
- `StatisticsService`
- `CompetitionService`
- `SettlementService`

Uwaga o obecnej implementacji:

- obecny kod nadal zawiera flow tworzenia i walidacji `Competition` bezpośrednio w kontrolerze
- wraz z rozwojem domeny powinno to zostać przeniesione do dedykowanych serwisów

## Zasady API

- API zwraca tylko dane potrzebne frontendowi.
- API nie powinno wystawiać pól technicznych, jeśli frontend ich nie używa.
- API powinno jawnie kształtować odpowiedzi pod use case'y frontendu.

Uwaga o obecnej implementacji:

- projekt już używa dedykowanych view factory dla API, co jest zgodne z tą zasadą

## Zasady EasyAdmin

- EasyAdmin służy wyłącznie do globalnych słowników oraz administracji.
- EasyAdmin nie jest docelowym interfejsem dla workflow użytkownika końcowego.

Uwaga o obecnej implementacji:

- `Sport` jest zgodny z tą zasadą
- `Competition` jest obecnie również zarządzane przez EasyAdmin, ale należy to traktować jako stan przejściowy, a nie architekturę docelową
