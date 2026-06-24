# Mermaid Diagram - Reservasi Futsal

## 1. UML Class Diagram
```mermaid
classDiagram
    class User {
        +id
        +name
        +email
        +password
        +role
    }

    class Field {
        +id
        +name
        +slug
        +description
        +image
        +status
    }

    class Booking {
        +id
        +user_id
        +field_id
        +booking_date
        +start_time
        +end_time
        +status
        +total_price
    }

    class Payment {
        +id
        +booking_id
        +amount
        +method
        +status
        +midtrans_order_id
        +snap_token
        +paid_at
    }

    class Price {
        +id
        +field_id
        +duration
        +price
        +is_active
    }

    class AuthController
    class FieldController
    class BookingController
    class PaymentController
    class ScheduleController
    class PriceController
    class VerificationController
    class MidtransPaymentController
    class IsAdmin

    User "1" --> "0..*" Booking : hasMany
    User "1" --> "0..*" Payment : hasMany
    Field "1" --> "0..*" Booking : hasMany
    Field "1" --> "0..*" Price : hasMany
    Booking "*" --> "1" User : belongsTo
    Booking "*" --> "1" Field : belongsTo
    Booking "1" --> "0..1" Payment : hasOne
    Payment "*" --> "1" Booking : belongsTo
    Price "*" --> "1" Field : belongsTo

    AuthController ..> User : auth logic
    FieldController ..> Field : CRUD
    BookingController ..> Booking : CRUD
    BookingController ..> Field : availability check
    BookingController ..> Price : price calculation
    PaymentController ..> Payment : payment CRUD
    PaymentController ..> Booking : update status
    PaymentController ..> MidtransPaymentController : gateway flow
    ScheduleController ..> Booking : schedule lookup
    ScheduleController ..> Field : field slots
    PriceController ..> Price : CRUD
    VerificationController ..> User : email verification
    IsAdmin ..> User : role check
```

## 2. Sequence Diagram - Auth Flow
```mermaid
sequenceDiagram
    actor User
    participant FE as Frontend Vue/Inertia
    participant AC as AuthController
    participant U as User Model
    participant DB as Database

    User->>FE: Submit login/register form
    FE->>AC: POST /auth/login or /auth/register
    AC->>AC: Validate input
    AC->>U: Check/create user
    U->>DB: Query/insert user data
    DB-->>U: User record
    U-->>AC: User result
    AC-->>FE: Response + session/token
    FE-->>User: Redirect to dashboard/home
```

## 3. Sequence Diagram - Browse Lapangan
```mermaid
sequenceDiagram
    actor User
    participant FE as Frontend Vue/Inertia
    participant FC as FieldController
    participant F as Field Model
    participant DB as Database

    User->>FE: Open daftar lapangan / detail lapangan
    FE->>FC: GET /fields or /fields/{field}
    FC->>F: Fetch field data
    F->>DB: Query fields
    DB-->>F: Field records
    F-->>FC: Field data
    FC-->>FE: Render page props
    FE-->>User: Show list/detail lapangan
```

## 4. Sequence Diagram - Cek Jadwal
```mermaid
sequenceDiagram
    actor User
    participant FE as Frontend Vue/Inertia
    participant SC as ScheduleController
    participant B as Booking Model
    participant F as Field Model
    participant DB as Database

    User->>FE: Choose tanggal dan jam
    FE->>SC: GET /schedule?field_id&date
    SC->>F: Load field info
    F->>DB: Query field
    DB-->>F: Field data
    SC->>B: Check booked slots
    B->>DB: Query bookings by field/date/time
    DB-->>B: Existing bookings
    B-->>SC: Availability result
    SC-->>FE: Available / unavailable slots
    FE-->>User: Display jadwal
```

## 5. Sequence Diagram - Buat Booking
```mermaid
sequenceDiagram
    actor User
    participant FE as Frontend Vue/Inertia
    participant BC as BookingController
    participant B as Booking Model
    participant F as Field Model
    participant P as Price Model
    participant DB as Database

    User->>FE: Submit booking form
    FE->>BC: POST /bookings
    BC->>BC: Validate booking data
    BC->>F: Check field exists and status
    F->>DB: Query field
    DB-->>F: Field record
    BC->>B: Check double booking slot
    B->>DB: Query existing bookings
    DB-->>B: Conflicting bookings
    B-->>BC: Availability status
    BC->>P: Calculate price
    P->>DB: Query active price
    DB-->>P: Price record
    P-->>BC: Total price
    BC->>B: Create booking
    B->>DB: Insert booking
    DB-->>B: Booking saved
    B-->>BC: Booking created
    BC-->>FE: Booking success + next step payment
    FE-->>User: Redirect to payment page
```

