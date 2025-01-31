# Architecture SOLID
app/
├── Http/
│   ├── Controllers/          # Contrôleurs légers, utilisent les services
│   ├── Requests/            # Form requests pour la validation
│   └── Resources/           # Resources pour la transformation des données
├── Interfaces/             # Toutes les interfaces
│   ├── Repositories/       # Interfaces des repositories
│   └── Services/          # Interfaces des services
├── Services/              # Implémentations des services
│   ├── Plant/            # Services liés aux plantes
│   └── Weather/          # Services liés à la météo
├── Repositories/          # Implémentations des repositories
│   ├── Plant/            # Repositories liés aux plantes
│   └── Weather/          # Repositories liés à la météo
├── Models/                # Modèles Eloquent
├── DTOs/                  # Data Transfer Objects
└── Providers/             # Service Providers pour les bindings
