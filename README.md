# projet-toubilib
# Toubilib - API RESTful (BUT Informatique S5)

Plateforme de gestion de rendez-vous médicaux développée avec **PHP 8.5**, **Slim 4**, **PHP-DI** et **PostgreSQL 18.6**.

## 📌 Fonctionnalités du TD1.4 (API RESTful, HATEOAS & CORS)

### 1. Routes API RESTful
L'API respecte la convention de nommage REST basée sur des ressources (noms au pluriel) et les méthodes HTTP appropriées :

* **Praticiens**
  * `GET /praticiens` : Liste tous les praticiens (support du filtrage ex: `?specialite=Cardiologie`).
  * `GET /praticiens/{id}` : Obtient le profil détaillé d'un praticien.
* **Rendez-vous**
  * `GET /rdvs/{id}` : Récupère les détails d'un rendez-vous avec liens HATEOAS.
  * `POST /rdvs` : Planifie un nouveau rendez-vous (`201 Created`).
  * `PATCH /rdvs/{id}/annuler` : Annule un rendez-vous existant (`200 OK`).

### 2. Format des données et HATEOAS
Les réponses JSON de consultation de ressource intègrent des hyperliens HATEOAS (`links`) :

```json
{
  "type": "ressource",
  "rendez_vous": {
    "id": "1",
    "praticien_id": "41ac1467-4874-3e88-a636-444eae7dc457",
    "patient_id": "pat123",
    "date_debut": "15/12/2026 14:30",
    "duree": 30,
    "motif_visite": "Consultation",
    "etat": "PLANIFIE"
  },
  "links": {
    "self": { "href": "/rdvs/1" },
    "praticien": { "href": "/praticiens/41ac1467-4874-3e88-a636-444eae7dc457" },
    "patient": { "href": "/patients/pat123" },
    "annuler": { "href": "/rdvs/1/annuler" },
    "honorer": { "href": "/rdvs/1/honorer" },
    "ignorer": { "href": "/rdvs/1/ignorer" }
  }
}