## 6. Sequence Diagram - Pembayaran Midtrans
```mermaid
sequenceDiagram
    actor User
    participant FE as Frontend Vue/Inertia
    participant PC as PaymentController
    participant MP as MidtransPaymentController
    participant Pay as Payment Model
    participant B as Booking Model
    participant Mid as Midtrans API
    participant DB as Database

    User->>FE: Click bayar booking
    FE->>PC: POST /payments
    PC->>B: Load booking
    B->>DB: Query booking detail
    DB-->>B: Booking data
    PC->>MP: Create Midtrans order
    MP->>Mid: Request snap token / transaction
    Mid-->>MP: Snap token + order id
    MP-->>PC: Payment payload
    PC->>Pay: Save payment record
    Pay->>DB: Insert payment
    DB-->>Pay: Payment saved
    Pay-->>PC: Payment created
    PC-->>FE: Return snap token / payment URL
    FE-->>User: Open Midtrans checkout

    Mid-->>PC: Callback / payment notification
    PC->>Pay: Update payment status
    Pay->>DB: Update payment row
    PC->>B: Update booking status
    B->>DB: Update booking row
    PC-->>FE: Payment status updated
```

## 7. Sequence Diagram - Batalkan Booking
```mermaid
sequenceDiagram
    actor User
    participant FE as Frontend Vue/Inertia
    participant BC as BookingController
    participant B as Booking Model
    participant P as Payment Model
    participant DB as Database

    User->>FE: Klik batalkan booking
    FE->>BC: POST /bookings/{id}/cancel
    BC->>B: Load booking
    B->>DB: Query booking by id
    DB-->>B: Booking data
    BC->>P: Check payment status
    P->>DB: Query payment
    DB-->>P: Payment record
    P-->>BC: Payment status
    BC->>B: Update status cancelled
    B->>DB: Update booking row
    BC-->>FE: Cancel success
    FE-->>User: Show status dibatalkan
```

## 8. Sequence Diagram - Admin Kelola Lapangan
```mermaid
sequenceDiagram
    actor Admin
    participant FE as Frontend Vue/Inertia
    participant IM as IsAdmin Middleware
    participant FC as FieldController
    participant F as Field Model
    participant DB as Database

    Admin->>FE: Submit kelola lapangan
    FE->>IM: Request admin route
    IM->>IM: Check role admin
    IM-->>FC: Authorized request
    FC->>FC: Validate input
    FC->>F: Create/update/delete field
    F->>DB: Persist changes
    DB-->>F: Saved
    FC-->>FE: Response CRUD success
    FE-->>Admin: Refresh daftar lapangan
```

## 9. Sequence Diagram - Admin Kelola Harga
```mermaid
sequenceDiagram
    actor Admin
    participant FE as Frontend Vue/Inertia
    participant IM as IsAdmin Middleware
    participant PR as PriceController
    participant P as Price Model
    participant DB as Database

    Admin->>FE: Ubah harga lapangan
    FE->>IM: Request admin route
    IM->>IM: Check role admin
    IM-->>PR: Authorized request
    PR->>PR: Validate price data
    PR->>P: Create/update price
    P->>DB: Save price record
    DB-->>P: Price saved
    PR-->>FE: Return success
    FE-->>Admin: Show harga terbaru
```

## 10. Sequence Diagram - Update Profil User
```mermaid
sequenceDiagram
    actor User
    participant FE as Frontend Vue/Inertia
    participant PC as ProfileController
    participant U as User Model
    participant DB as Database

    User->>FE: Submit profil baru
    FE->>PC: PUT /profile
    PC->>PC: Validate profile data
    PC->>U: Update user
    U->>DB: Save profile changes
    DB-->>U: Updated user
    U-->>PC: Success
    PC-->>FE: Return updated profile
    FE-->>User: Tampilkan perubahan
```

