# Roadmapa MVP

## Cel

Dostarczyć MVP, które pozwala uwierzytelnionym użytkownikom zarządzać danymi referencyjnymi związanymi z zakładami, tworzyć zakłady i przeglądać podstawowe statystyki przez frontend React oparty o Symfony REST API.

## Zasady

- dostarczaj pionowe wycinki funkcjonalne
- trzymaj reguły backendowe w serwisach
- wystawiaj tylko odpowiedzi API zorientowane na frontend
- rozróżniaj globalne słowniki od danych użytkownika
- nie przenoś workflow domenowego do EasyAdmin

## Etapy

### 1. Authentication

Zakres:

- logowanie i wylogowanie sesyjne
- endpoint aktualnego użytkownika
- ochrona tras we frontendzie
- rozdzielenie ról admina i zwykłego użytkownika

Status:

- częściowo zaimplementowane

Uwagi:

- backendowe uwierzytelnianie sesyjne istnieje
- frontendowe logowanie i chronione trasy istnieją
- rejestracja, reset hasła i szersze zarządzanie kontem nie są zaimplementowane

### 2. Sport

Zakres:

- globalny słownik sportów
- admin CRUD
- frontendowy odczyt

Status:

- w większości zaimplementowane

Uwagi:

- encja, repozytorium, endpoint API, fixtures i EasyAdmin CRUD już istnieją
- nazewnictwo pól słownikowych powinno zostać dopasowane do finalnego standardu, jeśli `icon` zostanie zastąpione przez `emoji`

### 3. Region

Zakres:

- globalny słownik regionów
- admin CRUD
- relacja z competition do region

Status:

- planowane

Uwagi:

- ten etap powinien zastąpić obecny skrót z `countryCode` tam, gdzie to właściwe

### 4. Competition

Zakres:

- globalne rozgrywki
- prywatne rozgrywki użytkownika
- zapytania uwzględniające własność
- wsparcie admina tylko dla rekordów globalnych

Status:

- częściowo zaimplementowane

Uwagi:

- obecne API wspiera listowanie i tworzenie prywatnych rozgrywek użytkownika
- encja już wspiera nullable `owner`
- wciąż brakuje relacji do regionu i wydzielenia logiki do warstwy serwisów

### 5. BetType

Zakres:

- globalne typy zakładów
- opcjonalne typy zakładów użytkownika
- jasny podział między słownikami systemowymi i prywatnymi

Status:

- planowane

### 6. Bookmaker

Zakres:

- globalny słownik bookmakerów
- admin CRUD
- zarządzanie aktywnością

Status:

- planowane

### 7. Tags

Zakres:

- tagi należące do użytkownika
- przypisywanie do zakładów
- wsparcie filtrowania

Status:

- planowane

### 8. Bet

Zakres:

- tworzenie i zarządzanie zakładami
- status rozliczenia
- powiązania z competition, bookmaker, bet type i tags
- serwisy walidacyjne i kalkulacyjne

Status:

- planowane

### 9. Statistics

Zakres:

- podsumowania ROI i zysku
- statystyki grupowane po sporcie, rozgrywkach, bookmakerze i tagu
- filtrowanie po okresie

Status:

- planowane

### 10. Dashboard

Zakres:

- widgety przeglądowe
- podsumowanie bieżących wyników
- skróty do kluczowych akcji

Status:

- planowane

## Sugerowana kolejność dostarczania

1. Dokończyć fundamenty uwierzytelniania.
2. Ustabilizować architekturę słowników przez `Sport` i `Region`.
3. Zrefaktoryzować `Competition` do backendowego flow opartego o serwisy.
4. Dodać `BetType`, `Bookmaker` i `Tags`.
5. Zaimplementować lifecycle `Bet` oraz settlement.
6. Zbudować endpointy statystyk i widoki dashboardu.
