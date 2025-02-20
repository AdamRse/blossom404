# Architecture SOLID
app/
├── Http/
│   ├── Controllers/                    # Contrôleurs de l'application
│   │   ├── AuthController.php          # Gestion de l'authentification
│   │   ├── PlantController.php         # Gestion des plantes
│   │   └── UserPlantController.php     # Gestion des plantes des utilisateurs
│   └── OpenApi/                        # Documentation Swagger/OpenAPI
│       ├── ApiInfo.php                 # Configuration générale de l'API
│       └── Schemas/                    # Schémas de documentation
│           ├── PlantSchemas.php
│           └── UserPlantSchemas.php
├── Interfaces/                         # Interfaces définissant les contrats
│   ├── Repositories/
│   │   └── Plant/
│   │       └── PlantRepositoryInterface.php
│   └── Services/
│       ├── Plant/
│       │   └── PerenualApiServiceInterface.php
│       ├── Watering/
│       │   └── WateringServiceInterface.php
│       └── Weather/
│           └── WeatherServiceInterface.php
├── Models/                             # Modèles Eloquent
│   ├── Plant.php                       # Modèle pour les plantes
│   └── User.php                        # Modèle pour les utilisateurs
├── Notifications/                      # Notifications Laravel
│   └── WateringReminder.php            # Notification de rappel d'arrosage
├── Providers/                          # Service Providers
│   ├── AppServiceProvider.php          # Configuration des services de base
│   └── RepositoryServiceProvider.php   # Binding des interfaces
├── Repositories/                       # Implémentation des repositories
│   └── Plant/
│       └── PlantRepository.php         # Accès aux données des plantes
└── Services/                           # Implémentation des services
    ├── Plant/
    │   └── PerenualApiService.php      # Service d'API externe pour les plantes
    ├── Watering/
    │   └── WateringService.php         # Service de gestion de l'arrosage
    └── Weather/
        └── WeatherService.php          # Service de météo

# Lancer le traitement des job en file d'attente
```bash
php artisan queue:work #En dev
php artisan queue:work --daemon #En prod
```