## 11. Sequence Diagram - Verifikasi Email / Reset Password
```mermaid
sequenceDiagram
    actor User
    participant FE as Frontend Vue/Inertia
    participant VC as VerificationController
    participant AC as AuthController
    participant U as User Model
    participant DB as Database
    participant Mail as Email Service

    User->>FE: Klik verifikasi / reset password
    FE->>VC: GET/POST verification endpoint
    VC->>U: Load user verification status
    U->>DB: Query user
    DB-->>U: User data
    VC-->>FE: Verification result / redirect

    User->>FE: Request reset password
    FE->>AC: POST /auth/forgot-password
    AC->>Mail: Send reset link
    Mail-->>User: Email reset password
```

## 12. Component Diagram
```mermaid
flowchart LR
    subgraph Frontend[Frontend]
        Browser[Browser/User]
        Pages[Vue Pages]
        Components[Vue Components]
        Inertia[Inertia Bridge]
    end

    subgraph Backend[Backend]
        Routes[Laravel Routes]
        Middleware[Middleware]
        Controllers[Controllers]
        Services[Services]
        Models[Models]
    end

    subgraph DataLayer[Data Layer]
        DB[(Database)]
        Storage[(Storage/Public Assets)]
    end

    subgraph External[External Services]
        Midtrans[Midtrans API]
        Email[Email Service]
    end

    Browser --> Pages
    Pages --> Components
    Pages --> Inertia
    Inertia --> Routes
    Routes --> Middleware
    Middleware --> Controllers
    Controllers --> Services
    Services --> Models
    Models --> DB
    Controllers --> Storage
    Controllers --> Midtrans
    Controllers --> Email
    Controllers --> Models
```

## 13. Arsitektur Diagram
```mermaid
flowchart TB
    subgraph ClientLayer[Client Layer]
        User[User / Admin]
        Browser[Web Browser]
    end

    subgraph Presentation[Presentation Layer]
        Vue[Vue 3 Pages]
        UI[Vue Components]
        Inertia[Inertia Bridge]
    end

    subgraph Application[Application / API Layer]
        Routes[Laravel Routes]
        Middleware[Auth / IsAdmin Middleware]
        Controllers[Controllers]
        Services[Services]
    end

    subgraph Domain[Domain Layer]
        Models[Models]
        BookingDomain[Booking Logic]
        PaymentDomain[Payment Logic]
        FieldDomain[Field Logic]
    end

    subgraph Infrastructure[Infrastructure Layer]
        DB[(MySQL Database)]
        Storage[(File Storage)]
        Midtrans[Midtrans Gateway]
        Mail[Email Service]
    end

    User --> Browser --> Vue --> UI --> Inertia --> Routes
    Routes --> Middleware --> Controllers --> Services --> Models
    Services --> BookingDomain
    Services --> PaymentDomain
    Services --> FieldDomain
    Models --> DB
    Controllers --> Storage
    Controllers --> Midtrans
    Controllers --> Mail
```

## 14. Arsitektur Sistem
```mermaid
flowchart TB
    subgraph Actors[Actors]
        User[User]
        Admin[Admin]
    end

    subgraph FrontendApp[Frontend Web App]
        Landing[Landing Page]
        BookingUI[Booking UI]
        DashboardUI[Dashboard UI]
        ProfileUI[Profile UI]
    end

    subgraph BackendApp[Laravel Backend]
        Auth[Auth Endpoints]
        Fields[Field API]
        Schedule[Schedule API]
        Bookings[Booking API]
        Payments[Payment API]
        Profile[Profile API]
        AdminGuard[IsAdmin Middleware]
    end

    subgraph DataServices[Data Services]
        MySQL[(MySQL Database)]
        Storage[(Storage / Public Assets)]
        Midtrans[(Midtrans)]
        Email[(Email Service)]
    end

    User --> Landing
    User --> BookingUI
    User --> ProfileUI
    Admin --> DashboardUI

    Landing --> Auth
    BookingUI --> Schedule
    BookingUI --> Bookings
    BookingUI --> Payments
    ProfileUI --> Profile
    DashboardUI --> AdminGuard
    AdminGuard --> Fields
    AdminGuard --> Bookings
    AdminGuard --> Payments

    Auth --> MySQL
    Fields --> MySQL
    Schedule --> MySQL
    Bookings --> MySQL
    Payments --> MySQL
    Profile --> MySQL

    Fields --> Storage
    Bookings --> Midtrans
    Payments --> Midtrans
    Auth --> Email
    Profile --> Email
```
