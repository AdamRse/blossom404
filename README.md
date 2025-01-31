# Architecture SOLID
app/
├── Interfaces/                      # Contient toutes les interfaces (contrats)
│   ├── Repositories/               # Interfaces pour l'accès aux données
│   │   ├── Plant/                  # Repositories pour la gestion des plantes
│   │   │   └── PlantRepositoryInterface.php    # Interface pour les opérations CRUD sur les plantes
│   │   └── Weather/                # Repositories pour la gestion de la météo
│   │       └── WeatherRepositoryInterface.php   # Interface pour l'accès aux données météo
│   └── Services/                   # Interfaces pour la logique métier
│       ├── Plant/                  # Services liés aux plantes
│       │   └── PerenualApiServiceInterface.php  # Interface pour l'API Perenual
│       └── Weather/                # Services liés à la météo
│           └── WeatherServiceInterface.php      # Interface pour le service météo
└── Services/                       # Implémentations des services
   ├── Plant/                      # Services concrets pour les plantes
   │   └── PerenualApiService.php             # Implémentation du service Perenual
   └── Weather/                    # Services concrets pour la météo
       └── WeatherService.php                  # Implémentation du service météo
